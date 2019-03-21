<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Activity Controller
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Joomlatools\Component\LOGman
 */
class ComLogmanControllerActivity extends ComActivitiesControllerActivity
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'formats'   => array('json', 'rss'),
            'behaviors' => array('com://admin/logman.controller.behavior.cursorable')
        ));
        parent::_initialize($config);
    }

    public function getRequest()
    {
        return KControllerModel::getRequest();
    }
}