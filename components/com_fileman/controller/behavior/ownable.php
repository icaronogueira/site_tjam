<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanControllerBehaviorOwnable extends KControllerBehaviorAbstract
{
    /**
     * The user folder.
     *
     * @var string|null
     */
    protected $_folder;

    /**
     * The file container.
     *
     * @var string
     */
    protected $_container;

    /**
     * Folders model.
     *
     * @var mixed
     */
    protected $_model;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_container = $config->container;
        $this->_model     = $config->model;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array('container' => 'fileman-user-files', 'model' => 'com:files.model.folders'));
        parent::_initialize($config);
    }

    public function isSupported()
    {
        return $this->getMixer()->getUser()->isAuthentic();
    }

    public function onMixin(KObjectMixable $mixer)
    {
        parent::onMixin($mixer);

        if ($this->isSupported())
        {
            $query = $mixer->getRequest()->getQuery();

            if (empty($query->folder)) {
                $query->folder = $this->getFolder();
            }

            // Push the container into the request
            $query->container = $this->_container;
        }
    }

    public function canAccess($folder)
    {
        return strpos((string) $folder, $this->getFolder()) === 0;
    }

    protected function _beforeRender(KControllerContextInterface $context)
    {
        return $this->canAccess($context->getRequest()->getQuery()->folder);
    }

    protected function _beforeAdd(KControllerContextInterface $context)
    {
        $request = $context->getRequest();

        // POST data will override query folder data, so check there first
        if (isset($request->getData()->folder)) {
            $folder = $request->getData()->folder;
        } else {
            $folder = $request->getQuery()->folder;
        }

        return $this->canAccess($folder);
    }

    protected function _beforeDelete(KControllerContextInterface $context)
    {
        return $this->_beforeRender($context);
    }

    /**
     * User folder getter.
     *
     * @throws RuntimeException if a user folder cannot be created.
     *
     * @return string|null The user folder, null if the there is no user folder, i.e. the user is not authenticated.
     */
    public function getFolder()
    {
        if (!$this->_folder)
        {
            $mixer = $this->getMixer();

            if ($mixer) {
                $user = $this->getMixer()->getUser();
            } else {
                $user = $this->getObject('user');
            }

            if ($user->isAuthentic())
            {
                $filter = $this->getObject('com://site/fileman.filter.userfolder');

                $folder = sprintf('%s', $filter->sanitize($user->getUsername()));

                if (!$this->_folderExists($folder) && !$this->_addFolder($folder))
                {
                    $folder = sprintf('id-%s', $user->getId());

                    if (!$this->_folderExists($folder) && !$this->_addFolder($folder)) {
                        throw new RuntimeException('Unable to create user folder '. $folder);
                    }
                }
            }
            else $folder = null;

            $this->_folder = $folder;
        }

        return $this->_folder;
    }

    /**
     * Checks if a given folder in a given container already exists.
     *
     * @param string $folder The folder to check.

     * @return bool True if the folder exists, false otherwise.
     */
    protected function _folderExists($folder)
    {
        $parts = explode('/', $folder);
        $name = array_pop($parts);

        $state = array(
            'container' => $this->_container,
            'folder'    => implode('/', $parts),
            'name'      => $name
        );

        $folder = $this->_getModel()->setState($state)->fetch();

        return (bool) count($folder);
    }

    /**
     * Creates a new folder in a given container.
     *
     * @param string $folder The folder to create.

     * @return bool True if the folder was successfully created, false otherwise.
     */
    protected function _addFolder($folder)
    {
        $parts = explode('/', $folder);
        $name = array_pop($parts);

        $state = array(
            'container' => $this->_container,
            'folder'    => implode('/', $parts),
            'name'      => $name
        );

        $result = $this->_getModel()->create($state)->save();

        return $result === false ? false : true;
    }

    protected function _getModel()
    {
        if (!$this->_model instanceof KModelInterface) {
            $this->_model = $this->getObject($this->_model);
        }

        $this->_model->reset();

        return $this->_model;
    }
}