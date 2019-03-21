<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_extrajudicial
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
			(ID: <?php echo $item->id; ?>, <?php echo $item->apelido;?>)
		</h2>
	</div>
	<table>
	<tr>
	<td valign="top">
		<dt class="article-info-term">
			<?php echo JText::_('COM_EXTRAJUDICIAL_FIELD_GROUP_SYSTEM'); ?>
		</dt>
		<dd class="createdby">
			<span style="font-weight: bold;"><?php echo JText::_('COM_EXTRAJUDICIAL_FIELD_ATIVO_LABEL');?>: </span>
			<span><?php echo $item->ativo ? 'Sim' : 'Não';?></span>
		</dd>
		<?php if(count($item->categoriaslocais) > 0) {?>
			<dd class="category-name">
				<div style="font-weight: bold;"><?php echo JText::_('COM_EXTRAJUDICIAL_FIELD_CATEGORIAS_LABEL');?>: </div>  
				<ul>
				<?php 
				foreach ($item->categoriaslocais as $cl) { 
					echo '<li>'.$cl->categoria->nome.'</li>';
				}
				?>
				</ul>
			</dd>
		<?php } ?>
		<?php if($item->create_time) {?>
			<dd class="published">
				<span style="font-weight: bold;"><?php echo JText::_('COM_EXTRAJUDICIAL_FIELD_CREATE_TIME_LABEL'); ?>: </span> 
				<span><?php echo MMDateHelper::formataDataHrBrExtenso($item->create_time);?></span>
			</dd>
		<?php } ?>
	</td>
	<td>
		<?php if($item->horarios || $item->endereco || $item->telefones || $item->emails) {?>
			<dt class="article-info-term">
				<?php echo JText::_('COM_EXTRAJUDICIAL_FIELD_GROUP_OUTROS'); ?>
			</dt>
			<?php if($item->horarios) {?>
				<dd class="published">
					<span style="font-weight: bold;"><?php echo JText::_('COM_EXTRAJUDICIAL_FIELD_HORARIOS_LABEL'); ?>: </span>
					<span><?php echo nl2br($item->horarios); ?></span>
				</dd>
			<?php } ?>			
			<?php if($item->endereco) {?>
				<dd class="published">
					<div style="font-weight: bold;"><?php echo JText::_('COM_EXTRAJUDICIAL_FIELD_ENDERECO_LABEL'); ?>: </div>
					<div><?php echo nl2br($item->endereco); ?></div>
				</dd>
			<?php } ?>
			<?php if($item->emails) {?>
				<dd class="published">
					<div style="font-weight: bold;"><?php echo JText::_('COM_EXTRAJUDICIAL_FIELD_EMAILS_LABEL'); ?>: </div>
					<div><?php echo nl2br($item->emails); ?></div>
				</dd>
			<?php } ?>
			<?php if($item->telefones) {?>
				<dd class="published">
					<div style="font-weight: bold;"><?php echo JText::_('COM_EXTRAJUDICIAL_FIELD_TELEFONES_LABEL'); ?>: </div>
					<div><?php echo nl2br($item->telefones); ?></div>
				</dd>
			<?php } ?>
		<?php } ?>
	</td>
	</tr>
	</table>
	<br />
	<div itemprop="articleBody">
		<?php $vigentes = $item->assentamentoItems; ?>		
		<?php if(count($vigentes) > 0) { ?>
			<?php echo JHtml::_('bootstrap.startAccordion', 'slide-local', array('active' => $active)); ?>
				
				<!-- ----------------------------------- BEGIN: ASSENTAMENTOS DO LOCAL -------------------------------------------------- -->
		
				<?php echo JHtml::_('bootstrap.addSlide', 'slide-local', JText::_('COM_EXTRAJUDICIAL_FIELD_GROUP_ASSENTAMENTOS_LOCAL'), 'group-assentamentos-local'); ?>
																
						
					<!-- ------------------------- BEGIN: VIGENTES DATA TABLE ----------------------------------- -->
					
					<table class="table table-striped" id="assentamentosVigentesList">			
				    
					<!-- --------------- BEGIN: VIGENTES TABLE HEADER ------------------- -->
				
					<thead>
						<tr>
							<td colspan="6" bgcolor="#99FF99">
								<div style="font-weight: bold;">
									<?php echo JText::_('COM_EXTRAJUDICIAL_FIELD_GROUP_ASSENTAMENTOS_LOCAL_VIGENTES');?>
								</div>
							</td>
						</tr>
						<tr>
							<th width="20%">
								<?php echo JText::_('COM_EXTRAJUDICIAL_FIELD_PESSOA_LABEL'); ?>
							</th>
							<th width="30%">
								<?php echo JText::_('COM_EXTRAJUDICIAL_FIELD_ANEXAS_LABEL'); ?>
							</th>
							<th width="20%" class="">
								<?php echo JText::_('COM_EXTRAJUDICIAL_FIELD_OBSERVACOES_LABEL'); ?>
							</th>							
						</tr>
					</thead>
					
					<!-- -------------- END: VIGENTES TABLE HEADER ----------------------- -->
											
					<!-- ------------------------ BEGIN: VIGENTES TABLE BODY -------------------------------- -->
			
					<tbody>
					<?php foreach ($vigentes as $i => $vigente) {  ?>
						<tr class="row<?php echo $i % 2; ?>" sortable-group-id="1">
							<td>
								<?php 
								echo
								($vigente->pessoa ? $vigente->pessoa->nome .' | ': '') . 
								($vigente->cargo ? $vigente->cargo->nome : '' ) . 
								( array_key_exists('principal',$vigente) 
								  ? 
								  ($vigente->principal->principal_id == $vigente->id ? ' (Principal)' : '') 
								  :								   
								  (array_key_exists('substitutos', $vigente) ? ' (Substituto)' : '')
								);?>							
							</td>
							<td>
								<?php if(property_exists($vigente, 'anexas')){ ?>
						      		<?php foreach ($vigente->anexas as $anexo) { ?>
						      			<?php echo $anexo->lnk_doc; ?>
						      		<?php } ?>
						        <?php } ?>
							</td>
							<td>
								<?php echo nl2br($vigente->observacoes); ?>
							</td>
						</tr>
					<?php } ?>
					</tbody>
					
					<!-- ------------------------ END: VIGENTES TABLE BODY ----------------------------------- -->
					
					</table>
					
					<!-- ------------------------------ END: VIGENTES DATA TABLE ----------------------------------------- -->
										
					<!-- -------------- BEGIN: NOMEACOES LOCAL PAGINATION ----------------- -->

					<div class="pagination">
						<?php echo ($this->assPagination? $this->assPagination->getPagesLinks() : ''); ?>
					</div>

					<!-- -------------- END: NOMEACOES LOCAL PAGINATION ------------------- -->	
			
				<?php echo JHtml::_('bootstrap.endSlide'); ?>					
				
				<!-- ----------------------------------- END: ASSENTAMENTOS DO LOCAL ---------------------------------------------------- -->

			<?php echo JHtml::_('bootstrap.endAccordion'); ?>
		<?php } ?>
	</div>
</div>