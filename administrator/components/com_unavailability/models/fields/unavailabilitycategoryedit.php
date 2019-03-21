<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_unavailability
 * @author		Marcus Martinho (marcus.martinho@tjam.jus.br)
 *
 * @copyright   Copyright (C) 2016 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

/**
 * Register external libraries
 */
JLoader::register('JFormFieldCategoryEdit',JPATH_ADMINISTRATOR.'/components/com_categories/models/fields/categoryedit.php');

use Joomla\Utilities\ArrayHelper;

class JFormFieldUnavailabilityCategoryEdit extends JFormFieldCategoryEdit {
	/**
	 * The form field type.
	 *
	 * @var    string
	 */
	public $type = 'UnavailabilityCategoryEdit';
	
	/**
	 * @var string
	 */
	protected $unavailabilityCategoryParentID = '';
	
	/**
	 * {@inheritDoc}
	 * @see JFormFieldCategoryEdit::setup()
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null) {
		$return = parent::setup($element, $value, $group);
		
		if ($return) {
			$category_parent_id = JComponentHelper::getParams('com_unavailability')->get('unavailability_category_parent_id');
			$this->unavailabilityCategoryParentID = $category_parent_id ? $category_parent_id : '';
		}
	
		return $return;
	}
	
	/**
	 * {@inheritDoc}
	 * @see JFormFieldCategoryEdit::__get()
	 */
	public function __get($name) {
		switch ($name) {
			case 'unavailabilityCategoryParentID':
				return $this->$name;
		}
	
		return parent::__get($name);
	}
	
	/**
	 * {@inheritDoc}
	 * @see JFormFieldCategoryEdit::__set()
	 */
	public function __set($name, $value) {
		$value = (string) $value;
	
		switch ($name) {
			case 'unavailabilityCategoryParentID':
				$this->$name = is_int($value) ? $value : '';
				break;
			default:
				parent::__set($name, $value);
		}
	}
	
	/**
	 * The same JFormFieldCategoryEdit::getOptions(), 
	 * except for the system parent filter, to retrive only
	 * the categories that Systems belongs to.
	 * 
	 * {@inheritDoc}
	 * @see JFormFieldCategoryEdit::getOptions()
	 */
	protected function getOptions() {
		$options = array();
		$published = $this->element['published'] ? $this->element['published'] : array(0, 1);
		$name = (string) $this->element['name'];
		
		// Let's get the id for the current item, either category or content item.
		$jinput = JFactory::getApplication()->input;
		
		// Load the category options for a given extension.
		
		// For categories the old category is the category id or 0 for new category.
		if ($this->element['parent'] || $jinput->get('option') == 'com_categories') {
			$oldCat = $jinput->get('id', 0);
			$oldParent = $this->form->getValue($name, 0);
			$extension = $this->element['extension'] ? (string) $this->element['extension'] : (string) $jinput->get('extension', 'com_content');
		} else {
			// For items the old category is the category they are in when opened or 0 if new.
			$oldCat = $this->form->getValue($name, 0);
			$extension = $this->element['extension'] ? (string) $this->element['extension'] : (string) $jinput->get('option', 'com_content');
		}
		
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true)
			->select('DISTINCT a.id AS value, a.title AS text, a.level, a.published, a.lft');
		
		$subQuery = $db->getQuery(true)
			->select('id,title,level,published,parent_id,extension,lft,rgt')
			->from('#__categories');
		
		// Filter by the extension type
		if ($this->element['parent'] == true || $jinput->get('option') == 'com_categories') {
			$subQuery->where('(extension = ' . $db->quote($extension) . ' OR parent_id = 0)');
		} else {
			$subQuery->where('(extension = ' . $db->quote($extension) . ')');
		}
		
		// Filter by unavailability category parent 
		// Differs from the original JFormFieldCategoryEdit::getOptions()
		if($this->unavailabilityCategoryParentID) {
			$subQuery->where('parent_id = '.$this->unavailabilityCategoryParentID);
		}
		
		// Filter language
		if (!empty($this->element['language'])) {
			$subQuery->where('language = ' . $db->quote($this->element['language']));
		}
		
		// Filter on the published state
		if (is_numeric($published)) {
			$subQuery->where('published = ' . (int) $published);
		} elseif (is_array($published)) {
			$subQuery->where('published IN (' . implode(',', ArrayHelper::toInteger($published)) . ')');
		}
		
