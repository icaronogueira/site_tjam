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

/**
 * @author Marcus
 *
 */
class ListingModelPessoaLocal extends JModelRESTList {
	protected $_params=null;
	protected $_lcl_id=null;
	protected $_psa_id=null;
	
	/**
	 * Sets up an array of all fields used by the view.
	 * @param array $config
	 */
	public  function __construct($lcl_id, $psa_id, $config = array()) {
		if(empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id',
				'nome',
				'pessoa_id',
				'local_id',
				'dthr_inicio',
				'dthr_final',
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
				'restclass'=>'pessoaLocal',
			);
		}
		
		$this->_lcl_id = $lcl_id;
		$this->_psa_id = $psa_id;
		
		parent::__construct($config);
	}
	
	/**
	 * {@inheritDoc}
	 * @see JModelRESTList::getRestQueryString()
	 */
	protected function getRestQueryString() {
		if($this->_lcl_id)
			return 'lcl_id='.$this->_lcl_id;
		else if($this->_psa_id)
			return 'psa_id='.$this->_psa_id;
	}
	
	/**
	 * {@inheritDoc}
	 * @see JModelList::populateState()
	 */
	protected function populateState($ordering = 'order', $direction = 'asc') {
		parent::populateState($ordering, $direction);
		
		$app = JFactory::getApplication('site'); // frontend application
		
		$prefix = 'psalcl';
		$limit  = $this->_params ? (int)$this->_params->get('restlistlimit') : 5;
		$start  = $app->input->get($prefix.'limitstart');
		
		$this->setState('list.prefix', $prefix);
		$this->setState('list.limit', $limit);
		$this->setState('list.start', $start);
	}	

}