<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_unavailability_validate
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$show_type = $params->get('show_type');

$item = count($list) > 0 ? $list[0] : null;

$logoPdf = JURI::root().'/modules/mod_unavailability_validate/media/img/logo_pdf.png';
$logoPrint = JURI::root().'/modules/mod_unavailability_validate/media/img/logo_print.png';

$detailsLnk = 
	$item 
	? 
		JRoute::_(
		'index.php?option=com_unavailability&view=unavailability&id='.
		(int)$item->id) 
	: 
	'#';

$previewLnk =
	$item
	?
	JRoute::_(
		'index.php?option=com_unavailability&view=unavailability&id='.
		(int)$item->id.'&tmpl=printfriendly&print=1&layout=default&page=')
	:
	'#';

$pdfLnk =
	$item
	?
	JRoute::_(
		'index.php?option=com_unavailability&view=unavailability&id='.
		(int)$item->id.'&tmpl=component&format=pdf')
	:
	'#';
?>

<!-- **************************** VALIDATION MESSAGE ******************************* -->

<?php if($item) { ?>
	<div style="color: green; padding-bottom: 5px;"><?php echo JText::_('MOD_UNAVAILABILITY_VALIDATE_MSG_VALID'); ?></div>
<?php } else { ?>
	<div style="color: red; padding-bottom: 5px;"><?php echo JText::_('MOD_UNAVAILABILITY_VALIDATE_MSG_INVALID'); ?></div>
<?php } ?>

<!-- ******************************* SHOW OPTIONS TYPE ****************************** -->

<?php if($show_type == 1) { // ...and the print preview link... ?>
	<?php if($item) { ?>
		<div style="padding-bottom: 5px;">
			<a href="<?php echo $previewLnk;?>" onclick="window.open(this.href,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;">
				<img alt="<?php echo JText::_('MOD_UNAVAILABILITY_VALIDATE_FIELD_SHOW_AS_PREVIEW');?>" 
					 src="<?php echo $logoPrint;?>"
				/>
				<?php echo $item->title; ?>
			</a>
		</div>
	<?php } ?>	
<?php } else if($show_type == 2) { // ...and the link to details... ?>
	<?php if($item) { ?>
		<div style="padding-bottom: 5px;">
			<a href="<?php echo $detailsLnk;?>">
				<?php echo $item->title; ?>
			</a>
		</div>
	<?php } ?>
<?php } else if($show_type == 3) { // ...and the PDF link...  ?>
	<?php if($item) { ?>
		<div style="padding-bottom: 5px;">
			<a href="<?php echo $pdfLnk;?>" onclick="window.open(this.href,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;">
				<img alt="<?php echo JText::_('MOD_UNAVAILABILITY_VALIDATE_FIELD_SHOW_AS_PDF');?>" 
				     src="<?php echo $logoPdf;?>"
				/>
				<?php echo $item->title; ?>
			</a>
		</div>
	<?php } ?>	
<?php } ?>

<form id="mod-unavailability-validate-form" action="<?php echo JRoute::_('index.php', true); ?>" method="post" class="form-inline">
	<div class="mod-unavailability<?php echo $params->get('moduleclass_sfx'); ?>">
		<button 
			type="submit" 
			tabindex="0" 
			name="Submit" 
			class="btn btn-primary">
			<?php echo JText::_('MOD_UNAVAILABILITY_VALIDATE_BACK_BUTTON_LABEL') ?>
		</button>
		<?php echo JHtml::_('form.token'); ?>
	</div>	
</form>