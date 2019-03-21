<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 - 2017 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanControllerBehaviorDeniable extends KControllerBehaviorAbstract
{
    protected function _afterAdd(KControllerContextInterface $context)
    {
        $folder = $context->result;

        if (!file_exists($folder->fullpath.'/.htaccess'))
        {
            $buffer ='DENY FROM ALL';
            file_put_contents($folder->fullpath.'/.htaccess', $buffer);
        }
    }
}