<?php
/**
 * @package    FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ModFileman_foldersHtml extends ModKoowaHtml
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'auto_fetch' => false,
            'model' => 'com:files.model.folders'
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
        if (count($context->data->folders)) {
            return parent::_actionRender($context);
        }

        return '';
    }


    protected function _fetchData(KViewContext $context)
    {
        $menu   = null;
        $params = new ComKoowaDecoratorParameter(new KObjectConfig(array('delegate' => $this->module->params)));
        $state  = $this->getModel()->getState();

        $menu = JFactory::getApplication()->getMenu()->getItem($this->module->params->get('page'));

        if (!$menu) {
            return;
        }

        $menu_folder = isset($menu->query['folder']) ? $menu->query['folder'] : '';

        if (empty($params->folder) || (!empty($menu_folder) && strpos($params->folder, $menu_folder) !== 0)) {
            $params->folder = $menu_folder;
        }

        $state->setValues($params->toArray());

        $state->container = 'fileman-files';
        $state->name      = null;

        $state->setProperty('container', 'internal', true);

        $query = $state->getValues();
        $query['folder'] = isset($query['folder']) ? rawurldecode($query['folder']) : '';

        $folder_controller = $this->getObject('com:files.controller.folder');
        $folder_controller->getRequest()->setQuery($query);
        $folders = $folder_controller->browse();

        $total = $folder_controller->getModel()->count();

        $humanize = $params->humanize_filenames;

        $setFilenames = function($folders) use($humanize, &$setFilenames) {
            foreach ($folders as $entity)
            {
                $entity->display_name = $humanize ? ucfirst(preg_replace('#[-_\s\.]+#i', ' ', $entity->name)) : $entity->name;

                if ($entity->hasChildren()) {
                    $setFilenames($entity->getChildren());
                }
            }
        };

        $setFilenames($folders);

        $context->data->folders  = $folders;
        $context->data->total  = $total;
        $context->data->params  = $params;

        parent::_fetchData($context);

        $context->parameters->total = $total;
    }

    public function getRoute($route = '', $fqr = false, $escape = true)
    {
        //Parse route
        $parts = array();

        if (is_string($route)) {
            parse_str(trim($route), $parts);
        } else {
            $parts = $route;
        }

        if (!isset($parts['Itemid']) && $this->module->params->get('page')) {
            $parts['Itemid'] = $this->module->params->get('page');
        }

        return parent::getRoute($parts, $fqr, $escape);
    }
}
