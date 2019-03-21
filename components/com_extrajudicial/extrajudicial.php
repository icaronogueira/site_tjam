<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_extrajudicial
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('JHtmlDocumentos', JPATH_SITE . '/components/com_extrajudicial/helpers/documentos.php');

$task = JFactory::getApplication()->input->get('task');

$controller = JControllerLegacy::getInstance('Extrajudicial');
$controller->execute($task);
$controller->redirect();
