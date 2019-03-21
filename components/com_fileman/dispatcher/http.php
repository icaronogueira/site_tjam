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
            'behaviors'  => array(
                'com://admin/fileman.dispatcher.behavior.attachable',
                'com://admin/fileman.dispatcher.behavior.routable',
                'com://site/fileman.dispatcher.behavior.connectable',
                'com://admin/fileman.dispatcher.behavior.scannable'
            )
        ));
        
        parent::_initialize($config);
    }

    /**
     * Overloaded execute function to handle exceptions in JSON requests
     */
    public function execute($action, KControllerContextInterface $context)
    {
        try {
            return parent::execute($action, $context);
        } catch (Exception $e) {
            return $this->_handleException($e);
        }
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

            if ($query->folder) {
                $query->folder = trim($query->folder, '/');
            }
        }

        return $this->_request;
    }

    protected function _handleException(Exception $e)
    {
        if ($this->getRequest()->getFormat() == 'json')
        {
            $obj = new stdClass;
            $obj->status = false;
            $obj->error  = $e->getMessage();
            $obj->code   = $e->getCode() ? $e->getCode() : 500;

            header($obj->code.' '.str_replace("\n", ' ', $e->getMessage()), true, $obj->code);

            echo json_encode($obj);

            JFactory::getApplication()->close();
        }
        else throw $e;

        return false;
    }
}