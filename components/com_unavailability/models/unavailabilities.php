<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_unavailability
 *
 * @copyright   Copyright (C) 2016 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
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
	 * {@inheritDoc}
	 * @see JModelList::populateState()
	 */
	protected function populateState($ordering = null, $direction = null) {
		$app = JFactory::getApplication('site'); // frontend application
	
		$categoryId = $app->input->getInt('catid');
		$this->setState('filter.category_id', $categoryId);
	
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
	
		parent::populateState('a.ordering', 'asc');
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
		$query->where('(a.state IN (1))'); // limit to published items
	
		// filter category...
		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId) && $categoryId > 0){
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
}