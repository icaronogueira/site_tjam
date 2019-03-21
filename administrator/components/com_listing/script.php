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

class com_listingInstallerScript {
		
	/**
	 * @param unknown_type $parent
	 */
	function install($parent) {
		$parent->getParent()->setRedirectURL('index.php?option=com_listing');
	}

	/**
	 * @param unknown_type $parent
	 */
	function uninstall($parent) {
		echo '<p>' . JText::_('COM_LISTING_UNINSTALL_TEXT') . '</p>';
	}

	/**
	 * @param unknown_type $parent
	 */
	function update($parent) {
		echo '<p>' . JText::_('COM_LISTING_UPDATE_TEXT') . '</p>';
	}

	/**
	 * @param unknown_type $type
	 * @param unknown_type $parent
	 */
	function preflight($type, $parent) {
		echo '<p>' . JText::_('COM_LISTING_PREFLIGHT_' . $type . '_TEXT') . '</p>';
	}

	/**
	 * @param unknown_type $type
	 * @param unknown_type $parent
	 */
	function postflight($type, $parent) {
		echo '<p>' . JText::_('COM_LISTING_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
	}
}