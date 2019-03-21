<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_sojam
 * @author		Marcus Martinho (marcus.martinho@tjam.jus.br)
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

class SojamHelper {
	
	/**
	 * @param string $vName
	 */
	public static function addSubmenu($vName='info') {
		JHtmlSidebar::addEntry(
			JText::_('COM_SOJAM_SUBMENU_INFO'),
			'index.php?option=com_sojam&view=info',
			$vName == 'info'
		);	
	}

}