<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_unavailability
 *
 * @copyright   Copyright (C) 2016 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * @author Marcus
 *
 */
class UnavailabilityViewUnavailability extends JViewLegacy {
	/**
	 * Stores the data array from the model.
	 * @var object[]
	 */
	protected $items;
	/**
	 * Stores the data array from the model.
	 * @var object[]
	 */
	protected $sistemas;
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
		$this->sistemas = $this->get('Sistemas');
		$this->params = JFactory::getApplication()->getParams();
		
		$app = JFactory::getApplication();
		$tpl = $app->input->get('tmpl'); // may use a diferent template
		
		if(count($errors = $this->get('Errors'))) {
			throw new RuntimeException(implode("\n", $errors), 500);
			return false;
		}
		
		if(count($this->items) > 0) {
			return parent::display($tpl);
		} else {
			throw new RuntimeException(JText::_('COM_UNAVAILABILITY_NO_ITENS_FOUND'),500);
			return false;
		}
	}
}