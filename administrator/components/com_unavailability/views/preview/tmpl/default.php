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

<?php if(!empty($this->sidebar)) { ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
<?php } ?> 
<div id="j-main-container" class="span10">
	<?php if (empty($this->items)) { ?>
		<div class="alert alert-no-items">
			<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
	<?php } else { ?>
		<div class="preview">
			<?php foreach ($this->items as $i => $item) { ?>
				<div class="unavailability">
					<div class="unavailability_title">
						<?php echo $item->title; ?>
					</div>
					<div class="unavailability_element">
						<strong>
							<?php echo JText::_('COM_UNAVAILABILITY_FIELD_DTHR_INICIO_LABEL');?>
						</strong>
						<?php echo JHtml::_('date', $item->dthr_inicio, JText::_('DATE_FORMAT_LC4'));?>
					</div>
					<div class="unavailability_element">
						<strong>
							<?php echo JText::_('COM_UNAVAILABILITY_FIELD_DTHR_FINAL_LABEL');?>
						</strong>
						<?php echo JHtml::_('date', $item->dthr_final, JText::_('DATE_FORMAT_LC4'));?>
					</div>
					<div class="unavailability_element">
						<strong>
							<?php echo JText::_('COM_UNAVAILABILITY_FIELD_DTHR_EMISSAO_LABEL');?>
						</strong>
						<?php echo JHtml::_('date', $item->dthr_emissao, JText::_('DATE_FORMAT_LC4'));?>
					</div>	
					<div class="unavailability_element">
						<strong>
							<?php echo JText::_('COM_UNAVAILABILITY_FIELD_RESPONSAVEL_LABEL');?>
						</strong>
						<?php echo $item->responsavel;?>
					</div>	
					<div class="unavailability_element">
						<strong>
							<?php echo JText::_('COM_UNAVAILABILITY_FIELD_SISTEMAS_LABEL');?>
						</strong>
						<?php echo nl2br($item->sistemas);?>
					</div>																			
				</div>
			<?php } ?>
		</div>
	<?php } ?>
</div>