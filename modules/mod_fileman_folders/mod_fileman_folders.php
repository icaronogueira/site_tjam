<?php
/**
 * @package    FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('_JEXEC') or die;

if (class_exists('Koowa'))
{
    try {
        echo KObjectManager::getInstance()->getObject('mod://site/fileman_folders.html')
        ->module($module)
        ->attribs($attribs)
        ->render();
    } catch(Exception $exception) {
        KObjectManager::getInstance()->getObject('exception.handler')->handleException($exception);
    }
}
