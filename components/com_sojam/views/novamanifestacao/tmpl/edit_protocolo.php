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
?>

<!-- *************** TITULO E TEXTO INTRODUTORIO *********************** -->
<h1 class="componentheading">CADASTRO DA MANIFESTAÇÃO</h1>

<dl id="system-message">
	<dt class="message">
		Mensagem
	</dt>
	<dd class="message message fade">
		<ul>
	        <li>
	            Sua manifestação foi cadastrada com sucesso.  
	        </li>
		</ul>
	</dd>
</dl>

<p style="text-align: justify;">
	Gerada Manifestação nº <i><span style="color: #ff0000; font-size: 160%; font-weight: bold;" ><?php echo $this->item->id; ?></span></i>
	<?php echo $this->item->nome ? ', na qual o Sr(a) <i>' . $this->item->nome . ' (CPF/CNPJ nº '. $this->item->cpf . ') '. '</i>': ''; ?>
	<?php echo $this->item->tipo ? ' registra um(a) <i>' . $this->item->tipo->nome . '</i>': ''; ?>
	<?php echo $this->item->origem ? ' via <i>' . $this->item->origem->nome . '</i>': ''; ?>.
</p>
<p>
	Utilize o número de protocolo em destaque para acompanhar 
	o andamento da sua manifestação. 
</p>
<?php 
if($this->item->processo) {
?>
	<p>Nº Processo Associado: <i><?php echo $this->item->processo; ?></i>.</p>
<?php 
}
?>