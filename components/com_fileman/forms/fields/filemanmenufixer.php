<?php
/**
 * @package    fileman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * This makes sure the form validates for existing menu items. Otherwise Joomla leaves the type field empty.
 *
 * Also adds some basic styling to parameters
 */
class JFormFieldFilemanmenufixer extends JFormField
{
    protected $type = 'Filemanmenufixer';

    protected function getInput()
    {
        $name = (string) $this->element['view'];

        $html = '
        <style type="text/css">#attrib-basic .control-group .control-label { width: 250px !important; }</style>
        <span class="js-fileman-menu-fixer-anchor" style="display: none"></span>
        <script type="text/javascript">
            jQuery(function($) {' .
                (!empty($name) ? 'jSelectPosition_jform_type('.json_encode(JText::_($name)).');' : '')
                . '

                var group = $(".js-fileman-menu-fixer-anchor").parents("div.control-group");

                if (group.length === 1) {
                    group.hide();
                }
            });
        </script>
        ';

        return $html;
    }
}
