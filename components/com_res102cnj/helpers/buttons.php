<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_res102cnj
 * @author		Marcus Martinho (marcus.martinho@tjam.jus.br)
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Component HTML Helper
 *
 */
class JHtmlBtns {
	
	protected $images = '';

	/**
	 * 
	 */
	public function __construct() {
		$this->images = JURI::root().'components/com_res102cnj/media/img/';
	}
	
	/**
	 * Função auxiliar de layout que retorna o
	 * arquivo de imagem correspondente ao $btn.
	 *
	 * @param string $btn
	 * @return string
	 * */
	public function legenda($btn) {
		switch($btn) {
			case 'delete' : {
				return '<img src="' . $this->images . 'delete.png" />';
				break;
			}
			case 'upload' : {
				return '<img src="' . $this->images . 'upload.png" />';
				break;
			}
		}
	}
	
	/**
	 * Função auxiliar de layout que retorna os
	 * botões a serem usados no $arq.
	 *
	 * @param string $arq Nome do arquivo.
	 * @param boolean $valido Se o arquivo é válido ou decorativo.
	 * @return string
	 * */
	public function acao($arq, $valido) {
		$delete = '<a href="javascript:;" onclick="submitdelete(\'' . $arq . '\');">' . $this->legenda('delete') . '</a>';
		$upload = '<a href="javascript:;" onclick="submitupload(\'' . $arq . '\');">' . $this->legenda('upload') . '</a>';
		if($valido) { // é arquivo valido?
			return "<table style='border:none;padding:0;margin:0;border-spacing:0;'><tr><td>" . $delete . "</td></tr><tr><td>" . $upload . "</td></tr></table>";
		} else {
			return "<table style='border:none;padding:0;margin:0;border-spacing:0;'><tr><td>" . $upload . "</td></tr></table>";
		}
	}
}