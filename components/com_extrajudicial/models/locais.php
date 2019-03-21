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

include_once 'categorias.php';

class ExtrajudicialModelLocais extends JModelRESTList {
	protected $_params=null;
	protected $_modelCategorias=null; 
	
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
				'endereco',
				'emails',
				'horarios',
				'cidade',
			);
		}
		
		$this->_params = JComponentHelper::getParams('com_extrajudicial');
		
		if(empty($config['restAPI'])) {
			$config['restAPI'] = array(
				'restusername'=>$this->_params ? $this->_params->get('restusername') : '',	
				'restpassword'=>$this->_params ? $this->_params->get('restpassword') : '',
				'restserverurl'=>$this->_params ? $this->_params->get('restserverurl') : '',
				'restclass'=>'local',
			);
		}
		
		parent::__construct($config);
	}
	
	/**
	 * {@inheritDoc}
	 * @see JModelRESTList::getRestQueryString()
	 */
	protected function getRestQueryString() {
		$categoryids = $this->getState('filter.categoryids');
		$categoryid = $this->getState('filter.categoryid');
		
		return 'categoriaids='.($categoryid ? $categoryid : $categoryids);
	}
	
	/**
	 * @return mixed|boolean
	 */
	public function getCategoryItems() {
		if(!$this->_modelCategorias) {
			$this->_modelCategorias = new ExtrajudicialModelCategorias();
		}
		return $this->_modelCategorias->getItems();
	}
	
	/**
	 * @return string
	 */
	public function getSelectedCategory() {
		return $this->getState('filter.categoryid');
	}
	
	/**
	 * {@inheritDoc}
	 * @see JModelList::populateState()
	 */
	protected function populateState($ordering = 'order', $direction = 'asc') {
		parent::populateState($ordering, $direction);
		
		$app = JFactory::getApplication('site'); // frontend application
	
		$categoryid = $app->input->get('categoryid', '', 'STRING');
		
		$categoryids = $this->getUserStateFromRequest(
			$this->context.'filter.categoryids',
			'filter_categoryids',
			$app->input->get('categoryids', '', 'STRING')
		);
		
		$this->setState('filter.categoryids', $categoryids);
		$this->setState('filter.categoryid', $categoryid);
		
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
	}
	
}