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

class UnavailabilityHelper {

	
	/**
	 * @param string $vName
	 */
	public static function addSubmenu($vName='unavailabilities') {
		JHtmlSidebar::addEntry(
			JText::_('COM_UNAVAILABILITY_SUBMENU_UNAVAILABILITIES'),
			'index.php?option=com_unavailability&view=unavailabilities',
			$vName == 'unavailabilities'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_UNAVAILABILITY_SUBMENU_CATEGORIES'),
			'index.php?option=com_categories&extension=com_unavailability',
			$vName == 'categories'	
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_UNAVAILABILITY_SUBMENU_SYSTEMS'),
			'index.php?option=com_unavailability&view=systems',
			$vName == 'systems'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_UNAVAILABILITY_SUBMENU_PREVIEW'),
			'index.php?option=com_unavailability&view=preview',
			$vName == 'preview'
		);		
	}
	
	/**
	 * Adds Count Items for Category Manager.
	 *
	 * @param   stdClass[]  &$items  The unavailability category objects
	 *
	 * @return  stdClass[]
	 *
	 * @since   3.5
	 */
	public static function countItems(&$items) {
		$db = JFactory::getDbo();
	
		foreach ($items as $item) {
			$item->count_trashed = 0;
			$item->count_archived = 0;
			$item->count_unpublished = 0;
			$item->count_published = 0;
			$query = $db->getQuery(true);
			$query->select('state, count(*) AS count')
				->from($db->qn('#__unavailability'))
				->where('catid = '.(int) $item->id)
				->group('state');
			$db->setQuery($query);
			$unavailabilities = $db->loadObjectList();
	
			foreach ($unavailabilities as $unavailability) {
				if ($unavailability->state == 1) {
					$item->count_published = $unavailability->count;
				}
	
				if ($unavailability->state == 0) {
					$item->count_unpublished = $unavailability->count;
				}
	
				if ($unavailability->state == 2) {
					$item->count_archived = $unavailability->count;
				}
	
				if ($unavailability->state == -2) {
					$item->count_trashed = $unavailability->count;
				}
			}
		}
	
		return $items;
	}
}