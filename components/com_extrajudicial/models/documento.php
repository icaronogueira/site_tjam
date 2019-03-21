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
class ExtrajudicialModelDocumento extends JModelRESTList {
	protected $_params=null;

	/**
	 * Sets up an array of all fields used by the view.
	 * @param array $config
	 */
	public  function __construct($config = array()) {
		if(empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id',
				'docpai_id',
				'titulo',
				'descricao',
				'origem',
				'link',
				'arquivo',
				'manual',
				'dje_volume',
				'dje_numero',
				'dje_caderno',
				'dje_pagina',
				'order',
			);
		}

		$this->_params = JComponentHelper::getParams('com_listing');

		if(empty($config['restAPI'])) {
			$config['restAPI'] = array(
				'restusername'=>$this->_params ? $this->_params->get('restusername') : '',
				'restpassword'=>$this->_params ? $this->_params->get('restpassword') : '',
				'restserverurl'=>$this->_params ? $this->_params->get('restserverurl') : '',
				'restclass'=>'documento',
			);
		}

		parent::__construct($config);
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