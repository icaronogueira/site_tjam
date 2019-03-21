<?php 
/**
 * @package     Joomla.Administrator
 * @subpackage  com_unavailability
 * @author		Marcus Martinho (marcus.martinho@tjam.jus.br)
 *
 * @copyright   Copyright (C) 2016 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

?>
<div id="system_dialog" title="<?php echo JText::_('COM_UNAVAILABILITY_DIALOG_SYSTEM_TITLE');?>">
	<?php echo JText::_('COM_UNAVAILABILITY_DIALOG_SYSTEM_DESC');?>	
	<form action="" id="system_dialog_form">
		<fieldset>
			<?php foreach ($this->systemForm->getFieldset('system_definition') as $field) { ?>
				<?php if( ($field->type != 'Editor') && $field->name != 'jform[id]' && $field->type != 'User') { ?>
					<div class="control-group">
						<div class="control-label">
							<?php echo $field->label; ?>
						</div>
						<div class="controls">
							<?php echo $field->input; ?>
						</div>
					</div>
				<?php } ?>
			<?php } ?>
			<?php echo JHtml::_('form.token'); ?>
		</fieldset>
		<a class="btn btn-primary button-select" title="<?php echo JText::_('COM_UNAVAILABILITY_BUTTON_NEW_SYSTEM_ADD_DESC'); ?>">
			<span id="new-system-add-button">
				<?php echo JText::_('COM_UNAVAILABILITY_BUTTON_NEW_SYSTEM_ADD_LABEL'); ?>
			</span>
		</a>
	</form>
</div>