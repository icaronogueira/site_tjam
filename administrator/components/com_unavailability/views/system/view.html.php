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

/**
 * View an Unavailability Document in particular
 * @author Marcus
 */
class UnavailabilityViewSystem extends JViewLegacy {
	/**
	 * @var array
	 */
	protected $item;
	/**
	 * @var object
	 */
	protected $form;
	
	/**
	 * {@inheritDoc}
	 * @see JViewLegacy::display()
	 */
	public function display($tpl=null) {
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');
		
		if(count($errors = $this->get('Errors'))) {
			throw new RuntimeException(implode("\n", $errors), 500);
			return false;
		}
		
		$this->addToolbar();
		return parent::display($tpl);
	}
	
	/**
	 * Toolbar setup
	 */
	protected function addToolbar() {
		// Get the toolbar object instance
		$bar = JToolbar::getInstance('toolbar');
		$user = JFactory::getUser();
	
		$isNew = ($this->item->id == 0);
		
		JFactory::getApplication()->input->set('hidemainmenu', true);
		
		JToolbarHelper::title(JText::_('COM_UNAVAILABILITY_SYSTEM'), '');
		
		if(!empty($this->item->catid)) { // if there is a category associated...
			//...check is user can edit the item of this category...
			$canEdit = $user->authorise('core.edit', 'com_unavailability.category.'.(int)$this->item->catid);
		} else { 
			$canEdit = true;
		}
		
		// authorised categories count...
		$authCategories = count($user->getAuthorisedCategories('com_unavailability', 'core.create'));
		
		if($canEdit || $authCategories > 0) {
			JToolbarHelper::apply('system.apply');
			JToolbarHelper::save('system.save');
		}
		
		if($authCategories > 0) {
			JToolbarHelper::save2new('system.save2new');
		}
		
		if(!$isNew && $authCategories > 0) {
			JToolbarHelper::save2copy('system.save2copy');
		}
		
		if(empty($this->item->id)) { 
			JToolbarHelper::cancel('system.cancel');
		} else {
			JToolbarHelper::cancel('system.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}