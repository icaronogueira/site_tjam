<?
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('_JEXEC') or die; ?>


<? // Loading necessary Markup, CSS and JS ?>
<?= helper('ui.load') ?>


<!-- Wrapper -->
<div class="k-wrapper k-js-wrapper">

    <!-- Content wrapper -->
    <div class="k-content-wrapper">

        <!-- Content -->
        <div class="k-content k-js-content">

            <!-- Component wrapper -->
            <div class="k-component-wrapper">

                <!-- Component -->
                <form class="k-component k-js-component">

                    <!-- Container -->
                    <div class="k-container">

                        <!-- Main fields -->
                        <div class="k-container__main">

                            <div class="k-form-group">
                                <label for="logman_linker"><?= translate('Select a resource to link') ?></label>
                                <?= helper('listbox.linker') ?>
                                <p class="k-form-info"><?= translate('Non-selectable listed items are not frontend linkable') ?></p>
                            </div>

                        </div><!-- .k-container__main -->

                    </div><!-- .k-container -->

                </form><!-- .k-component -->

            </div><!-- .k-component-wrapper -->

        </div><!-- .k-content -->

    </div><!-- .k-content-wrapper -->

</div><!-- .k-wrapper -->


<script>
    kQuery(function($)
    {
        var linker = $('#logman_linker');

        linker.on('select2:select', function(e)
        {
            var select = $(this);

            var link = '<a class="logman_linker" href="'+ select.val() +'">' + select.children().attr('title') + '</a>';
            window.parent.jInsertEditorText(link, <?= json_encode($editor) ?>);

            if (window.parent.SqueezeBox) {
                window.parent.SqueezeBox.close();
            }
        });
    });
</script>
