<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanControllerPermissionAttachment extends ComFilemanControllerPermissionAbstract
{
    public function canAttach()
    {
        return $this->canAdd();
    }

    public function canDetach()
    {
        return $this->canDelete();
    }
}