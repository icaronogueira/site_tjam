<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanViewFolderJson extends KViewJson
{
    protected function _getEntity(KModelEntityInterface $entity)
    {
        $data = parent::_getEntity($entity);

        $menu = JFactory::getApplication()->getMenu()->getActive();

        if (!$menu) {
            throw new RuntimeException('Invalid menu item');
        }

        $params = new ComKoowaDecoratorParameter(new KObjectConfig(array('delegate' => $menu->params)));

        $data['display_name']  = $params->humanize_filenames ? ucfirst(preg_replace('#[-_\s\.]+#i', ' ', $entity->name)) : $entity->name;

        return $data;
    }

    protected function _getEntityRoute(KModelEntityInterface $entity)
    {
        return $this->getRoute(sprintf('folder=%s&format=html', rawurlencode($entity->path)));
    }
}