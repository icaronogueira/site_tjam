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

class ExtrajudicialHelper {
	
	/**
	 * @param string $vName
	 */
	public static function addSubmenu($vName='info') {
		JHtmlSidebar::addEntry(
			JText::_('COM_EXTRAJUDICIAL_SUBMENU_INFO'),
			'index.php?option=com_extrajudicial&view=info',
			$vName == 'info'
		);	
	}

}