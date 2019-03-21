<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanModelScans extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
             ->insert('status', 'int')
             ->insert('container', 'cmd')
             ->insert('folder', 'com:files.filter.path')
             ->insert('name', 'string');
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if ($state->container) {
            $query->where('container = :container')->bind(array('container' => $state->container));
        }

        if ($state->folder) {
            $query->where('folder = :folder')->bind(array('folder' => $state->folder));
        }

        if ($state->name) {
            $query->where('name = :name')->bind(array('name' => $state->name));
        }

        if ($state->status !== null) {
            $query->where('status IN :status')->bind(array('status' => (array) $state->status));
        }
    }
}
