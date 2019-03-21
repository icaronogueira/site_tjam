<?
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('KOOWA') or die; ?>

<? // Loading necessary Markup, CSS and JS ?>
<?= helper('ui.load') ?>

<?= helper('behavior.keepalive'); ?>
<?= helper('behavior.validator'); ?>
<?= helper('behavior.modal'); ?>

<?= helper('translator.script', array('strings' => array(
    'Folder names can only contain letters, numbers, dash, underscore or colons',
    'Audio files',
    'Archive files',
    'Documents',
    'Images',
    'Video files',
    'Add another extension...'
))); ?>

<ktml:script src="media://com_fileman/js/jquery.tagsinput.js" />
<ktml:script src="media://com_fileman/js/admin/config.default.js" />

<? if (!$thumbnails_available): ?>
    <script>
        kQuery(function($) {
            $('input[name="thumbnails"]').attr('disabled', 'disabled');
            $('input[name="thumbnails"][value="0"]').attr('checked', 'checked');
        });
    </script>
<? endif ?>


<!-- Wrapper -->
<div class="k-wrapper k-js-wrapper">

    <!-- Overview -->
    <div class="k-content-wrapper">

        <!-- Content -->
        <div class="k-content k-js-content">

            <!-- Toolbar -->
            <ktml:toolbar type="actionbar">

            <!-- Component wrapper -->
            <div class="k-component-wrapper">

                <!-- Component -->
                <form class="k-component k-js-component k-js-form-controller" action="" method="post">

                    <!-- Container -->
                    <div class="k-container">

                        <!-- Main information -->
                        <div class="k-container__main">

                            <fieldset>
                                <div class="k-form-group">
                                    <label for="document_path"><?= translate('Store files in') ?></label>
                                    <div class="k-input-group">
                                        <input disabled required data-rule-storagepath class="k-form-control" type="text"
                                               value="<?= escape($config->file_path) ?>" id="file_path" name="file_path" />
                                        <div class="k-input-group__button">
                                            <button class="k-button k-button--default edit_document_path" type="button">
                                                <?= translate('Edit'); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="k-form-group">
                                    <label for="maximum_size"><?= translate('File size limit');?></label>
                                    <div class="k-input-group">
                                        <label class="k-input-group__addon file_size_checkbox">
                                            <input type="checkbox" <?= $config->maximum_size == 0 ? 'checked' : '' ?> style="margin: 0 5px 0 0;display:inline-block;vertical-align:middle;" />
                                            <?= translate('Unlimited') ?>
                                        </label>
                                        <input class="k-form-control" type="text" id="maximum_size"
                                               value="<?= floor($config->maximum_size/1048576); ?>"
                                               data-maximum="<?= $upload_max_filesize; ?>" />
                                        <span class="k-input-group__addon">
                                            <?= translate('MB'); ?>
                                        </span>
                                    </div>
                                    <p class="k-form-info">
                                        <?= translate('File size limit message', array(
                                            'link' => 'https://www.joomlatools.com/extensions/docman/documentation/troubleshooting/large-files/',
                                            'size' => floor($upload_max_filesize/1048576)-1)); ?>
                                    </p>
                                </div>
                                
                                <div class="k-form-group">
                                    <label><?= translate('Folder protection');?></label>
                                    <?= helper('select.booleanlist', array('name' => 'protect_folders', 'selected' => $config->protect_folders)); ?>
                                    <p class="k-form-info"><?=translate('Prevents direct access to folders and files')?></p>
                                </div>

                                <div class="k-form-group">
                                    <div class="k-form-group">
                                        <label for="maximum_image_size"><?= translate('Maximum image size')?></label>
                                        <?= helper('com:files.listbox.maximum_image_size', array(
                                            'name' => 'maximum_image_size',
                                            'selected' => $config->maximum_image_size
                                        )) ?>
                                    </div>
                                </div>

                                <div class="k-form-group">
                                    <div class="k-form-group">
                                        <label for="notification_emails"><?= translate('Notify these emails on file upload');?></label>
                                        <textarea class="k-form-control" rows="1" name="notification_emails"><?= implode("\r\n", (array) $config->notification_emails); ?></textarea>
                                        <p class="k-form-info">
                                            <?= translate('Each email address should go on a separate line'); ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="k-form-group">
                                    <div class="k-form-group">
                                        <label for="userfolder"><?= translate('Show user folder only');?></label>
                                        <?= helper('select.booleanlist', array('name' => 'userfolder', 'selected' => $config->userfolder)); ?>
                                        <p class="k-form-info">
                                            <?= translate('Enable to restrict FILElink access to folder and files owned by the logged in user'); ?>
                                        </p>
                                    </div>
                                </div>

                            </fieldset>

                            <fieldset>

                                <legend><?= translate('Allowed file extensions'); ?></legend>

                                <div style="display: none"  class="k-inline-form-group k-js-extension-preset">
                                    <p class="k-static-form-label k-js-extension-preset-label"></p>
                                    <div class="k-button-group">
                                        <button type="button" class="k-js-add k-button k-button--default k-button--tiny">
                                            <span class="k-icon-plus" aria-hidden="true"></span>
                                            <span class="k-visually-hidden"><?= translate('Plus icon') ?></span>
                                        </button>
                                        <button type="button" class="k-js-remove k-button k-button--default k-button--tiny">
                                            <span class="k-icon-minus" aria-hidden="true"></span>
                                            <span class="k-visually-hidden"><?= translate('Minus icon') ?></span>
                                        </button>
                                    </div>
                                </div><!-- .k-inline-form-group -->

                                <div class="k-form-group">
                                    <label for="allowed_extensions_tag"><?= translate('Select from presets'); ?></label>
                                    <div id="extension_groups" class="k-js-extension-groups extension-groups"></div>
                                </div>

                                <div class="k-form-group">
                                    <input type="text" class="k-form-control" name="allowed_extensions" id="allowed_extensions"
                                           value="<?= implode(',', KObjectConfig::unbox($config->allowed_extensions)); ?>"
                                           data-filetypes="<?= htmlentities(json_encode($filetypes)); ?>" />
                                </div>

                            </fieldset>

                        </div><!-- .k-container__main -->

                        <!-- Other information -->
                        <div class="k-container__sub">

                            <fieldset class="k-form-block">

                                <div class="k-form-block__header">
                                    <?= translate('Permissions') ?>
                                </div>

                                <div class="k-form-block__content">

                                    <div class="k-form-group">
                                        <p>
                                            <a class="k-button k-button--default" id="advanced-permissions-toggle" href="#advanced-permissions">
                                                <?= translate('Change action permissions')?>
                                            </a>
                                        </p>
                                        <p class="k-form-info k-color-error">
                                            <?= translate('For advanced use only'); ?>
                                        </p>
                                        <p class="k-form-info">
                                            <?= translate('If you would like to restrict actions like downloading a document, editing a category based on the user groups, you can use the Advanced Permissions screen.'); ?>
                                        </p>
                                    </div>
                                </div>

                                <?= import('modal_permissions.html'); ?>

                            </fieldset>

                            <fieldset>

                                <div class="k-form-block__header">
                                    <?= translate('Attachments') ?>
                                </div>

                                <div class="k-form-block__content">

                                    <div class="k-form-group">
                                        <p>
                                            <label for="attachments_layout"><?= translate('Layout');?></label>
                                            <?= helper('listbox.attachmentsLayout', array('selected' => $config->attachments_layout)) ?>
                                        </p>

                                        <p>
                                            <label for="attachments_icons"><?= translate('Show icons');?></label>
                                            <?= helper('select.booleanlist', array('id' => 'attachments_icons', 'name' => 'attachments_icons', 'selected' => $config->attachments_icons)) ?>
                                        </p>

                                        <p>
                                            <label for="attachments_info"><?= translate('Show attachments details');?></label>
                                            <?= helper('select.booleanlist', array('id' => 'attachments_info', 'name' => 'attachments_info', 'selected' => $config->attachments_info)) ?>
                                        </p>
                                        <p>
                                            <label for="attachments_lists"><?= translate('Show attachments on lists');?></label>
                                            <?= helper('select.booleanlist', array('id' => 'attachments_lists', 'name' => 'attachments_lists', 'selected' => $config->attachments_lists)) ?>
                                        </p>
                                    </div>
                                </div>

                            </fieldset>

                        </div><!-- .k-container__sub -->

                    </div><!-- .k-container -->

                </form><!-- .k-component -->

            </div><!-- .k-component-wrapper -->

        </div><!-- .k-content -->

    </div><!-- .k-content-wrapper -->

</div><!-- .k-wrapper -->
