<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */
defined('KOOWA') or die( 'Restricted access' ); ?>


<?= import('com:files.files.scripts.html');?>

<ktml:script src="media://com_fileman/js/fileman.js" />
<ktml:script src="media://com_fileman/js/clipboardjs/clipboard.min.js" />

<ktml:style src="media://com_fileman/css/tooltips.css"/>


<script>
    Files.sitebase = '<?= $sitebase; ?>';
    Files.token = '<?= $token; ?>';

    window.addEvent('domready', function() {
        var config = <?= json_encode(KObjectConfig::unbox(parameters()->config)); ?>,
            options = {
                root_url: <?= json_encode(object('request')->getSiteUrl()->toString()) ?>,
                cookie: {
                    path: '<?=object('request')->getSiteUrl()?>'
                },
                state: {
                    defaults: {
                        limit: <?= (int) parameters()->limit; ?>,
                        offset: <?= (int) parameters()->offset; ?>,
                        types: <?= json_encode(KObjectConfig::unbox(parameters()->types)); ?>
                    }
                },
                root_text: <?= json_encode(translate('Root folder')) ?>,
                types: <?= json_encode(KObjectConfig::unbox(parameters()->types)); ?>,
                container: <?= json_encode($container ? $container->toArray() : null); ?>,
                thumbnails: <?= json_encode($thumbnails ?: ($container->getParameters()->thumbnails ?: true)) ?>
            };
        options = Object.append(options, config);

        Files.app = new Files.App(options);

        Files.app.grid.addEvent('beforeRenderObject', function(context)
        {
            var root_url = this.options.root_url;

            <? $config = JFactory::getConfig(); ?>

            <? if (!$config->get('sef') || ($config->get('sef') && !$config->get('sef_rewrite'))): ?>
                 root_url += '/index.php'
            <? endif ?>

            var row = context.object;

            if (row.folder) {
                row.permalink = new URI(root_url + '/filelink/' + row.container +  '/' + row.folder + '/' + row.name);
            } else {
                row.permalink = new URI(root_url + '/filelink/' + row.container +  '/' + row.name);
            }
        }.bind(Files.app));

        Files.app.grid.addEvent('afterRenderObject', function(context)
        {
            var object = context.object;

            if (object.type != 'folder') {
                Fileman.copyboard({
                    target: kQuery('.k-js-fileman-copy', context.object.element).get(0),
                    tooltips: {
                        direction: 'w',
                        message: <?= json_encode(translate('Copied!')) ?>
                    }
                });

                Fileman.tooltips({
                    direction: 'w',
                    target: kQuery('.k-js-fileman-copy', context.object.element).get(0),
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
            }
        });
    });
</script>


<!-- Component wrapper -->
<div class="k-component-wrapper" id="files-app">

    <!-- Component -->
    <div class="k-component k-js-component" id="files-canvas">

        <!-- Title when sidebar is invisible -->
        <ktml:toolbar type="titlebar" mobile>

            <!-- Toolbar -->
            <ktml:toolbar type="actionbar">

                <!-- Scopebar -->
                <div class="k-scopebar k-js-scopebar k-scopebar--breadcrumbs">

                    <!-- Breadcrumb -->
                    <div class="k-scopebar__item k-scopebar__item--breadcrumbs">
                        <div id="files-pathway" class="k-breadcrumb"></div>
                    </div>

                    <!-- Buttons -->
                    <div class="k-scopebar__item k-scopebar__item--buttons">
                        <button class="k-scopebar__button k-js-layout-switcher" data-layout="icons" title="<?= translate('Show files as icons'); ?>">
                            <span class="k-js-switcher-icon k-icon-grid-four-up" aria-hidden="true"></span>
                            <span class="k-visually-hidden"><?= translate('Grid icon'); ?></span>
                        </button>
                        <button class="k-scopebar__button k-js-layout-switcher" data-layout="details" title="<?= translate('Show files in a list'); ?>">
                            <span class="k-js-switcher-icon k-icon-list" aria-hidden="true"></span>
                            <span class="k-visually-hidden"><?= translate('List icon'); ?></span>
                        </button>
                    </div>

                    <!-- Search -->
                    <div class="k-scopebar__item k-scopebar__item--search">
                        <?= helper('grid.search', array('submit_on_clear' => false, 'placeholder' => @translate('Find by file or folder name&hellip;'))) ?>
                    </div>

                </div><!-- .k-scopebar -->

                <? if (!isset(parameters()->config->can_upload) || parameters()->config->can_upload): ?>
                    <?= import('com:files.files.uploader.html');?>
                <? endif; ?>

                <div class="k-flex-wrapper k-position-relative">
                    <div id="files-grid-container">
                        <div id="files-grid"></div>
                        <div class="k-table-pagination" id="files-paginator-container">
                            <?= helper('paginator.pagination') ?>
                        </div>
                    </div>
                    <div class="k-loader-container">
                        <span class="k-loader k-loader--large"><?= translate('Loading') ?></span>
                    </div>
                </div>

    </div><!-- .k-component -->

</div><!-- .k-component-wrapper -->


<div class="k-dynamic-content-holder">

    <?= import('com:files.files.templates_icons.html'); ?>
    <?= import('com://admin/fileman.files.templates_details.html'); ?>

    <div id="files-new-folder-modal" class="k-ui-namespace k-small-inline-modal-holder mfp-hide">
        <div class="k-inline-modal">
            <form>
                <h3>
                    <?= translate('Create a new folder in {folder}', array(
                        'folder' => '<span class="upload-files-to"></span>'
                    )) ?>
                </h3>
                <div class="k-input-group">
                    <input class="k-form-control focus" type="text" id="files-new-folder-input" placeholder="<?= translate('Enter a folder name') ?>" />
                    <div class="k-input-group__button">
                        <button id="files-new-folder-create" class="k-button k-button--primary" disabled><?= translate('Create'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="files-move-modal" class="k-ui-namespace k-small-inline-modal-holder mfp-hide">
        <div class="k-inline-modal">
            <form>
                <h3><?= translate('Move to') ?></h3>
                <div class="k-js-tree-container k-tree"></div>
                <p>
                    <button class="k-button k-button--primary" ><?= translate('Move'); ?></button>
                </p>
            </form>
        </div>
    </div>

    <div id="files-copy-modal" class="k-ui-namespace k-small-inline-modal-holder mfp-hide">
        <div class="k-inline-modal">
            <form>
                <h3><?= translate('Copy to') ?></h3>
                <div class="k-js-tree-container k-tree"></div>
                <p>
                    <button class="k-button k-button--primary" ><?= translate('Copy'); ?></button>
                </p>
            </form>
        </div>
    </div>

</div>
