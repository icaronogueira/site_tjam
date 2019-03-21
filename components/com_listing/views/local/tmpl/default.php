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

$active = 'group-nomeacoes-local';

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
		<?php if(count($item->categoriaslocal) > 0) {?>
			<dd class="category-name">
				<div style="font-weight: bold;"><?php echo JText::_('COM_LISTING_FIELD_CATEGORIAS_LABEL');?>: </div>  
				<ul>
				<?php 
				foreach ($item->categoriaslocal as $cl) { 
					echo '<li>'.$cl->categoria->nome.'</li>';
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
	</td>
	<td>
		<?php if($item->localizacao || $item->endereco || $item->cep || $item->telefones) {?>
			<dt class="article-info-term">
				<?php echo JText::_('COM_LISTING_FIELD_GROUP_OUTROS'); ?>
			</dt>
			<?php if($item->localizacao) {?>
				<dd class="published">
					<span style="font-weight: bold;"><?php echo JText::_('COM_LISTING_FIELD_LOCALIZACAO_LABEL'); ?>: </span>
					<span><?php echo $item->localizacao; ?></span>
				</dd>
			<?php } ?>			
			<?php if($item->endereco) {?>
				<dd class="published">
					<div style="font-weight: bold;"><?php echo JText::_('COM_LISTING_FIELD_ENDERECO_LABEL'); ?>: </div>
					<div><?php echo nl2br($item->endereco); ?></div>
				</dd>
			<?php } ?>
			<?php if($item->cep) {?>
				<dd class="published">
					<span style="font-weight: bold;"><?php echo JText::_('COM_LISTING_FIELD_CEP_LABEL'); ?>: </span>
					<span><?php echo $item->cep; ?></span>
				</dd>
			<?php } ?>
			<?php if($item->telefones) {?>
				<dd class="published">
					<div style="font-weight: bold;"><?php echo JText::_('COM_LISTING_FIELD_TELEFONES_LABEL'); ?>: </div>
					<div><?php echo nl2br($item->telefones); ?></div>
				</dd>
			<?php } ?>
			<?php if($item->emails) {?>
				<dd class="published">
					<div style="font-weight: bold;"><?php echo JText::_('COM_LISTING_FIELD_EMAILS_LABEL'); ?>: </div>
					<div><?php echo nl2br($item->emails); ?></div>
				</dd>
			<?php } ?>
		<?php } ?>
	</td>
	</tr>
	</table>
	<br />
	<div itemprop="articleBody">
		<?php echo JHtml::_('bootstrap.startAccordion', 'slide-local', array('active' => $active)); ?>
			<?php if(count($nmcItens) > 0) { ?>
			
				<!-- ----------------------------------- BEGIN: NOMEACOES AO LOCAL -------------------------------------------------- -->
			
				<?php echo JHtml::_('bootstrap.addSlide', 'slide-local', JText::_('COM_LISTING_FIELD_GROUP_NOMEACOESLOCAL'), 'group-nomeacoes-local'); ?>
				
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
								<td colspan="6" bgcolor="#99FF99">
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
								<th width="10%" class="">
									<?php echo JText::_('COM_LISTING_FIELD_DTHR_INICIO_LABEL');?>
								</th>
								<th width="10%" class="">
									<?php echo JText::_('COM_LISTING_FIELD_DTHR_FINAL_LABEL'); ?>
								</th>
								<th width="20%" class="">
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
									<td colspan="6" bgcolor="#FFFFCC">
										<div style="font-weight: bold;">
											<?php echo JText::_('COM_LISTING_FIELD_GROUP_HISTORICAS');?>
										</div>
									</td>
								</tr>					
								<tr>
									<th width="30%" class="">
										<?php echo JText::_('COM_LISTING_FIELD_PESSOA_LABEL'); ?>
									</th>
									<th width="18%" class="">
										<?php echo JText::_('COM_LISTING_FIELD_DESIGNACAO_NOME_LABEL');?>
									</th>
									<th width="10%" class="">
										<?php echo JText::_('COM_LISTING_FIELD_DTHR_INICIO_LABEL');?>
									</th>
									<th width="10%" class="">
										<?php echo JText::_('COM_LISTING_FIELD_DTHR_FINAL_LABEL'); ?>
									</th>
									<th width="20%" class="">
										<?php echo JText::_('COM_LISTING_FIELD_DOCUMENTOS_LABEL'); ?>
									</th>									
									<th width="30%" class="">
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
				
					<!-- -------------- BEGIN: NOMEACOES LOCAL PAGINATION ----------------------- -->
	
					<div class="pagination">
						<?php echo ($nmcPaginacao ? $nmcPaginacao->getPagesLinks() : ''); ?>
					</div>
	
					<!-- -------------- END: NOMEACOES LOCAL PAGINATION ----------------------- -->	
				
				<?php echo JHtml::_('bootstrap.endSlide'); ?>					
					
				<!-- ----------------------------------- END: NOMEACOES AO LOCAL ---------------------------------------------------- -->
							
			<?php } ?>	
		<?php echo JHtml::_('bootstrap.endAccordion'); ?>
	</div>
</div>