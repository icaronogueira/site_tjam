<?
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('_JEXEC') or die;
?>

<?= helper('ui.load'); ?>
<?= helper('behavior.modal'); ?>

<? $colspan = $params->show_filesize ? ($params->show_modified_date ? 3 : 2) : ($params->show_modified_date ? 2 : 1) ?>

<? if ($can_copy) $colspan++ ?>

<ktml:script src="media://com_fileman/js/fileman.js" />
<ktml:script src="media://com_fileman/js/clipboardjs/clipboard.min.js" />
<ktml:script src="media://com_fileman/js/site/items.js" />
<ktml:script src="media://com_fileman/js/site/folder.js" />
<ktml:script src="media://koowa/com_files/js/ejs/ejs.js"/>

<ktml:style src="media://com_fileman/css/tooltips.css"/>

<script>
    kQuery(function($) {
        <? if ($params->track_downloads): ?>
        $('.fileman-view').click(function () {
            Fileman.trackEvent({action: 'Download', label: $(this).attr('data-path')});
        });
        <? endif; ?>

        Folder = new Fileman.Folder({
            post_url: "<?= route('format=json&folder=' . rawurlencode($folder->path), true, false) ?>",
            token: <?= json_encode($token) ?>,
        });

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

<ktml:toolbar type="actionbar">

<div class="fileman_table_layout">

    <? if ($params->show_page_heading): ?>
        <h1 class="fileman_header">
            <?= escape($params->page_heading); ?>
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
    <? endif; ?>

    <form action="" method="get" class="koowa_form k-js-grid-controller koowa_table_list">
        <? // Table ?>
        <table class="table table-striped koowa_table koowa_table--files">
            <tbody>
                <? foreach ($folders as $item): ?>
                    <tr class="fileman_folder">
                        <? if ( $can_delete ): ?>
                            <td width="10">
                                <input name="item-select" type="checkbox" />
                            </td>
                        <? endif; ?>
                        <td <?= ($colspan > 1) ? 'colspan="' . $colspan . '"' : '' ?>>
                            <span class="koowa_header">
                            <? if ($params->show_icon): ?>
                                <span class="koowa_header__item koowa_header__item--image_container">
                                    <a class="iconImage" href="<?= route('folder=' . rawurlencode($item->path)) ?>">
                                        <span class="k-icon-document-folder k-icon--size-medium" aria-hidden="true"></span>
                                        <span class="k-visually-hidden"><?= translate('folder'); ?></span>
                                    </a>
                                </span>
                            <? endif ?>
                            <span class="koowa_header__item">
                                <span class="koowa_wrapped_content">
                                    <span class="whitespace_preserver">
                                        <a href="<?= route('folder=' . rawurlencode($item->path)) ?>">
                                            <?= $item->display_name ?>
                                        </a>
                                    </span>
                                </span>
                            </span>
                            </span>
                        </td>
                        <? if ( $can_delete ): ?>
                            <td style="text-align: right">
                                <a class="btn btn-small btn-danger koowa_media__item__options__delete" data-action="delete-item" href="#">
                                    <?= translate('Delete') ?>
                                </a>
                            </td>
                        <? endif; ?>
                    </tr>
                <? endforeach ?>
                <? foreach ($files as $file): ?>
                    <tr class="fileman_file">
                        <? if ( $can_delete ): ?>
                            <td>
                                <input name="item-select" type="checkbox" />
                            </td>
                        <? endif; ?>
                        <td>
                            <span class="koowa_header">
                                <? if ($params->show_icon): ?>
                                <span class="koowa_header__item koowa_header__item--image_container">
                                    <a class="iconImage" data-path="<?= escape($file->path) ?>"
                                        <?= $params->download_in_blank_page ? 'target="_blank"' : ''; ?>
                                       href="<?= route('view=file&folder=' . rawurlencode($folder->path) . '&name=' . rawurlencode($file->name)) ?>">
                                        <span class="k-icon-document-<?= helper('com:files.icon.icon', array(
                                            'extension' => $file->extension
                                        )) ?> k-icon-document-<?= helper('com:files.icon.icon', array(
                                            'extension' => $file->extension
                                        )) ?> k-icon--size-medium" aria-hidden="true"></span>
                                    </a>
                                </span>
                                <? endif ?>
                                <span class="koowa_header__item">
                                    <span class="koowa_wrapped_content">
                                        <span class="whitespace_preserver">
                                            <a data-path="<?= escape($file->path) ?>"
                                                <?= $params->download_in_blank_page ? 'target="_blank"' : ''; ?>
                                                href="<?= route('view=file&folder=' . rawurlencode($folder->path) . '&name=' . rawurlencode($file->name)) ?>">
                                                <?=escape($file->display_name)?>
                                                (<?= strtolower($file->extension) ?><? if ($params->show_filesizes): ?>,
                                                    <?= helper('com:files.filesize.humanize', array('size' => $file->size)); ?><? endif; ?>)
                                            </a>
                                        </span>
                                    </span>
                                </span>
                            </span>
                        </td>
                        <? if ($params->show_filesize): ?>
                        <td class="k-no-wrap">
                            <?= helper('com:files.filesize.humanize', array('size' => $file->size)) ?>
                        </td>
                        <? endif ?>
                        <? if ($params->show_modified_date): ?>
                        <td class="koowa_table__dates">
                            <?= helper('date.format', array('date' => $file->modified_date)) ?>
                        </td>
                        <? endif ?>
                        <? if ( $can_copy ): ?>
                            <td style="text-align: right">
                                <a class="btn btn-small btn-default koowa_media__item__options__copy" data-action="copy-item"
                                   data-clipboard-text="<?= $file->permalink ?>" href="#">
                                    <span class="k-icon-clipboard"></span>
                                </a>
                            </td>
                        <? endif ?>
                        <? if ( $can_delete ): ?>
                        <td style="text-align: right">
                            <a class="btn btn-small btn-danger koowa_media__item__options__delete" data-action="delete-item" href="#">
                                <?= translate('Delete') ?>
                            </a>
                        </td>
                        <? endif; ?>
                    </tr>
                <? endforeach ?>
            </tbody>
        </table>
        <? if ($params->limit != 0 && max(parameters()->total, $folder_count) > 5): ?>
            <?= helper('paginator.pagination', array(
                'total'   => max(parameters()->total, $folder_count),
                'limit'   => parameters()->limit,
                'url'     => route('folder=' . rawurlencode($folder->path)),
                'offset'  => parameters()->offset,
                'attribs' => array(
                    'onchange' => 'this.form.submit();'
                )
            )) ?>
        <? endif; ?>
    </form>
