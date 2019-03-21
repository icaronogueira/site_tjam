<?
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 - 2017 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
?>

<?= helper('ui.load') ?>

<ktml:script src="media://koowa/com_files/js/ejs/ejs.js"/>
<ktml:script src="media://koowa/com_files/js/files.attachments.js"/>

<?= helper('ui.load') ?>

<ktml:script src="media://com_fileman/js/site/gallery.js"/>
<ktml:style src="media://com_fileman/css/site.css"/>

<?= helper('behavior.modal') ?>

<script>
    kQuery(function($) {
       $('.attachments').simpleGallery();
    });
</script>

<? if (count($attachments)): ?>
    <div style="clear: both">
        <h3><?= translate('Attachments') ?></h3>
        <div class="koowa_media--gallery">
            <div class="attachments koowa_media_wrapper koowa_media_wrapper--documents">
                <div class="koowa_media_contents">
                    <?php // this comment below must stay ?>
                    <div class="koowa_media"><!--
                    <? foreach ($attachments as $attachment): ?>
                        <? if ($attachment->file): ?>
                     --><div class="koowa_media__item">
                                <div class="koowa_media__item__content file">
                                    <?= import('com://site/fileman.attachment.default.html', array('attachment' => $attachment)) ?>
                                </div>
                            </div><!--
                        <? endif ?>
                    <? endforeach ?>
             --></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        kQuery(function($) {
            $('.koowa_media__item__image').closest('a').magnificPopup({
                type: 'image',
                gallery:{
                    enabled:true
                }
            });
        });
    </script>
<? endif ?>


