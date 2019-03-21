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

include_once 'assentamentos.php';

/**
 * @author Marcus
 *
 */
class ExtrajudicialModelLocal extends JModelRESTList {
	/**
	 * @var array
	 */
	protected $_params=null;
	/**
	 * @var ExtrajudicialModelAssentamentos[]
	 */
	protected $_modelAssentamentos=array(); // lista de assentamentos do local
	
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
	 * @return mixed|boolean
	 */
	public function getAssentamentoItems() {
		if(!$this->_modelAssentamentos) {
			$this->_modelAssentamentos= new ExtrajudicialModelAssentamentos($this->getState('filter.id',0),null);
		}
		return $this->_modelAssentamentos->getItems();
	}
	
	/**
	 * @return JPagination|NULL
	 */
	public function getAssentamentoPagination() {
		if($this->_modelAssentamentos)
			return $this->_modelAssentamentos->getPagination();
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