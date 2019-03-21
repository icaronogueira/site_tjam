<?
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('_JEXEC') or die; ?>


<?= helper('ui.load') ?>
<?= helper('behavior.bootstrap', array('javascript' => true)) ?>

<? // Setting up 'translations' to be used in JavaScript ?>
<?= helper('translator.script', array('strings' => array(
    'You will lose all unsaved data. Are you sure?'
))); ?>

<?
    $query     = url()->getQuery(true);
    $container = $query['container'];
?>

<? // Loading JavaScript ?>
<ktml:script src="media://com_fileman/js/filelink.upload.js" />

<script>
    kQuery(function() {
        new FILEman.BatchForm({
            <? if (isset($onBeforeInitialize)): ?>
            'onBeforeInitialize': <?= $onBeforeInitialize ?>,
            <? endif ?>

            <? if (isset($show_uploader)): ?>
            'show_uploader': <?= json_encode($show_uploader) ?>,
            <? endif ?>
        });

    });
</script>

<? // This file is being used on the frontend only for now; ?>


<!-- Wrapper -->
<div class="k-wrapper k-js-wrapper">

    <!-- Overview -->
    <div class="k-content-wrapper">

        <!-- Content -->
        <div class="k-content k-js-content">

            <!-- Toolbar -->
            <div class="k-toolbar k-js-toolbar">
                <button class="k-button k-button--default k-js-cancel">
                    <span class="k-icon-x" aria-hidden="true"></span>
                    <span class="k-button__text"><?= translate('Close') ?></span>
                </button>
            </div><!-- .k-toolbar -->

            <!-- Component wrapper -->
            <div class="k-component-wrapper">

                <!-- Component -->
                <div class="k-component k-js-component">

                    <!-- Container -->
                    <div class="k-container k-container--flex">

                        <div style="display: none" class="k-js-success-message k-alert k-alert--success k-dont-flex">
                            <?= translate('Files have been successfully uploaded.') ?>
                            <span style="display: none" class="k-js-close-modal-container"><?= translate('Click {wrapper_start}here{wrapper_end} to close the uploader.', array(
                                    'wrapper_start' => '<a class="k-js-close-modal" href="#">',
                                    'wrapper_end' => '</a>'
                                )); ?></span>
                        </div>

                        <?= helper('com:files.uploader.container', array(
                            'container' => $container,
                            'element' => '.fileman-uploader',
                            'options'   => array(
                                'multipart_params' => array(
                                    'folder' => object('request')->getQuery()->folder
                                ),
                                'multi_selection' => true,
                                'autostart' => true,
                                'url' => route(sprintf('view=file&thumbnails=small&container=%s&plupload=1&routed=1&format=json', $container), false, false)
                            ),
                            'attributes' => array(
                                'class' => array('k-upload--custom k-upload--flex')
                            )
                        )); ?>

                    </div><!-- .k-container -->

                </div><!-- .k-component -->

            </div><!-- .k-component-wrapper -->

        </div><!-- .k-content -->

    </div><!-- .k-content-wrapper -->

</div><!-- .k-wrapper -->
