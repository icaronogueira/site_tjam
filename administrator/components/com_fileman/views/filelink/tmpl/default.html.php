<?
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('_JEXEC') or die; ?>


<? $url = url()->getQuery(true) ?>

<? // Loading necessary Markup, CSS and JS ?>
<?= helper('ui.load', array('package' => 'fileman')) ?>

<script>
    kQuery(function($) {
        var container = $('.mce-in.mce-panel', window.parent.document);
        if (container.length) {
            container.addClass('k-joomla-modal-override');
        }
    });
</script>

<script>
	if (!Files) var Files = {};

	Files.Config = {
	    persistent: true,
        thumbnails: 'medium',
        cookie: {
	        name: 'filelink'
        },
		onInitialize: function(app)
		{
			Files.File = new Class({
				Extends: Files.File,

				initialize: function(object, options)
				{
					this.parent(object, options);

					// Set route.
					this.route = app.createRoute({view: 'file', format: 'html', name: this.name, folder: app.getPath()});
				}
			});

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
	var type = 'file',
		getFileLink = function() {
			var url = document.id('file-url').get('value'),
				str = '<a href="'+url+'" ',
				title = document.id('file-title').get('value'),
				text = document.id('file-text').get('value');
			if (!url || !text) {
				return false;
			}
			if (title) {
				str += 'title="'+title+'"';
			}
			if (!text) {
				text = url;
			}
			str += '>'+text+'</a>';

			return str;
		},
		getImageLink = function(wrapped) {

	        if (typeof wrapped === 'undefined') {
	            wrapped = true;
            }

            var row = JSON.parse(document.id('image-row').get('data-value'));
			if (!row) {
				return false;
			}
			var attrs = {};
			['align', 'alt', 'title'].each(function(id) {
				var value = document.id('image-'+id).get('value');
				if (value) {
					attrs[id] = value;
				}
			});

			var parts = [];
			Object.each(attrs, function(value, key) {
				parts.push(key+'="'+value+'"');
			});
			var attributes = parts.join(' ');

			var thumbnail = row.image;

            if (row.thumbnail)
            {
                var node = null;

                Files.app.grid.nodes.each(function(el)
                {
                    if (el.name == row.name) {
                        node = el;
                    }
                });

                if (node) {
                    thumbnail = node.encodePath(row.thumbnail.relative_path, Files.urlEncoder);
                }
            }

            var link = '';

            if (wrapped) {
                link =  '<img class="filelink" ' + attributes + ' src="'+ Files.sitebase + '/' + thumbnail +'" data-source="' + row.uri + '">';
            } else {
                link = thumbnail;
            }

            return link;
		};

	document.id('insert-link').addEvent('click', function(e) {
		e.stop();

		var callback = false;

        <? if (isset($url['callback'])): ?>
            callback = <?= json_encode($url['callback']) ?>;
        <? endif ?>

        var link = type === 'file' ? getFileLink() : getImageLink(callback === false ? true : false);

        if (callback)
        {
            if (typeof parent.window[callback] == 'function') {
                parent.window[callback](link);
            }
        }
        else
        {
            if (window.parent.jInsertEditorText && link) {
                window.parent.jInsertEditorText(link, Files.app.editor);
            }
        }

        if (window.parent.SqueezeBox && link) {
            window.parent.SqueezeBox.close();
        }

        if (typeof window.parent.kQuery !== 'undefined' && typeof window.parent.kQuery.magnificPopup !== 'undefined'
            && window.parent.kQuery.magnificPopup.instance && window.parent.kQuery.magnificPopup.instance.isOpen) {
            window.parent.kQuery.magnificPopup.close();
        }
	});

    document.id('insert-button-container').adopt(document.id('insert-form'));

	Files.app.grid.addEvent('clickImage', function(e) {
		var row = document.id(e.target).getParent('.files-node').retrieve('row');
		document.id('insert-form').setStyle('display', '');
		document.id('file-form').setStyle('display', 'none');
		document.id('image-form').setStyle('display', '');

		if (row)
		{
            document.id('image-row').set('data-value', JSON.stringify(row));
            type = 'image';
        }
	});
	Files.app.grid.addEvent('clickFile', function(e) {
		var row = document.id(e.target).getParent('.files-node').retrieve('row');
		document.id('insert-form').setStyle('display', '');
		document.id('file-form').setStyle('display', '');
		document.id('image-form').setStyle('display', 'none');

		document.id('file-url').set('value', row.route);

		var text = document.id('file-text');
		if (!text.get('value')) {
			text.set('value', row.name);
		}

		type = 'file';
	});

	if (window.parent.tinyMCE) {
		var text = window.parent.tinyMCE.activeEditor.selection.getContent({format:'raw'});
			if (text) {
			document.id('file-text').set('value', text);
			document.id('image-title').set('value', text);
		}
	}

	if(window.parent && window.parent != window && window.parent.SqueezeBox) {
		var modal = window.parent.SqueezeBox;

		document.id('insert-modal-cancel').addEvent('click', function(){
			modal.close();
		});
	} else {
		document.id('insert-modal-cancel').setStyle('display', 'none');
		document.getElement('.insert-or').setStyle('display', 'none');
	}
});
</script>


<form id="insert-form" style="display: none">
	<div id="file-form" class="k-content-block" style="display: none">
		<input type="hidden" id="file-url" value="" />
        <div id="file-form">
            <div class="k-form-group">
                <label for="file-title"><?= translate('Link Title') ?></label>
                <input type="text" class="k-form-control" id="file-title" value="" />
            </div>
            <div class="k-form-group">
                <label for="file-text"><?= translate('Text') ?></label>
                <input type="text" class="k-form-control" id="file-text" value="" />
            </div>
        </div>
	</div>
	<div id="image-form" class="k-content-block" style="display: none">
		<input type="hidden" id="image-row" data-value="" />
        <div class="k-form-group">
            <label for="image-alt"><?= translate('Description') ?></label>
            <input type="text" id="image-alt" value="" class="k-form-control" />
        </div>
        <div class="k-form-group">
            <label for="image-title"><?= translate('Title') ?></label>
            <input type="text" id="image-title" value="" class="k-form-control" />
        </div>
        <div class="k-form-group">
            <label for="image-align"><?= translate('Align') ?></label>
            <select id="image-align" title="Positioning of this image" class="k-form-control">
                <option value="" selected="selected"><?= translate('Not Set') ?></option>
                <option value="left"><?= translate('Left') ?></option>
                <option value="right"><?= translate('Right') ?></option>
            </select>
        </div>
	</div>
	<div class="k-content-block">
		<a class="k-button k-button--default" id="insert-modal-cancel" href="#"><?= translate('Cancel') ?></a>
		<span class="k-this-or-that"><?= translate('or') ?></span>
		<button type="button" id="insert-link" class="k-button k-button--primary"><?= translate('Insert') ?></button>
	</div>
</form>
