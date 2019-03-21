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
 * View Systems Definitions
 * @author Marcus
 */
class UnavailabilityViewSystems extends JViewLegacy {
	
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
		
		$this->addToolbar();
		$this->prepareSidebar();
		return parent::display($tpl);
	}
	
	/**
	 * Sidebar configuration and preparation
	 */
	protected function prepareSidebar() {		
		UnavailabilityHelper::addSubmenu('systems'); // activate the submenu (@see UnavailabilityHelper)
		$this->sidebar = JHtmlSidebar::render(); // prepare the sidebar to the view
	}
	
	/**
	 * Toolbar setup
	 */
	protected function addToolbar() {
		$user = JFactory::getUser();
		
		JToolbarHelper::title(JText::_('COM_UNAVAILABILITY_SYSTEMS'), '');
		
		if($user->authorise('core.create', 'com_unavailability.component') &&
		   $user->getAuthorisedCategories('com_unavailability', 'core.create') > 0 ) {
			JToolbarHelper::addNew('system.add');
		}
		
		if($user->authorise('core.edit', 'com_unavailability.component')) {
			JToolbarHelper::editList('system.edit');
		}
		
		if($user->authorise('core.admin', 'com_unavailability.component')) {
			JToolbarHelper::preferences('com_unavailability');
		}
		
		if($user->authorise('core.edit.state', 'com_unavailability.component')) {
			JToolbarHelper::publish('systems.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('systems.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolbarHelper::archiveList('systems.archive');
			JToolbarHelper::checkin('systems.checkin');
		}
		
		$trashed = $this->get('State')->get('filter.published') == -2; // if filter.published is active on trashed
		if($user->authorise('core.delete', 'com_unavailability.component') && $trashed) {
			JToolbarHelper::deleteList('', 'systems.delete', 'JTOOLBAR_DELETE');
		} elseif($user->authorise('core.edit.state', 'com_unavailability.component')) {
			JToolbarHelper::trash('systems.trash');
		}
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
			'a.id'=>JText::_('JGRID_HEADING_ID')
		);
	}
}