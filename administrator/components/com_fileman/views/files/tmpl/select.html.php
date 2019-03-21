<?
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('_JEXEC') or die; ?>

<?= helper('ui.load', array('package' => 'fileman')) ?>

<script>
    if (!Files) var Files = {};

    Files.Config = {
        onInitialize: function(app)
        {
            Files.Image = new Class({
                Extends: Files.Image,

                initialize: function(object, options)
                {
                    this.parent(object, options);

                    // Override the image property.
                    this.image = app.createRoute({view: 'file', format: 'html', name: this.name, folder: app.getPath()});
                }
            });
        }
    }
</script>

<ktml:content>

<script>
window.addEvent('domready', function() {
	var selected = {folder: '', name: ''},
		folder_id = 'jform_request_folder';

	document.id('insert-document').addEvent('click', function(e) {
		e.stop();

		window.parent.document.id('fileman-file-link-name').set('value', selected.name);
		window.parent.document.id(folder_id).set('value', selected.folder);

        if (window.parent.SqueezeBox) {
            window.parent.SqueezeBox.close();
        }
	});

	document.id('insert-button-container').adopt(document.id('document-insert-form'));

	var evt = function(e) {
		var target = document.id(e.target).getParent('.files-node');
		var row = target.retrieve('row');

		selected.folder = row.folder;
		selected.name = row.name;
		document.id('insert-document').set('disabled', false);
	};
	Files.app.grid.addEvent('clickFile', evt);
	Files.app.grid.addEvent('clickImage', evt);
});
</script>

<? // Used in the "Direct link to file modal ?>
<div id="document-insert-form">
	<button class="k-button k-button--primary" type="button" id="insert-document" disabled><?= translate('Insert') ?></button>
</div>
