<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
?>

<span class="koowa_header">
    <? if ($show_icon): ?>
        <span class="koowa_header__item koowa_header__item--image_container">
            <a class="iconImage" href="<?= route('option=com_fileman&view=file&routed=1&name=' . rawurlencode($attachment->file->name)
                                                 . '&container=fileman-attachments', true, false) ?>">
                <span class="k-icon-document-<?= helper('com:files.icon.icon', array(
                    'extension' => $attachment->file->extension
                )) ?> k-icon-document-<?= helper('com:files.icon.icon', array(
                    'extension' => $attachment->file->extension
                )) ?> k-icon--size-medium" aria-hidden="true"></span>
            </a>
        </span>
    <? endif ?>
        <span class="koowa_header__item">
            <span class="koowa_wrapped_content">
                <span class="whitespace_preserver">
                    <a href="<?= route('option=com_fileman&view=file&routed=1&name=' . rawurlencode($attachment->file->name)
                                       . '&container=fileman-attachments', true, false) ?>">
                        <span itemprop="name"><?=escape($attachment->file->name)?></span>
                        <? if ($show_info): ?>
                            (<span><?= strtolower($attachment->file->extension) ?></span>,&nbsp;<!--
                             --><span><?= helper('com:files.filesize.humanize', array('size' => $attachment->file->size));?></span>)
                        <? endif ?>
                    </a>
                </span>
            </span>
        </span>
</span>