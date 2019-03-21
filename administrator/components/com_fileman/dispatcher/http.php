<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanDispatcherHttp extends ComKoowaDispatcherHttp
{
	protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
        	'controller' => 'file',
            'behaviors'  => array(
                'com://admin/fileman.dispatcher.behavior.attachable',
                'com://admin/fileman.dispatcher.behavior.routable',
                'com://admin/fileman.dispatcher.behavior.scannable'
            )
        ));
        
        parent::_initialize($config);
    }

    public function getRequest()
    {
        if(!$this->_request instanceof KDispatcherRequestInterface)
        {
            $request = parent::getRequest();

            $query = $request->getQuery();

            if (!$query->container) {
                $query->container = 'fileman-files';
            }
        }

        return $this->_request;
    }
}