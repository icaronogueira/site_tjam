<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

require_once __DIR__.'/helper.php';

class com_filemanInstallerScript extends JoomlatoolsInstallerHelper
{
    public function getSystemErrors($type, $installer)
    {
        $errors = array();

        if (!$errors && $type !== 'update')
        {
            jimport('joomla.filesytem.file');
            jimport('joomla.filesytem.folder');

            $path = JPATH_ROOT.'/joomlatools-files';
            if (JFolder::exists($path))
            {
                // Try to write a file
                $test  = $path.'/removethisfile';
                $blank = '';
                if (!JFile::write($test, $blank)) {
                    $errors[] = JText::_('Document path is not writable. Please make sure that joomlatools-files folder in your site root is writable.');
                }
                elseif (JFile::exists($test)) {
                    JFile::delete($test);
                }

            }
            elseif (!JFolder::create($path))
            {
                $errors[] = JText::_('Document path cannot be automatically created. Please create a folder named joomlatools-files in your site root and make sure it is writable.');
            }
        }

        return $errors;
    }

    /**
     * Overridden to use a different privilege check
     *
     * @param array $privileges An array containing the privileges to be checked.
     *
     * @return array True An array containing the privileges that didn't pass the test, i.e. not granted.
     */
    protected function _checkDatabasePrivileges($privileges)
    {
        $db     = JFactory::getDbo();
        $failed = array();
        $table  = sprintf('#__fileman_dummy_table_%d', rand(1, 1000));

        // Check ALTER privilege
        try {
            $db->setQuery("CREATE TABLE $table (dummy TINYINT(1))")->execute();
            $db->setQuery("ALTER TABLE $table CHANGE dummy dummy TINYINT(2)")->execute();
        } catch (JDatabaseExceptionExecuting $e) {
            $failed[] = 'ALTER';
        }

        // Cleanup
        try {
            $db->setQuery("DROP TABLE IF EXISTS $table")->execute();
        } catch (Exception $e) {}

        return $failed;
    }

    public function afterInstall($type, $installer)
    {
        $this->_removeOldScheduler();

        $this->_createContainers($type);

        // Managers should be able to access the component by default just like com_media
        if ($type === 'install')
        {
            $rule = '{"core.admin":[],"core.manage":{"6":1},"core.create":[],"core.delete":[],"core.edit":[]}';
            JFactory::getDbo()
                ->setQuery(sprintf("UPDATE #__assets SET rules = '%s' WHERE name = '%s'", $rule, 'com_fileman'))
                ->query();
        }

        $products = array(
            'DOCman' => $this->_getComponentVersion('docman'),
            'LOGman' => $this->_getComponentVersion('logman')
        );
        $incompatible = array();

        foreach ($products as $product => $version) {
            if ($version && version_compare($version, '3.0.0-rc.1', '<')) {
                $incompatible[] = $product.' '.$version;
            }
        }

        if (count($incompatible)) {
            $warning = 'This is important! You need to upgrade %s to 3.0 too or your site will break. Please go to <a target="_blank" href="https://joomlatools.com">https://joomlatools.com</a> and download the latest versions.';
            JFactory::getApplication()->enqueueMessage(sprintf($warning, implode(' and ', $incompatible)), 'warning');
        }

        // Cleanup container
        $manager = KObjectManager::getInstance();

        $container = $manager->getObject('com:files.model.containers')->slug('fileman-files')->fetch();

        if (!$container->isNew())
        {
            $parameters = $container->getParameters();

            if ($parameters->allowed_mimetypes)
            {
                unset($parameters->allowed_mimetypes);
                $container->save();
            }
        }
    }

    protected function _removeOldScheduler()
    {
        $extension_id = $this->getExtensionId(array(
            'type'    => 'plugin',
            'element' => 'scheduler',
            'folder'  => 'system'
        ));

        if ($extension_id)
        {
            $this->_setCoreExtension($extension_id, 0);

            $i = new JInstaller();
            $i->uninstall('plugin', $extension_id);
        }
    }

    public function uninstall($installer)
    {
        parent::uninstall($installer);

        $db = JFactory::getDbo();
        $db->setQuery('SHOW TABLES LIKE '.$db->quote($db->replacePrefix('#__files_containers')));
        if ($db->loadResult()) {
            $db->setQuery("DELETE FROM `#__files_containers` WHERE `slug` = 'fileman-files'");
            $db->query();
            $db->setQuery("DELETE FROM `#__files_containers` WHERE `slug` = 'fileman-attachments'");
            $db->query();
        }
    }

