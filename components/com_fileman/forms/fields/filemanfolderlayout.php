<?php
/**
 * @package    fileman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Takes care of showing/hiding parameters for each layout.
 */
class JFormFieldFilemanfolderlayout extends JFormField
{
    protected $type = 'Filemanfolderlayout';

    protected function getInput()
    {
        $html = "
        <script type=\"text/javascript\">
        jQuery(function($)
        {
            var parent = $('#jform_params_filemanfolderlayout-lbl').parents('.control-group');

            if (parent.length) parent.hide();

            var selector = $('#jform_request_layout');

            if (selector.length) {
                var list = {
                    'table': [
                        '#jform_params_track_downloads',
                        '#jform_params_show_icon',
                        '#jform_params_preview_with_gdocs',
                        '#jform_params_force_download',
                        '#jform_params_download_in_blank_page',
                        '#jform_params_show_filesize',
                        '#jform_params_show_modified_date'
                    ],
                    'gallery': [
                        '#jform_params_track_views',
                        '#jform_params_show_filenames',
                        '#jform_params_show_thumbnails',
                        '#jform_params_show_images_only'
                    ]
                };

                var onChange = function()
                {
                    var selected = selector.val();

                    $.each(list, function(layout, fields)
                    {
                        $.each(fields, function(idx, field)
                        {
                            var container = $(field).parents('.control-group');

                            if (container.length)
                            {
                                if (selected == layout) {
                                    container.show();
                                } else {
                                    container.hide();
                                }
                            }
                        });

                    });
                };

                onChange();

                selector.change(onChange);
            }
        });
        </script>
        ";

        return $html;
    }
}
