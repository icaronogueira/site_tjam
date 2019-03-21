<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_unavailability
 * @author		Marcus Martinho (marcus.martinho@tjam.jus.br)
 *
 * @copyright   Copyright (C) 2016 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

$user = JFactory::getUser();
$input = JFactory::getApplication()->input;
$task = $input->get('task');

if (!$user->authorise('core.manage', 'com_unavailability')) { // check authorization to use component
	throw new JAccessExceptionNotallowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
}

$controller	= JControllerLegacy::getInstance('Unavailability');
$controller->execute($task);
$controller->redirect();