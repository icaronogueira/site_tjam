<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_res102cnj
 * @author		Marcus Martinho (marcus.martinho@tjam.jus.br)
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

//JHtml::_('bootstrap.tooltip');
//JHtml::_('behavior.multiselect');
//JHtml::_('formbehavior.chosen', 'select');

//$user      = JFactory::getUser();
//$userId    = $user->id;
//$listOrder = $this->escape($this->state->get('list.ordering'));
//$listDirn  = $this->escape($this->state->get('list.direction'));
//$canOrder  = $user->authorise('core.edit.state', 'com_listing.category'); // user permission to edit
//$saveOrder = $listOrder == 'a.ordering'; // check if list order is by ordering column 

//if ($saveOrder) {
	// render the save order functionality...
//	$saveOrderingUrl = 'index.php?option=com_unavailability&task=unavailabilities.saveOrderAjax&tmpl=component';
	// unavailabilityList id matches the unavailabilityList table above
//	JHtml::_('sortablelist.sortable', 'unavailabilityList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
//}
?>

<form action="<?php echo JRoute::_('index.php?option=com_res102cnj&view=info');?>" method="post" name="adminForm" id="adminForm">
	<?php if(!empty($this->sidebar)) { ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
	<?php } ?> 
	<div id="j-main-container" class="span10">
		
		<p>
		   Componente que disponibiliza/altera arquivos contendo informações sobre 
           dados de transparência orçamentária e de pessoal do órgão.
        </p>
		<h2>Suporte do Sistema de Listagens</h2>
		<p>
			O suporte deste componente é dado pelo endereço de email: 
			<a href="mailto:marcus.martinho@tjam.jus.br">marcus.martinho@tjam.jus.br</a>
		</p>
		<h2>Copyright</h2>
		<p>
		<p>Este componente não pode ser alterado, copiado ou distribuído sem autorização.</p>
		<p>
			TRIBUNAL DE JUSTIÇA DO ESTADO DO AMAZONAS<br />
			Divisão de Tecnologia da Informação e Comunicação (DVTIC)<br />
			Setor de Desenvolvimento de Sistemas (SDS)<br />
		</p>
		
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>