<?
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('KOOWA') or die; ?>

<? $thumbnail = $file->getThumbnail('small') ?>

<? $filter = object('com:files.filter.path') ?>

<?= helper('ui.load'); ?>

<? if ($file->isImage() && $params->show_thumbnails): ?>
<a class="koowa_media__item__link js-lightbox-item"
   data-path="<?= escape($file->path); ?>"
   data-width="<?= $file->width; ?>"
   data-height="<?= $file->height; ?>"
   href="<?= route('view=file&folder='.rawurlencode(parameters()->folder).'&name='.rawurlencode($file->name)) ?>"
   title="<?= escape($file->display_name) ?>">
<? else: ?>
<a class="koowa_media__item__link fileman-view"
    data-title="<?= escape($file->display_name); ?>"
    data-id="<?= 0; ?>"
    href="<?= route('view=file&folder='.rawurlencode(parameters()->folder).'&name='.rawurlencode($file->name)) ?>"
    title="<?= escape($file->display_name); ?>">
<? endif; ?>

<div class="koowa_media__item__content-holder">
    <? if($file->isImage()): ?>
    <div class="koowa_media__item__thumbnail">
        <img src="<?= !empty($thumbnail) ? 'root://' . $filter->encode($thumbnail->relative_path) : route('view=file&folder=' .
                                                                                 rawurlencode(parameters()->folder) .
                                                                                 '&name=' .
                                                                                 rawurlencode($file->name)) ?>"
             alt="<?= escape($file->display_name) ?>">
    </div>
    <? else: ?>
    <div class="koowa_media__item__icon">
        <span class="k-icon-document-<?= helper('com:files.icon.icon', array('extension' => $file->extension)) ?> k-icon--size-xlarge"></span>
    </div>
    <? endif; ?>
    <? if ($params->show_filenames): ?>
    <div class="koowa_header koowa_media__item__label">
        <div class="koowa_header__item koowa_header__item--title_container">
            <div class="koowa_wrapped_content">
                <div class="whitespace_preserver">
                    <div class="overflow_container">
                        <span class="js-gallery-caption" style="display: none">
                            <?= escape($file->exif_comment ?: $file->display_name) ?>
                        </span>
                        <?= escape($file->display_name) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <? endif; ?>
</div>
</a>

<? if ($can_delete || $can_copy): ?>
<div class="koowa_media__item__options">
    <? if ($can_delete): ?>
    <span class="koowa_media__item__options__select">
        <input name="item-select" type="checkbox" />
    </span>
    <? endif ?>
    <? if ($can_copy): ?>
    <a href="#" data-action="copy-link" class="koowa_media__item__options__copy" data-clipboard-text="<?= $file->permalink ?>">
        <span class="k-icon-clipboard"></span>
    </a>
    <? endif ?>
    <? if ($can_delete): ?>
    <a href="#" data-action="delete-item" class="koowa_media__item__options__delete">
        <span class="icon-trash"></span>
    </a>
    <? endif ?>
</div>
<? endif ?>
