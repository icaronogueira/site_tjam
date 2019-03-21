<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_listing
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('mmartinho.rest.restadmin');
jimport('mmartinho.rest.resttable');

include_once 'manifestacao.php';

/**
 * @author Marcus
 *
 */
class SojamModelMovimentacoes extends JModelRESTAdmin {
	
	const mvstsAgendada=0; 
	const mvstsCancelada=1; 
	const mvstsRealizada=2; 
	const mvstsNRealizada=3; 
	
	/**
	 * @var SojamModelManifestacao
	 */
	protected $_modelManifestacao=null;
	
	/**
	 * @var array
	 */
	protected $_params=null;
	
	/**
	 * The prefix to use with controller messages.
	 * @var string
	 */
	protected $text_prefix = 'COM_SOJAM';
	
	/**
	 * The type alias for this content type.
	 * @var    string
	 */
	public $typeAlias = 'com_sojam.movimentacoes';
	
	/**
	 * Context string for the model type.  This is used to handle uniqueness
	 * when dealing with the getStoreId() method and caching data structures.
	 */
	protected $context = null;
	
	/**
	 * Internal memory based cache array of data.
	 *
	 * @var    array
	 */
	protected $cache = array();
	
	/**
	 * @param array $config
	 */
	public function __construct($config) {
		if(empty($config['restAPI'])) {
			// REST remote class basic configuration from component params...
			$this->_params = JComponentHelper::getParams('com_sojam');
			$config['restAPI']= array(
				'restusername'=>$this->_params ? $this->_params->get('restusername') : '',
				'restpassword'=>$this->_params ? $this->_params->get('restpassword') : '',
				'restserverurl'=>$this->_params ? $this->_params->get('restserverurl') : '',
				'restclass'=>'movimentacao',
			);
		}
		parent::__construct($config);

		// Add the ordering filtering fields whitelist.
		if (isset($config['filter_fields'])) {
			$this->filter_fields = $config['filter_fields'];
		}
		
		// Guess the context as Option.ModelName.
		if (empty($this->context)) {
			$this->context = strtolower($this->option . '.' . $this->getName());
		}
	}
	
	/**
	 * {@inheritDoc}
	 * @see JRESTModel::getRESTTable()
	 */
	public function getRESTTable($type = 'Movimentacoes', $prefix = 'SojamRESTTable', $restConfig=array()) {
		return JRESTTable::getInstance($type, $prefix, $this->_restConfig);
	}
	
	/**
	 * Get the form object based on XML file where all the
	 * field are defined. It uses the loadFormData() function.
	 * @see SojamModelNovamanifestacao::loadFormData()
	 * {@inheritDoc}
	 * @see JModelRESTForm::getForm()
	 */
	public function getForm($data = array(), $loadData = true) {	
		$form = $this->loadForm(
			'com_sojam.movimentacoes',
			'movimentacoes',
			array('control'=>'jform', 'load_data'=>$loadData)
		);
	
		if(empty($form)) { // check a valid form...
			return false;
		}
	
		$params = JComponentHelper::getParams('com_sojam');
		if(!$params->get('use_captcha')) { // check if captcha is disabled...
			$form->removeField('captcha'); // ...remove captcha definition
		}
	
		return $form;
	}
	
	/**
	 * Loads the data just from user state as, in this case, 
	 * it is used only for the search.
	 * 
	 * {@inheritDoc}
	 * @see JModelRESTForm::loadFormData()
	 */
	protected function loadFormData() {
		$data = JFactory::getApplication('site')->getUserState(
			'com_sojam.default.movimentacoes.data',
			array()
		);
		return $data;
	}
	
	/**
	 * @return string
	 */
	public function getManifestacaoCpf(){
		return $this->getState('manifestacao.cpf');
	}
	
	/**
	 * @return string
	 */
	public function getManifestacaoId() {
		return $this->getState('manifestacao.id');
	}
	
	/**
	 * @return mixed
	 */
	public function getTask() {
		return JFactory::getApplication('site')->getUserState('task');
	}
	
	/**
	 * {@inheritDoc}
	 * @see JRESTModel::getRestQueryString()
	 */
	public function getRestQueryString() {
		$mnf_id = $this->getState('manifestacao.id');
		$cpf = $this->getState('manifestacao.cpf');
		return 'mnf_id='.$mnf_id.'&cpf='.$cpf;
	}
	
	/**
	 * @return object[]
	 */
	public function getItems() {
		return $this->_getList(); 
	}
	
	/**
	 * @return mixed|boolean
	 */
	public function getManifestacaoItems() {
		if(!$this->_modelManifestacao) {
			$this->_modelManifestacao= new SojamModelManifestacao(
				$this->getState('manifestacao.id',0), 
				$this->getState('manifestacao.cpf','')
			);
		}
		return $this->_modelManifestacao->getItems();
	}
	
	/**
	 * Retorna um array com as opcoes de estado da movimentacao.
	 *
	 * @return multitype:string
	 */
	public static function getOpcoesEstados() {
		return array(
			self::mvstsAgendada => 'Agendada',
			self::mvstsCancelada => 'Cancelada',
			self::mvstsRealizada => 'Realizada',
			self::mvstsNRealizada => 'Ñ Realizada',
		);
	}
	
	/**
	 * Retorna o texto da opcao de estado
	 *
	 * @return string
	 */
	public static function getTextoEstado($status) {
		$opcoes = self::getOpcoesEstados();
		return array_key_exists($status, $opcoes) ? $opcoes[$status] : null;
	}
	
	/**
	 * {@inheritDoc}
	 * @see JModelRESTAdmin::populateState()
	 */
	protected function populateState($ordering = 'order', $direction = 'asc') {
		parent::populateState($ordering, $direction);
		
		$app = JFactory::getApplication('site'); // frontend application
	
		$mnf_id = $app->input->getInt('manifestacao_id');
		$cpf = $app->input->getString('manifestacao_cpf'); 
		
		$this->setState('manifestacao.id', $mnf_id);
		$this->setState('manifestacao.cpf', $cpf);
				
		$limit = $this->_params ? (int)$this->_params->get('restlistlimit') : 5;
		
		$this->setState('list.limit',$limit);
		$this->setState('list.start',$app->input->get('start'));
	}	
	
}