<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_unavailability_validate
 *
 * @copyright   Copyright (C) 2016 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class ModUnavailabilityValidateHelper {
	
	/**
	 * @param integer $id
	 * @return boolean|mixed|void|unknown[]|mixed[]
	 */
	public static function getItem($id) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		// select...
		$query->select(
			'a.id,           '.
			'a.catid,        '.
			'a.title,        '.
			'a.state,        '.
			'a.sistemas,     '.
			'a.responsavel,  '.
			'a.dthr_inicio,  '.
			'a.dthr_final,   '.
			'a.dthr_emissao, '.
			'a.detalhes      '
		);
		
		// from...
		$query->from($db->quoteName('#__unavailability'). ' AS a');
		
		// join over the categories.
		$query->select($db->quoteName('c.title', 'category_title'))
			->join('LEFT', $db->quoteName('#__categories', 'c') . ' ON c.id = a.catid');
		
		// join over creator.
		$query->select($db->quoteName('u.name', 'user_name'))
			->join('LEFT', $db->quoteName('#__users', 'u') . ' ON u.id = a.created_by');
					
		// join over the systems using multivalue field.
		$query->select($db->quoteName('us.title', 'system_title'))
			->join('LEFT', $db->quoteName('#__unavailability_systems', 'us') . ' ON FIND_IN_SET(us.id, a.sistemas)');
		
		// filter id
		if($id) {
			$query->where('a.id ='.(int)$id);
		}
		
		$db->setQuery($query);
		try {
			$results = $db->loadObjectList();
		} catch (RuntimeException $e) {
			JError::raiseError(550,$e->getMessage());
			return false;
		}
		
		return $results;
	}
}