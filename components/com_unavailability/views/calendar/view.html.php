<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_unavailability
 *
 * @copyright   Copyright (C) 2016 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View Unavailability Documents in a Calendar layout
 * @author Marcus
 */
class UnavailabilityViewCalendar extends JViewLegacy{

	/**
	 * Category data
	 *
	 * @var  array
	 */
	protected $categories;
	
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
	 * Stores the pagination object from the model.
	 * @var
	 */
	protected $pagination;
	
	/**
	 * @var array
	 */
	protected $params;
	
	/**
	 * {@inheritDoc}
	 * @see JViewLegacy::display()
	 */
	public function display($tpl = null) {
		//$this->items = $this->get('Items'); //the items will be loaded by the calendar widget ajax request
		$this->state = $this->get('State');
		//$this->pagination = $this->get('Pagination');
		$this->categories = $this->get('CategoryOrders');
		
		if(count($errors = $this->get('Errors'))) {
			throw new RuntimeException(implode("\n", $errors), 500);
			return false;
		}
		
		return parent::display($tpl);
	}
	
	/**
	 * Used by the drop-down filter.
	 * @return string[]
	 */
	protected function getSortFields() {
		return array(
			'a.ordering'=>JText::_('COM_UNAVAILABILITY_FIELD_ORDERING_LABEL'),
			'a.catid'=>JText::_('JCATEGORY'),
			'a.title'=>JText::_('COM_UNAVAILABILITY_FIELD_TITLE_LABEL'),
			'a.dthr_emissao'=>JText::_('COM_UNAVAILABILITY_FIELD_DTHR_EMISSAO_LABEL'),
			'a.dthr_inicio'=>JText::_('COM_UNAVAILABILITY_FIELD_DTHR_INICIO_LABEL'),
			'a.dthr_final'=>JText::_('COM_UNAVAILABILITY_FIELD_DTHR_FINAL_LABEL'),
			'a.responsavel'=>JText::_('COM_UNAVAILABILITY_FIELD_RESPONSAVEL_LABEL'),
			'a.id'=>JText::_('JGRID_HEADING_ID')
		);
	}
}