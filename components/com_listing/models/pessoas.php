<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_listing
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('mmartinho.rest.restlist');

class ListingModelPessoas extends JModelRESTList {
	protected $_params=null;
	
	/**
	 * Sets up an array of all fields used by the view.
	 * @param array $config
	 */
	public  function __construct($config = array()) {
		if(empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id',
				'nome',
				'cpf',
				'identidade',
				'genero',
				'titulo',
				'ativo',
				'observacoes',
				'order',
			);
		}
		
		$this->_params = JComponentHelper::getParams('com_listing');
		
		if(empty($config['restAPI'])) {
			$config['restAPI'] = array(
				'restusername'=>$this->_params ? $this->_params->get('restusername') : '',	
				'restpassword'=>$this->_params ? $this->_params->get('restpassword') : '',
				'restserverurl'=>$this->_params ? $this->_params->get('restserverurl') : '',
				'restclass'=>'pessoa',
			);
		}
		
		parent::__construct($config);
	}
	
	/**
	 * {@inheritDoc}
	 * @see JModelRESTList::getRestQueryString()
	 */
	protected function getRestQueryString() {
		return 'titulos='.urlencode($this->getState('filter.titulos'));
	}
	
	/**
	 * @return array
	 */
	public function getTitulos() {
		return explode(',',$this->getState('filter.titulos'));
	}
	
	/**
	 * {@inheritDoc}
	 * @see JModelList::populateState()
	 */
	protected function populateState($ordering = 'order', $direction = 'asc') {
		parent::populateState($ordering, $direction);
		
		$app = JFactory::getApplication('site'); // frontend application
				
		$titulos = $this->getUserStateFromRequest(
			$this->context.'filter.titulos',
			'filter_titulos', 
			$app->input->get('titulos', '','STRING')
		);		
		$this->setState('filter.titulos', $titulos);
	
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
	}
	
}