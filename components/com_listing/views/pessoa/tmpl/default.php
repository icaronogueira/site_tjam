<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_listing
 * @author		Marcus Martinho (marcus.martinho@tjam.jus.br)
 *
 * @copyright   Copyright (C) 2016 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

jimport('joomla.html.html.bootstrap');
jimport('mmartinho.date.helper');

JHtml::_('bootstrap.framework');

$item = $this->items;

$localItens = $this->localItems;
$localPaginacao = $this->localPagination;

$atividadeItens = $this->atividadeItems;
$atividadePaginacao = $this->atividadePagination;

?>

<div class="item-page">
	<div class="page-header">
		<h2 itemprop="headline">
			<?php echo $this->escape($item->nome); ?>
			(ID: <?php echo $item->id; ?>)
		</h2>
	</div>

	<table>
	<tr>
	<td valign="top">		
		<dt class="article-info-term">
			<?php echo JText::_('COM_LISTING_FIELD_GROUP_SYSTEM'); ?>
		</dt>
		<dd class="createdby">
			<span style="font-weight: bold;"><?php echo JText::_('COM_LISTING_FIELD_ATIVO_LABEL');?>: </span>
			<span><?php echo $item->ativo ? 'Sim' : 'Não';?></span>
		</dd>
		<?php if($item->create_time) {?>
			<dd class="published">
				<span style="font-weight: bold;"><?php echo JText::_('COM_LISTING_FIELD_CREATE_TIME_LABEL'); ?>: </span> 
				<span><?php echo MMDateHelper::formataDataHrBrExtenso($item->create_time);?></span>
			</dd>
		<?php } ?>
		<?php if($item->titulo) { ?> 
			<dd class="published">
				<span style="font-weight: bold;"><?php echo JText::_('COM_LISTING_FIELD_TITULO_LABEL'); ?>: </span> 
				<span><?php echo $item->titulo ? $item->titulo : '-'; ?></span>
			</dd>
		<?php } ?>
	</td>
	<td valign="top">	
		<?php if($item->observacoes) {?>
			<dd class="published">
				<div style="font-weight: bold;"><?php echo JText::_('COM_LISTING_FIELD_COMENTARIOS_LABEL'); ?>: </div>
				<div><?php echo nl2br($item->observacoes); ?></div>
			</dd>
		<?php } ?>
	</td>
	</tr>
	</table>
	<br />
	<div itemprop="articleBody">	
		<?php echo $this->loadTemplate('comissoes');?>
		<?php echo $this->loadTemplate('locais');?>
		<?php echo $this->loadTemplate('atividades');?>
	</div>
</div>