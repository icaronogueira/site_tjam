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

// add this component libraries path to allow load library function to work...
JHtml::addIncludePath(JPATH_ADMINISTRATOR.
	'/components/com_unavailability/libraries/html');
// load library function component jquery ui classes into document head...
JHtml::_('unavailability.jquery.ui', array(
	'core','widget','position','mouse',
	'draggable','resizable','button', 'dialog'			
));
// load system_dialog events and configuration script into document head...
JFactory::getDocument()->addScript(JUri::base().
	'/components/com_unavailability/media/js/system_dialog.js');

$tabDetailsLabel = 
	empty($this->item->id) 
	? 
	JText::_('COM_UNAVAILABILITY_NEW_UNAVAILABILITY', true) 
	: 
	JText::sprintf('COM_UNAVAILABILITY_EDIT_UNAVAILABILITY', $this->item->id, true);	
?>

<?php
echo $this->loadTemplate('system_dialog'); // load edit_system_dialog into this view ?>

<form action="<?php echo JRoute::_('index.php?option=com_unavailability&layout=edit&id='.(int)$this->item->id);?>" 
	method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">	
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
							<?php if($field->fieldname == 'sistemas') {?>
								<a class="btn btn-primary button-select" title="<?php echo JText::_('COM_UNAVAILABILITY_BUTTON_NEW_SYSTEM_DESC'); ?>">
									<span id="new-system-button">
										<?php echo JText::_('COM_UNAVAILABILITY_BUTTON_NEW_SYSTEM_LABEL'); ?>
									</span>
								</a>
							<?php } ?>
						</div>
					</div>
				<?php } ?>				
			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</fieldset>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form> 