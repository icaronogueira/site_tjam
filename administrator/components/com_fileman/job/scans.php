<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanJobScans extends ComSchedulerJobAbstract
{
    const STATUS_PENDING = 0;

    const STATUS_SENT = 1;

    const STATUS_FAILED = 2;

    const STATUS_DEFERRED = 3;

    const MAXIMUM_PENDING_SCANS = 6;

    protected $_scans_model;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_scans_model = $config->scans_model;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'scans_model' => 'com://admin/fileman.model.scans',
            'frequency'   => ComSchedulerJobInterface::FREQUENCY_EVERY_FIVE_MINUTES
        ));

        parent::_initialize($config);
    }

    public function run(ComSchedulerJobContextInterface $context)
    {
        try {
            $i = 0;

            while ($context->hasTimeLeft() && $i < 4)
            {
                $this->purgeStaleScans();

                if (!$scan = $context->scan)
                {
                    $scan = $this->_getScansModel()
                                 ->status(self::STATUS_PENDING)
                                 ->limit(1)
                                 ->sort('created_on')->direction('desc')
                                 ->fetch();
                }

                if (!$scan->isNew() && !in_array($scan->status, array(self::STATUS_SENT, self::STATUS_DEFERRED)))
                {
                    $this->sendScan($scan, $context);

                    if ($scan->status == self::STATUS_SENT) {
                        $context->log('Sent request to scan ' . $scan->identifier);
                    }
                }

                $i++;
            }

            // TODO Not sure we need this check here, this is done on ::sendScan
            if (!$this->isSupported()) {
                $context->log('Joomlatools Connect credentials are missing');
            }

            // TODO Not sure we need this check here, this is done on ::sendScan
            if ($this->needsThrottling()) {
                $context->log('Waiting for active scans to complete before sending new ones');
            }
        }
        catch (Exception $e) {
            $context->log($e->getMessage());
        }

        return $this->complete();
    }

    public function sendScan($scan, ComSchedulerJobContextInterface $context)
    {
        $result = false;

        $entity = $scan->getEntity();

        if (!$entity->isNew())
        {
            if ($scan->status != self::STATUS_SENT)
            {
                if (!$this->isSupported()) {
                    $context->log('Joomlatools Connect is not installed, it is outdated or its credentials are missing');
                }

                if ($this->isLocal()) {
                    $context->log('File scan needs public server');
                }

                if ($this->needsThrottling()) {
                    $context->log('File scan is throttled');
                }

                $parameters = $scan->getParameters();

                $data = array(
                    'download_url' => (string) $this->_getDownloadUrl($entity),
                    'callback_url' => (string) $this->_getCallbackUrl(),
                    'filename'     => ltrim(basename(' ' . strtr($entity->path, array('/' => '/ ')))),
                    'user_data'    => array(
                        'container' => $entity->container,
                        'folder'    => $entity->folder,
                        'name'      => $entity->name
                    )
                );

                if ($target = $parameters->target) {
                    $data['user_data']['target'] = KObjectConfigJson::unbox($target);
                }

                if ($scan->thumbnail)
                {

                    if ($size = $parameters->size)
                    {
                        $data['thumbnail_size'] = array(
                            'width'  => $size->width,
                            'height' => $size->height
                        );
                    }
                }

                $response = PlgKoowaConnect::sendRequest('scanner/start', ['data' => $data]);

                if ($response && $response->status_code == 200)
                {
                    $scan->status = static::STATUS_SENT;
                    $scan->response = $response->body;
                    $scan->save();

                    $result = true;
                }
            }
        }
        else $scan->delete(); // Delete the scan

        return $result;
    }

    /**
     * Return a callback URL to the plugin with a JWT token
     *
     * @return KHttpUrlInterface
     */
    protected function _getCallbackUrl()
    {
        /** @var KHttpUrlInterface $url */
        $url = clone $this->getObject('request')->getSiteUrl();

        $query = array(
            'option'               => 'com_fileman',
            'view'                 => 'files',
            'format'               => 'json',
            'connect'              => 1,
            //'XDEBUG_SESSION_START' => 'PHPSTORM',
            'token'                => PlgKoowaConnect::generateToken()
        );

        if (substr($url->getPath(), -1) !== '/') {
            $url->setPath($url->getPath().'/');
        }

        $url->setQuery($query);

        return $url;
    }

    /**
     * Return a download URL with a JWT token for the given entity
     *
     * This will bypass all access checks to make sure thumbnail service can access the file
     *
     * @param  KModelEntityInterface $entity
     * @return KHttpUrlInterface
     */
    protected function _getDownloadUrl(KModelEntityInterface $entity)
    {
        /** @var KHttpUrlInterface $url */
        $url       = clone $this->getObject('request')->getSiteUrl();

        $query = array(
            'option'    => 'com_fileman',
            'view'      => 'file',
            'container' => $entity->container,
            'folder'    => $entity->folder,
            'name'      => $entity->name,
            //'XDEBUG_SESSION_START' => 'PHPSTORM',
            'serve'     => 1,
            'connect'   => 1,
            'token'     => PlgKoowaConnect::generateToken()
        );

        $url->setQuery($query);

        return $url;
    }

    public function needsThrottling()
    {
        $count = $this->_getScansModel()->status(self::STATUS_SENT)->count();

        return ($count >= self::MAXIMUM_PENDING_SCANS);
    }


    public function purgeStaleScans()
    {
        /*
         * Set status back to "not sent" for scans that did not receive a response for over an hour
         */
        /** @var KDatabaseQueryUpdate $query */
        $query = $this->getObject('database.query.update');

        $now = gmdate('Y-m-d H:i:s');

        $query
            ->values('status = ' . self::STATUS_PENDING)
            ->table(array('tbl' => 'fileman_scans'))
            ->where('status = ' . self::STATUS_SENT)
            ->where("GREATEST(created_on, modified_on) < DATE_SUB(:now, INTERVAL 5 MINUTE)")
            ->bind(['now' => $now]);

        $this->getObject('com://admin/fileman.database.table.scans')->getAdapter()->update($query);
    }

    public function addScan(KModelEntityInterface $entity, $config)
    {
        $result = false;

        if ($this->isSupported())
        {
            $scan = $this->getScan($entity);

            $scan->setProperties($config)->save();

            if (!$scan->isNew()) {
                $result = $scan;
            }
        }

        return $result;
    }

    public function getScan(KModelEntityInterface $entity)
    {
        $model = $this->_getScansModel();

        $scan = $model->container($entity->container)->folder($entity->folder)->name($entity->name)->fetch();

        if ($scan->isNew())
        {
            $scan = $model->create()->setProperties(array(
                'folder'    => $entity->folder,
                'name'      => $entity->name,
                'container' => $entity->container
            ));
        }

        return $scan;
    }

    public function isSupported()
    {
        return class_exists('PlgKoowaConnect') && PlgKoowaConnect::isSupported()
               && defined('PlgKoowaConnect::VERSION')
               && version_compare(PlgKoowaConnect::VERSION, '2.0.0', '>=');
    }

    public function isLocal()
    {
        return PlgKoowaConnect::isLocal();
    }

    public function isEnabled()
    {
        return $this->isSupported() && !$this->isLocal();
    }

    protected function _getScansModel()
    {
        return $this->getObject($this->_scans_model);
    }
}