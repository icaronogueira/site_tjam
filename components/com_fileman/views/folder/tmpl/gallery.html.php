<?
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('_JEXEC') or die; ?>

<?= helper('ui.load'); ?>
<?= helper('behavior.modal'); ?>
<?= import('com://site/fileman.folder.photoswipe.html'); ?>

<ktml:toolbar type="actionbar">

<ktml:script src="media://com_fileman/js/site/gallery.js" />
<ktml:script src="media://com_fileman/js/site/items.js" />
<ktml:script src="media://com_fileman/js/site/folder.js" />
<ktml:script src="media://koowa/com_files/js/ejs/ejs.js"/>
<ktml:script src="media://com_fileman/js/clipboardjs/clipboard.min.js" />
<ktml:script src="media://com_fileman/js/fileman.js" />

<ktml:style src="media://com_fileman/css/tooltips.css"/>

    <script>
    kQuery(function($) {
        var documentsGallery = $('.koowa_media_wrapper--documents'),
            categories_wrapper = $('.koowa_media_wrapper--categories'),
            itemWidth = parseInt($('.koowa_media_wrapper--documents .koowa_media__item').css('width'));

        // Documents gallery
        if ( documentsGallery ) {
            documentsGallery.simpleGallery({
                item: {
                    'width': itemWidth
                }
            });
        }

        Folder = new Fileman.Folder({
            post_url: "<?= route('format=json&folder=' . rawurlencode($folder->path), true, false) ?>",
            token: <?=json_encode($token)?>,
            container: '#fileman-folders .koowa_media',
        });

        // Categories gallery
        if (categories_wrapper)
        {
            categories_wrapper.simpleGallery({
                item: {
                    'width': itemWidth
                }
            });

            Folder.bind('after.add', function() {
                var plugin = categories_wrapper.data('simpleGallery');
                plugin.refresh();
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

        Fileman.copyboard({
            target: '.koowa_media__item__options__copy',
            tooltips: {
                message: <?= json_encode(translate('Copied!')) ?>
            }
        });

        Fileman.tooltips({
            target: '.koowa_media__item__options__copy',
            message: <?= json_encode(translate('Copy download link to clipboard')) ?>,
            handlers: {
                show: function (el) {
                    var that = this;
                    el.mouseover(function () {
                        that.show(el);
                    });
                }
            }
        });
    });
</script>

<div>

    <? if ($params->show_page_heading): ?>
    	<h1 class="fileman_header">
    		<?= escape($params->page_heading) ?>
    	</h1>

        <? // Folder Header ?>
        <? if ($folder->path): ?>
            <h3 class="koowa_header">
                <? // Header image ?>
                <span class="koowa_header__item koowa_header__item--image_container">
                    <span class="k-icon-document-folder k-icon--size-medium" aria-hidden="true"></span>
                    <span class="k-visually-hidden"><?= translate('folder'); ?></span>
                </span>

                <? // Header title ?>
                <span class="koowa_header__item">
                    <span class="koowa_wrapped_content">
                        <span class="whitespace_preserver">
                            <?= escape($folder->name); ?>
                        </span>
                    </span>
                </span>
            </h3>
        <? endif; ?>
    <? endif ?>

    <? // Documents & pagination  ?>
    <form action="" method="get" class="k-js-grid-controller">
        <div class="koowa_media--gallery">
            <div class="koowa_media_wrapper koowa_media_wrapper--categories">
                <div id="fileman-folders" class="koowa_media_contents">
                    <?php // these comments below must stay ?>
                    <div class="koowa_media"><!--
                        <? foreach ($folders as $item): ?>
                            --><div class="koowa_media__item">
                                <div class="koowa_media__item__content">
                                    <a class="koowa_media__item__link" href="<?= route('folder=' . rawurlencode($item->path)) ?>">
                                        <div class="koowa_header koowa_media__item__label">
                                            <div class="koowa_header__item koowa_header__item--image_container">
                                                <span class="k-icon-document-folder k-icon--size-medium" aria-hidden="true"></span>
                                                <span class="k-visually-hidden"><?= translate('folder'); ?></span>
                                            </div>
                                            <div class="koowa_header__item">
                                                <div class="koowa_wrapped_content">
                                                    <div class="whitespace_preserver">
                                                        <div class="overflow_container">
                                                            <?= $item->display_name ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    <? if($can_delete): ?>
                                    <div class="koowa_media__item__options">
                                        <span class="koowa_media__item__options__select">
                                            <input name="item-select" type="checkbox" />
                                        </span>
                                        <a href="#" data-action="delete-item" class="koowa_media__item__options__delete">
                                            <span class="icon-trash"></span>
                                        </a>
                                    </div>
                                    <? endif; ?>
                                </div>
                            </div><!--
                        <? endforeach ?>
                 --></div>
                </div>
            </div>
            <div class="koowa_media_wrapper koowa_media_wrapper--documents">
                <div class="koowa_media_contents">
                    <?php // these comments below must stay ?>
                    <div class="koowa_media"><!--
                        <? foreach ($files as $file): ?>
                     --><div class="koowa_media__item" itemscope itemtype="http://schema.org/ImageObject">
                          <div class="koowa_media__item__content file">
                              <?= import('com://site/fileman.file.gallery.html', array(
                                  'file' => $file,
                                  'params' => $params
                              )) ?>
                          </div>
                        </div><!--
                        <? endforeach ?>
                  --></div>
                </div>
            </div>
        </div>

        <? // Pagination ?>
        <? if ($params->limit != 0 && max(parameters()->total, $folder_count) > 5): ?>
            <form action="" method="get" class="k-js-form-controller">
                <?= helper('paginator.pagination', array(
                    'total'   => max(parameters()->total, $folder_count),
                    'limit'   => parameters()->limit,
                    'url'     => route('folder=' . rawurlencode($folder->path)),
                    'offset'  => parameters()->offset,
                    'attribs' => array(
                        'onchange' => 'this.form.submit();'
                    )
                )) ?>
            </form>
        <? endif ?>

    </form>
</div>

<div id="files-new-folder-modal" class="k-ui-namespace mfp-hide" style="max-width: 600px; position: relative; width: auto; margin: 20px auto;">
    <form class="files-modal well">
        <div style="text-align: center;">
            <h3 style=" float: none">
                <?= translate('Create a new folder') ?>
            </h3>
        </div>
        <div class="input-append">
            <input class="span5 focus" type="text" id="files-new-folder-input" placeholder="<?= translate('Enter a folder name') ?>" />
            <button id="files-new-folder-create" class="btn btn-primary" disabled><?= translate('Create'); ?></button>
        </div>
    </form>
</div>

<div id="fileman-folder-template" style="display: none">
    <div class="koowa_media__item">
        <div class="koowa_media__item__content">
            <a class="koowa_media__item__link" href="[%=url%]">
                <div class="koowa_header koowa_media__item__label">
                    <div class="koowa_header__item koowa_header__item--image_container">
                        <span class="k-icon-document-folder k-icon--size-medium" aria-hidden="true"></span>
                        <span class="k-visually-hidden"><?= translate('folder'); ?></span>
                    </div>
                    <div class="koowa_header__item">
                        <div class="koowa_wrapped_content">
                            <div class="whitespace_preserver">
                                <div class="overflow_container">
                                    [%=display_name%]
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            <? if($can_delete): ?>
                <div class="koowa_media__item__options">
                                        <span class="koowa_media__item__options__select">
                                            <input name="item-select" type="checkbox" />
                                        </span>
                    <a href="#" data-action="delete-item" class="koowa_media__item__options__delete">
                        <span class="icon-trash"></span>
                    </a>
                </div>
            <? endif; ?>
        </div>
    </div>
</div>
