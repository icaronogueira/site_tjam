<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
?>

<? $filter = object('com:files.filter.path') ?>

<a class="koowa_media__item__link" href="<?= route('option=com_fileman&view=file&routed=1&name=' .
    rawurlencode($attachment->file->name) . '&container=fileman-attachments', true, false) ?>">
    <div class="koowa_media__item__content-holder">
        <? if ($attachment->file->isImage()): ?>
            <div class="koowa_media__item__image">
                <? if (($thumbnail = $attachment->file->getThumbnail())): ?>
                    <div class="koowa_media__item__thumbnail">
                        <img class="fileman_attachment" src="<?= $filter->encode($thumbnail->relative_path) ?>"/>
                    </div>
                <? else: ?>
                    <div class="koowa_media__item__icon">
                        <img class="fileman_attachment" src="<?= route('option=com_fileman&view=file&routed=1&name=' . rawurlencode($attachment->file->name)
                                            . '&container=fileman-attachments', true, false) ?>"/>
                    </div>
                <? endif ?>
            </div>
        <? else: ?>
            <? if (($thumbnail = $attachment->file->getThumbnail())): ?>
                <div class="koowa_media__item__thumbnail">
                    <img class="fileman_attachment" src="<?= $filter->encode($thumbnail->relative_path) ?>"/>
                </div>
            <? else: ?>
                <div class="koowa_media__item__icon">
                    <span class="k-icon-document-<?= helper('com:files.icon.icon',
                        array('extension' => $attachment->file->extension)) ?> k-icon--size-xlarge">
                    </span>
                </div>
            <? endif ?>
        <? endif ?>
        <div class="koowa_header koowa_media__item__label">
            <div class="koowa_header__item koowa_header__item--title_container">
                <div class="koowa_wrapped_content">
                    <div class="whitespace_preserver">
                        <div class="overflow_container">
                            <span class="js-gallery-caption" style="display: none">
                                <?= escape($attachment->file->name) ?>
                            </span>
                            <?= escape($attachment->file->name) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</a>
