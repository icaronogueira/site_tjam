<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_unavailability
 * @author		Marcus Martinho (marcus.martinho@tjam.jus.br)
 *
 * @copyright   Copyright (C) 2016 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * View an Unavailability Document in particular
 * @author Marcus
 */
class UnavailabilityViewUpdunavailability extends JViewLegacy {
	/**
	 * @var array
	 */
	protected $item;
	/**
	 * @var object
	 */
	protected $form;
	/**
	 * @var JRegistry
	 */
	protected $params;
	
	/**
	 * {@inheritDoc}
	 * @see JViewLegacy::display()
	 */
	public function display($tpl=null) {
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');
		$this->params = JComponentHelper::getParams('com_unavailability');
		
		if(count($errors = $this->get('Errors'))) {
			throw new RuntimeException(implode("\n", $errors), 500);
			return false;
		}
		
		return parent::display($tpl);
	}

}