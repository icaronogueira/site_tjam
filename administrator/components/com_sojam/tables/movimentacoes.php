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

class SojamRESTTableMovimentacoes extends JRESTTable {
	
	/**
	 * @var integer
	 */
	protected $_mnf_id=null;
	
	/**
	 * Defines the RESTTable name.
	 * @param array $restConfig
	 */
	public function __construct(&$restConfig) {
		parent::__construct('movimentacao', 'id', $restConfig);
	}
	
	/**
	 * Prepared the data immediately before it is saved to 
	 * the REST Table.
	 * {@inheritDoc}
	 * @see JRESTTable::bind()
	 */
	public function bind($src, $ignore = '') {	
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
	 * @see JModelRESTList::getRestQueryString()
	 */
	protected function getRestQueryString() {
		if($this->_mnf_id)
			return 'mnf_id='.$this->_mnf_id;
	}
	
}