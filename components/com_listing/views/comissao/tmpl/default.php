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
$nmcItens = $this->nomeacoesItems;
$nmcPaginacao = $this->nomeacoesPagination;
$params = $this->params;

$hoje=strtotime('now');
$paraSempre=strtotime('now +10 years');
$desdeSempre=strtotime('now -10 years');

$active = 'group-nomeacoes-comissao';

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
		<?php if(count($item->categoriascomissao) > 0) {?>
			<dd class="category-name">
				<div style="font-weight: bold;"><?php echo JText::_('COM_LISTING_FIELD_CATEGORIAS_LABEL');?>: </div> 
				<ul>
					<?php 
					foreach ($item->categoriascomissao as $cc) { 
						echo '<li>'.$cc->categoria->nome.'</li>';
					}
					?>
				</ul>
			</dd>
		<?php } ?>
		<?php if($item->create_time) {?>
			<dd class="published">
				<span style="font-weight: bold;"><?php echo JText::_('COM_LISTING_FIELD_CREATE_TIME_LABEL'); ?>: </span> 
				<span><?php echo MMDateHelper::formataDataHrBrExtenso($item->create_time);?></span>
			</dd>
		<?php } ?>
		<?php if($item->dthr_inicio || $item->dthr_final) { ?> 
			<dt class="article-info-term">
				<?php echo JText::_('COM_LISTING_FIELD_GROUP_VIGENCIA'); ?>
			</dt>
			<dd class="published">
				<span style="font-weight: bold;"><?php echo JText::_('COM_LISTING_FIELD_DTHR_INICIO_LABEL'); ?>: </span> 
				<span><?php echo $item->dthr_inicio ? MMDateHelper::formataDataBr($item->dthr_inicio) : '-'; ?></span>
			</dd>
			<dd class="published">
				<span style="font-weight: bold;"><?php echo JText::_('COM_LISTING_FIELD_DTHR_FINAL_LABEL'); ?>: </span>
				<span><?php echo $item->dthr_final ? MMDateHelper::formataDataBr($item->dthr_final) : '-'; ?></span>
			</dd>
		<?php } ?>
	</td>
	<td valign="top">	
		<?php if(property_exists($item,'comentarios') || property_exists($item, 'anexas')) {?>
			<dt class="article-info-term">
				<?php echo JText::_('COM_LISTING_FIELD_GROUP_OUTROS'); ?>
			</dt>	
			<?php if($item->comentarios) {?>
				<dd class="published">
					<div style="font-weight: bold;"><?php echo JText::_('COM_LISTING_FIELD_COMENTARIOS_LABEL'); ?>: </div>
					<div><?php echo nl2br($item->comentarios); ?></div>
				</dd>
			<?php } ?>
			<?php if(property_exists($item, 'anexas')) { ?>
				<dd class="published">
					<div style="font-weight: bold;"><?php echo JText::_('COM_LISTING_FIELD_DOCUMENTOS_LABEL'); ?>: </div>
		      		<?php foreach ($item->anexas as $anexo) { ?>
		      			<?php echo $anexo->lnk_doc; ?>
		      		<?php } ?>
	      		</dd>
        	<?php } ?>	
        <?php } ?>
	</td>
	</tr>
	</table>
	<br />
	<div itemprop="articleBody">
		<?php echo JHtml::_('bootstrap.startAccordion', 'slide-comissao', array('active' => $active)); ?>
			<?php if(count($nmcItens) > 0) { ?>
			
				<!-- ----------------------------------- BEGIN: NOMEACOES AO COMISSAO -------------------------------------------------- -->
			
				<?php echo JHtml::_('bootstrap.addSlide', 'slide-comissao', JText::_('COM_LISTING_FIELD_GROUP_NOMEACOESCOMISSAO'), 'group-nomeacoes-comissao'); ?>
				
				    <?php // separa as nomeações vigentes das historicas... 
					$vigentes = array();
					$historicas = array();
					foreach ($nmcItens as $nl) { 					
						$dthr_inicio = $nl->dthr_inicio ? strtotime($nl->dthr_inicio) : $desdeSempre;
						$dthr_final = $nl->dthr_final ? strtotime($nl->dthr_final) : $paraSempre;
						
						if($dthr_inicio <= $hoje && $hoje <= $dthr_final) {
							$vigentes[] = $nl;
						} else {
							$historicas[] = $nl;
						}
					}
					?>	
									
					<?php if(count($vigentes) > 0) { ?>
							
						<!-- --------------------- BEGIN: VIGENTES DATA TABLE -------------------------------- -->
						
						<table class="table table-striped" id="nomeacoesVigentesList">			
					    
						<!-- --------------- BEGIN: VIGENTES TABLE HEADER ------------------- -->
					
						<thead>
							<tr>
								<td colspan="7" bgcolor="#99FF99">
									<div style="font-weight: bold;">
										<?php echo JText::_('COM_LISTING_FIELD_GROUP_VIGENTES');?>
									</div>
								</td>
							</tr>
							<tr>
								<th width="20%" class="">
									<?php echo JText::_('COM_LISTING_FIELD_PESSOA_LABEL'); ?>
								</th>
								<th width="10%" class="">
									<?php echo JText::_('COM_LISTING_FIELD_DESIGNACAO_NOME_LABEL'); ?>
								</th>
								<th width="15%" class="">
									<?php echo JText::_('COM_LISTING_FIELD_DTHR_INICIO_LABEL');?>
								</th>
								<th width="15%" class="">
									<?php echo JText::_('COM_LISTING_FIELD_DTHR_FINAL_LABEL'); ?>
								</th>
								<th width="10%" class="">
									<?php echo JText::_('COM_LISTING_FIELD_COMONUS_LABEL'); ?>
								</th>
								<th width="10%" class="">
									<?php echo JText::_('COM_LISTING_FIELD_DOCUMENTOS_LABEL'); ?>
								</th>
								<th width="20%" class="">
									<?php echo JText::_('COM_LISTING_FIELD_COMENTARIOS_LABEL'); ?>
								</th>							
							</tr>
						</thead>
						
						<!-- -------------- END: VIGENTES TABLE HEADER ----------------------- -->
												
						<!-- ------------------------ BEGIN: VIGENTES TABLE BODY -------------------------------- -->
				
						<tbody>
						<?php foreach ($vigentes as $i => $vigente) {  ?>
							<tr class="row<?php echo $i % 2; ?>" sortable-group-id="1">
								<td class="">
									<?php echo $vigente->pessoa ? $vigente->pessoa->nome : 'Anônimo'; ?>							
								</td>
								<td class="">
									<?php echo $vigente->nome; ?>
								</td>
								<td class="">
									<?php echo $vigente->dthr_inicio ? MMDateHelper::formataDataBr($vigente->dthr_inicio) : '-'; ?>
								</td>
								<td class="">
									<?php echo $vigente->dthr_final ? MMDateHelper::formataDataBr($vigente->dthr_final) : '-'; ?>
								</td>
								<td class="">
									<?php echo $vigente->comonus ? 'Sim' : 'Não';?>
								</td>
								<td class="">
									<?php if(property_exists($vigente, 'anexas')) { ?>
							      		<?php foreach ($vigente->anexas as $anexo) { ?>
							      			<?php echo $anexo->lnk_doc; ?>
							      		<?php } ?>
							        <?php } ?>
								</td>
								<td class="">
									<?php echo nl2br($vigente->comentarios); ?>
								</td>
							</tr>
						<?php } ?>
						</tbody>
						
						<!-- ------------------------ END: VIGENTES TABLE BODY ----------------------------------- -->
						
						</table>
						
						<!-- --------------------- END: VIGENTES DATA TABLE ---------------------------------- -->
						
					<?php } ?>
								
					<?php if(count($historicas) > 0) { ?>
							
							<!-- --------------------- BEGIN: HISTORICAS DATA TABLE -------------------------------- -->
							
							<table class="table table-striped" id="nomeacoesHistoricasList">			
						    
							<!-- --------------- BEGIN: HISTORICAS TABLE HEADER ------------------- -->
						
							<thead>
								<tr>
									<td colspan="7" bgcolor="#FFFFCC">
										<div style="font-weight: bold;">
											<?php echo JText::_('COM_LISTING_FIELD_GROUP_HISTORICAS');?>
										</div>
									</td>
								</tr>					
								<tr>
									<th width="20%" class="">
										<?php echo JText::_('COM_LISTING_FIELD_PESSOA_LABEL'); ?>
									</th>
									<th width="10%" class="">
										<?php echo JText::_('COM_LISTING_FIELD_DESIGNACAO_NOME_LABEL');?>
									</th>
									<th width="15%" class="">
										<?php echo JText::_('COM_LISTING_FIELD_DTHR_INICIO_LABEL');?>
									</th>
									<th width="15%" class="">
										<?php echo JText::_('COM_LISTING_FIELD_DTHR_FINAL_LABEL'); ?>
									</th>
									<th width="10%" class="">
										<?php echo JText::_('COM_LISTING_FIELD_COMONUS_LABEL'); ?>
									</th>									
									<th width="10%" class="">
										<?php echo JText::_('COM_LISTING_FIELD_DOCUMENTOS_LABEL'); ?>
									</th>									
									<th width="20%" class="">
										<?php echo JText::_('COM_LISTING_FIELD_COMENTARIOS_LABEL'); ?>
									</th>							
								</tr>
							</thead>
							
							<!-- -------------- END: HISTORICAS TABLE HEADER ----------------------- -->
							
							<!-- -------------------------- BEGIN: HISTORICAS TABLE BODY ----------------------------- -->
					
							<tbody>
							<?php foreach ($historicas as $i => $historica) {  ?>
								<tr class="row<?php echo $i % 2; ?>" sortable-group-id="1">
									<td class="">
										<?php echo $historica->pessoa ? $historica->pessoa->nome : 'Anônimo'; ?>							
									</td>
									<td class="">
										<?php echo $historica->nome; ?>
									</td>
									<td class="">
										<?php echo $historica->dthr_inicio ? MMDateHelper::formataDataBr($historica->dthr_inicio) : '-'; ?>
									</td>
									<td class="">
										<?php echo $historica->dthr_final ? MMDateHelper::formataDataBr($historica->dthr_final) : '-'; ?>
									</td>
									<td class="">
										<?php echo $historica->comonus ? 'Sim' : 'Não';?>
									</td>									
									<td class="">
										<?php if(property_exists($historica, 'anexas')) { ?>
								      		<?php foreach ($historica->anexas as $anexo) { ?>
								      			<?php echo $anexo->lnk_doc; ?>
								      		<?php } ?>
								        <?php } ?>									
									</td>
									<td class="">
										<?php echo nl2br($historica->comentarios); ?>
									</td>
								</tr>
							<?php } ?>
							</tbody>
							
							<!-- ------------------------- END: VIGENTES TABLE BODY ---------------------------------- -->
							
							</table>
							
							<!-- --------------------- END: VIGENTES DATA TABLE ---------------------------------- -->
						
					<?php } ?>	
				
					<!-- -------------- BEGIN: NOMEACOES COMISSAO PAGINATION ----------------------- -->
	
					<div class="pagination">
						<?php echo ($nmcPaginacao ? $nmcPaginacao->getPagesLinks() : ''); ?>
					</div>
	
					<!-- -------------- END: NOMEACOES COMISSAO PAGINATION ----------------------- -->	
				
				<?php echo JHtml::_('bootstrap.endSlide'); ?>					
					
				<!-- ----------------------------------- END: NOMEACOES A COMISSAO ---------------------------------------------------- -->
							
			<?php } ?>	
		<?php echo JHtml::_('bootstrap.endAccordion'); ?>
	</div>
</div>