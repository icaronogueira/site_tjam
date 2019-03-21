<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanTemplateHelperListbox extends ComKoowaTemplateHelperListbox
{
    public function folders($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'showroot' => true,
            'url_encode' => false,
            'folder' => null
        ));

        if ($config->page)
        {
            $folder = $this->getObject('com://admin/fileman.model.pages')->id($config->page)->fetch()->folder;

            if ($folder) {
                $config->folder = $folder;
            }
        }

        $tree = KObjectManager::getInstance()->getObject('com:files.controller.folder')
            ->container('fileman-files')
            ->tree(1)
            ->limit(0)
            ->browse();

        $options = array();
        if (!$config->folder && $config->showroot) {
            $options[] = array('label' => $this->getObject('translator')->translate('Root folder'), 'value' => '');
        }

        foreach ($tree as $folder) {
            $this->_addFolder($folder, $options, $config);
        }

        $config->options = $options;

        return $this->optionlist($config);
    }

    public function images($config = array())
    {
        $config = new KObjectConfig($config);

        $config->append(array(
            'version' => 'small',
            'attribs' => array('class' => 'k-form-control', 'size' => false)
        ));

        switch ($config->version)
        {
            case 'small':
                $versions = array(160, 320, 480);
                break;
            case 'large':
                $versions = array(640, 800, 960);
                break;
        }

        $options = array();

        foreach ($versions as $version) {
            $options[] = $this->option(array('label' => sprintf('%1$s x %1$s', $version), 'value' => $version));
        }

        $config->append(array('options' => $options));

        return KTemplateHelperSelect::optionlist($config);
    }

    public function attachmentsLayout($config = array())
    {
        $config = new KObjectConfig($config);

        $config->append(array(
            'name'    => 'attachments_layout',
            'attribs' => array('id' => 'attachments_layout', 'class' => 'k-form-control', 'size' => false)
        ));

        $options = array();

        $translator = $this->getObject('translator');

        if (!$config->options)
        {
            $options[] = (object) array('label' => $translator->translate('List'), 'value' => 'list');
            $options[] = (object) array('label' => $translator->translate('Gallery'), 'value' => 'gallery');

            $config->options = $options;
        }

        return KTemplateHelperSelect::optionlist($config);
    }

    protected function _addFolder($folder, &$options, $config)
    {
        if (!$config->folder || strpos($folder->path, $config->folder) === 0)
        {
            $padded = str_repeat('&nbsp;', 2*(count(explode('/', $folder->path)))).$folder->name;
            $path      = $config->url_encode ? htmlspecialchars(rawurlencode($folder->path), ENT_QUOTES) : $folder->path;
            $options[] = array('label' => $padded, 'value' => $path);
        }

        if ($folder->hasChildren())
        {
            foreach ($folder->getChildren() as $child) {
                $this->_addFolder($child, $options, $config);
            }
        }
    }

    public function pages($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'select2' => true,
            'options' => array(),
        ));

        $types = KObjectConfig::unbox($config->types);

        if (empty($types)) {
            $types = array('document', 'list', 'filteredlist', 'userlist', 'folder', 'userfolder');
        }

        $pages = $this->getObject('com://admin/fileman.model.pages')
                    ->language('all')
                    ->view($types)
                    ->fetch();

        $options = array();
        foreach ($pages as $page)
        {
            if (!isset($options[$page->menutype])) {
                $options[$page->menutype] = array();
            }

            $options[$page->menutype][] = array('value' => $page->id, 'label' => $page->title);
        }

        $config->options->append($options);

        return $this->optionlist($config);
    }
}
