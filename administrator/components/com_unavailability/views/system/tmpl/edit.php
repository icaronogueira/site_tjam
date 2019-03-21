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

$tabDetailsLabel = 
	empty($this->item->id) 
	? 
	JText::_('COM_UNAVAILABILITY_NEW_SYSTEM', true) 
	: 
	JText::sprintf('COM_UNAVAILABILITY_EDIT_SYSTEM', $this->item->id, true);
?>

<form action="<?php echo JRoute::_('index.php?option=com_unavailability&layout=edit&id='.(int)$this->item->id);?>" 
	method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">	
	<fieldset>
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active'=>'details'));?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', $tabDetailsLabel); ?>
				<?php foreach ($this->form->getFieldset('system_definition') as $field) { ?>
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
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form> 