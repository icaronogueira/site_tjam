<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
if (class_exists('Koowa'))
{
    echo KObjectManager::getInstance()->getObject('mod://admin/logman.html')
        ->module($module)
        ->attribs($attribs)
        ->render();
}