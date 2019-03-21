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
class SojamViewMovimentacoes extends JViewLegacy {
	/**
	 * Stores the data from the model.
	 * @var object
	 */
	protected $items;
	
	/**
	 * @var JPagination
	 */
	protected $pagination;
	
	/**
	 * @var object[]
	 */
	protected $manifestacao;
	
	/**
	 * @var object
	 */
	protected $form;
	
	/**
	 * @var array
	 */
	protected $params;
	
	/**
	 * Stores the state from the model.
	 * @var unknown
	 */
	protected $state;
	
	/**
	 * {@inheritDoc}
	 * @see JViewLegacy::display()
	 */
	public function display($tpl=null) {
		if($this->get('ManifestacaoId') && $this->get('ManifestacaoCpf')) { // if data is present...
			$this->items = $this->get('Items');
			$this->manifestacao = $this->get('ManifestacaoItems');
			$this->pagination = $this->get('Pagination');
			$this->state = $this->get('State');
			
			$this->params = JFactory::getApplication()->getParams();
			
			//JHtml::_('documentos.links', $this->items);
			
			$app = JFactory::getApplication();
			if($app->input->get('tmpl')) {
				$tpl = $app->input->get('tmpl'); // may use a diferent template
			}
			
			if(count($errors = $this->get('Errors'))) {
				throw new RuntimeException(implode("\n", $errors), 500);
				return false;
			}

			return parent::display('movimentacoes');
		} else {
			$this->form = $this->get('Form');
			return parent::display($tpl);
		}
	}
	
	/**
	 * Used by the drop-down filter.
	 * @return string[]
	 */
	protected function getSortFields() {
		return array(
			'a.dthr_agendamento'=>JText::_('COM_SOJAM_MOVIMENTACAO_FIELD_DTHR_AGENDAMENTO_LABEL'),
			'a.manifestacao_id'=>JText::_('COM_SOJAM_MOVIMENTACAO_FIELD_MANIFESTACAO_ID_LABEL'),
			'a.responsavel'=>JText::_('COM_SOJAM_MOVIMENTACAO_FIELD_RESPONSAVEL_LABEL'),
			'a.status'=>JText::_('COM_SOJAM_MOVIMENTACAO_FIELD_STATUS_LABEL'),
			'a.id'=>JText::_('COM_SOJAM_MOVIMENTACAO_FIELD_ID_LABEL')
		);
	}
}