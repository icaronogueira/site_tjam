<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_listing
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * @author Marcus
 *
 */
class ListingViewComissao extends JViewLegacy {
	/**
	 * Stores the data from the model.
	 * @var object
	 */
	protected $items;
	/**
	 * Stores the data array from the model.
	 * @var object[]
	 */
	protected $nomeacoesItems;
	/**
	 * @var JPagination
	 */
	protected $nomeacoesPagination;
	/**
	 * @var array
	 */
	protected $params;
	
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
	private function linkDocumentos(&$items) {
		$img_url = JURI::root().'/components/com_listing/media/img/file.png';;
		
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
								'index.php?option=com_listing&view=documento&task=download' .
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
	
	/**
	 * {@inheritDoc}
	 * @see JViewLegacy::display()
	 */
	public function display($tpl=null) {
		$this->items = $this->get('Items');
		$this->nomeacoesItems = $this->get('NomeacoesItems');
		$this->nomeacoesPagination = $this->get('NomeacoesPagination');
		$this->params = JFactory::getApplication()->getParams();
		
		JHtml::_('documentos.links', $this->items);
		JHtml::_('documentos.links', $this->nomeacoesItems);
		
		$app = JFactory::getApplication();
		if($app->input->get('tmpl'))
			$tpl = $app->input->get('tmpl'); // may use a diferent template
		
		if(count($errors = $this->get('Errors'))) {
			throw new RuntimeException(implode("\n", $errors), 500);
			return false;
		}
		
		if($this->items) {
			return parent::display($tpl);
		} else {
			throw new RuntimeException(JText::_('COM_LISTING_NO_ITENS_FOUND'),500);
			return false;
		}
	}
}