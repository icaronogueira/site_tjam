<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanViewFilesHtml extends ComKoowaViewHtml
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append([
            'decorator'  => $config->layout === 'select' ? 'koowa' : 'joomla'
        ]);

        parent::_initialize($config);
    }

    protected function _fetchData(KViewContext $context)
    {
        $container = $this->getConfig()->container;

        if ($container && $container == 'fileman-user-files')
        {
            $pages = $this->getObject('com://admin/fileman.model.pages')->view('userfolder')->count();

            if (!$pages)
            {
                $this->setLayout('no_userpage');
                $context->layout = $this->getLayout();
            }
        }

        parent::_fetchData($context);
    }
}