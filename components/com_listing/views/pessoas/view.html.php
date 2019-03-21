<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_listing
 * @author		Marcus Martinho (marcus.martinho@tjam.jus.br)
 * 
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * @author Marcus
 */
class ListingViewPessoas extends JViewLegacy {
	/**
	 * Titulos data
	 *
	 * @var  array
	 */
	protected $titulos;
	
	/**
	 * Stores the data array from the model.
	 * @var object[]
	 */
	protected $items;
	
	/**
	 * Stores the state from the model.
	 * @var unknown
	 */
	protected $state;
	
	/**
	 * Stores the pagination object from the model.
	 * @var 
	 */
	protected $pagination;
	
	/**
	 * @var unknown
	 */
	protected $params;

	/**
	 * {@inheritDoc}
	 * @see JViewLegacy::display()
	 */
	public function display($tpl = null) {
		$this->items = $this->get('Items');
		$this->state = $this->get('State');
		$this->pagination = $this->get('Pagination');
		$this->titulos = $this->get('Titulos');
		
		if(count($errors = $this->get('Errors'))) {
			throw new RuntimeException(implode("\n", $errors), 500);
			return false;
		}
		
		return parent::display($tpl);
	}

	/**
	 * Used by the drop-down filter.
	 * @return string[]
	 */
	protected function getSortFields() {
		return array(
			'order'=>JText::_('COM_LISTING_FIELD_ORDER_LABEL'),
			'nome'=>JText::_('COM_LISTING_FIELD_NOME_LABEL'),
			'titulo'=>JText::_('COM_LISTING_FIELD_TITULO_LABEL'),
			'cpf'=>JText::_('COM_LISTING_FIELD_CPF_LABEL'),
			'identidade'=>JText::_('COM_LISTING_FIELD_IDENTIDADE_LABEL'),
			'genero'=>JText::_('COM_LISTING_FIELD_GENERO_LABEL'),
			'ativo'=>JText::_('COM_LISTING_FIELD_ATIVO_LABEL'),
			'id'=>JText::_('JGRID_HEADING_ID')
		);
	}
}