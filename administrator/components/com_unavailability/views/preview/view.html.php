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
 * View Unavailability Documents
 * @author Marcus
 */
class UnavailabilityViewPreview extends JViewLegacy {
	
	/**
	 * Stores the data array from the model.
	 * @var object[]
	 */
	protected $items;
	
	/**
	 * Stores the state from the model.
	 * @var unknown
	 */
	protected $state;
	
	/**
	 * {@inheritDoc}
	 * @see JViewLegacy::display()
	 */
	public function display($tpl=null) {
		$this->items = $this->get('Items'); 
		$this->state = $this->get('State');
		
		if(count($errors = $this->get('Errors'))) {
			throw new RuntimeException(implode("\n", $errors), 500);
			return false;
		}
		
		$this->addToolbar();
		$this->prepareSidebar();
		return parent::display($tpl);
	}
	
	/**
	 * Sidebar configuration and preparation
	 */
	protected function prepareSidebar() {
		$url=JURI::root(true).'/administrator/components/com_unavailability/media/css/preview.css';
		JFactory::getDocument()->addStyleSheet($url);
		
		UnavailabilityHelper::addSubmenu('preview'); // activate the submenu (@see UnavailabilityHelper)
		$this->sidebar = JHtmlSidebar::render(); // prepare the sidebar to the view
	}
	
	/**
	 * Toolbar setup
	 */
	protected function addToolbar() {
		$user = JFactory::getUser();
		
		JToolbarHelper::title(JText::_('COM_UNAVAILABILITY_UNAVAILABILITIES_PREVIEW'), '');
		JToolbarHelper::back('COM_UNAVAILABILITY_BUTTON_BACK', 'index.php?option=com_unavailability');
	}
	
	/**
	 * Used by the drop-down filter.
	 * @return string[]
	 */
	protected function getSortFields() {
		return array(
			'a.ordering'=>JText::_('JGRID_HEADING_ORDERING'),
			'a.catid'=>JText::_('JCATEGORY'),
			'a.title'=>JText::_('JGLOBAL_TITLE'),
			'a.state'=>JText::_('JSTATUS'),
			'a.dthr_emissao'=>JText::_('COM_UNAVAILABILITY_FIELD_DTHR_EMISSAO_LABEL'),
			'a.dthr_inicio'=>JText::_('COM_UNAVAILABILITY_FIELD_DTHR_INICIO_LABEL'),
			'a.dthr_final'=>JText::_('COM_UNAVAILABILITY_FIELD_DTHR_FINAL_LABEL'),
			'a.responsavel'=>JText::_('COM_UNAVAILABILITY_FIELD_RESPONSAVEL_LABEL'),
			'a.id'=>JText::_('JGRID_HEADING_ID')
		);
	}
}