		$query->from('(' . (string) $subQuery . ') AS a')
			->join('LEFT', $db->quoteName('#__categories') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt');
		$query->order('a.lft ASC');
		
		// If parent isn't explicitly stated but we are in com_categories assume we want parents
		if ($oldCat != 0 && ($this->element['parent'] == true || $jinput->get('option') == 'com_categories')) {
			// Prevent parenting to children of this item.
			// To rearrange parents and children move the children up, not the parents down.
			$query->join('LEFT', $db->quoteName('#__categories') . ' AS p ON p.id = ' . (int) $oldCat)
				->where('NOT(a.lft >= p.lft AND a.rgt <= p.rgt)');
		
			$rowQuery = $db->getQuery(true);
			$rowQuery->select('a.id AS value, a.title AS text, a.level, a.parent_id')
				->from('#__categories AS a')
				->where('a.id = ' . (int) $oldCat);
			$db->setQuery($rowQuery);
			$row = $db->loadObject();
		}
		
		// Get the options.
		$db->setQuery($query);
		
		try {
			$options = $db->loadObjectList();
		} catch (RuntimeException $e) {
			JError::raiseWarning(500, $e->getMessage());
		}
		
		// Pad the option text with spaces using depth level as a multiplier.
		for ($i = 0, $n = count($options); $i < $n; $i++) {
			// Translate ROOT
			if ($this->element['parent'] == true || $jinput->get('option') == 'com_categories') {
				if ($options[$i]->level == 0) {
					$options[$i]->text = JText::_('JGLOBAL_ROOT_PARENT');
				}
			}
		
			// Displays language code if not set to All
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select($db->quoteName('language'))
				->where($db->quoteName('id') . '=' . (int) $options[$i]->value)
				->from($db->quoteName('#__categories'));
		
			$db->setQuery($query);
			$language = $db->loadResult();
		
			if ($options[$i]->published == 1) {
				$options[$i]->text = str_repeat('- ', $options[$i]->level) . $options[$i]->text;
			} else {
				$options[$i]->text = str_repeat('- ', $options[$i]->level) . '[' . $options[$i]->text . ']';
			}
		
			if ($language !== '*') {
				$options[$i]->text = $options[$i]->text . ' (' . $language . ')';
			}
		}
		
		// Get the current user object.
		$user = JFactory::getUser();
		
		// For new items we want a list of categories you are allowed to create in.
		if ($oldCat == 0) {
			foreach ($options as $i => $option) {
				/*
				 * To take save or create in a category you need to have create rights for that category unless the item is already in that category.
				 * Unset the option if the user isn't authorised for it. In this field assets are always categories.
				 */
				if ( $user->authorise('core.create', $extension . '.category.' . $option->value) != true 
					 && $option->level != 0) {
					unset($options[$i]);
				}
			}
		} else { // If you have an existing category id things are more complex.
			/*
			 * If you are only allowed to edit in this category but not edit.state, you should not get any
			 * option to change the category parent for a category or the category for a content item,
			 * but you should be able to save in that category.
			 */
			foreach ($options as $i => $option) {
				if ( $user->authorise('core.edit.state', $extension . '.category.' . $oldCat) != true 
					 && !isset($oldParent)) {
					if ($option->value != $oldCat) {
						unset($options[$i]);
					}
				}
		
				if ( $user->authorise('core.edit.state', $extension . '.category.' . $oldCat) != true 
					 && (isset($oldParent)) && $option->value != $oldParent) {
					unset($options[$i]);
				}
		
				/*
				 * However, if you can edit.state you can also move this to another category for which you have
				 * create permission and you should also still be able to save in the current category.
				 */
				if (( $user->authorise('core.create', $extension . '.category.' . $option->value) != true)
					  && ($option->value != $oldCat && !isset($oldParent))) {
					unset($options[$i]);
				}
		
				if (( $user->authorise('core.create', $extension . '.category.' . $option->value) != true)
					  && (isset($oldParent))
					  && $option->value != $oldParent) {
					unset($options[$i]);
				}
			}
		}
		
		if ( ($this->element['parent'] == true || $jinput->get('option') == 'com_categories')
			 && (isset($row) && !isset($options[0]))
			 && isset($this->element['show_root'])) {
			if ($row->parent_id == '1') {
				$parent = new stdClass;
				$parent->text = JText::_('JGLOBAL_ROOT_PARENT');
				array_unshift($options, $parent);
			}
			array_unshift($options, JHtml::_('select.option', '0', JText::_('JGLOBAL_ROOT')));
		}
		
		// Merge any additional options in the XML definition.
		return array_merge(
			JFormFieldList::getOptions(), // Differs from the original JFormFieldCategoryEdit::getOptions() 
			$options
		);
	}
}