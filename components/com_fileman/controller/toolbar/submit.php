<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanControllerToolbarSubmit extends ComKoowaControllerToolbarActionbar
{
    protected function _afterRead(KControllerContextInterface $context)
    {
        $this->addCommand('save', array('label' => 'Upload', 'icon' => 'icon-upload'));
    }
}