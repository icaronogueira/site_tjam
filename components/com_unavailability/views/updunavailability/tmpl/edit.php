<?php
/**
* @package     Joomla.Site
* @subpackage  com_unavailability
* @author		Marcus Martinho (marcus.martinho@tjam.jus.br)
*
* @copyright   Copyright (C) 2016 - 2021 Open Source Matters, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('_JEXEC') or die;

$tabDetailsLabel = 
	empty($this->item->id) 
	? 
	JText::_('COM_UNAVAILABILITY_NEW_UNAVAILABILITY', true) 
	: 
	JText::sprintf('COM_UNAVAILABILITY_EDIT_UNAVAILABILITY', $this->item->id, true);
?>

<form action="<?php echo JRoute::_('index.php?option=com_unavailability&view=updunavailability&layout=edit&id='.(int)$this->item->id);?>" 
	method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">	
	<div class="btn-toolbar">
		<div class="btn-group">
			<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('updunavailability.salvar')">
				<i class="icon-new"></i><?php echo JText::_('COM_UNAVAILABILITY_BUTTON_SAVE_AND_CLOSE');?>
			</button>
		</div>
		<div class="btn-group">
			<button type="button" class="btn" onclick="Joomla.submitbutton('updunavailability.aplicar')">
				<i class="icon-save"></i><?php echo JText::_('JSAVE');?>
			</button>
		</div>
		<div class="btn-group">
			<button type="button" class="btn" onclick="Joomla.submitbutton('updunavailability.cancelar')">
				<i class="icon-cancel"></i><?php echo JText::_('JCANCEL');?>
			</button>
		</div>
	</div>
	<fieldset>
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active'=>'details'));?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', $tabDetailsLabel); ?>
				<?php foreach ($this->form->getFieldset('unavailability_document') as $field) { ?>	
					<div class="control-group">
						<div class="control-label">
							<?php echo $field->label; ?>
						</div>
						<div class="controls">
							<?php echo $field->input; ?>
						</div>
					</div>
				<?php } ?>				
			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</fieldset>
	<input type="hidden" id="task" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form> 