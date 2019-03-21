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

$localItens = $this->localItems;
$localPaginacao = $this->localPagination;

$params = $this->params;

$hoje=strtotime('now');
$paraSempre=strtotime('now +10 years');
$desdeSempre=strtotime('now -10 years');

$slide = 'slide-locais';
$active = 'group-locais';
?>

<?php echo JHtml::_('bootstrap.startAccordion', $slide, array('active' => $active)); ?>
	<?php if(count($localItens) > 0) { ?>
	
		<!-- ----------------------------------- BEGIN: LOCAIS -------------------------------------------------- -->
	
		<?php echo JHtml::_('bootstrap.addSlide', $slide, JText::_('COM_LISTING_FIELD_GROUP_PESSOALOCAIS'), 'group-locais'); ?>
		
		    <?php // separa as vigentes das historicas... 
			$vigentes = array();
			$historicas = array();
			foreach ($localItens as $lcl) { 					
				$dthr_inicio = $lcl->dthr_inicio ? strtotime($lcl->dthr_inicio) : $desdeSempre;
				$dthr_final = $lcl->dthr_final ? strtotime($lcl->dthr_final) : $paraSempre;
				
				if($dthr_inicio <= $hoje && $hoje <= $dthr_final) {
					$vigentes[] = $lcl;
				} else {
					$historicas[] = $lcl;
				}
			}
			?>	
							
			<?php if(count($vigentes) > 0) { ?>
					
				<!-- --------------------- BEGIN: LOCAIS VIGENTES DATA TABLE -------------------------------- -->
				
				<table class="table table-striped" id="comissoesVigentesList">			
			    
				<!-- --------------- BEGIN: LOCAIS VIGENTES TABLE HEADER ------------------- -->
			
				<thead>
					<tr>
						<td colspan="6" bgcolor="#99FF99">
							<div style="font-weight: bold;">
								<?php echo JText::_('COM_LISTING_FIELD_GROUP_VIGENTES');?>
							</div>
						</td>
					</tr>
					<tr>
						<th class="">
							<?php echo JText::_('COM_LISTING_FIELD_NOME_LABEL'); ?>
						</th>
						<th class="">
							<?php echo JText::_('COM_LISTING_FIELD_DESIGNACAO_NOME_LABEL'); ?>
						</th>
						<th style="width: 50px;" class="">
							<?php echo JText::_('COM_LISTING_FIELD_DTHR_INICIO_LABEL');?>
						</th>
						<th style="width: 50px;" class="">
							<?php echo JText::_('COM_LISTING_FIELD_DTHR_FINAL_LABEL'); ?>
						</th>
						<th class="">
							<?php echo JText::_('COM_LISTING_FIELD_DOCUMENTOS_LABEL'); ?>
						</th>
						<th class="">
							<?php echo JText::_('COM_LISTING_FIELD_COMENTARIOS_LABEL'); ?>
						</th>							
					</tr>
				</thead>
				
				<!-- -------------- END: LOCAIS VIGENTES TABLE HEADER ----------------------- -->
										
				<!-- ------------------------ BEGIN: LOCAIS VIGENTES TABLE BODY -------------------------------- -->
		
				<tbody>
				<?php foreach ($vigentes as $i => $vigente) {  ?>
					<tr class="row<?php echo $i % 2; ?>" sortable-group-id="1">
						<td class="">
							<?php echo $vigente->local ? $vigente->local->nome : 'Sem Nome'; ?>
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
							<?php if(property_exists($vigente, 'anexas')){ ?>
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
				
				<!-- ------------------------ END: LOCAIS VIGENTES TABLE BODY ----------------------------------- -->
				
				</table>
				
				<!-- --------------------- END: LOCAIS VIGENTES DATA TABLE ---------------------------------- -->
				
			<?php } ?>
						
			<?php if(count($historicas) > 0) { ?>
					
					<!-- --------------------- BEGIN: LOCAIS HISTORICAS DATA TABLE -------------------------------- -->
					
					<table class="table table-striped" id="comissoesHistoricasList">			
				    
					<!-- --------------- BEGIN: LOCAIS HISTORICAS TABLE HEADER ------------------- -->
				
					<thead>
						<tr>
							<td colspan="6" bgcolor="#FFFFCC">
								<div style="font-weight: bold;">
									<?php echo JText::_('COM_LISTING_FIELD_GROUP_HISTORICAS');?>
								</div>
							</td>
						</tr>					
						<tr>
							<th class="">
								<?php echo JText::_('COM_LISTING_FIELD_NOME_LABEL');?>
							</th>
							<th class="">
								<?php echo JText::_('COM_LISTING_FIELD_DESIGNACAO_NOME_LABEL');?>
							</th>
							<th style="width: 50px;" class="">
								<?php echo JText::_('COM_LISTING_FIELD_DTHR_INICIO_LABEL');?>
							</th>
							<th style="width: 50px;" class="">
								<?php echo JText::_('COM_LISTING_FIELD_DTHR_FINAL_LABEL'); ?>
							</th>									
							<th class="">
								<?php echo JText::_('COM_LISTING_FIELD_DOCUMENTOS_LABEL'); ?>
							</th>									
							<th class="">
								<?php echo JText::_('COM_LISTING_FIELD_COMENTARIOS_LABEL'); ?>
							</th>							
						</tr>
					</thead>
					
					<!-- -------------- END: LOCAIS HISTORICAS TABLE HEADER ----------------------- -->
					
					<!-- -------------------------- BEGIN: LOCAIS HISTORICAS TABLE BODY ----------------------------- -->
			
					<tbody>
					<?php foreach ($historicas as $i => $historica) {  ?>
						<tr class="row<?php echo $i % 2; ?>" sortable-group-id="1">
							<td class="">
								<?php echo $historica->local ? $historica->local->nome : 'Sem Nome'; ?>
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
								<?php if(property_exists($historica, 'anexas')){ ?>
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
					
					<!-- ------------------------- END: LOCAIS VIGENTES TABLE BODY ---------------------------------- -->
					
					</table>
					
					<!-- --------------------- END: LOCAIS VIGENTES DATA TABLE ---------------------------------- -->
				
			<?php } ?>	
		
			<!-- -------------- BEGIN: LOCAIS PAGINATION ----------------------- -->

			<div class="pagination">
				<?php echo ($localPaginacao ? $localPaginacao->getPagesLinks() : ''); ?>
			</div>

			<!-- -------------- END: LOCAIS PAGINATION ----------------------- -->	
		
		<?php echo JHtml::_('bootstrap.endSlide'); ?>					
			
		<!-- ----------------------------------- END: LOCAIS ---------------------------------------------------- -->
					
	<?php } ?>	
<?php echo JHtml::_('bootstrap.endAccordion'); ?>