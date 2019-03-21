<?
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('_JEXEC') or die; ?>

<?= helper('ui.load', array(
    'package' => 'fileman',
    'wrapper' => false)) ?>

<?= import('com://site/fileman.folder.photoswipe.html') ?>

<ktml:script src="media://com_fileman/js/site/gallery.js" />

<script>
    kQuery(function($) {
        var documentsGallery = $('.koowa_media_wrapper--documents'),
            itemWidth = parseInt($('.koowa_media_wrapper--documents .koowa_media__item').css('width'));

        // Documents gallery
        if ( documentsGallery ) {
            documentsGallery.simpleGallery({
                item: {
                    'width': itemWidth
                }
            });
        }

        <? if ($params->track_views): ?>
        $('.fileman-view').click(function() {
            Fileman.trackEvent({action: 'Download', label: $(this).attr('data-path')});
        });

        $(document).on('photoswipeImageView', function(event, item) {
            if (item.path) {
                Fileman.trackEvent({action: 'Download', label: item.path});
            }
        });
        <? endif; ?>
    });
</script>

<div class="k-ui-namespace">
    <div class="mod_fileman mod_fileman--files">
        <div class="koowa_media--gallery">
            <div class="koowa_media_wrapper koowa_media_wrapper--documents">
                <div class="koowa_media_contents">
                    <?php // these comments below must stay ?>
                    <div class="koowa_media"><!--
                        <? foreach ($files as $file): ?>
                        --><div class="koowa_media__item" itemscope itemtype="http://schema.org/ImageObject">
                                <div class="koowa_media__item__content file">
                                    <? if ($params->show_thumbnails && !empty($file->thumbnail)): ?>
                                        <a class="koowa_media__item__link js-lightbox-item"
                                           data-path="<?= escape($file->path); ?>"
                                           data-width="<?= $file->width; ?>"
                                           data-height="<?= $file->height; ?>"
                                           href="<?= route('option=com_fileman&view=file&routed=1&folder='.rawurlencode(parameters()->folder).'&name='.rawurlencode($file->name)) ?>"
                                           title="<?= escape($file->display_name) ?>">
                                    <? else: ?>
                                        <a class="koowa_media__item__link fileman-view"
                                           data-title="<?= escape($file->display_name); ?>"
                                           data-id="<?= 0; ?>"
                                           href="<?= route('option=com_fileman&view=file&routed=1&folder='.rawurlencode(parameters()->folder).'&name='.rawurlencode($file->name)) ?>"
                                           title="<?= escape($file->display_name); ?>">
                                    <? endif ?>
                                    <div class="koowa_media__item__content-holder">
                                        <? if( $file->thumbnail ): ?>
                                            <div class="koowa_media__item__thumbnail">
                                                <img src="<?= 'root://' . $file->thumbnail->relative_path ?>" alt="<?= escape($file->display_name) ?>">
                                            </div>
                                        <? else: ?>
                                            <div class="koowa_media__item__icon">
                                                <span class="k-icon-document-image k-icon--size-xlarge"></span>
                                            </div>
                                        <? endif ?>
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
                                        <? endif ?>
                                    </div></a>
                                </div>
                            </div><!--
                        <? endforeach ?>
                --></div>
                </div>
            </div>
        </div>
    </div>
</div>