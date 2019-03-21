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

include_once 'pessoacomissao.php';

/**
 * @author Marcus
 *
 */
class ListingModelComissao extends JModelRESTList {
	protected $_params=null;
	protected $_modelPessoaComissao=null; // relacionamento com a nomeação
	
	/**
	 * Sets up an array of all fields used by the view.
	 * @param array $config
	 */
	public  function __construct($config = array()) {
		if(empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id',
				'nome',
				'dthr_inicio',
				'dthr_final',
				'ativo',
				'comentarios',
				'order',
			);
		}
	
		$this->_params = JComponentHelper::getParams('com_listing');
		
		if(empty($config['restAPI'])) {
			$config['restAPI'] = array(
				'restusername'=>$this->_params ? $this->_params->get('restusername') : '',
				'restpassword'=>$this->_params ? $this->_params->get('restpassword') : '',
				'restserverurl'=>$this->_params ? $this->_params->get('restserverurl') : '',
				'restclass'=>'comissao',
			);
		}		
		
		parent::__construct($config);
	}
	
	/**
	 * @return mixed|boolean
	 */
	public function getNomeacoesItems() {
		if(!$this->_modelPessoaComissao) {
			$this->_modelPessoaComissao = new ListingModelPessoaComissao($this->getState('filter.id',0),null);
		} 
		return $this->_modelPessoaComissao->getItems();
	}
	
	/**
	 * @return JPagination|NULL
	 */
	public function getNomeacoesPagination() {
		if($this->_modelPessoaComissao)
			return $this->_modelPessoaComissao->getPagination();
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