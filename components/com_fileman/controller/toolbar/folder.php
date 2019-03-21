<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanControllerToolbarFolder extends ComKoowaControllerToolbarActionbar
{
    /**
    * hides toolbar for the browse view
    *
    * @param KControllerContextInterface context object
    * @return void
    */
    protected function _afterBrowse(KControllerContextInterface $context)
    {
        return;
    }

    /**
    * create toolbar for the read view
    *
    * @param KControllerContextInterface context object
    * @return void
    */
    protected function _afterRead(KControllerContextInterface $context)
    {
        if ($this->getController()->canAdd())
        {
            $this->addCommand('folder');
            $this->addCommand('upload');
        }

        if ($this->getController()->canDelete())
        {
            $data = array(
                'csrf_token' => $this->getObject('user')->getSession()->getToken(),
                '_method' => 'delete'
            );

            $this->addCommand('delete', array(
                'attribs' => array(
                    'class' => array('btn-danger'),
                    'data-params' => htmlentities(json_encode($data))
                )
            ));
        }
    }

    protected function _commandDelete(KControllerToolbarCommand $command)
    {
        $translator = $this->getObject('translator');

        $command->append(array(
            'attribs' => array(
                'data-name'   => 'delete',
                'data-prompt' => $translator->translate('Deleted items will be lost forever. Would you like to continue?')
            )
        ));

        $command->icon = 'k-icon-trash';
    }

    protected function _commandFolder(KControllerToolbarCommand $command)
    {
        $command->label = $this->getObject('translator')->translate('New Folder');
        $command->icon = 'k-icon-plus';

        $command->append(array(
            'attribs' => array(
                'data-k-modal' => array('items' => array('type' => 'inline', 'src' => '#files-new-folder-modal'))
            )
        ));

        parent::_commandDialog($command);
    }

    protected function _commandUpload(KControllerToolbarCommand $command)
    {
        $state = $this->getController()->getModel()->getState();

        $command->icon = 'k-icon-data-transfer-upload';
        $command->href = 'view=filelink&container=fileman-files&layout=upload&folder=' . rawurlencode($state->folder);

        $command->append(array(
            'attribs' => array(
                'data-k-modal' => array(
                    'items'     => array(
                        'src'  => (string) $this->getController()->getView()->getRoute($command->href),
                        'type' => 'iframe'
                    ),
                    'modal'     => true,
                    'mainClass' => 'koowa_dialog_modal'
                )
            )
        ));

        parent::_commandDialog($command);
    }
}
