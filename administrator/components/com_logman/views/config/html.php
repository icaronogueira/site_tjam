<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComLogmanViewConfigHtml extends ComKoowaViewHtml
{
    protected function _fetchData(KViewContext $context)
    {
        $plugins = $this->getObject('com://admin/logman.model.plugins')->logger(true)->fetch();

        foreach ($plugins as $plugin)
        {
            $identifier = sprintf('plg_logman_%s', $plugin->getName());

            ComLogmanActivityTranslator::loadSysIni($identifier);

            $plugin->identifier = $identifier;
        }

        $context->data->plugins = $plugins;
        $context->data->token = $this->getObject('user')->getSession()->getToken();

        $context->data->sef_on = JFactory::getConfig()->get('sef');

        if ($context->data->sef_on)

        parent::_fetchData($context);
    }

}
