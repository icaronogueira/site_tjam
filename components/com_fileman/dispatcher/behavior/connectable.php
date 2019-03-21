<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanDispatcherBehaviorConnectable extends KControllerBehaviorAbstract
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array('priority' => self::PRIORITY_HIGH));
        parent::_initialize($config);
    }

    public function isSupported()
    {
        return $this->getObject('com://admin/fileman.job.scans')->isEnabled();
    }

    protected function _beforeDispatch(KControllerContextInterface $context)
    {
        /** @var KDispatcherRequest $request */
        $request = $this->getObject('request');
        $query   = $request->getQuery();

        if ($query->has('connect'))
        {
            if ($query->has('token'))
            {
                if (!PlgKoowaConnect::verifyToken($query->token)) {
                    throw new RuntimeException('Invalid JWT token');
                }

                if ($request->isGet() && $query->has('serve')) {
                    $this->_serveEntity($this->_getEntity($request));
                }
                elseif ($request->isPost())
                {
                    $result = array(
                        'result' => $this->_processPayload()
                    );

                    $this->getObject('response')
                        ->setContent(json_encode($result), 'application/json')
                        ->send();
                }

                return false;
            }
        }

        return true;
    }

    protected function _getEntity(KControllerRequestInterface $request)
    {
        $query = $request->getQuery();

        return $this->getObject('com:files.model.files')
                    ->container($query->container)
                    ->folder($query->folder)
                    ->name($query->name)
                    ->fetch();
    }

    /**
     * Serve a document for the consumption of the thumbnail service
     *
     * @param integer $id
     */
    protected function _serveEntity($entity)
    {
        if ($entity->isNew()) {
            throw new KControllerExceptionResourceNotFound('Entity not found');
        }

        /** @var KDispatcherResponseAbstract $response */
        $response = $this->getObject('response');

        $response->attachTransport('stream')
                 ->setContent($entity->fullpath, $entity->mimetype ?: 'application/octet-stream')
                 ->getHeaders()->set('Content-Disposition', ['attachment' => ['filename' => '"file"']]);

        $response->send();
    }

    /**
     * Updates the document thumbnail from the request payload
     *
     * @return boolean
     */
    protected function _processPayload()
    {
        /** @var KDispatcherRequest $request */
        $request = $this->getObject('request');
        $data = $request->getData();

        $user_data = $data->user_data;

        if (!isset($user_data['folder']) || !isset($user_data['name']) || !isset($user_data['container'])) {
            throw new RuntimeException('Missing user data');
        }

        $scan = $this->getObject('com://admin/fileman.model.scans')
                     ->container($user_data['container'])
                     ->folder($user_data['folder'])
                     ->name($user_data['name'])->fetch();

        if ($scan->isNew()) {
            throw new RuntimeException('Scan not found');
        }

        $entity = $scan->getEntity();

        if ($entity->isNew()) {
            throw new RuntimeException('Entity not found');
        }

        if ($scan->thumbnail && isset($data->thumbnail_url))
        {
            $model = $this->getObject('com:files.model.files');

            $container = $this->getObject('com:files.model.containers')
                              ->slug($user_data['target']['container'])
                              ->fetch();

            if ($container->isNew()) {
                throw new RuntimeException('Thumbnails container not found');
            }

            $model->container($container->slug)
                  ->name($user_data['target']['name'])
                  ->folder($user_data['target']['folder']);

            $thumbnail = $model->create();

            $file = $this->getObject('com:files.model.entity.url', array('data' => array('file' => $data->thumbnail_url)));

            if ($file->contents)
            {
                $thumbnail->contents = $file->contents;
                $thumbnail->save();
            }
            else throw new RuntimeException('Could not read thumbnail content');
        }

        if ($scan->ocr && isset($data->contents_url))
        {
            try {
                $file = $this->getObject('com:files.model.entity.url', array('data' => array('file' => $data->contents_url)));

                if ($file->contents)
                {
                    $model = $this->getObject('com://admin/fileman.model.contents');

                    $path = sprintf('%s/%s', $user_data['folder'], $user_data['name']);

                    $content = $model->container($user_data['container'])->path($path)->fetch();

                    if ($content->isNew()) {
                        $content = $model->create();
                    }

                    $content->setProperty('contents', $file->contents)->save();
                }
            }
            catch (Exception $e) {}
        }

        if (!empty($data->error)) {
            $scan->status = ComFilemanJobScans::STATUS_FAILED;
            PlgSystemJoomlatoolsscheduler::log(sprintf('Failed to process scan %s', $scan->identifier));
            $scan->save();
        } else {
            PlgSystemJoomlatoolsscheduler::log(sprintf('Processed scan %s', $scan->identifier));
            $scan->delete();
        }

        return true;
    }
}