    protected function _clearCache()
    {
        parent::_clearCache();

        JCache::getInstance('output', array('defaultgroup' => 'com_fileman.files'))->clean();
    }

    protected function _migrate()
    {
        if ($this->old_version && version_compare($this->old_version, '1.0.0RC4', '<='))
        {
            // Path encoding got removed in 1.0.0RC5
            $entity = KObjectManager::getInstance()->getObject('com:files.model.containers')->slug('fileman-files')->fetch();
            $path = $entity->fullpath;
            $rename = array();
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($iterator as $f)
            {
                $name = $f->getFilename();
                if ($name === rawurldecode($name)) {
                    continue;
                }

                $rename[$f->getPathname()] = $f->getPath().'/'.rawurldecode($name);
            }

            foreach ($rename as $from => $to) {
                rename($from, $to);
            }
        }

        if ($this->old_version && version_compare($this->old_version, '1.0.0RC4', '<='))
        {
            // format=raw was removed from URLs in RC4

            $items = $this->_getMenuItems(array('link' => array('view' => 'file')));

            foreach ($items as $item)
            {
                $item->link = str_replace('&format=raw', '', $item->link);
                $item->save();
            }
        }

        if ($this->old_version && version_compare($this->old_version, '1.0.0RC5', '<='))
        {
            // cache structure is changed. clean old cache folders
            jimport('joomla.filesystem.folder');

            $folders = JFolder::folders(JPATH_ROOT.'/cache', '^com_fileman');
            foreach ($folders as $folder) {
                JFolder::delete(JPATH_ROOT.'/cache/'.$folder);
            }
        }

        // Config was moved into the component itself
        if ($this->old_version && version_compare($this->old_version, '3', '<'))
        {
            $translator = KObjectManager::getInstance()->getObject('translator');
            JFactory::getApplication()->enqueueMessage($translator->translate(
                'FILEman no longer depends on the Joomla media manager settings. You can now use the Options button in FILEman to change settings'
            ));
        }

        if ($this->old_version == '3.0.0-beta1')
        {
            // Remove FILEman content plugin
            $manager = KObjectManager::getInstance();

            $query = $manager->getObject('lib:database.query.delete')
                             ->table('extensions')
                             ->where('name = :name')
                             ->bind(array('name' => 'plg_content_fileman'));

            $manager->getObject('lib:database.adapter.mysqli')->delete($query);
        }

        if (version_compare($this->old_version, '3.1.0-RC1', '<'))
        {
            $manager = KObjectManager::getInstance();

            $adapter = $manager->getObject('lib:database.adapter.mysqli');
            $query = $manager->getObject('lib:database.query.select');

            $query->table('extensions')
                  ->columns('extension_id')
                  ->where('element = :element')
                  ->bind(array('element' => 'com_fileman'));

            $id = $adapter->select($query, KDatabase::FETCH_FIELD);

            if ($id)
            {
                $query = $manager->getObject('lib:database.query.select');

                $query->table('menu')
                      ->columns(array('id', 'link'))
                      ->where('type = :type')
                      ->where('component_id = :component_id')
                      ->where('published = :published')
                      ->bind(array('type' => 'component', 'component_id' => $id, 'published' => 1));

                if ($items = $adapter->select($query, Kdatabase::FETCH_ARRAY_LIST))
                {
                    $folders = array();

                    foreach ($items as $item)
                    {
                        $link = parse_url($item['link']);

                        if (isset($link['query']))
                        {
                            parse_str($link['query'], $query);

                            if (isset($query['view']) && $query['view'] == 'userfolder' && !empty($query['folder']))
                            {
                                $folder = $query['folder'];

                                $folders[] = $folder;

                                $query = $manager->getObject('lib:database.query.update');

                                $query->table('menu')
                                      ->values('link = :link')
                                      ->where('id = :id')
                                      ->bind(array(
                                          'id'   => $item['id'],
                                          'link' => str_replace('&folder=' . $folder, '', $item['link'])
                                      ));

                                $adapter->execute($query);
                            }
                        }
                    }

                    if (count($folders) === 1)
                    {
                        $folder = current($folders);

                        $base_path = JPATH_ROOT . '/joomlatools-files/';

                        $source = $base_path . 'fileman-files/' . $folder;
                        $target = $base_path . 'fileman-user-files';

                        jimport('joomla.filesystem.folder');

                        JFolder::delete($target);

                        // Move files and folders to the new container
                        if (!JFolder::move($source, $target)) {
                            JFactory::getApplication()->enqueueMessage(JText::_('Unable to move user files and folders to their new location (joomlatools-files/fileman-user-files). You will need to manually move these files and folders yourself'), 'error');
                        }
                    }
                    elseif (count($folders) > 1) JFactory::getApplication()->enqueueMessage(JText::_('The installer have not moved the users folders and files to their new location since there is more than one FILEman user files menu item available in your site. You will need to manually move the files and folders from one of them to their new location (joomlatools-files/fileman-user-files)'), 'error');
                }
            }
        }

        // v3.1.4
        if (!$this->_columnExists('fileman_scans', 'parameters'))
        {
            // Add the parameters columns and delete thumbnail scans not yet sent
            $queries = [
                "TRUNCATE TABLE `#__fileman_scans`;",
                "ALTER TABLE `#__fileman_scans` ADD COLUMN `parameters` TEXT AFTER `response`;"
            ];

            foreach ($queries as $query) {
                $this->_executeQuery($query);
            }
        }

        if ($this->_columnExists('fileman_scans', 'path'))
        {
            // Split path into folder and name
            $queries = [
                "TRUNCATE TABLE `#__fileman_scans`;",
                "ALTER TABLE `#__fileman_scans` CHANGE `path` `folder` VARCHAR(512) NOT NULL DEFAULT '';",
                "ALTER TABLE `#__fileman_scans` ADD `name` VARCHAR(255) NOT NULL DEFAULT '' AFTER `folder`;"
            ];

            foreach ($queries as $query) {
                $this->_executeQuery($query);
            }
        }
    }

