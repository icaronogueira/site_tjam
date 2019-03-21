<?php
/**
* @package     Unabailability.Libraries
* @subpackage  HTML
*
* @copyright   Copyright (C) 2016 - 2021 Open Source Matters, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('JPATH_PLATFORM') or die;

/**
 * Utility class for jQuery JavaScript behaviors
 *
 */
abstract class UnavailabilityJquery {
	/**
	 * @var    array  Array containing information for loaded files
	 */
	protected static $loaded = array();

	/**
	 * Method to load the jQuery JavaScript framework into the document head
	 *
	 * If debugging mode is on an uncompressed version of jQuery is included for easier debugging.
	 *
	 * @param   boolean  $noConflict  True to load jQuery in noConflict mode [optional]
	 * @param   mixed    $debug       Is debugging mode on? [optional]
	 * @param   boolean  $migrate     True to enable the jQuery Migrate plugin
	 *
	 * @return  void
	 */
	public static function framework($noConflict = true, $debug = null, $migrate = true) {
		// Only load once
		if (!empty(static::$loaded[__METHOD__])) {
			return;
		}

		// If no debugging value is set, use the configuration setting
		if ($debug === null) {
			$config = JFactory::getConfig();
			$debug  = (boolean) $config->get('debug');
		}

		JHtml::_('script', 'jui/jquery.min.js', false, true, false, false, $debug);

		// Check if we are loading in noConflict
		if ($noConflict) {
			JHtml::_('script', 'jui/jquery-noconflict.js', false, true, false, false, false);
		}

		// Check if we are loading Migrate
		if ($migrate) {
			JHtml::_('script', 'jui/jquery-migrate.min.js', false, true, false, false, $debug);
		}

		static::$loaded[__METHOD__] = true;

		return;
	}

	/**
	 * Method to load the jQuery UI JavaScript framework into the document head
	 *
	 * If debugging mode is on an uncompressed version of jQuery UI is included for easier debugging.
	 *
	 * @param   array  $components  The jQuery UI components to load [optional]
	 * @param   mixed  $debug       Is debugging mode on? [optional]
	 *
	 * @return  void
	 *
	 */
	public static function ui(array $components = array('core'), $debug = null) {
		// url to jQuery ui js library
		$uiUrl = JUri::base().'/components/com_unavailability/media/jui';
		
     	// Set an array containing the supported jQuery UI components handled by this method
		$supported = array(
			'core',
			'widget',
			'button',
			'dialog',
			'draggable',
			'mouse',
			'position',
			'resizable', 
			'effect', 
			'effect-shake',
		);

		// Include jQuery
		static::framework();
		
		JFactory::getDocument()->addStyleSheet($uiUrl.'/themes/base/minified/jquery-ui.min.css');
		
		// Load each of the requested components
		foreach ($components as $component) {
			// Only attempt to load the component if it's supported in core and hasn't already been loaded
			if (in_array($component, $supported) && empty(static::$loaded[__METHOD__][$component])) {
				JFactory::getDocument()->addScript($uiUrl.'/js/jquery.ui.'.$component.'.min.js');
				static::$loaded[__METHOD__][$component] = true;
			}
		}

		return;
	}
}
