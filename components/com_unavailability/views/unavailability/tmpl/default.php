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

jimport('joomla.html.html.bootstrap');

JHtml::_('bootstrap.framework');

$item = $this->items[0];
$params = $this->params;
?>

<div class="item-page">
	<div class="page-header">
		<h2 itemprop="headline">
			<?php echo $this->escape($item->title); ?>
		</h2>
	</div>
	<?php echo JHtml::_('icon.icons', $item, $params);?>
	<dl class="article-info muted">
		<dt class="article-info-term">
			<?php echo JText::_('COM_UNAVAILABILITY_FIELD_GROUP_SYSTEM'); ?>
		</dt>
		<dd class="createdby">
			<?php echo JText::_('COM_UNAVAILABILITY_FIELD_CREATED_BY_LABEL');?>: <?php echo $item->user_name;?>
		</dd>
		<dd class="category-name">
			Categoria: <?php echo $item->category_title;?>
		</dd>
		<dd class="published">
			Publicado: <?php echo JHtml::_('date', $item->publish_up, JText::_('DATE_TIME_FORMAT'));?>
		</dd>
	</dl>
	<div itemprop="articleBody">
		<?php echo JHtml::_('bootstrap.startAccordion', 'slide-unavailability', array('active' => 'group-basic')); ?>
			<?php if($item->id || $item->responsavel || $item->dthr_emissao) {?>
				<?php echo JHtml::_('bootstrap.addSlide', 'slide-unavailability', JText::_('COM_UNAVAILABILITY_FIELD_GROUP_BASIC'), 'group-basic'); ?>
					<?php if($item->id) {?>
						<span style="font-weight: bold;"><?php echo JText::_('COM_UNAVAILABILITY_FIELD_ID_LABEL'); ?></span> 
						<span><?php echo $item->id; ?></span><br />
					<?php } ?>
					
					<?php if($item->responsavel) {?>
						<span style="font-weight: bold;"><?php echo JText::_('COM_UNAVAILABILITY_FIELD_RESPONSAVEL_LABEL'); ?></span>
						<span><?php echo $item->responsavel; ?></span><br />
					<?php } ?>
					
					<?php if($item->dthr_emissao) {?>
						<span style="font-weight: bold;"><?php echo JText::_('COM_UNAVAILABILITY_FIELD_DTHR_EMISSAO_LABEL'); ?></span>
						<span><?php echo JHtml::_('date', $item->dthr_emissao, JText::_('DATE_TIME_FORMAT')); ?></span>
					<?php } ?>
				<?php echo JHtml::_('bootstrap.endSlide'); ?>
			<?php } ?>
			<?php if($this->sistemas) {?>
				<?php echo JHtml::_('bootstrap.addSlide', 'slide-unavailability', JText::_('COM_UNAVAILABILITY_FIELD_GROUP_AFECTED_SYSTEMS'), 'group-afected-systems'); ?>	
					<div style="padding-left: 10px;">
						<?php foreach ($this->sistemas as $sistema) { ; ?>
								<?php echo $sistema;?><br />
						<?php } ?>
					</div>
				<?php echo JHtml::_('bootstrap.endSlide'); ?>	
			<?php } ?>
			<?php if($item->dthr_inicio || $item->dthr_final) {?>	
				<?php echo JHtml::_('bootstrap.addSlide', 'slide-unavailability', JText::_('COM_UNAVAILABILITY_FIELD_GROUP_EXPIRATION'), 'group-expiration'); ?>
					<?php if($item->dthr_inicio) {?>
						<span style="font-weight: bold;"><?php echo JText::_('COM_UNAVAILABILITY_FIELD_DTHR_INICIO_LABEL'); ?></span>: 
						<span><?php echo JHtml::_('date', $item->dthr_inicio, JText::_('DATE_TIME_FORMAT')); ?></span><br />
					<?php } ?>
					
					<?php if($item->dthr_final) {?>
						<span style="font-weight: bold;"><?php echo JText::_('COM_UNAVAILABILITY_FIELD_DTHR_FINAL_LABEL'); ?></span>: 
						<span><?php echo JHtml::_('date', $item->dthr_final, JText::_('DATE_TIME_FORMAT')); ?></span><br />
					<?php } ?>
				<?php echo JHtml::_('bootstrap.endSlide'); ?>
			<?php } ?>
			<?php if($item->detalhes) {?>
				<?php echo JHtml::_('bootstrap.addSlide', 'slide-unavailability', JText::_('COM_UNAVAILABILITY_FIELD_GROUP_DETAILS'), 'group-details'); ?>								
					<div style="padding-left: 10px;"><?php echo $item->detalhes; ?></div>		
				<?php echo JHtml::_('bootstrap.endSlide'); ?>
			<?php } ?>	
		<?php echo JHtml::_('bootstrap.endAccordion'); ?>
	</div>
</div>