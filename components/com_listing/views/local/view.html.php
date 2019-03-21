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
class ListingViewLocal extends JViewLegacy {
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
	 * {@inheritDoc}
	 * @see JViewLegacy::display()
	 */
	public function display($tpl=null) {
		$this->items = $this->get('Items');
		$this->nomeacoesItems = $this->get('NomeacoesItems');
		$this->nomeacoesPagination = $this->get('NomeacoesPagination');
		$this->params = JFactory::getApplication()->getParams();
		
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