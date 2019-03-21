<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_unavailability_validate
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<form id="mod-unavailability-validate-form" action="<?php echo JRoute::_('index.php', true); ?>" method="post" class="form-inline">
	<div class="mod-unavailability<?php echo $params->get('moduleclass_sfx'); ?>">
		<label for="mod-unavailability-validate-id">
			<?php echo JText::_('MOD_UNAVAILABILITY_VALIDATE_ID_LABEL') ?>
		</label>
		<input 
			id="mod-unavailability-validate-id" 
			type="text" 
			name="unavailability_id" 
			class="input-small" 
			tabindex="0" 
			size="18" 
			placeholder="<?php echo JText::_('MOD_UNAVAILABILITY_VALIDATE_ID_LABEL') ?>" />
		<button 
			type="submit" 
			tabindex="0" 
			name="Submit" 
			class="btn btn-primary">
			<?php echo JText::_('MOD_UNAVAILABILITY_VALIDATE_SUBMIT_BUTTON_LABEL') ?>
		</button>
		<?php echo JHtml::_('form.token'); ?>
	</div>	
</form>