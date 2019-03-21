<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('_JEXEC') or die; ?>

<div>
    <div id="container">
        <?= helper('com://admin/fileman.listbox.folders', array(
            'select2' => true,
            'attribs' => array('id' => 'fileman_folder'),
            'name'    => isset($field) ? $field : 'fileman_folder'
        )); ?>
    </div>
</div>
