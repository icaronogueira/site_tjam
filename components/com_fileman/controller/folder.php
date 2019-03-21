<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanControllerFolder extends ComFilemanControllerAbstract
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'formats' => array('rss'),
            'model'   => 'com:files.model.folders'
        ));

        parent::_initialize($config);
    }
    
    protected function _beforeRender(KControllerContextInterface $context)
    {
        $request = $context->getRequest();

        if ($this->isDispatched())
        {
            $menu = JFactory::getApplication()->getMenu()->getActive();

            $internals = array('sort', 'direction', 'container');

            if ($menu && $request->isSafe())
            {
                $query         = $request->query;
                $params        = new ComKoowaDecoratorParameter(new KObjectConfig(array('delegate' => $menu->params)));
                $default_limit = (int) JFactory::getApplication()->getCfg('list_limit');

                if ($params->limit == 0) {
                    $limit = 0; // Unlimitted
                } elseif (!$query->limit) {
                    $limit = $default_limit;
                } else {
                    $limit = $query->limit;
                }

                // Set limit as internal
                if ($limit == 0 || ($params->limit == -1 && $limit == $default_limit)) {
                    $internals[] = 'limit';
                }

                $this->sort($params->sort)->direction($params->direction)->limit($limit);
            }

            $state = $this->getModel()->getState();

            foreach ($internals as $internal) {
                $state->setProperty($internal, 'internal', true);
            }
        }
    }

    protected function _beforeDelete(KControllerContextInterface $context)
    {
        $state = $this->getModel()->getState();

        // Properly translate FILEman routes before delete.
        if (!$state->name && $state->folder) {
            $folder = explode('/', ltrim($state->folder, '/'));
            $this->name(array_pop($folder))->folder(implode('/', $folder));
        }
    }
    
    public function getView()
    {
        $view = parent::getView();

        if (!$view->can_delete || !$view->can_add)
        {
            $view->can_delete = $this->canDelete();
            $view->can_add    = $this->canAdd();
        }

        $menu = JFactory::getApplication()->getMenu()->getActive();

        $folder = '';

        if ($menu && isset($menu->query['folder'])) {
            $folder = trim($menu->query['folder'], '/');
        }

        $view->getConfig()->append(array('base_path' => $folder));

        return $view;
    }
}
