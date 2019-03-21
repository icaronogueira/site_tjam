<?php
/**
 * @package    LOGman
 * @copyright   Copyright (C) 2018 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComLogmanJobSynchronize extends ComSchedulerJobAbstract
{
    const STATUS_SYNCED = 1;

    const STATUS_UNSYNCED = 0;

    const STATUS_FAILED = -1;

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'frequency' => ComSchedulerJobInterface::FREQUENCY_EVERY_QUARTER_HOUR
        ));

        parent::_initialize($config);
    }

    public function run(ComSchedulerJobContextInterface $context)
    {
        if (!$this->_hasConnect()) {
            $context->log('Connect is disabled');

            return $this->skip();
        }

        if (!$this->_isSyncEnabled()) {
            $context->log('Synchronization is disabled');

            return $this->skip();
        }

        $model = $this->_getModel()->sync_status(0)->limit(50);
        $view  = $this->getObject('com://admin/logman.view.activities.json', [
            'layout' => 'stream',
            'model'  => $model
        ]);

        $unsynced_activities = $this->_getModel()->sync_status(0)->limit(50)->fetch();
        $new_activities      = [];

        if (!count($unsynced_activities)) {
            $context->log('Everything is synced');

            return $this->skip();
        }

        $this->_loadTranslations();

        /*
         * Bind to view so that we can use stream rendering without duplicating the code
         */
        $callback = Closure::bind(function() use (&$new_activities, $unsynced_activities) {
            foreach ($unsynced_activities as $activity) {
                $new_activities[$activity->id] = $activity->toArray();
                $new_activities[$activity->id]['metadata'] = json_decode($activity->metadata);
                $new_activities[$activity->id]['stream'] = $this->_getEntity($activity);
            }

            return false;
        }, $view, $view);

        $view->addCommandCallback('before.render', $callback);
        $view->render();

        try
        {
            $response = PlgKoowaConnect::sendRequest('activities', [
                'data' => [
                    'url'        => (string)$this->getObject('request')->getSiteUrl(),
                    'activities' => $new_activities
                ]
            ]);
            $response = json_decode($response->body, true);
            $synced   = [];
            $failed   = [];

            if (empty($response['results'])) {
                throw new RuntimeException('No results');
            }

            foreach ($response['results'] as $uuid => $status)
            {
                if ($status) {
                    $synced[] = $uuid;
                } else {
                    $failed[] = $uuid;
                }
            }

            $this->_setSyncStatus($synced, static::STATUS_SYNCED);
            $this->_setSyncStatus($failed, static::STATUS_FAILED);

            $can_continue = $this->_getModel()->sync_status(static::STATUS_UNSYNCED)->count() && $context->hasTimeLeft();

            return $can_continue ? $this->suspend() : $this->complete();
        }
        catch (Exception $e)
        {
            if (JDEBUG) { throw $e; }

            return $this->complete();
        }
    }

    protected function _hasConnect()
    {
        return class_exists('PlgKoowaConnect') && PlgKoowaConnect::isSupported()
            && defined('PlgKoowaConnect::VERSION') && version_compare(PlgKoowaConnect::VERSION, '2.0.0', '>=');
    }

    protected function _isSyncEnabled()
    {
        $plugin = JPluginHelper::getPlugin('koowa', 'connect');

        if ($plugin) {
            $params = json_decode($plugin->params);

            return $params->activities;
        }

        return false;
    }

    protected function _getModel()
    {
        static $model;

        if (!$model)
        {
            $callback = function($context) {
                $state = $context->subject->getState();

                if (!is_null($state->sync_status))
                {
                    $context->query->join('logman_synchronization AS synchronization', 'tbl.uuid = synchronization.uuid', 'LEFT');

                    if ($state->sync_status === 0 || $state->sync_status === '0')
                    {
                        $context->query->where('synchronization.status IS NULL OR synchronization.status = :sync_status')
                            ->bind(['sync_status' => $state->sync_status]);
                    }
                    else
                    {
                        $context->query->where('synchronization.status = :sync_status')
                            ->bind(['sync_status' => $state->sync_status]);
                    }
                }
            };

            $model = $this->getObject('com://admin/logman.model.activities');
            $model->getState()->insert('sync_status', 'int');
            $model->addCommandCallback('before.fetch', $callback);
            $model->addCommandCallback('before.count', $callback);
        }

        return $model;
    }

    protected function _setSyncStatus(array $activity_uuids, $status)
    {
        if (count($activity_uuids))
        {
            $values = [];

            foreach ($activity_uuids as $uuid) {
                $values[] = [$uuid, $status];
            }
            $query = $this->getObject('lib:database.query.insert')
                ->replace()
                ->table('logman_synchronization');

            foreach ($values as $row) {
                $query->values($row);
            }

            return $this->getObject('database.adapter.mysqli')->insert($query);
        }

        return false;
    }

    /**
     * Force-load English keys for message rendering
     */
    protected function _loadTranslations()
    {
        $translator = $this->getObject('translator');
        $translator->setLocale('en-GB');
        $translator->load('com:activities', true);
        $translator->load('com//admin/logman', true);
    }
}