<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanViewUserfolderHtml extends ComFilemanViewHtml
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->auto_fetch = false;
        parent::_initialize($config);
    }

    protected function _fetchData(KViewContext $context)
	{
        $menu   = JFactory::getApplication()->getMenu()->getActive();

        if (!$menu) {
            throw new RuntimeException('Invalid menu item');
        }

        if ($this->getLayout() === 'default' && isset($menu->query['layout'])) {
            $this->setLayout($menu->query['layout']);
            $context->layout = $menu->query['layout'];
        }

        $params = new ComKoowaDecoratorParameter(new KObjectConfig(array('delegate' => $menu->params)));

        //$folders = $this->getModel()->fetch();

        $state = $this->getModel()->getState();

        $query = $state->getValues();

		if ($this->getLayout() === 'gallery') {
			$query['types'] = array('image');
        }

        $folder_controller = $this->getObject('com:files.controller.folder');
        $folder_controller->getRequest()->setQuery($query);

        $folders      = $folder_controller->browse();
        $folder_count = $folder_controller->getModel()->count();

        $file_controller = $this->getObject('com:files.controller.file');
        $file_controller->getRequest()->setQuery($query);

        $files = $file_controller->browse();
        $total = $file_controller->getModel()->count();

        $humanize = $params->humanize_filenames;

        foreach ($folders as $folder) {
            $folder->display_name  = $humanize ? ucfirst(preg_replace('#[-_\s\.]+#i', ' ', $folder->name)) : $folder->name;
        }

        foreach ($files as $file)
        {
            $file->display_name  = $humanize ? ucfirst(preg_replace('#[-_\s\.]+#i', ' ', $file->filename)) : $file->name;

            $request = $this->getObject('request');

            $permalink = sprintf('%s/filelink/%s', $request->getSiteUrl(), rawurlencode($file->container));

            if ($file->folder)
            {
                $path = explode('/', $file->folder);

                foreach ($path as $folder) {
                    $permalink .= '/' . rawurlencode($folder);
                }
            }

            $permalink .= '/' . rawurlencode($file->name);

            $file->permalink = $permalink;
        }

        $params->show_page_heading = JFactory::getApplication()->getParams()->get('show_page_heading');

        // Always show folders on user views.
        $params->show_folders = 1;

        if (!$params->page_heading) {
            $params->page_heading = $menu->title;
        }

        $parts = explode('/', $state->folder);

        $name = array_pop($parts);

        $current = $this->getObject('com:files.model.folders')
                        ->container('fileman-user-files')
                        ->folder(implode('/', $parts))
                        ->name($name)
                        ->fetch();

        $context->data->can_copy = $params->get('show_copy_link', 1);

        $context->data->folder         = $current;
        $context->data->folders        = $folders;
        $context->data->folder_count   = $folder_count;
        $context->data->files          = $files;
        $context->data->total          = $total;
        $context->data->params         = $params;
        $context->data->thumbnail_size = array('x' => 200, 'y' => 150);

        $this->_setPathway($context);

        parent::_fetchData($context);

        $context->parameters->total = $total;
	}
    protected function _setPathway(KViewContext $context)
    {
        $parts = explode('/', trim($context->data->folder->path, '/'));

        // Shift the user folder from the path
        $user = array_shift($parts);

        if (count($parts))
        {
            $pathway = JFactory::getApplication()->getPathway();

            /*
            $menu   = $context->data->menu;
            $append = '';

            $base_path = $context->data->base_path;

            if ($base_path && strpos($path, $base_path) === 0)
            {
                $path = substr($path, strlen($base_path)+1, strlen($path));
                $append = $base_path;
            }
            */

            foreach ($parts as $i => $part)
            {
                if ($part !== $context->data->folder->name)
                {
                    $path = $user . '/' . implode('/', array_slice($parts, 0, $i + 1));
                    $link = $this->getRoute('offset=0&layout=' . $this->getLayout() . '&view=userfolder&folder=' . rawurlencode($path));
                }
                else $link = '';

                $pathway->addItem(ucfirst($part), $link);
            }
        }
    }
}
