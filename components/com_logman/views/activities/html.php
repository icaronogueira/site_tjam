<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Html Activities View
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Joomlatools\Component\LOGman
 */
class ComLogmanViewActivitiesHtml extends ComKoowaViewHtml
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array('links' => false));

        parent::_initialize($config);
    }

    protected function _fetchData(KViewContext $context)
    {
        $params = JFactory::getApplication()->getMenu()->getActive()->params;

        $context->data->append(array(
            'params'     => $params,
            'show_date'  => $params->get('show_date'),
            'show_time'  => $params->get('show_time'),
            'show_icons' => $params->get('show_icons'),
        ));

        if ($next = $this->getModel()->getTable()->getNext())
        {
            $url = $this->getRoute(sprintf('tmpl=component&offset=%s&direction=%s', key($next), current($next)), false, false);
            $context->data->append(array('next' => $url));
        }

        return parent::_fetchData($context);
    }
}