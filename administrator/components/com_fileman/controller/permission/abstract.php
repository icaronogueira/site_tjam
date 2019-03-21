<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

abstract class ComFilemanControllerPermissionAbstract extends ComKoowaControllerPermissionAbstract
{
    public function canMove()
    {
        return $this->canDelete() && $this->canAdd();
    }

    public function canCopy()
    {
        return $this->canAdd();
    }

    /**
     * {@inheritdoc}
     */
    public function canAdd()
    {
        return $this->getObject('user')->authorise('core.create', 'com_fileman') === true;
    }

    /**
     * {@inheritdoc}
     */
    public function canEdit()
    {
        return $this->getObject('user')->authorise('core.edit', 'com_fileman') === true;
    }

    /**
     * {@inheritdoc}
     */
    public function canDelete()
    {
        return $this->getObject('user')->authorise('core.delete', 'com_fileman') === true;
    }
}