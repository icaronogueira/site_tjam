<?
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('KOOWA') or die; ?>

<?= helper('ui.load', array(
    'package' => 'fileman',
    'wrapper' => false
)); ?>

<div class="k-ui-namespace">
    <div class="mod_fileman mod_fileman--folders <?= JFactory::getLanguage()->isRTL() ? ' k-ui-rtl' : 'k-ui-ltr' ?>">
      <?= import('mod://site/fileman_folders._folders.html', array(
          'folders' => $folders,
          'params' => $params,
          'level' => 1
      )); ?>
    </div>
</div>