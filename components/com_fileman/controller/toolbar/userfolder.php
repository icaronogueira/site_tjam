<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanControllerToolbarUserfolder extends ComFilemanControllerToolbarFolder
{
    protected function _commandUpload(KControllerToolbarCommand $command)
    {
        $state = $this->getController()->getModel()->getState();

        $command->icon = 'k-icon-data-transfer-upload';
        $command->href = 'view=filelink&container=fileman-user-files&layout=upload&&folder=' . rawurlencode($state->folder);

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
