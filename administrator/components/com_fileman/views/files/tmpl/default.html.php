<?
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('_JEXEC') or die; ?>


<script>
    if (!Files) var Files = {};

    Files.Config = {
        thumbnails: 'small' // Use small thumbnails
    };
</script>

<? // Loading necessary Markup, CSS and JS ?>
<?= helper('ui.load', array('package' => 'fileman')); ?>


<!-- Wrapper -->
<div class="k-wrapper k-js-wrapper">

    <!-- Title when sidebar is invisible -->
    <ktml:toolbar type="titlebar" title="<?= translate('Files'); ?>" mobile>

    <!-- Overview -->
    <div class="k-content-wrapper">

        <!-- Sidebar -->
        <?= import('default_sidebar.html', array('tree' => true)); ?>

        <!-- Content -->
        <div class="k-content k-js-content">

            <!-- Toolbar -->
            <ktml:toolbar type="actionbar">

            <ktml:content>

        </div><!-- k-content -->

    </div><!-- .k-content-wrapper -->

</div><!-- .k-wrapper -->


<script>
    window.addEvent('domready', function()
    {
        var refreshButtons = function()
        {
            var buttons = kQuery('.k-is-hideable');

            if (kQuery('.files-node.k-is-selected').length) {
                buttons.removeClass('k-is-disabled');
            } else {
                buttons.addClass('k-is-disabled');
            }
        };

        Files.app.grid.addEvent('afterCheckNode', refreshButtons);
        Files.app.grid.addEvent('afterDeleteNode', refreshButtons);
        Files.app.grid.addEvent('afterReset', refreshButtons);
        Files.app.grid.addEvent('afterMoveNodes', refreshButtons);

        refreshButtons();
    });
</script>
