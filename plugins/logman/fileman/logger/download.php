<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Download FILEman Logger
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Joomlatools\Plugin\LOGman
 */
class PlgLogmanFilemanLoggerDownload extends PlgLogmanFilemanLoggerNode
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->actions = array('after.render');

        parent::_initialize($config);
    }

    public function log($action, KModelEntityInterface $object, KObjectIdentifierInterface $subject)
    {
        // Do not log streaming (with range headers) requests.
        if (!$this->getObject('request')->isStreaming()) {
            parent::log($action, $object, $subject);
        }
    }

    public function getActivityData(KModelEntityInterface $object, KObjectIdentifierInterface $subject, $action = null)
    {
        $data = parent::getActivityData($object, $subject, $action);

        // Fix resource action.
        if ($data['name'] === 'file') {
            $data['action'] = 'download';
        }

        return $data;
    }

    public function getActivityStatus(KModelEntityInterface $object, $action = null)
    {
        return (parent::getActivityStatus($object, $action) != KModelEntityInterface::STATUS_FAILED) ? 'downloaded' : null;
    }

    public function getActivityObject(KCommandInterface $command)
    {
        // Return the document being downloaded.
        return $command->result;
    }
}