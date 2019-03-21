<?
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<div class="fileman_player" style="clear: both">
    <audio
        data-media-id="0"
        data-title="<?= $name ?>"
        data-category="fileman"
        controls>
        <source src="<?= $url ?>" type="audio/<?= $extension ?>" />
    </audio>
</div>