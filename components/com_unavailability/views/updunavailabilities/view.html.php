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
class UnavailabilityViewUpdunavailabilities extends JViewLegacy {
	
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
	 * @var unknown
	 */
	protected $pagination;
	
	/**
	 * Stores the current logged in user
	 * @var unknown
	 */
	protected $user;
	
	/**
	 * {@inheritDoc}
	 * @see JViewLegacy::display()
	 */
	public function display($tpl=null) {
		$this->categories = $this->get('CategoryOrders');
		$this->items      = $this->get('Items');
		$this->state      = $this->get('State');
		$this->pagination = $this->get('Pagination');
		// searchtools layout helper objects...
		$this->filterForm    = $this->get('FilterForm');    
		$this->activeFilters = $this->get('ActiveFilters');  
		
		if(count($errors = $this->get('Errors'))) {
			throw new RuntimeException(implode("\n", $errors), 500);
			return false;
		}
		
		$this->user = JFactory::getUser();
		
		if($this->user->id == 0) {
			$joomlaLoginUrl = 'index.php?option=com_users&view=login';
			$errorMsg = (
				JText::_('COM_UNAVAILABILITY_ERROR_MUST_LOGIN').
				'<br>'.
				'<a href="'. $joomlaLoginUrl .'">'. 
					JText::_('COM_UNAVAILABILITY_LOG_IN') .
				'</a>'
			);
			JError::raiseWarning(403, $errorMsg);
		} else {
			return parent::display($tpl);
		}
	}
	
	/**
	 * Used by the drop-down filter.
	 * @return string[]
	 */
	protected function getSortFields() {
		return array(
			'a.ordering'=>JText::_('COM_UNAVAILABILITY_FIELD_ORDERING_LABEL'),
			'a.catid'=>JText::_('COM_UNAVAILABILITY_FIELD_CATEGORY_LABEL'),
			'a.title'=>JText::_('COM_UNAVAILABILITY_FIELD_TITLE_LABEL'),
			'a.state'=>JText::_('COM_UNAVAILABILITY_FIELD_STATUS_LABEL'),
			'a.dthr_emissao'=>JText::_('COM_UNAVAILABILITY_FIELD_DTHR_EMISSAO_LABEL'),
			'a.dthr_inicio'=>JText::_('COM_UNAVAILABILITY_FIELD_DTHR_INICIO_LABEL'),
			'a.dthr_final'=>JText::_('COM_UNAVAILABILITY_FIELD_DTHR_FINAL_LABEL'),
			'a.responsavel'=>JText::_('COM_UNAVAILABILITY_FIELD_RESPONSAVEL_LABEL'),
			'a.id'=>JText::_('COM_UNAVAILABILITY_FIELD_ID_LABEL')
		);
	}
}