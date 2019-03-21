<?php
/**
* @package     Joomla.Site
* @subpackage  com_res102cnj
* @author	Marcus Martinho (marcus.martinho@tjam.jus.br)
*
* @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('_JEXEC') or die;

class JHtmlRemoteHost {
	private $_permited;
	private $_ip;
	private $_params;
	
	/**
	 * @param string $addrlist
	 */
	public function __construct($addrlist='') {
		if(!$addrlist) { // se não estiver definida...
			$this->_params = JComponentHelper::getParams('com_res102cnj');
			$this->_permited = $this->_params->get('permitedespecialaddrs');			
		} else {
			$this->_permited = $addrlist;
		}
		$this->_ip = $_SERVER['REMOTE_ADDR']; // IP do host remoto acessando o sistema
	}
	
	/**
	 * @return string
	 */
	public function getPermited() {
		return $this->_permited;
	}
	
	/**
	 * @param string $addrlist
	 */
	public function setPermited($addrlist) {
		$this->_permited = $addrlist;
	}
	
	/**
	 * @return string
	 */
	public function getIP() {
		return $this->_ip;
	}
	
	/**
	 * Verifica se o IP remoto bate com algum IP permitido.
	 * @return boolean
	 */
	public function isPermited() {
		$permitido = false;
		$permited_especial_addrs = preg_replace('/\s+/', '', $this->_permited); // limpa a propriedade
		if($permited_especial_addrs) { // tem algo a verificar?
			$ip_remoto = $this->_ip;
			$ips_permitidos = explode(',', $permited_especial_addrs ); // transforma a lista em um array
			foreach ($ips_permitidos as $ip_permitido ) { // para cada ip permitido...
				if ( substr($ip_remoto, 0, strlen($ip_permitido)) == $ip_permitido ) { // se pelo menos um bate...
					$permitido = true; // eh permitido
					break; // sai
				}
			}
		} else { // ...não tem algo a verificar...
			$permitido = true;
		}
		return $permitido;
	}
	
	/**
	 * Verifica se o IP remoto bate com algum IP na lista de exclusao.
	 * @return boolean
	 */
	public function isExcluded() {
		return $this->isPermited();
	}	
}