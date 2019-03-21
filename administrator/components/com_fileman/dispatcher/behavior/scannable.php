<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanDispatcherBehaviorScannable extends KControllerBehaviorAbstract
{
   protected $_job;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_model = $config->model;
        $this->_job   = $config->job;

        if ($this->isSupported())
        {
            // Make document files thumbnailable when connect is supported
            $this->getIdentifier('com:files.database.behavior.thumbnailable')
                 ->getConfig()
                 ->append(array(
                     'thumbnailable_extensions' => ComFilemanModelEntityScan::$thumbnail_extensions
                 ));

            $identifiers = array('com:files.model.entity.file', 'com:files.model.entity.thumbnail');

            foreach ($identifiers as $identifier) {
                $this->getIdentifier($identifier)->getConfig()->append(array(
                    'behaviors' => array('com://admin/fileman.database.behavior.scannable' => array('job' => $this->_job))
                ));
            }
        }
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'job'      => 'com://admin/fileman.job.scans',
            'priority' => static::PRIORITY_HIGH, // High priority so that it runs first
        ));

        parent::_initialize($config);
    }

    public function isSupported()
    {
        return $this->_getJob()->isEnabled();
    }


    protected function _getJob()
    {
        if (!$this->_job instanceof ComSchedulerJobInterface) {
            $this->_job = $this->getObject($this->_job);
        }

        return $this->_job;
    }

    protected function _afterForward(KDispatcherContextInterface $context)
    {
        $result = $context->result;
        $request = $context->getRequest();

        if (!$request->isSafe() && $result->isScanable())
        {
            $response = $context->getResponse();

            foreach ($result->getScanMessages() as $type => $messages)
            {
                foreach ($messages as $message) {
                    $response->addMessage($message, $type);
                }
            }
        }
    }
}