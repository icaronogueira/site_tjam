<?php
/**
 * @package    FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanModelConfigs extends KModelAbstract
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()->insert('page', 'int');
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'state' => 'com://admin/fileman.model.config.state',
        ));

        parent::_initialize($config);
    }

    protected function _actionFetch(KModelContext $context)
    {
        $item = clone $this->getObject('com://admin/fileman.model.entity.config');

        return $item;
    }
}
