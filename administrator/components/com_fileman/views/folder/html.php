<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanViewFolderHtml extends ComKoowaViewHtml
{
    protected function _fetchData(KViewContext $context)
    {
        if ($this->getLayout() == 'select' && ($field = $this->getConfig()->field)) {
            $context->data->field = $field;
        }

        parent::_fetchData($context);
    }
}