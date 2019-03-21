<?
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('KOOWA') or die; ?>

<?= translate('Submit notification mail body', array(
    'file_name'   => $file->name,
    'sitename'    => $sitename,
    'folder_link' => $folder_url,
    'folder_name' => empty($file->folder) ? translate('root') : $file->folder,
    'file_link'   => $file_url
)); ?>