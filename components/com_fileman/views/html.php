<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanViewHtml extends ComKoowaViewHtml
{
    protected function _fetchData(KViewContext $context)
    {
        $context->data->token = $this->getObject('user')->getSession()->getToken();

        parent::_fetchData($context);
    }
}