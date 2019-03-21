<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanControllerFolder extends ComFilesControllerFolder
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array('model' => 'com:files.model.folders'));
        parent::_initialize($config);
    }

    protected function _beforeRender(KControllerContextInterface $context)
    {
        $request = $context->getRequest();

        if ($page = $request->getQuery()->page)
        {
            $page = $this->getObject('com://admin/fileman.model.pages')->id($page)->fetch();

            if (!$page->isNew())
            {
                if ($folder = $page->folder) {
                    $this->getModel()->folder($folder);
                }
            }
        }

        $view = $this->getView();

        if ($view->getName() == 'folder' && $view->getLayout() == 'select')
        {
            if ($field = $request->getQuery()->field) {
                $view->getConfig()->field = $field;
            }
        }
    }
}