    protected function _getMenuItems($conditions = array())
    {
        $result = array();

        $id = JComponentHelper::getComponent('com_fileman')->id;

        if ($id)
        {
            $conditions = new KObjectConfig($conditions);

            $conditions->append(array('link' => array(), 'menu' => array('component_id' => $id)));

            $table = KObjectManager::getInstance()->getObject('com://admin/fileman.database.table.menus', array('name' => 'menu'));

            $items = $table->select($conditions->menu->toArray());

            foreach ($items as $item)
            {
                parse_str(str_replace('index.php?', '', $item->link), $query);

                foreach ($conditions->link as $param => $value) {
                    if (isset($query[$param]) && $query[$param] != $value) $result[] = $item;
                }
            }
        }

        return $result;
    }

    protected function _createContainers($type)
    {
        if (!extension_loaded('gd'))
        {
            $translator = KObjectManager::getInstance()->getObject('translator');
            JFactory::getApplication()->enqueueMessage($translator->translate('Your server does not have the necessary GD image library for thumbnails.'));
        }

        $extensions = explode(',', 'csv,doc,docx,html,key,keynote,odp,ods,odt,pages,pdf,pps,ppt,pptx,rtf,tex,txt,xls,xlsx,xml,bmp,exif,gif,ico,jpeg,jpg,png,psd,tif,tiff,3gp,asf,avi,flv,m4v,mkv,mov,mp4,mpeg,mpg,ogg,rm,swf,vob,wmv,aac,aif,aiff,alac,amr,au,cdda,flac,m3u,m4a,m4p,mid,mp3,mpa,pac,ra,wav,wma');

        $files = KObjectManager::getInstance()->getObject('com:files.model.containers')->slug('fileman-files')->fetch();

        if ($files->isNew())
        {
            $files->create(array(
                'slug'       => 'fileman-files',
                'path'       => 'joomlatools-files/fileman-files',
                'title'      => 'FILEman',
                'parameters' => array(
                    'thumbnails_container' => 'fileman-thumbnails',
                    'allowed_extensions'   => $extensions,
                    'maximum_size'         => 0,
                    'thumbnails'           => true
                )
            ));
        }
        else $files->getParameters()->append(array('thumbnails_container' => 'fileman-thumbnails'));

        $files->save();

        $thumbnails = KObjectManager::getInstance()->getObject('com:files.model.containers')->slug('fileman-thumbnails')->fetch();

        if ($thumbnails->isNew())
        {
            $thumbnails->create(array(
                'slug'       => 'fileman-thumbnails',
                'path'       => 'joomlatools-files/fileman-thumbnails',
                'title'      => 'FILEman Thumbnails',
                'parameters' => array(
                    'versions' => array(
                        'small'  => array(
                            'dimension' => array('width' => 320, 'height' => 320),
                            'crop'      => false
                        ),
                        'medium' => array(
                            'dimension' => array('width' => 768, 'height' => 768),
                            'crop'      => false
                        ),
                        'large'  => array(
                            'dimension' => array('width' => 1024, 'height' => 1024),
                            'crop'      => false
                        )
                    )
                )
            ));

            $thumbnails->save();
        }

        $attachments = KObjectManager::getInstance()->getObject('com:files.model.containers')->slug('fileman-attachments')->fetch();

        if ($attachments->isNew())
        {
            $attachments->create(array(
                'slug'       => 'fileman-attachments',
                'path'       => 'joomlatools-files/fileman-attachments',
                'title'      => 'FILEman Attachments',
                'parameters' => array(
                    'thumbnails_container' => 'fileman-attachments-thumbnails',
                    'allowed_extensions'   => $extensions,
                    'maximum_size'         => 0,
                    'thumbnails'           => true,
                    'check_duplicates' => 'confirm'
                )
            ));
        }
        else $attachments->getParameters()->append(array('thumbnails_container' => 'fileman-attachments-thumbnails', 'check_duplicates' => 'confirm'));

        $attachments->save();

        $attachments_thumbnails = KObjectManager::getInstance()->getObject('com:files.model.containers')->slug('fileman-attachments-thumbnails')->fetch();

        if ($attachments_thumbnails->isNew())
        {
            $attachments_thumbnails->create(array(
                'slug'       => 'fileman-attachments-thumbnails',
                'path'       => 'joomlatools-files/fileman-attachments-thumbnails',
                'title'      => 'FILEman Attachments Thumbnails',
                'parameters' => array('dimension' => array('width' => 320, 'height' => 320), 'crop' => false)
            ));

            $attachments_thumbnails->save();
        }
        else
        {
            // Fix parameters. Prior to 3.1.2 they were wrapped inside an additional array.
            $attachments_thumbnails->parameters = json_encode(array('dimension' => array('width' => 320, 'height' => 320), 'crop' => false));

            $attachments_thumbnails->save();
        }

        $user_files = KObjectManager::getInstance()->getObject('com:files.model.containers')->slug('fileman-user-files')->fetch();

        if ($user_files->isNew())
        {
            $user_files->create(array(
                'slug'       => 'fileman-user-files',
                'path'       => 'joomlatools-files/fileman-user-files',
                'title'      => 'FILEman User Files',
                'parameters' => array(
                    'thumbnails_container' => 'fileman-user-thumbnails',
                    'allowed_extensions'   => $extensions,
                    'maximum_size'         => 0,
                    'thumbnails'           => true
                )
            ));
        }
        else $user_files->getParameters()->append(array('thumbnails_container' => 'fileman-user-thumbnails'));

        $user_files->save();

        $user_thumbnails = KObjectManager::getInstance()->getObject('com:files.model.containers')->slug('fileman-user-thumbnails')->fetch();

        if ($user_thumbnails->isNew())
        {
            $user_thumbnails->create(array(
                'slug'       => 'fileman-user-thumbnails',
                'path'       => 'joomlatools-files/fileman-user-thumbnails',
                'title'      => 'FILEman User Thumbnails',
                'parameters' => array(
                    'versions' => array(
                        'small'  => array(
                            'dimension' => array('width' => 320, 'height' => 320),
                            'crop'      => false
                        ),
                        'medium' => array(
                            'dimension' => array('width' => 768, 'height' => 768),
                            'crop'      => false
                        ),
                        'large'  => array(
                            'dimension' => array('width' => 1024, 'height' => 1024),
                            'crop'      => false
                        )
                    )
                )
            ));

            $user_thumbnails->save();
        }

        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');

        $protectable_containers = array('fileman-user-files', 'fileman-attachments');

        if ($type == 'install') {
            $protectable_containers[] = 'fileman-files'; // Protect main container by default;
        }

        foreach (array($files, $attachments, $thumbnails, $attachments_thumbnails, $user_files, $user_thumbnails) as $entity)
        {
            if (!$entity->isNew() && $entity->path)
            {
                $path = JPATH_ROOT.'/'.$entity->path;
                if (!JFolder::exists($path))
                {
                    if (!JFolder::create($path)) {
                        JFactory::getApplication()->enqueueMessage(sprintf(JText::_(
                            'Document path cannot be automatically created. Please create the folder structure %s in your site root.', $entity->path)), 'error'
                        );
                    }
                }

                if (in_array($entity->slug, $protectable_containers))
                {
                    $entity->mixin('com://admin/fileman.mixin.folder.protect');
                    $entity->protect();
                }
            }
        }
    }
}