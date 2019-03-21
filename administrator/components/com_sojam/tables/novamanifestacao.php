<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_sojam
 * @author		Marcus Martinho (marcus.martinho@tjam.jus.br)
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

use function PasswordCompat\binary\check;

jimport('mmartinho.rest.resttable');

class SojamRESTTableNovamanifestacao extends JRESTTable {
	/**
	 * @var boolean
	 */
	protected $_validacaoCPF=false;
	
	/**
	 * Defines the RESTTable name.
	 * @param array $restConfig
	 */
	public function __construct(&$restConfig) {
		parent::__construct('manifestacao', 'id', $restConfig);
	}
	
	/**
	 * Prepared the data immediately before it is saved to 
	 * the REST Table.
	 * {@inheritDoc}
	 * @see JRESTTable::bind()
	 */
	public function bind($src, $ignore = '') {
		$params = JComponentHelper::getParams('com_sojam');
		
		if($params) {
			$this->_validacaoCPF = $params->get('validacaocpf');
			if(is_object($src)) {
				$src->origem_id = $params->get('origemid');
				$src->assunto_id = $params->get('assuntoid');
			} else {
				$src['origem_id'] = $params->get('origemid');
				$src['assunto_id'] = $params->get('assuntoid');
			}
		}
		
		return parent::bind($src, $ignore);
	}
	
	/**
	 * Validation procedure before store. 
	 * {@inheritDoc}
	 * @see JRESTTable::check()
	 */
	public function check() {
		return true;
	}
	
	/**
	 * {@inheritDoc}
	 * @see JRESTTable::getRestQueryString()
	 */
	protected function getRestQueryString() {
		return $this->_validacaoCPF ? 'validacaoCPF=1' : 'validacaoCPF=0';
	}
	
}