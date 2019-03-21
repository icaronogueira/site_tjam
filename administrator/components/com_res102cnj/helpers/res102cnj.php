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

class Res102cnjHelper {
	
	/**
	 * @param string $vName
	 */
	public static function addSubmenu($vName='info') {
		JHtmlSidebar::addEntry(
			JText::_('COM_RES102CNJ_SUBMENU_INFO'),
			'index.php?option=com_res102cnj&view=info',
			$vName == 'info'
		);	
	}

}