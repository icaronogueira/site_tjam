<?
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('_JEXEC') or die; ?>

<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:media="http://search.yahoo.com/mrss/">
    <channel>
    <title><?=escape($params->get('page_heading', translate('Files')))?> - <?=escape($sitename)?></title>
    <link><?= $channel_link ?></link>
    <atom:link href="<?= $feed_link ?>" rel="self" type="application/rss+xml"/>
    <language><?= $language ?></language>
    <sy:updatePeriod><?= $update_period ?></sy:updatePeriod>
    <sy:updateFrequency><?= $update_frequency ?></sy:updateFrequency>
    <?foreach($files as $file):?>
        <item>
            <title><?= escape($file->display_name); ?></title>
            <link><?= route('view=file&folder='.rawurlencode($folder->path).'&name='.rawurlencode($file->name).'&format=html') ?></link>
            <guid isPermaLink="true"><?= route('view=file&folder='.rawurlencode($folder->path).'&name='.rawurlencode($file->name).'&format=html') ?></guid>
            <media:title type="plain"><?= escape($file->display_name); ?></media:title>
            <media:content
              url="<?= route('view=file&folder='.rawurlencode($folder->path).'&name='.rawurlencode($file->name).'&format=html') ?>"
              type="<?= $file->mimetype ?>"
              filesize="<?= $file->size ?>"
              <? if ($params->show_thumbnails && !empty($file->thumbnail)): ?>
              <? if ($file->width): ?>
              width="<?= $file->width; ?>"
              <? endif; ?>
              <? if ($file->height): ?>
              height="<?= $file->height; ?>"
              <? endif; ?>
              <? endif; ?>
            />
            <? if(!empty($file->thumbnail)): ?>
            <media:thumbnail
            url="<?= route('view=file&folder='.rawurlencode($folder->path).'&name='.rawurlencode($file->name).'&format=html') ?>"
            <? if ($file->width): ?>
            width="<?= $file->width; ?>"
            <? endif; ?>
            <? if ($file->height): ?>
            height="<?= $file->height; ?>"
<? endif; ?>
        />
        <? endif; ?>
        </item>
    <? endforeach ?>
    </channel>
</rss>
