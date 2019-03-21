<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanDatabaseBehaviorScannable extends KModelBehaviorAbstract
{
    protected $_job;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_job = $config->job;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array('job' => 'com://admin/fileman.job.scans'));
        parent::_initialize($config);
    }

    protected function _getJob()
    {
        if (!$this->_job instanceof ComSchedulerJobInterface) {
            $this->_job = $this->getObject($this->_job);
        }

        return $this->_job;
    }

    public function isSupported()
    {
        return $this->_getJob()->isEnabled();
    }

    /**
     * Hooks into thumbnail controller and stops the default local thumbnail generation
     *
     * Returns false if the document is in queue to be scanned
     *
     * @param KControllerContextInterface $context
     * @return bool
     */
    protected function _beforeSave(KDatabaseContextInterface $context)
    {
        $result = true;

        $entity = $context->getSubject();

        if ($entity->getIdentifier()->getName() == 'thumbnail' && $entity->source && !is_string($entity->source))
        {
            if (!$entity->version || $entity->version == 'large')
            {
                $config = array(
                    'ocr'        => $entity->source->isImage() ? false : true,
                    'thumbnail'  => true,
                    'parameters' => array(
                        'size'   => array(
                            'width'  => $entity->dimension['width'],
                            'height' => $entity->dimension['height']
                        ),
                        'target' => array(
                            'container' => $entity->getContainer()->slug,
                            'name'      => $entity->name,
                            'folder'    => $entity->folder
                        )
                    )
                );

                if (strpos($entity->folder, 'tmp_') !== false) {
                    $config['status'] = 3; // Mark as suspended to be sent at a later time
                }

                $job = $this->_getJob();

                if ($scan = $job->addScan($entity->source, $config))
                {
                    PlgSystemjoomlatoolsscheduler::runJob($job, array('scan' => $scan));
                    $result = false;
                }
            }
            else
            {
                if ($thumbnail = $entity->source->getThumbnail('large')) {
                    $this->source = $thumbnail; // Use the large thumbnail to create the rest
                } else {
                    $result = false; // Hold the thumbnails creation
                }
            }
        }

        return $result;
    }

    protected function _afterSave(KDatabaseContextInterface $context)
    {
        $entity = $context->getSubject();

        if ($entity->getIdentifier()->getName() == 'file' && $this->getStatus() != KDatabase::STATUS_FAILED)
        {
            $this->_getJob()->addScan($entity, array(
                'ocr'        => true,
                'parameters' => array(
                    'target' => array(
                        'container' => $entity->getContainer()->slug,
                        'name'      => $entity->name,
                        'folder'    => $entity->folder
                    )
                )
            ));
        }
    }
}