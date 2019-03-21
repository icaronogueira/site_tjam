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

class JFormFieldSystems extends JFormFieldList {
	/**
	 * The form field type.
	 *
	 * @var    string
	 */
	protected $type = 'systems';

	/**
	 * Get client list in text/value format for a select field
	 *
	 * @return  array
	 */
	public static function getSystemsOptions() {
		$options = array();
	
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('a.id AS value, a.title AS text')
			->from('#__unavailability_systems AS a')
			->where('a.state = 1')
			->order('a.title');
	
		// Get the options.
		$db->setQuery($query);
	
		try {
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e) {
			JError::raiseWarning(500, $e->getMessage());
		}
	
		array_unshift($options, JHtml::_('select.option', '0', JText::_('COM_UNAVAILABILITY_FIELD_SISTEMAS_SELECT')));
	
		return $options;
	}
	
	/**
	 * {@inheritDoc}
	 * @see JFormFieldList::getInput()
	 */
	public function getInput() {
		return parent::getInput();
	}
	
	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 */
	public function getOptions() {
		return array_merge(parent::getOptions(), self::getSystemsOptions());
	}
}
