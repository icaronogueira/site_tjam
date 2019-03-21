<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class plgButtonLogmanlinker extends JPlugin
{
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    public function onDisplay($name)
    {
        $button = new JObject();
        $button->class = 'btn';

        $button->set('modal', true);
        $button->set('link', 'index.php?option=com_logman&amp;view=linker&amp;e_name='.$name.'&amp;tmpl=koowa');
        $button->set('text', JText::_('PLG_LOGMANLINKER_BUTTON_LINKER'));
        $button->set('name', 'link');
        $button->set('options', "{handler: 'iframe', size: {x: 600, y: 300}}");

        return $button;
    }
}
