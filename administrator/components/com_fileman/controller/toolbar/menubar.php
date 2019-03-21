<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanControllerToolbarMenubar extends ComKoowaControllerToolbarMenubar
{
    public function getCommands()
    {
        // Parent call adds commands from the component manifest
        parent::getCommands();

        $container = $this->getController()->getRequest()->getQuery()->container;

        $this->addCommand('All Files', array(
            'href'   => 'option=com_fileman&view=files&container=fileman-files&folder=&layout=default',
            'active' => ($container == 'fileman-files') ? true : false
        ));

        $this->addCommand('User Files', array(
            'href'   => 'option=com_fileman&view=files&container=fileman-user-files&folder=&layout=default',
            'active' => ($container == 'fileman-user-files') ? true : false
        ));

        if ($this->getController()->canAdmin()) {
            $this->addCommand('Settings', array(
                'href'   => 'option=com_fileman&view=config',
                'active' => false
            ));
        }

        return $this->_commands;
    }
}