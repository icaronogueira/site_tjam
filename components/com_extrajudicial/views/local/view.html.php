<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_extrajudicial
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * @author Marcus
 *
 */
class ExtrajudicialViewLocal extends JViewLegacy {
	/**
	 * Stores the data from the model.
	 * @var object
	 */
	protected $items;
	/**
	 * Stores the data array from the model.
	 * @var object[]
	 */
	protected $assentamentoItems;
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
		$this->assentamentoItems = $this->get('AssentamentoItems');
		
		JHtml::_('documentos.links', $this->assentamentoItems);
		
		$this->params = JFactory::getApplication()->getParams();
		
		if(count($errors = $this->get('Errors'))) {
			throw new RuntimeException(implode("\n", $errors), 500);
			return false;
		}
		
		return parent::display($tpl);
	}
}