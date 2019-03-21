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

class com_res102cnjInstallerScript {
		
	/**
	 * @param unknown_type $parent
	 */
	function install($parent) {
		$parent->getParent()->setRedirectURL('index.php?option=com_res102cnj');
		
		jimport('joomla.filesystem.folder');
		
		$app = JFactory::getApplication();
		
		$folders = array(
			JPATH_SITE.'/media/com_res102cnj',
		);
		
		foreach ($folders as $folder) {
			if (!JFolder::exists($folder)) {
				if (!JFolder::create($folder)) {
					$app->enqueueMessage('Não foi possível criar pasta: '.$folder, 'error');
				}
			}
		}
	}

	/**
	 * @param unknown_type $parent
	 */
	function uninstall($parent) {
		echo '<p>' . JText::_('COM_RES102CNJ_UNINSTALL_TEXT') . '</p>';
	}

	/**
	 * @param unknown_type $parent
	 */
	function update($parent) {
		echo '<p>' . JText::_('COM_RES102CNJ_UPDATE_TEXT') . '</p>';
	}

	/**
	 * @param unknown_type $type
	 * @param unknown_type $parent
	 */
	function preflight($type, $parent) {
		echo '<p>' . JText::_('COM_RES102CNJ_PREFLIGHT_' . $type . '_TEXT') . '</p>';
	}

	/**
	 * @param unknown_type $type
	 * @param unknown_type $parent
	 */
	function postflight($type, $parent) {
		echo '<p>' . JText::_('COM_RES102CNJ_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
	}
}