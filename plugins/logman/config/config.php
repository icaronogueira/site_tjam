<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Config LOGman Plugin.
 *
 * Provides event handlers for dealing with com_config events.
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Joomlatools\Plugin\LOGman
 */
class PlgLogmanConfig extends ComLogmanPluginJoomla
{
    protected function _getComponentObjectData($data, $event)
    {
        return array('name' => $data->element, 'id' => $data->extension_id);
    }
}