</div>

<div style="display: none">
    <table>
        <tbody id="fileman-folder-template">
            <tr class="fileman_folder">
                <? if ( $can_delete ): ?>
                    <td>
                        <input name="item-select" type="checkbox" />
                    </td>
                <? endif; ?>
                <td <?= ($colspan > 1) ? 'colspan="' . $colspan . '"' : '' ?>>
                    <span class="koowa_header">
                        <? if ($params->show_icon): ?>
                            <span class="koowa_header__item koowa_header__item--image_container">
                                <a class="iconImage" href="[%=url%]">
                                    <span class="k-icon-document-folder k-icon--size-medium" aria-hidden="true"></span>
                                    <span class="k-visually-hidden"><?= translate('folder'); ?></span>
                                </a>
                            </span>
                        <? endif ?>
                        <span class="koowa_header__item">
                            <span class="koowa_wrapped_content">
                                <span class="whitespace_preserver">
                                    <a href="[%=url%]">
                                        [%=display_name%]
                                    </a>
                                </span>
                            </span>
                        </span>
                    </span>
                </td>
                <? if ( $can_delete ): ?>
                    <td>
                        <a class="btn btn-small btn-danger koowa_media__item__options__delete" data-action="delete-item" href="#">
                            <?= translate('Delete') ?>
                        </a>
                    </td>
                <? endif; ?>
            </tr>
        </tbody>
    </table>
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


