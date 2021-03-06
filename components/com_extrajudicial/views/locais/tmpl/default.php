<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_listing
 * @author		Marcus Martinho (marcus.martinho@tjam.jus.br)
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

JHtml::addIncludePath(JPATH_ROOT.'/administrator/components/com_extrajudicial/helpers/html'); // helpers do Backend

JHtml::_('behavior.framework');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$categoryids = $this->escape($this->state->get('list.categoryids'));
$sortFields = $this->getSortFields();
?>

<!-- -------------------------- BEGIN: ARTICLE STYLE TABLE OF CONTENTS ----------------------------------- -->

<div class="item-page" itemscope itemtype="https://schema.org/Article">
	
	<!-- ---------------------- BEGIN: PAGE HEADER ------------------- -->
	
	<div class="page-header">
		<h2 itemprop="headline">
			<?php echo $this->escape($this->params->get('page_title')); ?>
		</h2>
	</div>
	
	<!-- ---------------------- END: PAGE HEADER --------------------- -->
	
	<!-- ---------------------- BEGIN: GENERAL INFO --------------------->
	
	<dl class="article-info muted">
		<dt class="article-info-term"> 
			Detalhes
		</dt>
		<dd class="category-name">
			<!-- ---------------- BEGIN: TABLE OF CONTENTS ---------------- -->
			
			<div class="article-index">
				<ul class="nav nav-tabs nav-stacked">
					<li>
						Categorias			
					</li>	
					<?php foreach ($this->categories as $catitem) { ?>
						<li class="toclink">
							<?php echo $catitem->link; ?>
						</li>
					<?php } ?>
				</ul>
			</div>
			
			<!-- ---------------- END: TABLE OF CONTENTS ------------------ -->		
		</dd>
	</dl>
	
	<!-- ---------------------- END: GENERAL INFO ----------------------->
	
	<!-- ------------------------------------------ BEGIN: LOCAIS LIST ----------------------------------- -->
	
	<div itemprop="articlebody">
			
		<form action="<?php echo JRoute::_('index.php?option=com_extrajudicial&view=locais'); ?>" method="post" name="adminForm" id="adminForm">
			
			<!-- ---------------------------------------- BEGIN: SEARCH AND FILTERING ------------------------------------->
			
			<div id="filter-bar" class="btn-toolbar">
				
				<!-- --------------------------- BEGIN: SEARCH FIELDS AND BUTTONS ------------------- -->
				
				<div class="filter-search btn-group pull-left">
					<label for="filter_search" class="element-invisible"> 
						<?php echo JText::_('COM_EXTRAJUDICIAL_SEARCH_IN_TITLE');?>
					</label> 
					<input 
						type="text" 
						name="filter_search" 
						id="filter_search"
						placeholder="<?php echo JText::_('COM_EXTRAJUDICIAL_BUSCA_NO_NOME'); ?>"
						value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
						title="<?php echo JText::_('COM_EXTRAJUDICIAL_BUSCA_NO_NOME'); ?>" 
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
								
				<!-- ----------------------------- BEGIN: LIMIT, ORDER AND DIRECTION BOX ----------------- -->
				
				<div class="btn-group pull-left hidden-phone">
					<label for="limit" class="element-invisible"> 
						<?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?>
					</label>
					<?php echo $this->pagination->getLimitBox(); ?>
					<label for="directionTable" class="element-invisible"> 
						<?php echo JText::_('COM_EXTRAJUDICIAL_ORDER_DESC');?>
					</label> 
					<select 
						class="input-medium"
						name="directionTable" 
						id="directionTable" 
						onchange="Joomla.tableOrdering('<?php echo $listOrder; ?>', this.value);">
						<option value="">
							<?php echo JText::_('COM_EXTRAJUDICIAL_ORDER_DESC');?>
						</option>
						<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"';?>>
							<?php echo JText::_('COM_EXTRAJUDICIAL_ORDER_ASCENDING');?>
						</option>
						<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"';?>>
							<?php echo JText::_('COM_EXTRAJUDICIAL_ORDER_DESCENDING');?>
						</option>
					</select>
					<label for="sortTable" class="element-invisible"> 
						<?php echo JText::_('COM_EXTRAJUDICIAL_SORT_BY');?>
					</label> 
					<select 
						class="input-medium"
						name="sortTable" 
						id="sortTable" 
						onchange="Joomla.tableOrdering(this.value, '<?php echo $listDirn; ?>');">
						<option value="">
							<?php echo JText::_('COM_EXTRAJUDICIAL_SORT_BY');?>
						</option>
						<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
					</select>					
				</div>
				
				<!-- ----------------------------- END: LIMIT, ORDER AND DIRECTION BOX ------------------- -->
				
			</div>
		
			<!-- ---------------------------------------- END: SEARCH AND FILTERING --------------------------------------->
			
			<div class="clearfix"></div>
		
			<!-- ------------------------------------------------ BEGIN: DATA TABLE -------------------------------------------------- -->
			
			<table class="table table-striped" id="localList">
			
				<!-- ------------------------------- BEGIN: TABLE HEADER ------------------------------------- -->
			
				<thead>
					<tr>
						<th width="1%" class="nowrap center hidden-phone">
							<?php 
							echo JHtml::_('grid.sort', 
								'<i class="icon-menu-2"></i>', 'order', $listDirn, $listOrder, 
								null, 
								'asc', 
								'COM_EXTRAJUDICIAL_FIELD_ORDER_LABEL'
							); 
							?>
						</th>
						<th width="30%" class="">
							<?php 
							echo JHtml::_('grid.sort', 
								'COM_EXTRAJUDICIAL_FIELD_NOME_LABEL', 
								'nome', $listDirn, $listOrder
							); 
							?>
						</th>
						<th width="18%" class="nowrap center hidden-phone">
							<?php 
							echo JHtml::_('grid.sort', 
								'COM_EXTRAJUDICIAL_FIELD_APELIDO_LABEL', 
								'apelido', $listDirn, $listOrder
							); 
							?>
						</th>
						<th width="18%" class="hidden-phone">
							<?php 
							echo JHtml::_('grid.sort', 
								'COM_EXTRAJUDICIAL_FIELD_HORARIOS_LABEL', 
								'horarios', $listDirn, $listOrder
							); 
							?>
						</th>
						<th width="18%" class="center hidden-phone">
							<?php
							echo JHtml::_('grid.sort', 
								'COM_EXTRAJUDICIAL_FIELD_TELEFONES_LABEL', 
								'telefones', $listDirn, $listOrder
							); 
							?>
						</th>
						<th width="18%" class="nowrap center">
							<?php 
							echo JHtml::_('grid.sort', 
								'COM_EXTRAJUDICIAL_FIELD_ATIVO_LABEL', 
								'ativo', $listDirn, $listOrder
							); 
							?>
						</th>							
						<th width="1%" class="nowrap center hidden-phone">
							<?php 
							echo JHtml::_('grid.sort', 
								'JGRID_HEADING_ID', 
								'id', $listDirn, $listOrder
							); 
							?>
						</th>
					</tr>
				</thead>
				
				<!-- ------------------------------- END: TABLE HEADER --------------------------------------- -->
				
				<!-- ---------------- BEGIN: TABLE FOOTER --------------- -->
				
				<tfoot>
					<tr>
						<td colspan="12">
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
								<td class="order nowrap center hidden-phone">
									<span class="sortable-handler inactive" >
										<i class="icon-menu"></i>
									</span>
								</td>
								<td class="">
									<a href="<?php echo JRoute::_(
									   'index.php?option=com_extrajudicial&view=local&id='.
										(int)$item->id); ?>"
									>
										<?php echo $this->escape($item->nome); ?>
									</a>							
								</td>
								<td class="hidden-phone">
									<?php echo $this->escape($item->apelido);?>
								</td>
								<td class="hidden-phone">
									<?php echo $this->escape($item->horarios); ?>
								</td>
								<td class="hidden-phone">
									<?php echo $this->escape($item->telefones); ?>
								</td>
								<td class="nowrap center">
									<?php echo $item->ativo ? 'Sim' : 'Não'; ?>
								</td>												
								<td class="center hidden-phone">
									<?php echo (int)$item->id; ?>
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
								<?php echo JText::_('COM_EXTRAJUDICIAL_NO_ITENS_FOUND'); ?>
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
	
	</div>
	
	<!-- ------------------------------------------ END: LOCAIS LIST ------------------------------------- -->
	
</div>

<!-- -------------------------- END: ARTICLE STYLE TABLE OF CONTENTS ------------------------------------- -->