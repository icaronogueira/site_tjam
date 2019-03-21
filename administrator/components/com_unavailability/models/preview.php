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

class UnavailabilityModelPreview extends JModelList {

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
				'a.responsavel,  '.
				'a.dthr_inicio,  '.
				'a.dthr_final,   '.
				'a.dthr_emissao, '.
				'a.sistemas,     '.
				'a.detalhes,     '.
				'a.state,        '.
				'a.checked_out   '
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

		$query->where('(a.state IN (0, 1))'); // limit to published and unpublished items

		return $query;
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
		// Load the parameters.
		$this->setState('params', JComponentHelper::getParams('com_unavailability'));

		// List state information.
		parent::populateState($ordering, $direction); // ordering by order (default)
	}

}