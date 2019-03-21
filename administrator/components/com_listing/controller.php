<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_listing
 * @author		Marcus Martinho (marcus.martinho@tjam.jus.br)
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Register external libraries
 */
JLoader::register('ListingHelper',JPATH_ADMINISTRATOR.'/components/com_listing/helpers/listing.php');

/**
 * Component Base Controller.
 * @author Marcus
 *
 */
class ListingController extends JControllerLegacy {
	/**
	 * The default view for the display method.
	 * @var string
	 */
	protected $default_view = 'info'; 

	public function display($cachable = false, $urlparams = false) {
		return parent::display($cachable, $urlparams);
	}
}