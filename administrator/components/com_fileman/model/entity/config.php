<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanModelEntityConfig extends KModelEntityAbstract implements KObjectMultiton
{
    /**
     * Joomla asset cache
     *
     * @var JTableAsset
     */
    protected static $_asset;

    public function __construct($config = array())
    {
        parent::__construct($config);

        if (!empty($config->auto_load)) {
            $this->load();
        }
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'data'      => array(
                'protect_folders'    => true,
                'attachments_layout' => 'list',
                'attachments_icons'  => 1,
                'attachments_info'   => 1,
                'attachments_lists'  => 0,
            ),
            'auto_load' => true
        ));

        parent::_initialize($config);
    }

    public function isNew()
    {
        return false;
    }

    public function isLockable()
    {
        return false;
    }

    public function getFilesContainer()
    {
        return $this->getObject('com:files.model.containers')->slug('fileman-files')->fetch();
    }

    public function getAttachmentsContainer()
    {
        return $this->getObject('com:files.model.containers')->slug('fileman-attachments')->fetch();
    }

    public function getUserContainer()
    {
        return $this->getObject('com:files.model.containers')->slug('fileman-user-files')->fetch();
    }

    public function load()
    {
        $extension = $this->getObject('com:koowa.model.extensions')
                          ->type('component')->element('com_fileman')->fetch();

        $this->setProperties(json_decode($extension->parameters));

        $container  = $this->getFilesContainer();
        $parameters = $container->getParameters();

        $this->file_path = $container->path;

        $keys = array(
            'thumbnails',
            'allowed_extensions',
            'maximum_size',
            'maximum_image_size',
            'allowed_mimetypes'
        );

        foreach ($keys as $key) {
            $this->$key = $parameters->$key;
        }
    }

    protected function _getAsset()
    {
        if (!self::$_asset instanceof JTableAsset)
        {
            self::$_asset = JTable::getInstance('Asset');
            self::$_asset->loadByName('com_fileman');
        }

        return self::$_asset;
    }

    /**
     * Copied from JForm
     *
     * @param array $rules
     * @return array
     */
    protected function _filterAccessRules($rules)
    {
        $return = array();
        foreach ((array) $rules as $action => $ids)
        {
            // Build the rules array.
            $return[$action] = array();
            foreach ($ids as $id => $p)
            {
                if ($p !== '') {
                    $return[$action][$id] = ($p == '1' || $p == 'true') ? true : false;
                }
            }
        }

        return $return;
    }

    public function save()
    {
        // System variables shoulnd't be saved
        foreach (array('csrf_token', 'option', 'action', '_action', '_method', 'format', 'layout', 'task') as $var)
        {
            unset($this->_data[$var]);
            unset($this->_modified[$var]);
        }

        if (!empty($this->rules))
        {
            $rules	= new JAccessRules($this->_filterAccessRules($this->rules));
            $asset	= JTable::getInstance('asset');

            if (!$asset->loadByName('com_fileman')) {
                $root	= JTable::getInstance('asset');
                $root->loadByName('root.1');
                $asset->name = 'com_fileman';
                $asset->title = 'com_fileman';
                $asset->setLocation($root->id, 'last-child');
            }

            $asset->rules = (string) $rules;

            if (!($asset->check() && $asset->store()))
            {
                $translator = $this->getObject('translator');
                $this->getObject('response')->addMessage(
                    $translator->translate('Changes to the ACL rules could not be saved.'), 'warning'
                );
            }

            unset($this->_data['rules']);
        }

        if (!empty($this->_data['allowed_extensions']) && is_string($this->_data['allowed_extensions'])) {
            $this->allowed_extensions = explode(',', $this->_data['allowed_extensions']);
        }

        if (isset($this->notification_emails) && is_string($this->notification_emails))
        {
            if (!empty($this->notification_emails)) {
                $emails = array_map('trim', explode("\n", trim($this->notification_emails)));
            }
            else $emails = array();

            $this->notification_emails = $emails;
        }

        // Auto-set allowed mimetypes based on the extensions
        /*if (!empty($this->allowed_extensions))
        {
            $mimetypes = $this->getObject('com://admin/fileman.model.mimetypes')
                    ->extension($this->allowed_extensions)
                    ->fetch();

            $results = array();
            foreach ($mimetypes as $mimetype) {
                $results[] = $mimetype->mimetype;
            }

            $this->allowed_mimetypes = array_values(array_unique(array_merge($this->allowed_mimetypes, $results)));
        }*/

        // If the document path changed try to move the files to their new location
        $files_container = $this->getFilesContainer();
        $this->_savePath($files_container);

        // These are all going to be saved into com_files
        $data = array();

        $vars = array(
            'thumbnails',
            'allowed_extensions',
            'maximum_size',
            'maximum_image_size',
            'allowed_mimetypes'
        );

        foreach ($vars as $var)
        {
            $value = $this->$var;

            if ($var == 'thumbnails') {
                $value = (bool) $value;
            }

            if (!empty($value) || ($value === '' || $value === false || $value === 0 || $value === '0')) {
                $data[$var] = $value;
            }
            unset($this->_data[$var]);
            unset($this->_modified[$var]);
        }
        unset($data['file_path']);

        $files_container->getParameters()->merge($data);
        $files_container->save();

        $attachments_container = $this->getAttachmentsContainer();
        $attachments_container->getParameters()->merge($data);
        $attachments_container->save();

        $user_container = $this->getUserContainer();
        $user_container->getParameters()->merge($data);
        $user_container->save();

        $files_container->mixin('com://admin/fileman.mixin.folder.protect');

        if ($this->protect_folders) {
            $files_container->protect();
        } else {
            $files_container->unprotect();
        }

        // Get the jos_extensions row entry for FILEman
        $extension = $this->getObject('com:koowa.model.extensions')
                          ->type('component')->element('com_fileman')->fetch();

        $extension->parameters = $this->getProperties();
        $extension->save();

        return true;
    }

    protected function _savePath(KModelEntityInterface $entity)
    {
        if (!$this->isModified('file_path')) {
            return;
        }

        $translator = $this->getObject('translator');

        $path     = rtrim($this->file_path, '\\/');
        $from     = $entity->path;
        $fullpath = $entity->fullpath;

        $entity->path = $path;

        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');

        if (!JFolder::exists($entity->fullpath))
        {
            $folder_exists = false;

            if (!JFolder::create($entity->fullpath))
            {
                $translator->translate('Failed to create the "{folder}" folder. Double check your folder permissions and try again', $entity->fullpath);

                $entity->path = $from;

                return;
            }
        }
        else $folder_exists = true;

        // Canonicalize the path
        $entity->path = realpath($entity->fullpath);

        // Remove Joomla root path if necessary
        if (strpos($entity->fullpath, JPATH_ROOT) === 0) {
            $entity->path = str_replace(JPATH_ROOT . '/', '', $entity->fullpath);
        }

        if ($entity->fullpath ===  $fullpath) {
            return;
        }

        if ($entity->fullpath === JPATH_ROOT . '/joomlatools-files') {
            $this->getObject('response')->addMessage($translator->translate('joomlatools-files is a special folder used for other FILEman features. You can only use a subfolder of it to store your files'), 'error');
            return;
        }

        if (!preg_match('#^[0-9A-Za-z:_\-\\\/\.]+$#', $path)) {
            $this->getObject('response')->addMessage($translator->translate('Document path can only contain letters, numbers, dash or underscore'), 'error');
            return;
        }

        $db = JFactory::getDBO();

        $query = sprintf("SELECT COUNT(*) FROM #__menu WHERE path = %s", $db->quote($path));

        if ($db->setQuery($query)->loadResult())
        {
            $this->getObject('response')->addMessage(
                $translator->translate('A menu item on your site uses this path as its alias. In order to ensure that your site works correctly, the document path was left unchanged.'),
                'error'
            );

            return;
        }

        JCache::getInstance('output', array('defaultgroup' => 'com_fileman.files'))->clean();

        $images = JPATH_ROOT . '/images';

        if ($fullpath !== $images && $entity->fullpath !== $images)
        {
            if (!$folder_exists) JFolder::delete($entity->fullpath); // Safe to delete, it's empty

            if (JFolder::move($fullpath, $entity->fullpath) !== true) {
                $this->getObject('response')->addMessage(
                    $translator->translate('Failed to move existing files from folder "{from}" to "{to}". Please move your files manually',
                        array('from' => $fullpath, 'to' => $entity->fullpath)
                    ), 'warning'
                );
            }
        }

        $entity->save(); // Save the changes
    }

    public function getProperty($column)
    {
        $result = parent::getProperty($column);

        if (in_array($column, array('allowed_extensions', 'allowed_mimetypes')))
        {
            if ($result instanceof KObjectConfigInterface) {
                return $result->toArray();
            }
            elseif (!is_array($result)) {
                return array();
            }
        }

        // Disable thumbnails if these cannot be generated.
        if ($column == 'thumbnails' && $result) {
            $result = $this->thumbnailsAvailable();
        }

        return $result;
    }

    /**
     * Utility function for checking if the server can generate thumbnails.
     *
     * @return bool True if it can, false otherwise.
     */
    public function thumbnailsAvailable()
    {
        return extension_loaded('gd')/* || extension_loaded('imagick')*/;
    }
}
