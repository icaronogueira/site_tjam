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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$canOrder  = $user->authorise('core.edit.state', 'com_unavailability.category'); // user permission to edit
$saveOrder = $listOrder == 'a.ordering'; // check if list order is by ordering column 

if ($saveOrder) {
	// render the save order functionality...
	$saveOrderingUrl = 'index.php?option=com_unavailability&task=unavailabilities.saveOrderAjax&tmpl=component';
	// unavailabilityList id matches the unavailabilityList table above
	JHtml::_('sortablelist.sortable', 'unavailabilityList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
?>

<form action="<?php echo JRoute::_('index.php?option=com_unavailability&view=unavailabilities');?>" method="post" name="adminForm" id="adminForm">
	<?php if(!empty($this->sidebar)) { ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
	<?php } ?> 
	<div id="j-main-container" class="span10">
		<?php
		// Search tools bar (@see view.html display function)
		echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>
		<?php if (empty($this->items)) { ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php } else { // @see layout helper searchtools ?>
			<table class="table table-striped" id="unavailabilityList">
				<thead>
					<tr>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
						</th>
						<th width="1%" class="center">
							<?php echo JHtml::_('grid.checkall'); ?>
						</th>
						<th width="1%" style="min-width:55px" class="nowrap center">
							<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
						</th>						
						<th class="nowrap">
							<?php echo JHtml::_('searchtools.sort', 'COM_UNAVAILABILITY_FIELD_TITLE_LABEL', 'a.title', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_UNAVAILABILITY_FIELD_DTHR_EMISSAO_LABEL', 'a.dthr_emissao', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_UNAVAILABILITY_FIELD_DTHR_INICIO_LABEL', 'a.dthr_inicio', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_UNAVAILABILITY_FIELD_DTHR_FINAL_LABEL', 'a.dthr_final', $listDirn, $listOrder); ?>
						</th>	
						<th width="15%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_UNAVAILABILITY_FIELD_RESPONSAVEL_LABEL', 'a.responsavel', $listDirn, $listOrder); ?>
						</th>																							
						<th width="1%" class="nowrap center">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>					
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="13">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>				
				<tbody>
				<?php foreach ($this->items as $i => $item) { 
					$item->edit_link = JRoute::_('index.php?option=com_unavailability&task=unavailability.edit&id='.(int)$item->id);
					$item->cat_link  = JRoute::_('index.php?option=com_categories&extension=com_unavailability&task=edit&type=other&cid[]=' . $item->catid);
					$canCreate  	 = $user->authorise('core.create',     'com_unavailability.category.' . $item->catid);
					$canEdit    	 = $user->authorise('core.edit',       'com_unavailability.category.' . $item->catid);
					$canCheckin 	 = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
					$canChange  	 = $user->authorise('core.edit.state', 'com_unavailability.category.' . $item->catid) && $canCheckin;
						
				?>
					<tr class="row<?php echo $i % 2;?>" sortable-group-id="<?php echo $item->catid; ?>">
						<td class="order nowrap center hidden-phone">
							<?php
							$iconClass = '';
							if (!$canChange) {
								$iconClass = ' inactive';
							}
							elseif (!$saveOrder) {
								$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
							}
							?>
							<span class="sortable-handler <?php echo $iconClass ?>">
								<span class="icon-menu"></span>
							</span>
							<?php if ($canChange && $saveOrder) { ?>
								<input type="text" style="display:none" name="order[]" 
									size="5" class="width-20 text-area-order "
									value="<?php echo $item->ordering; ?>"  />
							<?php } ?>
						</td>
						<td class="center">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="center">
							<div class="btn-group">
								<?php echo JHtml::_('jgrid.published', $item->state, $i, 'unavailabilities.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
								<?php // Create dropdown items and render the dropdown list.
								if ($canChange) {
									JHtml::_('actionsdropdown.' . ((int) $item->state === 2 ? 'un' : '') . 'archive', 'cb' . $i, 'unavailabilities');
									JHtml::_('actionsdropdown.' . ((int) $item->state === -2 ? 'un' : '') . 'trash', 'cb' . $i, 'unavailabilities');
									echo JHtml::_('actionsdropdown.render', $this->escape($item->title));
								}
								?>
							</div>
						</td>												
						<td>
							<div class="pull-left">
								<?php if ($item->checked_out) : ?>
									<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'unavailabilities.', $canCheckin); ?>
								<?php endif; ?>
								<?php if ($canEdit) { ?>
									<a href="<?php echo $item->edit_link;?>">
										<?php echo $this->escape($item->title); ?>
									</a>
								<?php } else { ?>
									<?php echo $this->escape($item->title); ?>
								<?php } ?>
								<span class="small">
									<?php echo '('.substr(JText::_('COM_UNAVAILABILITY_FIELD_ALIAS_LABEL').': '.$item->alias.')', 0, 50); ?>
								</span>
								<div class="small">
									<?php echo substr(JText::_('JCATEGORY') . ': ' . $this->escape($item->category_title), 0, 50); ?>
								</div>
							</div>
						</td>
						<td class="nowrap small hidden-phone">
							<?php echo JHtml::_('date', $item->dthr_emissao, JText::_('DATE_FORMAT_LC4')); ?>
						</td>
						<td class="nowrap small hidden-phone">
							<?php echo JHtml::_('date', $item->dthr_inicio, JText::_('DATE_FORMAT_LC4')); ?>
						</td>
						<td class="nowrap small hidden-phone">
							<?php echo JHtml::_('date', $item->dthr_final, JText::_('DATE_FORMAT_LC4')); ?>
						</td>	
						<td class="nowrap hidden-phone">
							<?php echo $this->escape($item->responsavel); ?>
						</td>										
						<td class="hidden-phone">
							<?php echo (int) $item->id; ?>
						</td>					
					</tr>
				<?php } ?>
				</tbody>
			</table>
		<?php } ?>
		
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>