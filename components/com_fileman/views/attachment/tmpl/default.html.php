<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 - 2017 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
?>

<a class="koowa_media__item__link" href="<?= route('option=com_fileman&view=file&routed=1&name=' .
    urlencode($attachment->file->name . '&exp_token=' . helper('route.token', array('expire' => '+1 hour'))) .
    '&container=fileman-attachments', true, false) ?>">
    <div class="koowa_media__item__content-holder">
        <? if ($attachment->file->isImage()): ?>
            <div class="koowa_media__item__image">
                <? if (($thumbnail = $attachment->file->getThumbnail()) && !$thumbnail->isNew()): ?>
                    <div class="koowa_media__item__thumbnail">
                        <img src="<?= $thumbnail->thumbnail ?>"/>
                    </div>
                <? else: ?>
                    <div class="koowa_media__item__icon">
                        <span class="k-icon-document-image k-icon--size-xlarge"></span>
                    </div>
                <? endif ?>
            </div>
        <? else: ?>
            <div class="koowa_media__item__icon">
                <span class="k-icon-document-default k-icon--size-xlarge"></span>
            </div>
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
