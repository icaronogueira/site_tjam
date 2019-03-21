<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_extrajudicial
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('mmartinho.rest.restlist');

class ExtrajudicialModelCategorias extends JModelRESTList {
	protected $_params=null;
	
	protected $_category_first=null;
	
	/**
	 * Sets up an array of all fields used by the view.
	 * @param array $config
	 */
	public  function __construct($config = array()) {
		if(empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id',
				'nome',
				'apelido',
				'order',
			);
		}
		
		$this->_params = JComponentHelper::getParams('com_extrajudicial');
		
		if(empty($config['restAPI'])) {
			$config['restAPI'] = array(
				'restusername'=>$this->_params ? $this->_params->get('restusername') : '',
				'restpassword'=>$this->_params ? $this->_params->get('restpassword') : '',
				'restserverurl'=>$this->_params ? $this->_params->get('restserverurl') : '',
				'restclass'=>'categoria',
			);
		}
		
		parent::__construct($config);
	}
	
	/**
	 * {@inheritDoc}
	 * @see JModelList::populateState()
	 */
	protected function populateState($ordering = 'order', $direction = 'asc') {
		parent::populateState($ordering, $direction);
	}
	
	/**
	 * Return the first category of the category list.
	 *
	 * @return object
	 */
	public function getFirstCategory() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_category_first)) {
			$categorias = $this->getItems();
			$this->_category_first = $categorias[0]; // a primeira categoria
		}
		
		return $this->_category_first;
	}
}