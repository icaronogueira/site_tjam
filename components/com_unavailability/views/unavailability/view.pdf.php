<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_unavailability
 *
 * @copyright   Copyright (C) 2016 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class UnavailabilityViewUnavailability extends JViewLegacy {	
	
	protected $state;
	protected $items;
	protected $print;
	protected $sistemas;
	
	function display($tpl = null) {
		$this->items = $this->get('Items');
		$this->sistemas = $this->get('Sistemas');
		$this->params = JFactory::getApplication()->getParams();
		
		// modify the option to work as an article...
		$app = JFactory::getApplication();
		$app->input->set('option', 'com_content');
		$document = JFactory::getDocument();

		if(count($errors = $this->get('Errors'))) {
			$document->setHeader(JText::_('COM_UNAVAILABILITY_ERRORS_FOUND'));
			$document->setArticleText(implode("\n", $errors));
		} else {
			if(count($this->items) > 0) {		
				// set the 'fake' article attributes...
				$document->setHeader(JText::_('COM_UNAVAILABILITY_COMPONENT_DESC'));
				$document->setArticleText($this->pdfBody());
				$document->setArticleTitle($this->pdfTitle());
			} else {
				$document->setHeader(JText::_('COM_UNAVAILABILITY_NO_ITENS_FOUND'));
				$document->setArticleText(JText::_('COM_UNAVAILABILITY_NO_ITENS_FOUND'));
				$document->setArticleTitle(JText::_('COM_UNAVAILABILITY_NO_ITENS_FOUND'));
			}
		}
	}
	
	/**
	 * Decide to load the template or
	 * use the "detalhes" data 
	 * @return string
	 */
	function pdfBody() {
		return $this->loadTemplate('printfriendly');
	}
	
	/**
	 * @return string
	 */
	function pdfTitle() {
		$item = $this->items[0];
		return $item->title ? $item->title : JText::_('COM_UNAVAILABILITY_COMPONENT_LABEL');
	}
	
}