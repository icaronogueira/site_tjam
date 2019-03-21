<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_listing
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('JHtmlDocumentos', JPATH_SITE . '/components/com_listing/helpers/documentos.php');

$task = JFactory::getApplication()->input->get('task');

$controller = JControllerLegacy::getInstance('Listing');
$controller->execute($task);
$controller->redirect();
