<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanModelContents extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $state = $this->getState();

        $state->insert('container', 'cmd', null, true, array('path'))
              ->insert('path', 'com:files.filter.path', null, true, array('container'));
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array('table' => 'file_contents'));
        parent::_initialize($config);
    }

    protected function _actionCreate(KModelContext $context)
    {
        $state = $this->getState();

        $context->append(array('entity' => array('path' => $state->path, 'container' => $state->container)));

        return parent::_actionCreate($context);
    }
}