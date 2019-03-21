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

/**
 * @author Marcus
 *
 */
class ExtrajudicialModelAssentamentos extends JModelRESTList {
	protected $_params=null;
	
	protected $_lcl_id=null;
	
	/**
	 * Sets up an array of all fields used by the view.
	 * @param array $config
	 */
	public  function __construct($lcl_id, $config = array()) {
		if(empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id',
				'cargo_id',
				'pessoa_id',
				'local_id',
				'tipo',
				'dthr_inicio',
				'dthr_final',
			);
		}
	
		$this->_params = JComponentHelper::getParams('com_extrajudicial');
		
		if(empty($config['restAPI'])) {
			$config['restAPI'] = array(
				'restusername'=>$this->_params ? $this->_params->get('restusername') : '',
				'restpassword'=>$this->_params ? $this->_params->get('restpassword') : '',
				'restserverurl'=>$this->_params ? $this->_params->get('restserverurl') : '',
				'restclass'=>'assentamento',
			);
		}
		
		$this->_lcl_id = $lcl_id;
		
		parent::__construct($config);
	}
	
	/**
	 * {@inheritDoc}
	 * @see JModelRESTList::getRestQueryString()
	 */
	protected function getRestQueryString() {
		return 'lcl_id='.$this->_lcl_id;
	}
	
	/**
	 * {@inheritDoc}
	 * @see JModelList::populateState()
	 */
	protected function populateState($ordering = 'order', $direction = 'asc') {
		parent::populateState($ordering, $direction);
		
		$app = JFactory::getApplication('site'); // frontend application
		
		$limit = $this->_params ? (int)$this->_params->get('restlistlimit') : 5;
		
		$this->setState('list.limit',$limit);
		$this->setState('list.start',$app->input->get('start'));
	}	

}