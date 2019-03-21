<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_listing
 * @author		Marcus Martinho (marcus.martinho@tjam.jus.br)
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * View Info
 * @author Marcus
 */
class ListingViewInfo extends JViewLegacy {
	
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
		//$this->categories = $this->get('CategoryOrders');
		//$this->items      = $this->get('Items');
		//$this->state      = $this->get('State');
		//$this->pagination = $this->get('Pagination');
		// searchtools layout helper objects...
		//$this->filterForm    = $this->get('FilterForm');    
		//$this->activeFilters = $this->get('ActiveFilters');  
		
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
		ListingHelper::addSubmenu('info'); // activate the submenu (@see ListingHelper)
		$this->sidebar = JHtmlSidebar::render(); // prepare the sidebar to the view
	}
	
	/**
	 * Toolbar setup
	 */
	protected function addToolbar() {
		$user = JFactory::getUser();
		
		JToolbarHelper::title(JText::_('COM_LISTING_INFO'), '');
		
		if($user->authorise('core.admin', 'com_listing.component')) {
			JToolbarHelper::preferences('com_listing');
		}
	}

}