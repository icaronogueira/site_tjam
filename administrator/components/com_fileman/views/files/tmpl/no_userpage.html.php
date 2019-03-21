<?
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('_JEXEC') or die; ?>

<? // Loading necessary Markup, CSS and JS ?>
<?= helper('ui.load', array('package' => 'fileman')); ?>


<!-- Wrapper -->
<div class="k-wrapper k-js-wrapper">

    <!-- Title when sidebar is invisible -->
    <ktml:toolbar type="titlebar" title="<?= translate('Files'); ?>" mobile>

        <!-- Overview -->
        <div class="k-content-wrapper">

            <!-- Sidebar -->
            <?= import('default_sidebar.html', array('tree' => false)); ?>

            <!-- Content -->
            <div class="k-content k-js-content">

                <!-- Toolbar -->
                <ktml:toolbar type="actionbar">

                <!-- Component wrapper -->
                <div class="k-component-wrapper">

                    <!-- Component -->
                    <form class="k-component" method="POST" action="<?= route('option=com_menus&view=item&client_id=0&layout=edit&id=0') ?>">

                        <div class="k-empty-state">
                            <p>
                                <?= translate('It seems like you don\'t have any user files menu items yet.'); ?>
                            </p>
                            <p>
                                <input name="jform[type]" type="hidden" value="<?= base64_encode('{"id":0,"title":"COM_FILEMAN_VIEW_USER","request":{"option":"com_fileman","view":"userfolder"}}')?>"/>
                                <input name="task" type="hidden" value="item.setType"/>
                                <input name="fieldtype" type="hidden" value="type"/>
                                <?= JHtml::_( 'form.token' ); ?>
                                <button type="submit" class="k-button k-button--success k-button--large"><?= translate('Add your first menu item') ?></button>
                            </p>
                        </div>

                    </form><!-- .k-component -->

                </div><!-- .k-component-wrapper -->

            </div><!-- k-content -->

        </div><!-- .k-content-wrapper -->

</div><!-- .k-wrapper -->
