<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_unavailability
 * @author		Marcus Martinho (marcus.martinho@tjam.jus.br)
 *
 * @copyright   Copyright (C) 2016 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

$logoImg = JURI::root().'/components/com_unavailability/media/img/logo_tjam_assinatura.png';

$item = $this->items[0];
$params = $this->params;
?>

<!-- ****************** DOCUMENT LOGO ********************* -->

<div style="text-align: center; ">
	<img style="width: 50px;" src="<?php echo $logoImg;?>">
</div>
<p style="text-align: center;">
	<b>DECLARAÇÃO # <?php echo $item->id;?></b><br />
	Indisponibilidade para <?php echo $item->category_title;?>
</p>

<!-- ****************************** DOCUMENT BODY *********************************** -->

<?php if($item->detalhes) { // if alternative text is set... ?>
	<?php echo $item->detalhes;?>
<?php } else { 
	// compose "sistemas" array information...
	$sysCount = count($this->sistemas);
	$sysList='';
	if($sysCount > 0) { // first at all: does it exists?
		$plural = $sysCount > 1;  // more than one element
		$lastKey = $sysCount - 1;
		$beforeLastKey = $lastKey - 1;
		// compose the sysList paragraph sentence to be used on document text
		foreach ($this->sistemas as $key=>$sistema) {
			if($key == $lastKey)
				$sysList .= $sistema;
			else if($key == $beforeLastKey)
				$sysList .= $sistema.' e ';
			else
				$sysList .= $sistema.', ';
		}
	}
	// Did the unavailability occur on the same day?
	$sameDay =
		JHtml::_('date', $item->dthr_inicio, JText::_('DATE_FORMAT')) ==
		JHtml::_('date', $item->dthr_final, JText::_('DATE_FORMAT'));
	?>
	<p>
		Declaro para o devidos fins que,
		<?php if($sameDay) {?>
			no dia 
			<?php echo JHtml::_('date', $item->dthr_inicio, JText::_('DATE_FORMAT'));?>
			das
			<?php echo JHtml::_('date', $item->dthr_inicio, JText::_('TIME_FORMAT'));?> 
			às
			<?php echo JHtml::_('date', $item->dthr_final, JText::_('TIME_FORMAT'));?>
			,
		<?php } else { ?>
			entre 
			<?php echo JHtml::_('date', $item->dthr_inicio, JText::_('DATE_TIME_FORMAT'));?>
			e
			<?php echo JHtml::_('date', $item->dthr_final, JText::_('DATE_TIME_FORMAT'));?>
			, 
		<?php } ?>
		<?php if($sysList) { // if "sistemas" were selected... ?>
			<?php echo $plural ? 'os sistemas' : 'o sistema';?> 
			<?php echo $sysList;?>
			<?php echo $plural ? 'estavam indisponíveis' : 'estava indisponível';?>
			para acesso.	
		<?php } else { // if no "sistemas" selected... ?>
			todos os sistemas deste Egrégio Tribunal de Justiça estavam indisponíveis 
			para acesso. 
		<?php } ?>
	</p>
<?php } ?>

<!-- ************************* DOCUMENT FINALIZATION ************************** -->

<p>
	O TJAM reconhece a validade das informações aqui fornecidas,
	nos termos dos Art. 10, § 2º da Lei 11.419/2006.	 
</p>
<p style="text-align: right;">
	Manaus, 
	<?php echo JHtml::_('date', $item->dthr_emissao, JText::_('DATE_FORMAT'));?>.
</p>
<p style="text-align: right;">
	<?php echo $item->responsavel;?>
</p>
