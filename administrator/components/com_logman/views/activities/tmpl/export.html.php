<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('_JEXEC') or die; ?>

<?= helper('behavior.jquery')?>
<?= helper('behavior.logman')?>

<script>
    kQuery(document).ready(function($) {
        var Export = new Logman.Export({url: '<?=$export_url?>'});
        Export.bind('exportComplete', function(e, data) {
            if (data.exported) {
                var msg = <?= json_encode(translate("EXPORT_DOWNLOAD"))?>;
                setTimeout(function() {
                    window.location = "<?=JRoute::_('index.php?option=com_logman&view=activities&export=1', false)?>";
                }, 3000);
            } else {
                var msg = <?= json_encode(translate("EXPORT_EMPTY"))?>;
            }
            $('#logman-export-bar').parent().removeClass('active');
            $('#logman-export-message').fadeOut('slow', function() {
                $(this).html(msg).fadeIn('slow');
                $('#logman-export-bar').parent().addClass('progress-success')
            });
        });
        Export.bind('exportUpdate', function(e, data) {
            $('#logman-export-bar').css('width', data.completed + '%');
        });
        $('#logman-export-button').one('click', function(event) {
            event.preventDefault();
            $(this).attr('disabled', 'disabled');
            Export.start();
        });
    });
</script>


<div id="logman-export" class="k-ui-namespace k-small-inline-modal-holder mfp-hide">
    <div class="k-inline-modal">
        <form>
            <h3 class="k-inline-modal__title">
                <?=translate('Export to CSV')?>
            </h3>
            <p class="logman_export_dialog__message" id="logman-export-message"><?= translate('EXPORT_INIT')?></p>
            <div class="logman-export-dialog__progress">
                <div class="progress progress-striped active">
                    <div class="bar" style="width: 0%" id="logman-export-bar"></div>
                </div>
            </div>
            <div class="logman-export-dialog__buttons">
                <button type="button" class="k-button k-button--primary" id="logman-export-button"><?= translate('Export')?></button>
            </div>
        </form>
    </div>
</div>
