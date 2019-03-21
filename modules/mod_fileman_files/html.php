<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ModFileman_filesHtml extends ModKoowaHtml
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'auto_fetch' => false,
            'model' => 'com:files.model.files'
        ));

        parent::_initialize($config);
    }

    /**
     * Return the views output
     *
     * @param KViewContext	$context A view context object
     * @return string  The output of the view
     */
    protected function _actionRender(KViewContext $context)
    {
        if (count($context->data->files)) {
            return parent::_actionRender($context);
        }

        return '';
    }

    /**
     * Sets the layout from the parameters
     *
     * @param KViewContext $context
     */
    protected function _beforeRender(KViewContext $context)
    {
        if ($layout = $this->module->params->layout)
        {
            $this->setLayout($layout);
        }
    }

    protected function _fetchData(KViewContext $context)
    {
        $menu   = null;
        $params = $this->module->params;
        $state  = $this->getModel()->getState();

        if ($params->get('page'))
        {
            $menu = JFactory::getApplication()->getMenu()->getItem($params->get('page'));

            if ($menu)
            {
                $menu_folder = isset($menu->query['folder']) ? $menu->query['folder'] : '';

                if (!empty($menu_folder) && strpos($params->folder, $menu_folder) !== 0) {
                    $params->set('page', null);
                }
            }
        }

        $state->setValues($params->toArray());

        $state->container = 'fileman-files';
        $state->name      = null;

        $state->setProperty('container', 'internal', true);

        $query = $state->getValues();

        if ($this->getLayout() == 'gallery') {
            $params->show_thumbnails = true;
        }

        $query['thumbnails'] = $params->show_thumbnails ? 'small' : false;
        $query['folder']     = isset($query['folder']) ? rawurldecode($query['folder']) : '';

        $menu_layout = isset($menu->query['layout']) ? $menu->query['layout'] : '';

        if ($this->getLayout() === 'gallery' || $menu_layout === 'gallery') {
            $query['types'] = array('image');
        }

        $controller = $this->getObject('com:files.controller.file');
        $controller->getRequest()->setQuery($query);

        $files = $controller->browse();

        foreach ($files as $file) {
            $file->display_name = $params->humanize_filenames ? ucfirst(preg_replace('#[-_\s\.]+#i', ' ', $file->filename)) : $file->name;
        }

        $context->data->files  = $files;
        $context->data->params = $params;

        $context->layout = $this->getLayout();

        parent::_fetchData($context);
    }

    public function getRoute($route = '', $fqr = false, $escape = true)
    {
        //Parse route
        $parts = array();

        $params = $this->module->params;

        if (is_string($route)) {
            parse_str(trim($route), $parts);
        } else {
            $parts = $route;
        }

        if (!isset($parts['Itemid']) && $params->get('page')) {
            $parts['Itemid'] = $params->get('page');
        }

        return parent::getRoute($parts, $fqr, $escape);
    }
}
