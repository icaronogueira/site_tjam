<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanFilterUserfolder extends ComFilesFilterPath
{
    public function sanitize($value)
    {
        $value = trim(str_replace(array('\\', '/'), '', $value));

        return parent::sanitize($value);
    }
}