<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_res102cnj
 * @author		Marcus Martinho (marcus.martinho@tjam.jus.br)
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

class Res102cnjModelAnexos extends JModelList {
	
	/*************** properties ***************/
	
	protected $tam_min_arq = 0;       // min file size in bytes
	protected $tam_max_arq = 0;       // max file size in bytes
	protected $meses = array();       // year months
	protected $res102cnjdir = '';     // path to attached files 
	protected $anosretroativos = 0;   // how many pasted years to show
	protected $res102cnjurl='';       // res 102 url 
	protected $res102cnjlink='';      // url link label
	protected $res102cnjintro='';     // intro text
	
	/**
	 * Sets up an array of all fields used by the view.
	 * @param array $config
	 */
	public  function __construct($config = array()) {
		if(empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'state', 'a.state',
				'alias','a.alias',
				'catid', 'a.catid', 'category_title',
				'checked_out', 'a.checked_out',
				'publish_up','a.publish_up',
				'publish_down', 'a.publish_down',
				'ordering', 'a.ordering'
			);
		}
		
		parent::__construct($config);
		
		$params = JComponentHelper::getParams('com_res102cnj');
		
		/*************** properties init **************/
		
		// months names ...
		$this->meses[1] = 'Jan';
		$this->meses[2] = 'Fev';
		$this->meses[3] = 'Mar';
		$this->meses[4] = 'Abr';
		$this->meses[5] = 'Mai';
		$this->meses[6] = 'Jun';
		$this->meses[7] = 'Jul';
		$this->meses[8] = 'Ago';
		$this->meses[9] = 'Set';
		$this->meses[10] = 'Out';
		$this->meses[11] = 'Nov';
		$this->meses[12] = 'Dez';
		
		$this->res102cnjdir = $params->get('res102cnjdir', 'media/com_res102cnj'); // default: media path for pdf
		$this->anosretroativos = $params->get('anosretroativos', 5); // default: 5 anos
		$this->tam_max_arq = $params->get('tam_max_arq', 8388608);  // default: 8M
		$this->tam_min_arq = $params->get('tam_min_arq', 10); // default: 10 bytes (less than this, is an empty file)
		$this->res102cnjurl = $params->get('res102cnjurl','');
		$this->res102cnjlink = $params->get('res102cnjlink','');
		$this->res102cnjintro = $params->get('res102cnjintro','');
	}
	
	/**
	 * Upload Object instantiation.
	 *
	 * @param $_FILES['fieldname'] $arqObj
	 * @return Upload
	 */
	private function _handleinit($arqObj) {
		require_once(JPATH_COMPONENT.'/libraries/upload/classupload.php');
		$handle = new Upload($arqObj, 'pt_BR'); // create handle obj, sending the file to server temp dir	
		// Configs...
		$handle->file_auto_rename = false;            // don't rename files 
		$handle->file_max_size = $this->tam_max_arq;   
		$handle->allowed = array('application/pdf', 'application/pdf;'); // allowed files types
		
		return $handle;
	}
	
	/**
	 * Human readble format 
	 * 
	 * @param double $a_bytes
	 * @return string
	 */
	private static function _format_bytes($a_bytes) {
		if ($a_bytes < 1024) {
			return $a_bytes .' B';
		} elseif ($a_bytes < 1048576) {
			return round($a_bytes / 1024, 2) .' KB';
		} elseif ($a_bytes < 1073741824) {
			return round($a_bytes / 1048576, 2) . ' MB';
		} elseif ($a_bytes < 1099511627776) {
			return round($a_bytes / 1073741824, 2) . ' GB';
		} elseif ($a_bytes < 1125899906842624) {
			return round($a_bytes / 1099511627776, 2) .' TB';
		} elseif ($a_bytes < 1152921504606846976) {
			return round($a_bytes / 1125899906842624, 2) .' PB';
		} elseif ($a_bytes < 1180591620717411303424) {
			return round($a_bytes / 1152921504606846976, 2) .' EB';
		} elseif ($a_bytes < 1208925819614629174706176) {
			return round($a_bytes / 1180591620717411303424, 2) .' ZB';
		} else {
			return round($a_bytes / 1208925819614629174706176, 2) .' YB';
		}
	}
	
	/**
	 * Check if file is valid.
	 *
	 * @param string $arq Caminho e arquivo
	 * @return boolean
	 */
	public function arquivo_valido($arq) {
		$arqpath = JPATH_ROOT.'/'.$this->res102cnjdir.'/'. $arq;
		if(file_exists($arqpath)) { // file exists? 
			if(filesize($arqpath) > $this->tam_min_arq) { // Is it bigger than min?
				return true;
			} else {
				unlink($arqpath); //...try to exclude invalid file
				return false;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * Try to delete file.
	 *
	 * @param string $arq Caminho e arquivo
	 * @return boolean
	 */
	public function arquivo_excluir($arq) {
		$erro = '';
		$arqpath = JPATH_ROOT.'/'.$this->res102cnjdir.'/'. $arq;
		if(file_exists($arqpath)) { // file exists? 
			if(unlink($arqpath)) //...try to exclude
				$erro = '';
				else
					$erro = 'Não foi possível excluir o arquivo ' . $arqpath;
		} else
			$erro = 'Arquivo não existe';
			return $erro;
	}
	
	/**
	 * File upload procedure.
	 *
	 * @param $_FILES['fieldname'] $arqObj
	 * @param string $arq
	 * @return string
	 */
	public function arquivo_enviar($arqObj, $arq) {
		$erro = '';
		if(!is_null($arqObj)) {
			$handle = $this->_handleinit($arqObj);
			if ($handle->uploaded) { // file sucessfully uploaded?
				$caminho = JPATH_ROOT.'/' .$this->res102cnjdir.'/';
				if($this->arquivo_valido($arq)) { // valid file?
					$handle->file_overwrite = true; // allow update it ...
				} else {
					$handle->file_overwrite = false; // Disallow to update it...
				} 
				// change file name and extension...
				$handle->file_new_name_body = substr($arq,0,strpos($arq, '.'));  $handle->file_new_name_ext = 'pdf';
				$handle->process($caminho); // try to move to path 
				if($handle->processed) { // moved successfully?
					$erro = '';
				} else {
					$erro = 'Erro: ' . $handle->error;
				}
			} else {
				$erro = 'Não foi possível enviar o arquivo para o servidor. Erro: ' . $handle->error ;
			}
		} else {
			$erro = 'Arquivo não foi selecionado';
		}
		return $erro;
	}
	
	/**
	 * @return string
	 */
	public function get_tam_max() {
		return self::_format_bytes($this->tam_max_arq);
	}
	
	/**
	 * @return string
	 */
	public function get_tam_min() {
		return self::_format_bytes($this->tam_min_arq);
	}
	
	/**
	 * @return string
	 */
	public function getRes102cnjdir() {
		return $this->res102cnjdir;
	}
	
	/**
	 * @return array
	 */
	public function getMeses() {
		return $this->meses;
	}
	
	/**
	 * @return number
	 */
	public function getAnosretroativos() {
		return $this->anosretroativos;
	}
	
	/**
	 * @return string
	 */
	public function getRes102cnjurl() {
		return $this->res102cnjurl;
	}
	
	/**
	 * @return string
	 */
	public function getRes102cnjlink() {
		return $this->res102cnjlink;
	}
	
	/**
	 * @return string
	 */
	public function getRes102cnjintro() {
		return $this->res102cnjintro;
	}
}