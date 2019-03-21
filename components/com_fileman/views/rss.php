<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanViewRss extends KViewRss
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'auto_fetch' => false
        ));

        parent::_initialize($config);
    }

    public function getLayout()
    {
        return 'com://site/fileman.files.default';
    }

    protected function _fetchData(KViewContext $context)
    {
        $context->data->append(array(
            'sitename'  => JFactory::getApplication()->getCfg('sitename'),
            'language'  => JFactory::getLanguage()->getTag(),
            'description'  => ''
        ));

        parent::_fetchData($context);
    }
}
