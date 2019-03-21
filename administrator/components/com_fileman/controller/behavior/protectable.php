<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 - 2017 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanControllerBehaviorProtectable extends KControllerBehaviorAbstract
{
    protected function _beforeDelete(KControllerContextInterface $context)
    {
        $folder = $this->getModel()->fetch();

        if (!$folder->isNew())
        {
            $menu_items = JMenu::getInstance('site')->getItems(array('component'), array('com_fileman'));

            foreach ($menu_items as $menu_item)
            {
                if (strpos($menu_item->link, 'view=userfolder') !== false)
                {
                    $url = $this->getObject('lib:http.url', array('url' => $menu_item->link));

                    $query = $url->getQuery(true);

                    if (strpos($query['folder'], $folder->path) === 0)
                    {
                        $translator =  $this->getObject('translator');

                        throw new RuntimeException(
                            $translator->translate('The "{folder}" folder is protected and cannot be deleted',
                                array('folder' => $folder->path)
                            ), KHttpResponse::FORBIDDEN
                        );
                    }
                }
            }
        }
    }
}