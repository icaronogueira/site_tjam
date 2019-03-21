<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('KOOWA') or die;
$url = url()->getQuery(true);
$row = $url['row'];
?>


<?= helper('ui.load') ?>


<?= import('com:files.attachments.manage.html', array('debug' => (bool) JFactory::getApplication()->getCfg('debug'))) ?>


<script>
    kQuery(function($)
    {
        var container = $('.mce-in.mce-panel', window.parent.document);

        if (container.length) {
            container.addClass('k-joomla-modal-override');
        }

        var replaceCount = function(text, count) {
            return text.replace(/\(.*?\)/, '(' + count + ')');
        };

        var updateButtonCount = function()
        {
            var selectors = ['a.btn.fileman-attachments.modal-button', 'a.btn.k-js-iframe-modal.fileman-attachments'];

            var that = this;

            $.each(selectors, function(idx, selector)
            {
                var el = $(selector, window.parent.document);

                if (el.length)
                {
                    el.html(replaceCount(el.html(), that.getCount()));
                    el.attr('title', replaceCount(el.attr('title'), that.getCount()));
                    return false;
                }
            });
        }.bind(Files.app.grid);

        Files.app.options.router.defaults.option = 'com_fileman';

        if (<?= (int) version_compare(JVERSION, '3.5', '>=') ?> &&
        <?= (int) (JFactory::getApplication()->getCfg('editor') != 'jce') ?> &&
        window.parent.tinyMCE !== undefined) // TinyMCE specific (>= J3.5)
        {
            window.parent.tinyMCE.activeEditor.windowManager.windows[0].on('close', function()
            {
                var el = $('i.mce-ico.fileman-attachments', window.parent.document);

                var div = el.closest('div');

                if (div.length && div.attr('aria-label')) {
                    div.attr('aria-label', replaceCount(div.attr('aria-label'), this.getCount()));
                }

                var span = div.find('span.mce-txt');

                if (span.length) {
                    span.html(replaceCount(span.html(), this.getCount()));
                }
            }.bind(Files.app.grid));

            var updateWindowCount = function()
            {
                var container = $('.mce-window-head .mce-title', window.parent.document);

                if (container.length) {
                    container.html(replaceCount(container.html(), this.getCount()));
                }
            }.bind(Files.app.grid);

            // Update attachements label count.
            Files.app.grid.addEvent('afterInsertRows', function() {
                updateWindowCount();
            });

            // Update attachements label count.
            Files.app.grid.addEvent('afterDeleteNode', function() {
                updateWindowCount();
            });
        }
        else if (window.parent.SqueezeBox !== undefined && window.parent.SqueezeBox.isOpen) // Generic (< J3.5)
        {
            window.parent.SqueezeBox.addEvent('onClose', function() {updateButtonCount()});
        }
        else if (typeof window.parent.kQuery !== 'undefined' && typeof window.parent.kQuery.magnificPopup !== 'undefined'
            && window.parent.kQuery.magnificPopup.instance && window.parent.kQuery.magnificPopup.instance.isOpen)
        {
            var magnificPopup = window.parent.kQuery.magnificPopup;

            var old = magnificPopup.instance.close;

            magnificPopup.instance.close = function()
            {
                updateButtonCount();
                magnificPopup.instance.close = old;
                magnificPopup.proto.close.call(magnificPopup);
            };
        }

        Files.app.grid.addEvent('afterAttachAttachment', function(data)
        {
            $.each(['form[name="adminForm"]', 'form.k-js-form-controller'], function(idx, form) {
                form = $(form, parent.document);

                if (form.length && !form.find('input[name="fileman_attachment_row"]').length)
                {
                    // Append the the row value to update the row columns when attaching files to new items.
                    form.append($('<input type="hidden" name="fileman_attachment_row" value=<?= json_encode($row) ?> />'));
                    return false;
                }
            });
        });

        Files.app.grid.addEvent('onClickAttachment', function(e)
        {
            var selected = that.grid.selected;

            if (selected && typeof that.grid.nodes[selected] !== 'undefined')
            {
                var file = that.grid.nodes[selected].file;

                if (file.type == 'image' && !file.thumbnail) {
                    this.preview.getElement('img').set('src', this.createRoute(
                        {
                            view: 'file',
                            format: 'html',
                            name: file.name,
                            routed: 1
                        }));
                }
            }
        }.bind(Files.app));
    });
</script>
