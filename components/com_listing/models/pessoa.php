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
include_once 'pessoalocal.php';
include_once 'atividade.php';

/**
 * @author Marcus
 *
 */
class ListingModelPessoa extends JModelRESTList {
	protected $_params=null;
	protected $_modelPessoaComissao=null;
	protected $_modelPessoaLocal=null; 
	protected $_modelAtividade=null;
	
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
	 * @return mixed|boolean
	 */
	public function getComissaoItems() {
		if(!$this->_modelPessoaComissao) {
			$this->_modelPessoaComissao = new ListingModelPessoaComissao(null,$this->getState('filter.id',0));
		} 
		return $this->_modelPessoaComissao->getItems();
	}
	
	/**
	 * @return JPagination|NULL
	 */
	public function getComissaoPagination() {
		if($this->_modelPessoaComissao)
			return $this->_modelPessoaComissao->getPagination();
		else 
			return null;
	}
	
	/**
	 * @return mixed|boolean
	 */
	public function getLocalItems() {
		if(!$this->_modelPessoaLocal) {
			$this->_modelPessoaLocal = new ListingModelPessoaLocal(null,$this->getState('filter.id',0));
		}
		return $this->_modelPessoaLocal->getItems();
	}
	
	/**
	 * @return JPagination|NULL
	 */
	public function getLocalPagination() {
		if($this->_modelPessoaLocal)
			return $this->_modelPessoaLocal->getPagination();
		else
			return null;
	}
	
	/**
	 * @return mixed|boolean
	 */
	public function getAtividadeItems() {
		if(!$this->_modelAtividade) {
			$this->_modelAtividade = new ListingModelAtividade($this->getState('filter.id',0));
		}
		return $this->_modelAtividade->getItems();
	}
	
	/**
	 * @return JPagination|NULL
	 */
	public function getAtividadePagination() {
		if($this->_modelAtividade)
			return $this->_modelAtividade->getPagination();
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