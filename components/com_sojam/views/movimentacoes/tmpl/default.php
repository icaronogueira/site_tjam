<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_sojam
 * @author		Marcus Martinho (marcus.martinho@tjam.jus.br)
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('_JEXEC') or die;

JHtml::_('behavior.formvalidator');

JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "profile.cancel" || document.formvalidator.isValid(document.getElementById("profile-form")))
		{
			Joomla.submitform(task, document.getElementById("profile-form"));
		}
	};
');
?>

<form action="<?php echo JRoute::_('index.php?option=com_sojam&view=movimentacoes&layout=default');?>" 
	method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">	
	<div class="btn-toolbar">
		<div class="btn-group">
			<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('movimentacoes.buscar')">
				<i class="icon-new"></i><?php echo JText::_('COM_SOJAM_BUTTON_BUSCAR');?>
			</button>
		</div>
		<div class="btn-group">
			<button type="button" class="btn" onclick="Joomla.submitbutton('movimentacoes.cancelar')">
				<i class="icon-cancel"></i><?php echo JText::_('JCANCEL');?>
			</button>
		</div>
	</div>
	<fieldset>
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active'=>'details'));?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_SOJAM_MOVIMENTACOES', true)); ?>
				<?php foreach ($this->form->getFieldset('sojam_movimentacoes') as $field) { ?>	
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