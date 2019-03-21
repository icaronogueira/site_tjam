<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_unavailability_validate
 *
 * @copyright   Copyright (C) 2016 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('ModUnavailabilityValidateHelper', __DIR__ . '/helper.php');

$app  = JFactory::getApplication();
$id = $app->input->get('unavailability_id');

if($id) {
	$list = ModUnavailabilityValidateHelper::getItem($id);
	$layout = 'list';
} else {
	$list = null;
	$layout = 'form';
}

require JModuleHelper::getLayoutPath('mod_unavailability_validate', $layout);