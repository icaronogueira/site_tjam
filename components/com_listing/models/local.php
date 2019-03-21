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

include_once 'pessoalocal.php';

/**
 * @author Marcus
 *
 */
class ListingModelLocal extends JModelRESTList {
	protected $_params=null;
	protected $_modelPessoaLocal=null; // relacionamento com a nomeação
	
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
				'telefones',
				'localizacao',
				'ativo',
				'order',
			);
		}
	
		$this->_params = JComponentHelper::getParams('com_listing');
		
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
	 * @return mixed|boolean
	 */
	public function getNomeacoesItems() {
		if(!$this->_modelPessoaLocal) {
			$this->_modelPessoaLocal = new ListingModelPessoaLocal($this->getState('filter.id',0),null);
		} 
		return $this->_modelPessoaLocal->getItems();
	}
	
	/**
	 * @return JPagination|NULL
	 */
	public function getNomeacoesPagination() {
		if($this->_modelPessoaLocal)
			return $this->_modelPessoaLocal->getPagination();
		else 
			return null;
	}
	
	/**
	 * {@inheritDoc}
	 * @see JModelList::populateState()
	 */
	protected function populateState($ordering = 'order', $direction = 'asc') {
		parent::populateState($ordering, $direction);
		
		$app = JFactory::getApplication('site'); // frontend application
	
		$this->setState('filter.id', $app->input->getInt('id'));
	}	

}