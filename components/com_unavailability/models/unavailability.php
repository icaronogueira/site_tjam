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
 * @author Marcus
 *
 */
class UnavailabilityModelUnavailability extends JModelList {
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
				'sistemas', 'a.sistemas', 'system_title',
				'responsavel', 'a.responsavel',
				'dthr_inicio', 'a.dthr_inicio',
				'dthr_final', 'a.dthr_final',
				'dthr_emissao', 'a.dthr_emissao',
				'detalhes', 'a.detalhes',
				'catid', 'a.catid', 'category_title',
				'created_by', 'a.created_by', 'user_name',
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
	
		$id = $app->input->getInt('id');
		$this->setState('id', $id);
	
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
				'a.sistemas,     '.
				'a.responsavel,  '.
				'a.dthr_inicio,  '.
				'a.dthr_final,   '.
				'a.dthr_emissao, '.
				'a.detalhes,     '.
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

		// join over creator.
		$query->select($db->quoteName('u.name', 'user_name'))
			->join('LEFT', $db->quoteName('#__users', 'u') . ' ON u.id = a.created_by');
			
		// join over the users for the checked out user.
		$query->select($db->quoteName('uc.name', 'editor'))
			->join('LEFT', $db->quoteName('#__users', 'uc') . ' ON uc.id = a.checked_out');
	
		// join over the systems using multivalue field.
		$query->select($db->quoteName('us.title', 'system_title'))
			->join('LEFT', $db->quoteName('#__unavailability_systems', 'us') . ' ON FIND_IN_SET(us.id, a.sistemas)');
				
		// filter id
		$id = $this->getState('id');
		if($id) {
			$query->where('a.id ='.(int)$id);
		}
	
		return $query;
	}
	
	/**
	 * Multivalued field associated to
	 * Sistema Model
	 * @see getListQuery() multivalue field join
	 * 
	 * @return string[]
	 */
	public function getSistemas() {
		$items = $this->getItems();
		$sistemas = array();
		foreach ($items as $item) {
			if($item->system_title) 
				$sistemas[]=$item->system_title;
		}
		return $sistemas;
	}
}