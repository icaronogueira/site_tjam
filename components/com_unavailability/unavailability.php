<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_unavailability
 *
 * @copyright   Copyright (C) 2016 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('JHtmlIcon', JPATH_SITE . '/components/com_unavailability/helpers/icon.php');

$task = JFactory::getApplication()->input->get('task');

$controller = JControllerLegacy::getInstance('Unavailability');
$controller->execute($task);
$controller->redirect();
