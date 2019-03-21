<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_extrajudicial
 * @author		Marcus Martinho (marcus.martinho@tjam.jus.br)
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Register external libraries
 */
JLoader::register('ExtrajudicialHelper', JPATH_ADMINISTRATOR.'/components/com_extrajudicial/helpers/extrajudicial.php');

/**
 * Component Base Controller.
 * @author Marcus
 *
 */
class ExtrajudicialController extends JControllerLegacy {
	/**
	 * The default view for the display method.
	 * @var string
	 */
	protected $default_view = 'info'; 

	public function display($cachable = false, $urlparams = false) {
		return parent::display($cachable, $urlparams);
	}
}