<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

return array(
    'aliases' => array(
        'com://site/fileman.template.helper.icon'         => 'com://admin/fileman.template.helper.icon',
        'com://site/fileman.template.helper.uploader'         => 'com://admin/fileman.template.helper.uploader',

        'com://site/fileman.template.helper.paginator'    => 'com://admin/fileman.template.helper.paginator',
        'com://site/fileman.controller.attachment'        => 'com://admin/fileman.controller.attachment',
        'com://site/fileman.controller.filelink'          => 'com://admin/fileman.controller.filelink',
        'com://site/fileman.template.helper.listbox'      => 'com://admin/fileman.template.helper.listbox',
        'com://site/fileman.controller.behavior.syncable' => 'com://admin/fileman.controller.behavior.syncable',
        'com://site/fileman.template.helper.route'        => 'com://admin/fileman.template.helper.route'
    ),
    'identifiers' => array(
        'com:scheduler.controller.dispatcher' => array(
            'jobs' => array(
                'com://admin/fileman.job.scans',
                'com://admin/fileman.job.attachments'
            )
        )
    )
);
