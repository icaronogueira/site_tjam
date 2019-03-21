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
    'wrapper' => false
)); ?>
<?= helper('behavior.jquery'); ?>

<ktml:script src="media://com_fileman/js/fileman.js" />

<? if ($params->track_downloads): ?>
<script>
    kQuery(function($) {
        $('.fileman-view').click(function() {
            Fileman.trackEvent({action: 'Download', label: $(this).attr('data-path')});
        });
    });
</script>
<? endif; ?>

<div class="k-ui-namespace">
    <div class="mod_fileman mod_fileman--files">

        <ul<?= $params->show_icon ? ' class="mod_fileman_icons"' :'' ?>>
        <?php foreach($files as $file): ?>
            <li class="mod_fileman--file">
                <div class="koowa_header">
                    <? if ($params->show_icon): ?>
                    <span class="koowa_header__item koowa_header__item--image_container">
                        <a class="koowa_header__image_link fileman-view" data-path="<?= escape($file->path); ?>"
                            <?= $params->download_in_blank_page ? 'target="_blank"' : ''; ?>
                           href="<?= route('component=fileman&view=file&folder='.$params->folder.'&name='.$file->name);?>">
                            <span class="k-icon-document-<?= helper('com:files.icon.icon', array(
                                'extension' => $file->extension
                            )) ?> k-icon-document-<?= helper('com:files.icon.icon', array(
                                'extension' => $file->extension
                            )) ?> " aria-hidden="true"></span>
                        </a>
                    </span>
                    <? endif ?>

                    <span class="koowa_header__item">
                        <span class="koowa_wrapped_content">
                            <span class="whitespace_preserver">
                                <a data-path="<?= escape($file->path); ?>"
                                   class="fileman-view"
                                    <?= $params->download_in_blank_page ? 'target="_blank"' : ''; ?>
                                   href="<?= route('component=fileman&view=file&folder='.$params->folder.'&name='.$file->name);?>">
                                    <?= escape($file->display_name) ?></a>
                            </span>
                        </span>
                    </span>
                </div>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
</div>
