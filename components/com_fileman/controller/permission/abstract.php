<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

abstract class ComFilemanControllerPermissionAbstract extends ComKoowaControllerPermissionAbstract
{
    /*
     * A list of containers for which actions are restricted
     */
    protected $_closed_containers = array(
        'fileman-thumbnails',
        'fileman-attachments-thumbnails',
        'fileman-user-thumbnails'
    );

    public function canAdd()
    {
        $result = ($this->getObject('user')->authorise('core.create', 'com_fileman') === true);
        $menu   = JFactory::getApplication()->getMenu()->getActive();

        if ($menu && $menu->query['view'] === 'submit') {
            $result = true; // menu access permissions take effect here
        }

        $controller = $this->getMixer();

        // Disallow add actions on closed containers
        if ($result && $controller->isDispatched() && $container = $controller->getRequest()->getData()->container) {
            $result = !in_array($container, $this->_closed_containers);
        }

        return $result;
    }

    public function canDelete()
    {
        $result = parent::canDelete();

        $controller = $this->getMixer();

        // Disallow delete actions on closed containers
        if ($result && $controller->isDispatched() && $container = $controller->getRequest()->getQuery()->container) {
            $result = !in_array($container, $this->_closed_containers);
        }

        return $result;
    }

    public function canRender()
    {
        $result = true;

        $menu       = JFactory::getApplication()->getMenu()->getActive();
        $view       = $this->getRequest()->query->view;
        $controller = $this->getMixer();


        if (!$menu || $menu->id != $this->getRequest()->query->Itemid) {
            // Render filelink and file views, and com_files views regardless of the menu item
            $result = in_array($view, array('filelink', 'file')) || $this->getMixer()->getIdentifier()->package === 'files';
        } else {
            $folder = isset($menu->query['folder']) ? $menu->query['folder'] : null;

            if (!empty($folder) && strpos($this->getRequest()->query->folder, $folder) !== 0) {
                $result = false;
            }
        }

        // Disallow render action on closed containers
        if ($result && $controller->isDispatched() && $container = $controller->getRequest()->getQuery()->container) {
            $result = !in_array($container, $this->_closed_containers);
        }

        return $result;
    }
}
