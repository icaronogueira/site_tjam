<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_sojam
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('mmartinho.rest.restlist');

/**
 * @author Marcus
 *
 */
class SojamModelManifestacao extends JModelRESTList {
	protected $_id=0;
	protected $_cpf='';
	protected $_params=null;
	
	/**
	 * Sets up an array of all fields used by the view.
	 * @param array $config
	 */
	public  function __construct($id=0, $cpf='', $config = array()) {
		if(empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id',
				'tipo_id',
				'nome',
				'cpf',
				'tpessoa',
				'email',
				'endereco',
				'cep',
				'telefone',
				'celular',
				'contato',
				'processo',
				'partes',
				'uf',
				'cidade',
				'texto',
				'relacionamento',
				'resposta_por',
				'origem_id',
				'local_id',
				'assunto_id',
				'status',
				'create_time',
				'create_user_id',
				'update_time',
				'update_user_id',
				'ip_estacao',
			);
		}
	
		$this->_params = JComponentHelper::getParams('com_sojam');
		if(empty($config['restAPI'])) {
			$config['restAPI'] = array(
				'restusername'=>$this->_params ? $this->_params->get('restusername') : '',
				'restpassword'=>$this->_params ? $this->_params->get('restpassword') : '',
				'restserverurl'=>$this->_params ? $this->_params->get('restserverurl') : '',
				'restclass'=>'manifestacao',
			);
		}
		
		$this->_id = $id;
		$this->_cpf = $cpf;
		
		parent::__construct($config);
	}
	
	/**
	 * {@inheritDoc}
	 * @see JModelRESTList::getRestQueryString()
	 */
	protected function getRestQueryString() {
		$cpf = $this->getState('filter.cpf');
		return 'cpf='.$cpf;
	}
	
	/**
	 * {@inheritDoc}
	 * @see JModelList::populateState()
	 */
	protected function populateState($ordering = 'order', $direction = 'asc') {
		parent::populateState($ordering, $direction);
		
		$app = JFactory::getApplication('site'); // frontend application
	
		$this->setState('filter.id', ($this->_id ? $this->_id : $app->input->getInt('id')) );
		$this->setState('filter.cpf', ($this->_cpf ? $this->_cpf : $app->input->getString('cpf')) );
	}	

}