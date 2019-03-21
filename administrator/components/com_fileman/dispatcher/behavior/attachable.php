<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanDispatcherBehaviorAttachable extends ComFilesDispatcherBehaviorAttachable
{
    public function isSupported()
    {
        // Overridden to coexists with routable behavior.
        return $this->getMixer()->getRequest()->getQuery()->container == $this->_container;
    }

    protected function _beforeDispatch(KDispatcherContextInterface $context)
    {
        $query = $this->getMixer()->getRequest()->getQuery();

        if ($query->view == 'attachments' && $query->layout == 'editor') {
            $query->layout = 'com://admin/fileman.attachments.editor';
        }

        parent::_beforeDispatch($context);
    }
}