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

class UnavailabilityModelUnavailabilities extends JModelList {
	
	/**
	 * Sets up an array of all fields used by the view.
	 * @param array $config
	 */
	public  function __construct($config = array()) {
		if(empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'state', 'a.state',
				'alias','a.alias',
				'responsavel', 'a.responsavel',
				'dthr_inicio', 'a.dthr_inicio',
				'dthr_final', 'a.dthr_final',
				'dthr_emissao', 'a.dthr_emissao',
				'catid', 'a.catid', 'category_title',
				'checked_out', 'a.checked_out',
				'publish_up','a.publish_up',
				'publish_down', 'a.publish_down',
				'ordering', 'a.ordering'
			);
		}
		
		parent::__construct($config);
	}
	
	/**
	 * Method to get the maximum ordering value for each category.
	 *
	 * @return  array
	 */
	public function &getCategoryOrders() {
		if (!isset($this->cache['categoryorders'])) {
			$db = $this->getDbo();
			$query = $db->getQuery(true)
				->select('MAX(ordering) as ' . $db->quoteName('max') . ', catid')
				->select('catid')
				->from('#__unavailability')
				->group('catid');
			$db->setQuery($query);
			$this->cache['categoryorders'] = $db->loadAssocList('catid', 0);
		}
	
		return $this->cache['categoryorders'];
	}
	
	/**
	 * Prepares the query to select the information 
	 * from database.
	 * {@inheritDoc}
	 * @see JModelList::getListQuery()
	 */
	protected function getListQuery() {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		
		// select...
		$query->select(
			$this->getState('list.select', 
				'a.id,           '.
				'a.catid,        '.
				'a.alias,		 '.
				'a.title,        '.
				'a.state,        '.
				'a.responsavel,  '. 
				'a.dthr_inicio,  '.
				'a.dthr_final,   '.
				'a.dthr_emissao, '.
				'a.checked_out,  '.
				'a.publish_up,   '.
				'a.publish_down, '.
				'a.ordering'
			)
		);
		
		// from...
		$query->from($db->quoteName('#__unavailability'). ' AS a');
		
		// join over the categories.
		$query->select($db->quoteName('c.title', 'category_title'))
			->join('LEFT', $db->quoteName('#__categories', 'c') . ' ON c.id = a.catid');
		
		// join over the users for the checked out user.
		$query->select($db->quoteName('uc.name', 'editor'))
			->join('LEFT', $db->quoteName('#__users', 'uc') . ' ON uc.id = a.checked_out');
		
		// filter search keyword...
		$search = $this->getState('filter.search');
		if(!empty($search)) {
			if(stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int)substr($search, 3));
			} else {
				$search = $db->quote('%'.$db->escape($search, true).'%');
				$query->where('(a.title LIKE '.$search.' OR a.responsavel LIKE '.$search.')');
			}
		}
		// filter published...
		$published = $this->getState('filter.published');
		if(is_numeric($published)) {
			$query->where('a.state='.(int)$published);
		} elseif($published === '') {
			$query->where('(a.state IN (0, 1, 2, -2))'); // all states
		}
		
		// filter category...
		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId)){
			$query->where($db->quoteName('a.catid') . ' = '.(int)$categoryId);
		}
		
		// order...
		$orderCol = $this->getState('list.ordering', 'a.title');
		$orderDirn = $this->getState('list.direction', 'asc');
		if($orderCol == 'a.ordering' || $orderCol == 'category_title') {
			$orderCol = $db->quoteName('c.title').' '.$orderDirn.','.$db->quoteName('a.ordering');
		}
		$query->order($db->escape($orderCol.' '.$orderDirn));
		
		return $query;
	}
	
	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 */
	protected function getStoreId($id = '') {
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.category_id');
	
		return parent::getStoreId($id);
	}
	
	/**
	 * {@inheritDoc}
	 * @see JModelLegacy::getTable()
	 */
	public function getTable($type = 'Unavailability', $prefix = 'UnavailabilityTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 * {@inheritDoc}
	 * @see JModelList::populateState()
	 */
	protected function populateState($ordering='a.ordering', $direction='asc') {
		// Load the filter state.
		$this->setState('filter.published', $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '', 'string'));
		$this->setState('filter.search', $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string'));
		$this->setState('filter.category_id', $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id', '', 'cmd'));
		
		// Load the parameters.
		$this->setState('params', JComponentHelper::getParams('com_unavailability'));
		
		// List state information.
		parent::populateState($ordering, $direction); // ordering by order (default)
	}

}