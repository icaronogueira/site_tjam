<?
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
        
defined('KOOWA') or die; ?>

<?= helper('ui.load');?>
<?= helper('behavior.keepalive'); ?>
<?= helper('behavior.validator'); ?>


<div class="fileman_submit_layout">

    <? if ($params->show_page_heading): ?>
        <h2 class="fileman_header"><?= escape($params->page_heading) ?></h2>
    <? endif ?>

    <ktml:toolbar type="actionbar">

    <form action="" method="post" class="koowa_form k-js-form-controller" enctype="multipart/form-data">
        <fieldset>
            <legend><?= translate('Select a file')?></legend>
            <div class="form-group">

                <div class="k-file-input-container">
                    <div class="k-file-input">
                        <input class="k-js-file-input" id="file-input" data-multiple-caption="<?= translate('{count} files selected'); ?>" type="file" name="file" required  />
                        <label for="file-input">
                            <span class="k-file-input__button">
                                <span class="k-icon-cloud-upload" aria-hidden="true"></span>
                                <?= translate('Choose a file&hellip;'); ?>
                            </span>
                            <span class="k-file-input__files"></span>
                        </label>
                    </div>
                </div>

            </div>
        </fieldset>
    </form>

</div>

<script type="text/javascript">
    /*
     Originally written by By Osvaldas Valutis, www.osvaldas.info
     Adapted by Robin Poort, www.robinpoort.com
     Available for use under the MIT License
     */

    kQuery(function($) {
        console.log('test');
        ( function ( document, window, index )
        {
            var inputs = document.querySelectorAll('.k-js-file-input');
            Array.prototype.forEach.call( inputs, function( input )
            {
                var label	 = input.nextElementSibling,
                    labelVal = label.innerHTML;

                input.addEventListener('change', function( e )
                {
                    var fileName = '';
                    if( this.files && this.files.length > 1 )
                        fileName = ( this.getAttribute('data-multiple-caption') || '' ).replace( '{count}', this.files.length );
                    else
                        fileName = e.target.value.split( '\\' ).pop();

                    if( fileName )
                        label.querySelector('.k-file-input__files').innerHTML = fileName;
                    else
                        label.innerHTML = labelVal;
                });

                // Add class for drop hover
                input.ondragover = function(ev) { this.classList.add('has-drop-focus'); };
                input.ondragleave = function(ev) { this.classList.remove('has-drop-focus'); };
                input.ondragend = function(ev) { this.classList.remove('has-drop-focus'); };
                input.ondrop = function(ev) { this.classList.remove('has-drop-focus'); };

                // Firefox bug fix
                input.addEventListener('focus', function(){ input.classList.add('has-focus'); });
                input.addEventListener('blur', function(){ input.classList.remove('has-focus'); });
            });
        }( document, window, 0 ));
    });
</script>