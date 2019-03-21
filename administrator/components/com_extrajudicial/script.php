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

class com_extrajudicialInstallerScript {
		
	/**
	 * @param unknown_type $parent
	 */
	function install($parent) {
		$parent->getParent()->setRedirectURL('index.php?option=com_extrajudicial');
	}

	/**
	 * @param unknown_type $parent
	 */
	function uninstall($parent) {
		echo '<p>' . JText::_('COM_EXTRAJUDICIAL_UNINSTALL_TEXT') . '</p>';
	}

	/**
	 * @param unknown_type $parent
	 */
	function update($parent) {
		echo '<p>' . JText::_('COM_EXTRAJUDICIAL_UPDATE_TEXT') . '</p>';
	}

	/**
	 * @param unknown_type $type
	 * @param unknown_type $parent
	 */
	function preflight($type, $parent) {
		echo '<p>' . JText::_('COM_EXTRAJUDICIAL_PREFLIGHT_' . $type . '_TEXT') . '</p>';
	}

	/**
	 * @param unknown_type $type
	 * @param unknown_type $parent
	 */
	function postflight($type, $parent) {
		echo '<p>' . JText::_('COM_EXTRAJUDICIAL_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
	}
}