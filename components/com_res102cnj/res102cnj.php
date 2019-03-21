<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_res102cnj
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('JHtmlIcon', JPATH_SITE . '/components/com_res102cnj/helpers/icon.php');
JLoader::register('JHtmlRemoteHost', JPATH_SITE . '/components/com_res102cnj/helpers/remotehost.php');
JLoader::register('JHtmlBtns', JPATH_SITE . '/components/com_res102cnj/helpers/buttons.php');

$task = JFactory::getApplication()->input->get('task');

$controller = JControllerLegacy::getInstance('Res102cnj');
$controller->execute($task);
$controller->redirect();
