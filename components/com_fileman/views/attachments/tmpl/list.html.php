<?
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
?>

<?= helper('ui.load') ?>

<? if (count($attachments)): ?>
    <div class="koowa-attachments">
        <h3><?= translate('Attachments') ?></h3>
        <ul class="attachments-list">
            <? foreach ($attachments as $attachment): ?>
                <? if ($attachment->file): ?>
                    <li>
                        <?= import("com://site/fileman.attachment.list.html", array('attachment' => $attachment)) ?>
                    </li>
                <? endif ?>
            <? endforeach ?>
        </ul>
    </div>
<? endif ?>


