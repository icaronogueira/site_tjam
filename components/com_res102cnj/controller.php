<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_res102cnj
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Res102cnj main Controller
 */
class Res102cnjController extends JControllerLegacy {
	
	/**
	 * AJAX function
	 * Return all document events between dates.
	 */
	function events() {		
		require_once(JPATH_COMPONENT.'/libraries/utils.php');
		
		$unavailabilities = null;
		$calendar = $this->getModel('calendar'); 
		if($calendar) {
			$unavailabilities= $calendar->getItems();
		}
		
		// Accumulate an output array of event data arrays.
		$output_arrays = array();
		foreach ($unavailabilities as $unavailability) {
			$link= 
				'index.php?option=com_unavailability'.
				'&id='.(int)$unavailability->id . 
				'&view=unavailability'.
				'&tmpl=component'.
				'&format=pdf';
			
			$array = array(
				'title'=>$unavailability->title,
				'url'=>$link,
				'start'=>$unavailability->dthr_inicio,
				'end'=> $unavailability->dthr_final
			);
			
			// Convert the input array into a useful Event object
			$event = new Event($array, $calendar->getTimeZone());
			if($calendar->getStartDate() && $calendar->getEndDate()) {
				// If the event is in-bounds, add it to the output
				if ($event->isWithinDayRange(
						parseDateTime($calendar->getStartDate()), 
						parseDateTime($calendar->getEndDate()))) {
					$output_arrays[] = $event->toArray();
				}
			} else {
				$output_arrays[] = $event->toArray();
			}
		}
		
		// Send JSON to the client.
		echo json_encode($output_arrays);
		
		jexit(); // don't render the template
	}
}
