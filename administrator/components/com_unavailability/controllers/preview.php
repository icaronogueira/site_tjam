<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_unavailability
 * @author		Marcus Martinho (marcus.martinho@tjam.jus.br)
 *
 * @copyright   Copyright (C) 2016 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * @author Marcus
 *
 */
class UnavailabilityControllerPreview extends JControllerForm {
	/**
	 * @var    string  The prefix to use with controller messages.
	 */
	protected $text_prefix = 'COM_UNAVAILABILITY_PREVIEW';
	/**
	 * The default view for the display method.
	 * @var string
	 */
	protected $default_view = 'preview';
	/**
	 * The URL view list variable.
	 *
	 * @var string
	 */
	protected $view_list = 'unavailabilities';
	/**
	 * Hold a JInput object for easier access to the input variables.
	 *
	 * @var    JInput
	 */
	protected $input;
	
	/**
	 * {@inheritDoc}
	 * @see JControllerForm::getModel()
	 */
	public function getModel($name='Preview', 
		$prefix='UnavailabilityModel', 
		$config=array('ignore_request'=>true)) {
		return parent::getModel($name,$prefix, $config);
	}
	
	/**
	 * Register no-default tasks
	 * @param array $config
	 */
	public function __construct($config=array()) {
		parent::__construct($config);
	}
	
	/**
	 * {@inheritDoc}
	 * @see JControllerLegacy::display()
	 */
	public function display($cachable=false, $urlparams = array()) {
		return parent::display($cachable,$urlparams);
	}
}