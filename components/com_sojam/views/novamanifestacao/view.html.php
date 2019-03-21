<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_listing
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('mmartinho.rest.restview');

/**
 * @author Marcus
 *
 */
class SojamViewNovamanifestacao extends JViewLegacy {
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
		$app = JFactory::getApplication();
		
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');
		
		//$this->params = JComponentHelper::getParams('com_sojam');
		$this->params = $app->getMenu()->getActive()->params;
		
		if(count($errors = $this->get('Errors'))) {
			throw new RuntimeException(implode("\n", $errors), 500);
			return false;
		}
		
		// Check if there are some warnings or errors in app queue messages until now...
		$messages = $app->getMessageQueue();
		$hasErrors = false;
		foreach ($messages as $message) {
			foreach ($message as $k=>$v) {
				if($k == 'type' && ($v == 'warning' || $v == 'error')) {
					$hasErrors = true;
					break;
				}
			}
		}
		
		if($app->input->get('show') && !$hasErrors) { // finally, display protocol
			$tpl = $app->input->get('show');
		}
		
		return parent::display($tpl);
			
	}
}