<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanControllerToolbarFile extends ComKoowaControllerToolbarActionbar
{
    public function getCommands()
    {
        $controller = $this->getController();

        if ($controller->canAdd())
        {
            $this->addNewfolder(array(
                'label' => 'New Folder',
                'allowed' => $controller->canAdd(),
                'icon' => 'k-icon-plus',
                'attribs' => array('class' => array('js-open-folder-modal k-button--success'))
            ));

            if ($controller->canCopy()) {
                $this->addCopy(array('attribs' => array('class' => array('k-is-hideable', 'k-is-disabled'))));
            }

            if ($controller->canMove()) {
                $this->addMove(array('attribs' => array('class' => array('k-is-hideable', 'k-is-disabled'))));
            }
        }

        if ($controller->canDelete()) {
            $this->addDelete(array('attribs' => array('class' => array('k-is-hideable', 'k-is-disabled'))));
        }

        $this->addSeparator();

        $this->addRefresh();

        return parent::getCommands();
    }

    protected function _commandRefresh(KControllerToolbarCommand $command)
    {
        $command->icon = 'k-icon-loop-circular';
    }

    protected function _commandMove(KControllerToolbarCommand $command)
    {
        $command->attribs['href'] = '#';
        $command->icon = 'k-icon-move';
    }

    protected function _commandCopy(KControllerToolbarCommand $command)
    {
        $command->icon = 'k-icon-layers';
    }
}