<?php

    /**
    * @author    JoomShaper http://www.joomshaper.com
    * @copyright Copyright (C) 2010 - 2013 JoomShaper
    * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2
    */

    defined('JPATH_BASE') or die;

    jimport('joomla.form.formfield');
    jimport('joomla.filesystem.folder');
    jimport('joomla.filesystem.file');
    
    class JFormFieldIcon extends JFormField {

        protected $type = 'icon';

        protected function getInput()
        {

            include(dirname(__FILE__) . '/fontawesome-icons.php');

            asort($sp_feature_icons);

            foreach($sp_feature_icons as $icon)
            {
                $options[] = JHTML::_( 'select.option', $icon, ucfirst(str_replace('fa-', '', $icon)) );
            }
            
            return JHTML::_('select.genericlist', $options, 'jform[params]['.$this->fieldname.']', '', 'value', 'text', $this->value);
        }
    }
