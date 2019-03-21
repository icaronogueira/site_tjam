<?php
/**
* @package     Joomla.Site
* @subpackage  com_sojam
* @author	Marcus Martinho (marcus.martinho@tjam.jus.br)
*
* @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('_JEXEC') or die;

abstract class JHtmlSojamDocumentos {
	
	/**
	 * Acrescenta o link dos documentos nos itens.
	 * Cada Origem do documento possui um link
	 * diferenciado:
	 *
	 * - ANEXO: link para a tarefa de download;
	 * - DJE: link para o Diário da Justiça Eletrônica.
	 *   Este utiliza a função remota que insere a refe-
	 *   rencia na propriedade LINK do objeto do documento
	 *   retornado pela API REST (Veja o event handler
	 *   afterFind do modelo Documento no servidor REST);
	 * - Citação: qualquer link ou apenas um texto;
	 *
	 * @param array $items Itens a serem manipulados.
	 */
	public static function links(&$items) {
		$img_url = JURI::root().'/components/com_sojam/media/img/file.png';;
	
		if(is_array($items))
			$nItems = count($items);
		else
			$nItems = 1;
	
		for($i = 0; $i < $nItems; $i++) { // para cada item...
			if(is_array($items))
				$item =& $items[$i];
			else
				$item =& $items;
						
			$nAnx = count($item->anexas);
			for($j = 0; $j < $nAnx; $j++) {  // para cada anexo de cada item...
				$anx =& $item->anexas[$j];
				switch ($anx->documento->origem) {
					case 1 : // link de download de anexo
						$anx->lnk_doc =
							'<a href="' .
								'index.php?option=com_sojam&view=documento&task=download' .
								'&id=' . $anx->documento_id . '" target="_blank">' .
								'<img src="'.$img_url.'" title="'. $anx->documento->titulo .'" />' .
							'</a>';
					break;
					case 2 : // link ao DJE
						$anx->lnk_doc =
							'<a href="'.$anx->documento->link .'" target="_blank">' .
								'<img src="'.$img_url.'" title="'. $anx->documento->titulo .'" />' .
							'</a>';
					break;
					default : // link de citação
						if(filter_var($anx->documento->manual, FILTER_VALIDATE_URL)) {
							$anx->lnk_doc =
								'<a href="'. $anx->documento->manual .'" target="_blank">' .
									'<img src="'.$img_url.'" title="'. $anx->documento->titulo .'" />' .
								'</a>';
						} else {
							$anx->lnk_doc =
								'<img src="'.$img_url.'" title="'. $anx->documento->titulo .'" />';
						}
					break;
				}
			}
		}
	}
}