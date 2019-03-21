<?php
/**
 * @category    FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanTemplateHelperString extends KTemplateHelperAbstract
{
    public function humanize($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'string' => '',
            'strip_extension' => false
        ));

        $string = $config->string;

        if ($config->strip_extension) {
            $string = ltrim(pathinfo(' '.strtr($string, array('/' => '/ ')), PATHINFO_FILENAME));
        }

        $string = str_replace(array('_', '-', '.'), ' ', $string);
        $string = ucwords($string);

        return $string;
    }
}
