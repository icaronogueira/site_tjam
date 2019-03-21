<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_res102cnj
 * @author		Marcus Martinho (marcus.martinho@tjam.jus.br)
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Register external libraries
 */
JLoader::register('Res102cnjHelper', JPATH_ADMINISTRATOR.'/components/com_res102cnj/helpers/res102cnj.php');

/**
 * Component Base Controller.
 * @author Marcus
 *
 */
class Res102cnjController extends JControllerLegacy {
	/**
	 * The default view for the display method.
	 * @var string
	 */
	protected $default_view = 'info'; 

	public function display($cachable = false, $urlparams = false) {
		return parent::display($cachable, $urlparams);
	}
}