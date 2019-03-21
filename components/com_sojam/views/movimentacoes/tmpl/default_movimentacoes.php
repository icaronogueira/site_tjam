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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

//JHtml::addIncludePath(JPATH_ROOT.'/administrator/components/com_sojam/helpers/html'); // helpers do Backend

JHtml::_('behavior.framework');

jimport('mmartinho.date.helper');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$categoryids = $this->escape($this->state->get('list.categoryids'));
$sortFields = $this->getSortFields();
?>

<h3>MANIFESTAÇÃO Nº <?php echo $this->manifestacao->id;?></h3>

Manifestação de <b><?php echo $this->manifestacao->tipo->nome;?></b> 
feita por <b><?php echo $this->manifestacao->nome;?> 
(CPF: <?php echo $this->manifestacao->cpf;?>)</b> 
via <b><?php echo $this->manifestacao->origem->nome;?></b> 
em <b><?php echo MMDateHelper::formataDataBr($this->manifestacao->create_time);?></b>.

<br />

<h3>MOVIMENTAÇÕES</h3>

<form action="<?php echo JRoute::_('index.php?option=com_sojam&view=movimentacoes'); ?>" method="post" name="adminForm" id="adminForm">
	
	<!-- ---------------------------------------- BEGIN: SEARCH AND FILTERING ------------------------------------->
	
	<div id="filter-bar" class="btn-toolbar">
		
		<!-- --------------------------- BEGIN: SEARCH FIELDS AND BUTTONS ------------------- -->
		
		<div class="filter-search btn-group pull-left">
			<label for="filter_search" class="element-invisible"> 
				<?php echo JText::_('COM_SOJAM_SEARCH_IN_TITLE');?>
			</label> 
			<input 
				type="text" 
				name="filter_search" 
				id="filter_search"
				placeholder="<?php echo JText::_('COM_SOJAM_BUSCA_NO_NOME'); ?>"
				value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
				title="<?php echo JText::_('COM_SOJAM_BUSCA_NO_NOME'); ?>" 
			/>
		</div>
		<div class="btn-group pull-left">
			<button 
				class="btn hasTooltip" 
				type="button"
				title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"
				onclick="Joomla.submitform(this.form);">
				<i class="icon-search"></i>
			</button>
			<button 
				class="btn hasTooltip" 
				type="button"
				title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"
				onclick="document.id('filter_search').value='';Joomla.submitform(this.form);">
				<i class="icon-remove"></i>
			</button>
		</div>
		
		<!-- --------------------------- END: SEARCH FIELDS AND BUTTONS --------------------- -->
		
		<!-- --------------- BEGIN: PAGINATION LIMIT BOX  ----------------- -->
		
		<div class="btn-group pull-right hidden-phone">
			<label for="limit" class="element-invisible"> 
				<?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?>
			</label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
		
		<!-- --------------- END: PAGINATION LIMIT BOX  ------------------- -->
		
		<!-- ----------------------------- BEGIN: ORDER DIRECTION BOX ------------------------ -->
		
		<div class="btn-group pull-right hidden-phone">
			<label for="directionTable" class="element-invisible"> 
				<?php echo JText::_('COM_SOJAM_ORDER_DESC');?>
			</label> 
			<select 
				class="input-medium"
				name="directionTable" 
				id="directionTable" 
				onchange="Joomla.tableOrdering('<?php echo $listOrder; ?>', this.value);">
				<option value="">
					<?php echo JText::_('COM_SOJAM_ORDER_DESC');?>
				</option>
				<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"';?>>
					<?php echo JText::_('COM_SOJAM_ORDER_ASCENDING');?>
				</option>
				<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"';?>>
					<?php echo JText::_('COM_SOJAM_ORDER_DESCENDING');?>
				</option>
			</select>
		</div>
		
		<!-- ----------------------------- END: ORDER DIRECTION BOX -------------------------- -->
		
		<!-- ------------------------------- BEGIN: SORT FIELDS OPTIONS ------------------------ -->
		
		<div class="btn-group pull-right">
			<label for="sortTable" class="element-invisible"> 
				<?php echo JText::_('COM_SOJAM_SORT_BY');?>
			</label> 
			<select 
				class="input-medium"
				name="sortTable" 
				id="sortTable" 
				onchange="Joomla.tableOrdering(this.value, '<?php echo $listDirn; ?>');">
				<option value="">
					<?php echo JText::_('COM_SOJAM_SORT_BY');?>
				</option>
				<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
			</select>
		</div>
		
		<!-- ------------------------------- END: SORT FIELDS OPTIONS -------------------------- -->
		
	</div>

	<!-- ---------------------------------------- END: SEARCH AND FILTERING --------------------------------------->
	
	<div class="clearfix"></div>

	<!-- ------------------------------------------------ BEGIN: DATA TABLE -------------------------------------------------- -->
	
	<table class="table table-striped" id="localList">
	
		<!-- ------------------------------- BEGIN: TABLE HEADER ------------------------------------- -->
	
		<thead>
			<tr>
				<th width="20%" class="">
					<?php 
					echo JHtml::_('grid.sort', 
						'COM_SOJAM_FIELD_MOVIMENTACAO_STATUS_LABEL', 
						'status', $listDirn, $listOrder
					); 
					?>
				</th>
				<th width="20%" class="nowrap center">
					<?php 
					echo JHtml::_('grid.sort', 
						'COM_SOJAM_FIELD_MOVIMENTACAO_DTHR_AGENDAMENTO_LABEL', 
						'dthr_agendamento', $listDirn, $listOrder
					); 
					?>
				</th>
				<th width="20%" class="nowrap center">
					<?php 
					echo JHtml::_('grid.sort', 
						'COM_SOJAM_FIELD_MOVIMENTACAO_RESPONSAVEL_LABEL', 
						'responsavel', $listDirn, $listOrder
					); 
					?>
				</th>
				<th width="40%" class="nowrap center">
					<?php 
					echo JHtml::_('grid.sort', 
						'COM_SOJAM_FIELD_MOVIMENTACAO_TXT_CIDADAO_LABEL', 
						'txt_cidadao', $listDirn, $listOrder
					); 
					?>
				</th>				
			</tr>
		</thead>
		
		<!-- ------------------------------- END: TABLE HEADER --------------------------------------- -->
		
		<!-- ---------------- BEGIN: TABLE FOOTER --------------- -->
		
		<tfoot>
			<tr>
				<td colspan="6">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		
		<!-- ---------------- END: TABLE FOOTER ----------------- -->
		
		<!-- --------------------------------------- BEGIN: TABLE BODY ---------------------------------------- -->
		
		<tbody>
			<?php 
			if(count($this->items) > 0) { 
			?>
				<?php 
				foreach ($this->items as $i => $item) { 
				?>
					<tr class="row<?php echo $i % 2; ?>" sortable-group-id="1">
						<td class="nowrap center">
							<?php echo SojamModelMovimentacoes::getTextoEstado($item->status);?>
						</td>
						<td class="nowrap center">
							<?php echo $item->dthr_agendamento ? MMDateHelper::formataDataBr($item->dthr_agendamento) : '-'; ?>
						</td>
						<td class="nowrap center">
							<?php echo $item->responsavel; ?>
						</td>												
						<td class="nowrap center hidden-phone">
							<?php echo $item->txt_cidadao; ?>
						</td>
					</tr>
				<?php 
				} 
				?>
			<?php 
			}  else {
			?>
				<tr class="row1">
					<td colspan="7" class="center">
						<?php echo JText::_('COM_SOJAM_NO_ITENS_FOUND'); ?>
					</td>
				</tr>
			<?php 
			}
			?>
		</tbody>
		
		<!-- --------------------------------------- END: TABLE BODY ------------------------------------------ -->
		
	</table>
	
	<!-- ------------------------------------------------ END: DATA TABLE ---------------------------------------------------- -->

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="catid" value="<?php echo $catid; ?>" /> 
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" /> 
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>