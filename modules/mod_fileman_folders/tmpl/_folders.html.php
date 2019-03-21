<?
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('KOOWA') or die; ?>

<ul <?= $params->show_icon ? ' class="mod_docman_icons"' :'' ?>>
    <?php foreach($folders as $folder) : ?>
    <li class="module_folder module_folder__level<?= $level ?>">
        <p class="koowa_header koowa_header--margin">
            <span class="koowa_header__item koowa_header__item--image_container">
                <a href="<?= $folder->path; ?>">
                    <? if ($params->show_icon): ?>
                        <span class="k-icon-document-folder <?= isset($class) ? $class : '' ?>" aria-hidden="true"></span>
                        <span class="k-visually-hidden"><?= translate('folder'); ?></span>
                    <? endif ?>
                </a>
            </span>
            <span class="koowa_header__item">
                <span class="koowa_wrapped_content">
                    <span class="whitespace_preserver">
                        <a href="<?= route('component=fileman&view=folder&folder='.$folder->path) ?>">
                          <?= escape($folder->display_name);?>
                        </a>
                    </span>
                </span>
            </span>
        </p>
    </li>
    <? if ($folder->hasChildren()) : ?>
        <li class="module_folder module_folder__level<?= $level ?>">
            <?= import('mod://site/fileman_folders._folders.html', array(
                'folders' => $folder->getChildren(),
                'params' => $params,
                'level' => ++$level
            )); ?>
        </li>
    <? endif; ?>
    <?php endforeach; ?>
</ul>
