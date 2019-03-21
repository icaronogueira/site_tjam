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

$atividadeItens = $this->atividadeItems;
$atividadePaginacao = $this->atividadePagination;

$params = $this->params;

$hoje=strtotime('now');
$paraSempre=strtotime('now +10 years');
$desdeSempre=strtotime('now -10 years');

$slide = 'slide-atividades';
$active = 'group-atividades';
?>

<?php echo JHtml::_('bootstrap.startAccordion', $slide, array('active' => $active)); ?>
	<?php if(count($atividadeItens) > 0) { ?>
	
		<!-- ----------------------------------- BEGIN: ATIVIDADES -------------------------------------------------- -->
	
		<?php echo JHtml::_('bootstrap.addSlide', $slide, JText::_('COM_LISTING_FIELD_GROUP_PESSOAATIVIDADES'), 'group-atividades'); ?>
		
		    <?php // separa as vigentes das historicas... 
			$vigentes = array();
			$historicas = array();
			foreach ($atividadeItens as $atv) { 					
				$dthr_inicio = $atv->dthr_inicio ? strtotime($atv->dthr_inicio) : $desdeSempre;
				$dthr_final = $atv->dthr_final ? strtotime($atv->dthr_final) : $paraSempre;
				
				if($dthr_inicio <= $hoje && $hoje <= $dthr_final) {
					$vigentes[] = $atv;
				} else {
					$historicas[] = $atv;
				}
			}
			?>	
							
			<?php if(count($vigentes) > 0) { ?>
					
				<!-- --------------------- BEGIN: ATIVIDADES VIGENTES DATA TABLE -------------------------------- -->
				
				<table class="table table-striped" id="comissoesVigentesList">			
			    
				<!-- --------------- BEGIN: ATIVIDADES VIGENTES TABLE HEADER ------------------- -->
			
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
				
				<!-- -------------- END: ATIVIDADES VIGENTES TABLE HEADER ----------------------- -->
										
				<!-- ------------------------ BEGIN: ATIVIDADES VIGENTES TABLE BODY -------------------------------- -->
		
				<tbody>
				<?php foreach ($vigentes as $i => $vigente) {  ?>
					<tr class="row<?php echo $i % 2; ?>" sortable-group-id="1">
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
				
				<!-- ------------------------ END: ATIVIDADES VIGENTES TABLE BODY ----------------------------------- -->
				
				</table>
				
				<!-- --------------------- END: ATIVIDADES VIGENTES DATA TABLE ---------------------------------- -->
				
			<?php } ?>
						
			<?php if(count($historicas) > 0) { ?>
					
					<!-- --------------------- BEGIN: ATIVIDADES HISTORICAS DATA TABLE -------------------------------- -->
					
					<table class="table table-striped" id="comissoesHistoricasList">			
				    
					<!-- --------------- BEGIN: ATIVIDADES HISTORICAS TABLE HEADER ------------------- -->
				
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
					
					<!-- -------------- END: ATIVIDADES HISTORICAS TABLE HEADER ----------------------- -->
					
					<!-- -------------------------- BEGIN: ATIVIDADES HISTORICAS TABLE BODY ----------------------------- -->
			
					<tbody>
					<?php foreach ($historicas as $i => $historica) {  ?>
						<tr class="row<?php echo $i % 2; ?>" sortable-group-id="1">
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
					
					<!-- ------------------------- END: ATIVIDADES VIGENTES TABLE BODY ---------------------------------- -->
					
					</table>
					
					<!-- --------------------- END: ATIVIDADES VIGENTES DATA TABLE ---------------------------------- -->
				
			<?php } ?>	
		
			<!-- -------------- BEGIN: ATIVIDADES PAGINATION ----------------------- -->

			<div class="pagination">
				<?php echo ($atividadePaginacao ? $atividadePaginacao->getPagesLinks() : ''); ?>
			</div>

			<!-- -------------- END: ATIVIDADES PAGINATION ----------------------- -->	
		
		<?php echo JHtml::_('bootstrap.endSlide'); ?>					
			
		<!-- ----------------------------------- END: ATIVIDADES ---------------------------------------------------- -->
					
	<?php } ?>	
<?php echo JHtml::_('bootstrap.endAccordion'); ?>