<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanControllerFile extends ComKoowaControllerModel
{
    protected function _initialize(KObjectConfig $config)
    {
        $behaviors = array(
            'previewable',
            'com:files.controller.behavior.thumbnailable',
            'com://site/fileman.controller.behavior.contentable'
        );

        if ($menu = JFactory::getApplication()->getMenu()->getActive())
        {
            $link = $menu->link;

            parse_str($link, $query);

            if (isset($query['view']) && $query['view'] == 'userfolder') {
                $behaviors[] = 'ownable';
            }
        }

        $config->append(array(
            'behaviors' => $behaviors,
            'model'     => 'com:files.model.files',
            'formats'   => array('json')
        ));


        parent::_initialize($config);
    }

    public function getRequest()
    {
        $request = parent::getRequest();
        $query   = $request->query;

        if($this->isDispatched())
        {
            //Set force download
            $menu = JFactory::getApplication()->getMenu()->getActive();

            if ($menu && $menu->params->get('force_download')) {
                $query->set('force-download', 1);
            }
        }

        return $request;
    }

    protected function _actionRender(KControllerContextInterface $context)
    {
        $file = $this->getObject('com:files.controller.file')->setRequest($this->getRequest())->read();

        try
        {
            $this->getResponse()
                ->attachTransport('stream')
                ->setContent($file->fullpath, $file->mimetype ?: 'application/octet-stream');
        }
        catch (InvalidArgumentException $e) {
            throw new KControllerExceptionResourceNotFound('File not found');
        }

        return $file;
    }
}
