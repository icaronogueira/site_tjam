<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanViewFolderRss extends ComFilemanViewRss
{
    protected function _fetchData(KViewContext $context)
    {
        $state  = $this->getModel()->getState();
        $parts  = explode('/', $state->folder);
        $state->name   = array_pop($parts);
        $state->folder = implode('/', $parts);
        $folder = $this->getModel()->reset()->fetch();

        $menu  = JFactory::getApplication()->getMenu()->getActive();
        $params = new ComKoowaDecoratorParameter(new KObjectConfig(array('delegate' => $menu->params)));

        $state->setProperty('sort', 'default', $params->sort);
        $state->setProperty('direction', 'default', $params->direction);
        $state->setProperty('container', 'internal', true);

        $state->folder = ($state->folder ? $state->folder.'/' : '').$state->name;
        $state->name   = null;

        $query = $state->getValues();
        $query['folder'] = isset($query['folder']) ? rawurldecode($query['folder']) : '';
        $query['thumbnails'] = (bool) $params->show_thumbnails;

        $file_controller = $this->getObject('com:files.controller.file');
        $file_controller->getRequest()->setQuery($query);

        $files = $file_controller->browse();

        $humanize = $params->humanize_filenames;

        foreach ($files as $f) {
            $f->display_name = $humanize ? ucfirst(preg_replace('#[-_\s\.]+#i', ' ', $f->filename)) : $f->name;
        }

        $parent = null;

        if ($menu->query['folder'] !== $folder->path)
        {
            $path   = explode('/', $folder->path);
            $parent = count($path) > 1 ? implode('/', array_slice($path, 0, count($path)-1)) : '';
            $params->page_heading = ucfirst($folder->name);
        }

        if (!$params->page_heading) {
            $params->page_heading = $menu->title;
        }

        $context->data->append(array(
            'channel_link' => $this->getRoute('layout=gallery&folder='.rawurlencode($folder->path).'&format=html'),
            'feed_link' => $this->getRoute('layout=default&folder='.rawurlencode($folder->path).'&format=rss'),
            'folder' => $folder,
            'files' => $files,
            'parent' => $parent,
            'params' => $params,
            'menu' => $menu
        ));

        parent::_fetchData($context);
    }
}
