<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

return array(
    'aliases' => array(
        'com://admin/logman.model.resources' => 'com:activities.model.resources'
    ),
    'identifiers' => array(
        'com:koowa.dispatcher.http' => array('behaviors' => array('com://admin/logman.dispatcher.behavior.redirectable'))
    )
);