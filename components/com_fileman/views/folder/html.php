<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanViewFolderHtml extends ComFilemanViewHtml
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->auto_fetch = false;

        parent::_initialize($config);
    }

    protected function _fetchData(KViewContext $context)
    {
        $state  = $this->getModel()->getState();

        $parts  = explode('/', $state->folder);
        $state->name   = array_pop($parts);
        $state->folder = implode('/', $parts);

        if ($state->name)
        {
            $current = $this->getModel()->reset()->fetch();

            if ($current->isNew()) $current = $this->getModel()->create(array('entity' => $state->toArray()));
        }
        else $current = $this->getModel()->create();

        $menu = JFactory::getApplication()->getMenu()->getActive();

        if (!$menu) {
            throw new RuntimeException('Invalid menu item');
        }

        if ($this->getLayout() === 'default' && isset($menu->query['layout'])) {
            $this->setLayout($menu->query['layout']);
            $context->layout = $menu->query['layout'];
        }

        $params = new ComKoowaDecoratorParameter(new KObjectConfig(array('delegate' => $menu->params)));

        $state->setProperty('sort', 'default', $params->sort);
        $state->setProperty('direction', 'default', $params->direction);
        $state->setProperty('container', 'internal', true);

        $state->folder = ($state->folder ? $state->folder.'/' : '').$state->name;
        $state->name   = null;

        $query = $state->getValues();

        $query['folder'] = isset($query['folder']) ? $query['folder'] : '';

        if ($this->getLayout() === 'gallery' && $params->get('show_images_only', 1)) {
            $query['types'] = array('image');
        }

        $folders = array();

        if ($params->show_folders)
        {
            $folder_controller = $this->getObject('com:files.controller.folder');
            $folder_controller->getRequest()->setQuery($query);

            $folders      = $folder_controller->browse();
            $folder_count = $folder_controller->getModel()->count();
        }
        else $folder_count = 0;

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
            $file->display_name = $humanize ? ucfirst(preg_replace('#[-_\s\.]+#i', ' ', $file->filename)) : $file->name;

            $file->permalink = $this->getObject('com://admin/fileman.template.helper.route')
                                    ->permalink(array('file' => $file));
        }

        $params->show_page_heading = JFactory::getApplication()->getParams()->get('show_page_heading');

        if (!$params->page_heading) {
            $params->page_heading = $menu->title;
        }

        $context->data->can_copy = $params->get('show_copy_link', 1);

        $context->data->folder         = $current;
        $context->data->files          = $files;
        $context->data->total          = $total;
        $context->data->folders        = $folders;
        $context->data->folder_count   = $folder_count;
        $context->data->params         = $params;
        $context->data->menu           = $menu;
        $context->data->thumbnail_size = array('x' => 200, 'y' => 150);

        $this->_setPathway($context);

        parent::_fetchData($context);

        $context->parameters->total = $total;
    }

	protected function _setPathway(KViewContext $context)
	{
        $base_path     = $this->getConfig()->base_path;
        $relative_path = trim(str_replace($base_path, '', $context->data->folder->path), '/');

  		if ($relative_path)
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

            $parts = explode('/', $relative_path);

            foreach ($parts as $i => $part)
            {
                if ($part !== $context->data->folder->name)
                {
                    $path = $base_path.'/'.implode('/', array_slice($parts, 0, $i+1));
                    $link = $this->getRoute('offset=0&layout=' . $this->getLayout() . '&view=folder&folder=' . rawurlencode($path));
                }
                else $link = '';

                $pathway->addItem(ucfirst($part), $link);
            }
  		}
	}

    /**
     * Returns currently active menu item
     *
     * Default menu item for the site will be returned if there is no active menu items
     *
     * @return object
     */
    public function getActiveMenu()
    {
        $menu = JFactory::getApplication()->getMenu()->getActive();

        if (is_null($menu)) {
            $menu = JFactory::getApplication()->getMenu()->getDefault();
        }

        return $menu;
    }

    /**
     * Create a route based on a query string.
     *
     * Automatically adds the menu item ID to links
     *
     * {@inheritdoc}
     */
    public function getRoute($route = '', $fqr = false, $escape = true)
    {
        if (is_string($route)) {
            parse_str(trim($route), $parts);
        } else {
            $parts = $route;
        }

        if (!isset($parts['Itemid'])) {
            $parts['Itemid'] = $this->getActiveMenu()->id;
        }

        $route = parent::getRoute($parts, $fqr, $escape);

        $query = $route->getQuery(true);

        if ($query['view'] == 'folder' && isset($query['offset']))
        {
            unset($query['offset']);
            $route->setQuery($query);
        }

        return $route;
    }
}
