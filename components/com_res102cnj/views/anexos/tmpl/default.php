<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_res102cnj
 * @author		Marcus Martinho (marcus.martinho@tjam.jus.br)
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

$uri = JFactory::getURI();
$url = $uri->toString(); 

$res102cnjdir = JURI::root().$this->model->getRes102cnjdir(); // diretório
$meses = $this->model->getMeses();   // nome dos meses de JAN a DEZ
$anosretroativos = $this->model->getAnosretroativos(); // quantos anos retroativos mostrar?
$minimo = $this->model->get_tam_min();  // tamanho min de arquivo
$maximo = $this->model->get_tam_max();  // tamanho max de arquivo
$gerenciar = $this->gerenciar; // chave para exibir os elementos de gestão de arquivos
$user = $this->user; // usuário conectado
$erro = $this->erro; // erro retornado pelas funções de manipulação de arquivos
$pageYOffset = $this->pageYOffset;  // posição vertical da tela
$pageXOffset = $this->pageXOffset;  // posição horizontal da tela
$btns = $this->btns;
?>

<?php 
if($gerenciar) {  
?>
	<!-- ******************** PROCESSAMENTO FORMULARIO ********************* -->
	<script type="text/javascript">
		function submitdelete(arq) {
			document.forms["res102cnjForm"].pageYOffset.value= window.pageYOffset;
			document.forms["res102cnjForm"].pageXOffset.value= window.pageXOffset;
			document.forms["res102cnjForm"].task.value='delete';
			document.forms["res102cnjForm"].arq.value=arq;
			if(confirm("Excluir o arquivo " + arq + "?")) {
				document.forms["res102cnjForm"].submit();
			}
		}
	
		function submitupload(arq) {
			if(document.forms["res102cnjForm"].arqenvio.value != '') {
				document.forms["res102cnjForm"].task.value='upload';
				document.forms["res102cnjForm"].arq.value=arq;
				document.forms["res102cnjForm"].pageYOffset.value= window.pageYOffset;
				document.forms["res102cnjForm"].pageXOffset.value= window.pageXOffset;				
				document.forms["res102cnjForm"].submit();
			} else 
				alert('Escolha o arquivo a ser enviado primeiro (alto da página)');
		}	
	</script>
<?php 
} 
?>

<!-- ****************************************************** TITULO E TEXTO INTRODUTORIO ******************************************** -->

<h2 class="contentheading">
	<?php echo $this->model->getRes102cnjlink();?>
</h2>
<hr />
<p style="text-align: justify;">
	<span style="font-style: italic;" >
		<?php echo $this->model->getRes102cnjintro();?>
	</span>
</p>
<!-- ***************************************************** REFERENCIA DA RESOLUCAO ************************************************* -->
<p>
	Veja também: 
	<a href="<?php echo $this->model->getRes102cnjurl();?>" target="_blank">
		<?php echo $this->model->getRes102cnjlink();?>
	</a>
	<span style="font-style: italic;">(link sujeito a alterações)</span>
</p>

<div style="height: 10px;"></div>

<!-- *********************************************************** LEGENDA *********************************************************** -->

<table style="width: 400px; " border="0" align="right">
<tbody>
<tr>
	<td style="text-align: right;">
		<span style="font-size: 10pt;"><strong>Legenda</strong>:</span>
	</td>
	<td style="text-align: right;">
		<span style="font-size: 10pt;">Publicado:</span>
	</td>
	<td style="border: 1px solid #000000; background-color: #e0f1f6; width: 10px;">
		<span style="font-size: 10pt;"></span>
	</td>
	<td style="text-align: right;">
		<span style="font-size: 10pt;">A Publicar:</span>
	</td>
	<td style="border: 1px solid #000000; background-color: #f8dfdf; width: 10px;">
		<span style="font-size: 10pt;"></span>
	</td>
	<?php if($gerenciar) { // add manager legend... ?>
		<td style="text-align: right;">
			<span style="font-size: 10pt;">Excluir:</span>
		</td>
		<td style="width: 10px;">
			<span style="font-size: 10pt;"><?php echo $btns->legenda('delete');?></span>
		</td>
		<td style="text-align: right;">
			<span style="font-size: 10pt;">Enviar:</span>
		</td>
		<td style="width: 10px;">
			<span style="font-size: 10pt;"><?php echo $btns->legenda('upload');?></span>
		</td>				
	<?php } ?>
</tr>
</tbody>
</table>

<div style="height: 25px;"></div>

<?php if($gerenciar) { ?>
	<!-- ******************************************** FORMULARIO DE GESTAO DE ARQUIVOS ********************************************* -->
	
	<form action="<?php echo $url; ?>" enctype="multipart/form-data" method="post" name="res102cnjForm" id="res102cnjForm">
	
		<!-- ************************ CAMPO DE ENVIO DE ARQUIVO ************************** -->
	
		<div id="arqpanel">
			<span style="font-style: italic;">Arquivo PDF; tam. mín: <?php echo $minimo;?>; tam. máx: <?php echo $maximo;?></span>
			<input type="file" size="32" name="arqenvio" id="arqenvio" value="" />
		</div>
		
		<?php if($erro <> '') {  ?>
			<!-- ********************* MSG DE ERRO *************** -->
			<p style="text-align: center;">
				<span style="background-color: red; padding: 5px;">
					<?php echo $erro;?>
				</span>
			</p>
		<?php } ?>		
<?php } ?>

		<!-- ******************************************************** ANEXO I ***************************************************** -->
		

		<table style="width: 100%;" border="0" align="center">
		<tbody>
		<tr>
			<td style="text-align: center; background-color: #000099;" colspan="14">
				<span style="color: #ffffff;">
					<strong>
						Dados de Gestão Orçamentária e Financeira
					</strong>
				</span>
			</td>
		</tr>
		<tr>
			<td style="text-align: center;" colspan="14">
				<strong>
					Anexo I - Gestão Financeira consolidada por Mês
				</strong>
			</td>
		</tr>
		   <?php
		        $dataanx1 = getdate(); 
		        $anoatualanx1 = $dataanx1['year'];
		        $minanoanx1 = $anoatualanx1 - $anosretroativos;
		        if($minanoanx1 < 2007 ) {
		            $minanoanx1 = 2007;
		        }
		        $maxanoanx1 = $anoatualanx1;
		        for ($anoanx1 = $minanoanx1; $anoanx1 <= $maxanoanx1; $anoanx1++) { // para cada ano...
		            echo "<tr>" ;
		            echo "<td>" . $anoanx1 . "</td>" ;
		            $anoarquivoanx1 = substr($anoanx1,2,2);  
		            for ($mesanx1 = 1; $mesanx1 <= 12; $mesanx1++) {  // para cada mes...
		                $arq = "01_" . str_pad($mesanx1,2,"0",STR_PAD_LEFT) . "_" . $anoarquivoanx1 . ".pdf";  
		                if ($this->model->arquivo_valido($arq)) {
							echo "<td style='text-align: center; background-color: #e0f1f6;'>" .
									($gerenciar ? "<div style='float: left;'>" : "<div>") .  
									"<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>" . $meses[$mesanx1] ."</a></div>" .
								 	($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
								 "</td>";
		                } else {
							echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
									($gerenciar ? "<div style='float: left;'>" : "<div>") .
									$meses[$mesanx1] . "</div>" .
									($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "") .
								"</td>";                
		                }
		            }
		            // RAP...
		            $arq = "01_00_" . $anoarquivoanx1 . ".pdf";
		            if ( $this->model->arquivo_valido($arq) ) { 
						echo "<td style='text-align: center; background-color: #e0f1f6;'>" .
								($gerenciar ? "<div style='float: left;'>" : "<div>") .
								"<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>RAP</a></div>" .
								($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
							 "</td>";
		            } else {
		               echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
		                 		($gerenciar ? "<div style='float: left;'>" : "<div>") .
		                 		"RAP" . "</div>" .
		                 		($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "") .
		                    "</td>" ;
		            }
		            echo "</tr>" ;
		       }
		    ?>
		</tbody>
		</table>
		
		<p> </p>
		
		<!-- ******************************************************** ANEXO II ***************************************************** -->
		
		<table style="width: 100%;" border="0" align="center">
		<tbody>
		<tr>
			<td style="text-align: center;" colspan="14">
				<strong>
					Anexo II - Informações Orçamentárias consolidadas por Mês
				</strong>
			</td>
		</tr>
		   <?php
		        $dataanx2 = getdate(); 
		        $anoatualanx2 = $dataanx2['year'];
		        $minanoanx2 = $anoatualanx2 - $anosretroativos;
		        if($minanoanx2 < 2007 ) {
		            $minanoanx2 = 2007;
		        }
		        $maxanoanx2 = $anoatualanx2;
		        for ($anoanx2 = $minanoanx2; $anoanx2 <= $maxanoanx2; $anoanx2++) { // para cada ano...
		            echo "<tr>" ;
		            echo "<td>" . $anoanx2 . "</td>" ;
		            $anoarquivoanx2 = substr($anoanx2,2,2);  
		            for ($mesanx2 = 1; $mesanx2 <= 12; $mesanx2++) {   // para cada mes...
		                $arq =  "02_" . str_pad($mesanx2,2,"0",STR_PAD_LEFT) . "_" . $anoarquivoanx2 . ".pdf";  
		                if ($this->model->arquivo_valido($arq)) {                   
							echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
									($gerenciar ? "<div style='float: left;'>" : "<div>") .
								 	"<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>" . $meses[$mesanx2] ."</a></div>" .
								 	($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
								 "</td>";
		                } else {
							echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
									($gerenciar ? "<div style='float: left;'>" : "<div>") .
									$meses[$mesanx2] . "</div>" .
									($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
							 	 "</td>";                
		                }
		            }
		            // RAP...
		            $arq =  "02_00_" . $anoarquivoanx2 . ".pdf";
		            if ( $this->model->arquivo_valido($arq) ) {                
		               echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
								($gerenciar ? "<div style='float: left;'>" : "<div>") .
								"<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>RAP</a></div>" .
								($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
							"</td>";
		            } else {
		               echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
				               ($gerenciar ? "<div style='float: left;'>" : "<div>") .
				               "RAP" . "</div>" . 
				               ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
		               		"</td>" ;
		            }
		            echo "</tr>" ;
		       }
		    ?>
		</tbody>
		</table>
		
		<p> </p>
		
		<!-- ******************************************************** ANEXO III ***************************************************** -->
		
		<table style="width: 100%;" border="0" align="center">
		<tbody>
		<tr>
			<td style="text-align: center; background-color: #000099;" colspan="14">
				<span style="color: #ffffff;">
					<strong>
						Estrutura Remuneratórias
					</strong>
				</span>
			</td>
		</tr>
		<tr>
			<td style="text-align: center;" colspan="14">
				<strong>
					Anexo III-a -Cargos Efetivos
				</strong>
			</td>
		</tr>
		   <?php
		        $dataanx3 = getdate(); 
		        $anoatualanx3 = $dataanx3['year'];
		        $minanoanx3 = $anoatualanx3 - $anosretroativos;
		        if($minanoanx3 < 2009 ) {
		            $minanoanx3 = 2009;
		        }
		        $maxanoanx3 = $anoatualanx3;
		        for ($anoanx3 = $minanoanx3; $anoanx3 <= $maxanoanx3; $anoanx3++) { // para cada ano...
		            echo "<tr>" ;
		            echo "<td>" . $anoanx3 . "</td>" ;
		            $anoarquivoanx3 = substr($anoanx3,2,2);  
		            for ($mesanx3 = 1; $mesanx3 <= 12; $mesanx3++) { // para cada mes...
		                $arq =  "31_" . str_pad($mesanx3,2,"0",STR_PAD_LEFT) . "_" . $anoarquivoanx3 . ".pdf";  
		                if ($this->model->arquivo_valido($arq)) {
							echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
									($gerenciar ? "<div style='float: left;'>" : "<div>") .
									"<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>" . $meses[$mesanx3] ."</a></div>" .
									($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
								 "</td>";
		                } else {
							echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
									($gerenciar ? "<div style='float: left;'>" : "<div>") .
									$meses[$mesanx3] . "</div>" .
									($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
								 "</td>";                
		                }
		            }
		            // RAP...
		            $arq =  "31_00_" . $anoarquivoanx3 . ".pdf";
		            if ( $this->model->arquivo_valido($arq) ) { 
		               echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
				               ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		               		   "<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>RAP</a></div>" . 
		               		   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
		               		"</td>";
		            } else {
		               echo "<td style='text-align: center; background-color: #f8dfdf;'>".
				               ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		               		   "RAP" . "</div>" .
		               		   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
		                	"</td>" ;
		            }
		            echo "</tr>" ;
		       }
		    ?>
		<tr>
			<td style="text-align: center;" colspan="14">
				<strong>
					Anexo III-b - Cargos em Comissão e Funções de Confiança
				</strong>
			</td>
		</tr>
		   <?php
		        for ($anoanx3 = $minanoanx3; $anoanx3 <= $maxanoanx3; $anoanx3++) { // para cada ano...
		            echo "<tr>" ;
		            echo "<td>" . $anoanx3 . "</td>" ;
		            $anoarquivoanx3 = substr($anoanx3,2,2);  
		            for ($mesanx3 = 1; $mesanx3 <= 12; $mesanx3++) {  // para cada mes...
		                $arq =  "32_" . str_pad($mesanx3,2,"0",STR_PAD_LEFT) . "_" . $anoarquivoanx3 . ".pdf";  
		                if ($this->model->arquivo_valido($arq)) {
		                   echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
				                   ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		                   		   "<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>" . $meses[$mesanx3] . "</a></div>" .
		                   		   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
		                   		"</td>";
		                } else {
		                   echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
				                   ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		                   		   $meses[$mesanx3] . "</div>" . 
		                   		   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
		                   		"</td>";  
		                }
		            }
		            // RAP...
		            $arq =  "32_00_" . $anoarquivoanx3 . ".pdf";
		            if ( $this->model->arquivo_valido($arq) ) { 
		               echo "<td style='text-align: center; background-color: #e0f1f6;'>" .
				               ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		               		   "<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>RAP</a></div>" . 
		               		   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
		               		"</td>";
		            } else {
		               echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
				               ($gerenciar ? "<div style='float: left;'>" : "<div>") .
				               "RAP" . "</div>" . 
				               ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
		               		"</td>" ;
		            }
		            echo "</tr>" ;
		       }
		    ?>
		<tr>
			<td style="text-align: center;" colspan="14">
				<strong>
					Anexo III-c - Estrutura Remuneratória dos Conselheiros e Juízes Auxiliares
				</strong>
			</td>
		</tr>
		   <?php
		        for ($anoanx3 = $minanoanx3; $anoanx3 <= $maxanoanx3; $anoanx3++) { // para cada ano...
		            echo "<tr>" ;
		            echo "<td>" . $anoanx3 . "</td>" ;
		            $anoarquivoanx3 = substr($anoanx3,2,2);  
		            for ($mesanx3 = 1; $mesanx3 <= 12; $mesanx3++) {  // para cada mes...
		                $arq =  "33_" . str_pad($mesanx3,2,"0",STR_PAD_LEFT) . "_" . $anoarquivoanx3 . ".pdf";  
		                if ($this->model->arquivo_valido($arq)) {
		                   echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
				                   ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		                    	   "<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>" . $meses[$mesanx3] ."</a></div>" .
		                    	   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
		                   		"</td>";
		                } else {
		                   echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
				                   ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		                   		   $meses[$mesanx3] . "</div>" .
		                   		   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
		                   		"</td>";                
		                }
		            }
		            // RAP...
		            $arq =  "33_00_" . $anoarquivoanx3 . ".pdf";
		            if ( $this->model->arquivo_valido($arq) ) { 
		               echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
				               ($gerenciar ? "<div style='float: left;'>" : "<div>") .
				               "<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>RAP</a></div>" . 
				               ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
		               		"</td>";
		            } else {
		               echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
				               ($gerenciar ? "<div style='float: left;'>" : "<div>") .
				               "RAP" . "</div>" .
				               ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
		               		"</td>" ;
		            }
		            echo "</tr>" ;
		       }
		    ?>
		</tbody>
		</table>
		
		<p> </p>
		
		<!-- *********************************************************** ANEXO IV ********************************************************* -->
		
		<table style="width: 100%;" border="0" align="center">
		<tbody>
		<tr>
			<td style="text-align: center; background-color: #000099;" colspan="14">
				<span style="color: #ffffff;">
					<strong>
						Quantitativos de Cargos Efetivos e Comissionados, ocupados e vagos, 
					    por forma de provimento, origem funcional e situação funcional dos 
					    ocupantes
					</strong>
				</span>
			</td>
		</tr>
		<tr>
			<td style="text-align: center;" colspan="14">
				<strong>
					Anexo IV-a: Cargos Efetivos
				</strong>
			</td>
		</tr>
		   <?php
		        $dataanx4 = getdate(); 
		        $anoatualanx4 = $dataanx4['year'];
		        $minanoanx4 = $anoatualanx4 - $anosretroativos;
		        if($minanoanx4 < 2009 ) {
		            $minanoanx4 = 2009;
		        }
		        $maxanoanx4 = $anoatualanx4;
		        for ($anoanx4 = $minanoanx4; $anoanx4 <= $maxanoanx4; $anoanx4++) { // para cada ano...
		            echo "<tr>" ;
		            echo "<td>" . $anoanx4 . "</td>" ;
		            $anoarquivoanx4 = substr($anoanx4,2,2);  
		            for ($mesanx4 = 1; $mesanx4 <= 12; $mesanx4++) {  // para cada mes...
		                $arq =  "41_" . str_pad($mesanx4,2,"0",STR_PAD_LEFT) . "_" . $anoarquivoanx4 . ".pdf";  
		                if ($this->model->arquivo_valido($arq)) {
							echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
									($gerenciar ? "<div style='float: left;'>" : "<div>") .
									"<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>" . $meses[$mesanx4] ."</a></div>" .
									($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
								 "</td>";
		                } else {
		                   echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
				                   ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		                   		   $meses[$mesanx4] . "</div>" .
		                   		   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
		                   		"</td>";                
		                }
		            }
		            // RAP...
		            $arq =  "41_00_" . $anoarquivoanx4 . ".pdf";
		            if ( $this->model->arquivo_valido($arq) ) { 
		               echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
				               ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		               		   "<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>RAP</a></div>" .
		               		   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
		               		"</td>";
		            } else {
		               echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
				               ($gerenciar ? "<div style='float: left;'>" : "<div>") .
				               "RAP" . "</div>" .
				               ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
		               		"</td>" ;
		            }
		            echo "</tr>" ;
		       }
		    ?>
		<tr>
			<td style="text-align: center;" colspan="14">
				<strong>
					Anexo IV-b: Cargos em Comissão e Funções de Confiança
				</strong>
			</td>
		</tr>
		   <?php
		        for ($anoanx4 = $minanoanx4; $anoanx4 <= $maxanoanx4; $anoanx4++) {  // para cada ano...
		            echo "<tr>" ;
		            echo "<td>" . $anoanx4 . "</td>" ;
		            $anoarquivoanx4 = substr($anoanx4,2,2);  
		            for ($mesanx4 = 1; $mesanx4 <= 12; $mesanx4++) {  // para cada mes...
		                $arq =  "42_" . str_pad($mesanx4,2,"0",STR_PAD_LEFT) . "_" . $anoarquivoanx4 . ".pdf";  
		                if ($this->model->arquivo_valido($arq)) {
		                   echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
				                   ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		                   		   "<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>" . $meses[$mesanx4] ."</a></div>" .
		                   		   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
		                   		"</td>";
		                } else {
		                   echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
				                   ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		                   		   $meses[$mesanx4] . "</div>" .
		                   		   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
		                   		"</td>";                
		                }
		            }
		            // RAP...
		            $arq =  "42_00_" . $anoarquivoanx4 . ".pdf";
		            if ( $this->model->arquivo_valido($arq) ) { 
		               echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
				               ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		               		   "<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>RAP</a></div>" . 
		               		   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
		               		"</td>";
		            } else {
		               echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
				               ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		               		   "RAP" . "</div>" .
		               		   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
		               		"</td>" ;
		            }
		            echo "</tr>" ;
		       }
		    ?>
		<tr>
			<td style="text-align: center;" colspan="14">
				<strong>
					Anexo IV-c: Origem Funcional dos ocupantes de Cargos em Comissão e Funções de Confiança
				</strong>
			</td>
		</tr>
		   <?php
		        for ($anoanx4 = $minanoanx4; $anoanx4 <= $maxanoanx4; $anoanx4++) { // para cada ano...
		            echo "<tr>" ;
		            echo "<td>" . $anoanx4 . "</td>" ;
		            $anoarquivoanx4 = substr($anoanx4,2,2);  
		            for ($mesanx4 = 1; $mesanx4 <= 12; $mesanx4++) {  // para cada mes...
		                $arq =  "43_" . str_pad($mesanx4,2,"0",STR_PAD_LEFT) . "_" . $anoarquivoanx4 . ".pdf";  
		                if ($this->model->arquivo_valido($arq)) {
		                   echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
				                     ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		                   		     "<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>" . $meses[$mesanx4] ."</a></div>" .
		                   		     ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
		                   		"</td>";
		                } else {
		                   echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
				                     ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		                   			 $meses[$mesanx4] . "</div>" .
		                   			 ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
		                   		"</td>";                
		                }
		            }
		            // RAP...
		            $arq =  "43_00_" . $anoarquivoanx4 . ".pdf";
		            if ( $this->model->arquivo_valido($arq) ) { 
		                  echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
				                    ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		                  			"<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>RAP</a></div>" . 
		                  			($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
		                  	   "</td>";
		            } else {
		               echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
				                 ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		               			 "RAP" . "</div>" .
		               			 ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
		               		"</td>" ;
		            }
		            echo "</tr>" ;
		       }
		    ?>
		<tr>
			<td style="text-align: center;" colspan="14" valign="top">
				<strong>
					Anexo IV-d: Situação Funcional dos Servidores Ativos
				</strong>	
			</td>
		</tr>
		   <?php
		        for ($anoanx4 = $minanoanx4; $anoanx4 <= $maxanoanx4; $anoanx4++) { // para cada ano...
		            echo "<tr>" ;
		            echo "<td>" . $anoanx4 . "</td>" ;
		            $anoarquivoanx4 = substr($anoanx4,2,2);  
		            for ($mesanx4 = 1; $mesanx4 <= 12; $mesanx4++) {   // para cada mes...
		                $arq =  "44_" . str_pad($mesanx4,2,"0",STR_PAD_LEFT) . "_" . $anoarquivoanx4 . ".pdf";  
		                if ($this->model->arquivo_valido($arq)) {
		                   echo "<td style='text-align: center; background-color: #e0f1f6;'>" .
				                     ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		                   		     "<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>" . $meses[$mesanx4] ."</a></div>" .
		                   		     ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
		                   		"</td>";
		                } else {
		                   echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
				                     ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		                   			 $meses[$mesanx4] . "</div>" .
		                   			 ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
		                   		"</td>";                
		                }
		            }
		            // RAP...
		            $arq =  "44_00_" . $anoarquivoanx4 . ".pdf";
		            if ( $this->model->arquivo_valido($arq) ) { 
		               echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
				                 ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		               			 "<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>RAP</a></div>" . 
		               			 ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
		               		"</td>";
		            } else {
		               echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
				                 ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		               			 "RAP" . "</div>" .
		               			 ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
		               		"</td>" ;
		            }
		            echo "</tr>" ;
		       }
		    ?>
		<tr>
			<td style="text-align: center;" colspan="14">
				<strong>
					Anexo IV-e: Cargos de Magistrados do quadro de pessoal do órgão
				</strong>
			</td>
		</tr>
		   <?php
		        for ($anoanx4 = $minanoanx4; $anoanx4 <= $maxanoanx4; $anoanx4++) {  // para cada ano...
		            echo "<tr>" ;
		            echo "<td>" . $anoanx4 . "</td>" ;
		            $anoarquivoanx4 = substr($anoanx4,2,2);  
		            for ($mesanx4 = 1; $mesanx4 <= 12; $mesanx4++) {  // para cada mes...
		                $arq =  "45_" . str_pad($mesanx4,2,"0",STR_PAD_LEFT) . "_" . $anoarquivoanx4 . ".pdf";  
		                if ($this->model->arquivo_valido($arq)) {
		                   echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
				                   ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		                   		   "<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>" . $meses[$mesanx4] ."</a></div>" .
		                   		   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
		                   		"</td>";
		                } else {
		                   echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
				                   ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		                   		   $meses[$mesanx4] . "</div>" .
		                   		   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
		                   		"</td>";                
		                }
		            }
		            // RAP...
		            $arq =  "45_00_" . $anoarquivoanx4 . ".pdf";
		            if ( $this->model->arquivo_valido($arq) ) { 
		               echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
				               ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		               		   "<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>RAP</a></div>" . 
		               		   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
		               		"</td>";
		            } else {
		               echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
				               ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		               		   "RAP" . "</div>" .
		               		   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
		               		"</td>" ;
		            }
		            echo "</tr>" ;
		       }
		    ?>
		<tr>
			<td style="text-align: center;" colspan="14">
				<strong>
					Anexo IV-f: Situação funcional dos Magistrados ativos do quadro de pessoal do órgão
				</strong>
			</td>
		</tr>
		   <?php
		        for ($anoanx4 = $minanoanx4; $anoanx4 <= $maxanoanx4; $anoanx4++) {  // para cada ano...
		            echo "<tr>" ;
		            echo "<td>" . $anoanx4 . "</td>" ;
		            $anoarquivoanx4 = substr($anoanx4,2,2);  
		            for ($mesanx4 = 1; $mesanx4 <= 12; $mesanx4++) {  // para cada mes...
		                $arq =  "46_" . str_pad($mesanx4,2,"0",STR_PAD_LEFT) . "_" . $anoarquivoanx4 . ".pdf";  
		                if ($this->model->arquivo_valido($arq)) {
		                   echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
				                   ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		                   		   "<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>" . $meses[$mesanx4] ."</a></div>" .
		                   		   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
		                   		"</td>";
		                } else {
		                   echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
				                   ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		                   		   $meses[$mesanx4] . "</div>" .
		                   		   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
		                   		"</td>";                
		                }
		            }
		            // RAP...
		            $arq =  "46_00_" . $anoarquivoanx4 . ".pdf";
		            if ( $this->model->arquivo_valido($arq) ) { 
		               echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
				               ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		               		   "<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>RAP</a></div>" . 
		               		   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
		               		"</td>";
		            } else {
		               echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
				               ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		               		   "RAP" . "</div>" .
		               		   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
		               		"</td>" ;
		            }
		            echo "</tr>" ;
		       }
		    ?>
		<tr>
			<td style="text-align: center;" colspan="14">
				<strong>
					Anexo IV-g: Magistrados não integrantes do quadro próprio em exercício no órgão
				</strong>
			</td>
		</tr>
		   <?php
		        for ($anoanx4 = $minanoanx4; $anoanx4 <= $maxanoanx4; $anoanx4++) {  // para cada ano...
		            echo "<tr>" ;
		            echo "<td>" . $anoanx4 . "</td>" ;
		            $anoarquivoanx4 = substr($anoanx4,2,2);  
		            for ($mesanx4 = 1; $mesanx4 <= 12; $mesanx4++) {  // para cada mes...
		                $arq =  "47_" . str_pad($mesanx4,2,"0",STR_PAD_LEFT) . "_" . $anoarquivoanx4 . ".pdf";  
		                if ($this->model->arquivo_valido($arq)) {
		                   echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
				                   ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		                   		   "<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>" . $meses[$mesanx4] ."</a></div>" .
		                   		   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
		                   		"</td>";
		                } else {
		                   echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
				                   ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		                   		   $meses[$mesanx4] . "</div>" .
		                   		   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
		                   		"</td>";                
		                }
		            }
		            // RAP...
		            $arq =  "47_00_" . $anoarquivoanx4 . ".pdf";
		            if ( $this->model->arquivo_valido($arq) ) { 
		               echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
				               ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		               		   "<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>RAP</a></div>" . 
		               		   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
		               		"</td>";
		            } else {
		               echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
				               ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		               		   "RAP" . "</div>" .
		               		   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
		               		"</td>" ;
		            }
		            echo "</tr>" ;
		       }
		    ?>	
		<tr>
			<td style="text-align: center;" colspan="14">
				<strong>
					Anexo IV-h: Quantitativos de benefícios e dependentes de benefícios assistenciais
				</strong>
			</td>
		</tr>
		   <?php
		        for ($anoanx4 = $minanoanx4; $anoanx4 <= $maxanoanx4; $anoanx4++) {  // para cada ano...
		            echo "<tr>" ;
		            echo "<td>" . $anoanx4 . "</td>" ;
		            $anoarquivoanx4 = substr($anoanx4,2,2);  
		            for ($mesanx4 = 1; $mesanx4 <= 12; $mesanx4++) {  // para cada mes...
		                $arq =  "48_" . str_pad($mesanx4,2,"0",STR_PAD_LEFT) . "_" . $anoarquivoanx4 . ".pdf";  
		                if ($this->model->arquivo_valido($arq)) {
		                   echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
				                   ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		                   		   "<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>" . $meses[$mesanx4] ."</a></div>" .
		                   		   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
		                   		"</td>";
		                } else {
		                   echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
				                   ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		                   		   $meses[$mesanx4] . "</div>" .
		                   		   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
		                   		"</td>";                
		                }
		            }
		            // RAP...
		            $arq =  "48_00_" . $anoarquivoanx4 . ".pdf";
		            if ( $this->model->arquivo_valido($arq) ) { 
		               echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
				               ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		               		   "<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>RAP</a></div>" . 
		               		   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
		               		"</td>";
		            } else {
		               echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
				               ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		               		   "RAP" . "</div>" .
		               		   ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
		               		"</td>" ;
		            }
		            echo "</tr>" ;
		       }
		    ?>		    	    		    
		</tbody>
		</table>
		
		<p> </p>
		
		<!-- ******************************************************** ANEXO V ***************************************************** -->
		
		<table style="width: 100%;" border="0" align="center">
		<tbody>
		<tr>
			<td style="text-align: center; background-color: #000099;" colspan="14">
				<span style="color: #ffffff;">
					<strong>
						Relação de Membros da Magistratura e demais Agentes Públicos
					</strong>
				</span>
			</td>
		</tr>
		<tr>
			<td style="text-align: center;" colspan="14">
				<strong>
					Anexo V
				</strong>
			</td>
		</tr>
		   <?php
		        $dataanx5 = getdate(); 
		        $anoatualanx5 = $dataanx5['year'];
		        $minanoanx5 = $anoatualanx5 - $anosretroativos;
		        if($minanoanx5 < 2009 ) {
		            $minanoanx5 = 2009;
		        }
		        $maxanoanx5 = $anoatualanx5;
		        for ($anoanx5 = $minanoanx5; $anoanx5 <= $maxanoanx5; $anoanx5++) {  // para cada ano...
		            echo "<tr>" ;
		            echo "<td>" . $anoanx5 . "</td>" ;
		            $anoarquivoanx5 = substr($anoanx5,2,2);  
		            for ($mesanx5 = 1; $mesanx5 <= 12; $mesanx5++) {  // para cada mes...
		                $arq =  "05_" . str_pad($mesanx5,2,"0",STR_PAD_LEFT) . "_" . $anoarquivoanx5 . ".pdf";  
		                if ($this->model->arquivo_valido($arq)) {
		                   echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
				                     ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		                   		     "<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>" . $meses[$mesanx5] ."</a></div>" .
		                   		     ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
		                   		"</td>";
		                } else {
		                   echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
				                     ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		                   		     $meses[$mesanx5] . "</div>" .
		                   		     ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
		                   		"</td>";                
		                }
		            }
		            // RAP...
		            $arq =  "05_00_" . $anoarquivoanx5 . ".pdf";
		            if ( $this->model->arquivo_valido($arq) ) { 
		               echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
				                 ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		               			 "<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>RAP</a></div>" . 
		               			 ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
		               		"</td>";
		            } else {
		               echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
				                 ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		               			 "RAP" . "</div>" .
		               			 ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
		               		"</td>" ;
		            }
		            echo "</tr>" ;
		       }
		    ?>
		</tbody>
		</table>
		
		<p> </p>
		
		<!-- ******************************************************** ANEXO VI ***************************************************** -->
		
		<table style="width: 100%;" border="0" align="center">
		<tbody>
		<tr>
			<td style="text-align: center; background-color: #000099;" colspan="14">
				<span style="color: #ffffff;">
					<strong>
						Relação de Empregados de Empresas Contratadas em Exercício no Órgão
					</strong>
				</span>
			</td>
		</tr>
		<tr>
			<td style="text-align: center;" colspan="14">
				<strong>
					Anexo VI
				</strong>
			</td>
		</tr>
		   <?php
		        $dataanx6 = getdate(); 
		        $anoatualanx6 = $dataanx6['year'];
		        $minanoanx6 = $anoatualanx6 - $anosretroativos;
		        if($minanoanx6 < 2009 ) {
		            $minanoanx6 = 2009;
		        }
		        $maxanoanx6 = $anoatualanx6;
		        for ($anoanx6 = $minanoanx6; $anoanx6 <= $maxanoanx6; $anoanx6++) { // para cada ano...
		            echo "<tr>" ;
		            echo "<td>" . $anoanx6 . "</td>" ;
		            $anoarquivoanx6 = substr($anoanx6,2,2);  
		            for ($mesanx6 = 1; $mesanx6 <= 12; $mesanx6++) {  // para cada mes...
		                $arq =  "06_" . str_pad($mesanx6,2,"0",STR_PAD_LEFT) . "_" . $anoarquivoanx6 . ".pdf";  
		                if ($this->model->arquivo_valido($arq)) {
		                  echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
				                    ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		                  			"<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>" . $meses[$mesanx6] ."</a></div>" .
		                  			($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
		                  		"</td>";
		                } else {
		                   echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
				                     ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		                   			 $meses[$mesanx6] . "</div>" .
		                   			 ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
		                   		"</td>";                
		                }
		            }
		            // RAP...
		            $arq =  "06_00_" . $anoarquivoanx6 . ".pdf";
		            if ( $this->model->arquivo_valido($arq) ) { 
		               echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
				                 ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		               			 "<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>RAP</a></div>" . 
		               			 ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
		               		"</td>";
		            } else {
		               echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
				                 ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		               			 "RAP" . "</div>" .
		               			 ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
		               		"</td>" ;
		            }
		            echo "</tr>" ;
		       }
		    ?>
		</tbody>
		</table>
		
		<p> </p>
		
		<!-- *********************************************************** ANEXO VII ******************************************************** -->
		
		<table style="width: 100%;" border="0" align="center">
		<tbody>
		<tr>
			<td style="text-align: center; background-color: #000099;" colspan="14">
				<span style="color: #ffffff;">
					<strong>
						Servidores ou Empregados não integrantes do quadro próprio, 
						em exercício no órgão sem Cargo em Comissão ou Função de 
						Confiança
					</strong>
				</span>
			</td>
		</tr>
		<tr>
			<td style="text-align: center;" colspan="14">
				<strong>
					Anexo VII
				</strong>
			</td>
		</tr>
		   <?php
		        $dataanx7 = getdate(); 
		        $anoatualanx7 = $dataanx7['year'];
		        $minanoanx7 = $anoatualanx7 - $anosretroativos;
		        if($minanoanx7 < 2009 ) {
		            $minanoanx7 = 2009;
		        }
		        $maxanoanx7 = $anoatualanx7;
		        for ($anoanx7 = $minanoanx7; $anoanx7 <= $maxanoanx7; $anoanx7++) {  // para cada ano...
		            echo "<tr>" ;
		            echo "<td>" . $anoanx7 . "</td>" ;
		            $anoarquivoanx7 = substr($anoanx7,2,2);  
		            for ($mesanx7 = 1; $mesanx7 <= 12; $mesanx7++) {  // para cada mes...
		                $arq =  "07_" . str_pad($mesanx7,2,"0",STR_PAD_LEFT) . "_" . $anoarquivoanx7 . ".pdf";  
		                if ($this->model->arquivo_valido($arq)) {
		                   echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
				                     ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		                   			 "<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>" . $meses[$mesanx7] ."</a></div>" .
		                   			 ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
		                   		"</td>";
		                } else {
		                   echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
				                     ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		                   			 $meses[$mesanx7] . "</div>" .
		                   			 ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
		                   		"</td>";                
		                }
		            }
		            // RAP...
		            $arq =  "07_00_" . $anoarquivoanx7 . ".pdf";
		            if ( $this->model->arquivo_valido($arq) ) { 
		               echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
				                 ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		               			 "<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>RAP</a></div>" . 
		               			 ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
		               		"</td>";
		            } else {
		               echo "<td style='text-align: center; background-color: #f8dfdf;'>". 
				                 ($gerenciar ? "<div style='float: left;'>" : "<div>") .
		               			 "RAP" . "</div>" .
		               			 ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
		               		"</td>" ;
		            }
		            echo "</tr>" ;
		       }
		    ?>
		</tbody>
		</table>
		
		<p> </p>
		
		<!-- ******************************************************** ANEXO VIII ***************************************************** -->
		
		<table style="width: 100%;" border="0" align="center">
		<tbody>
		<tr>
			<td style="text-align: center; background-color: #000099;" colspan="15">
				<span style="color: #ffffff;">
					<strong>
						Remunerações e Diárias pagas a Membros da Magistratura, Servidores, 
						Colaboradores e Colaboradores Eventuais
					</strong>
				</span>
			</td>
		</tr>
		<tr>
			<td style="text-align: center;" colspan="14">
				<strong>
					Anexo VIII
				</strong>
			</td>
		</tr>
		   <?php
		        $dataanx8 = getdate(); 
		        $anoatualanx8 = $dataanx8['year'];
		        $minanoanx8 = $anoatualanx8 - $anosretroativos;
		        if($minanoanx8 < 2009 ) {
		            $minanoanx8 = 2009;
		        }
		        $maxanoanx8 = $anoatualanx8;
		        for ($anoanx8 = $minanoanx8; $anoanx8 <= $maxanoanx8; $anoanx8++) {  // para cada ano...
		            echo "<tr>" ;
		            echo "<td>" . $anoanx8 . "</td>" ;
	                $anoarquivoanx8 = substr($anoanx8,2,2);  
	                for ($mesanx8 = 1; $mesanx8 <= 12; $mesanx8++) { // para cada mes...
	                    $arq =  "08_" . str_pad($mesanx8,2,"0",STR_PAD_LEFT) . "_" . $anoarquivoanx8 . ".pdf";  
	                    if ($this->model->arquivo_valido($arq)) {
	                       echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
			                         ($gerenciar ? "<div style='float: left;'>" : "<div>") .
	                       			 "<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>" . $meses[$mesanx8] ."</a></div>" .
	                       			 ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
	                       		"</td>";
	                    } else {
	                       echo "<td style='text-align: center; background-color: #f8dfdf;'>" . 
			                         ($gerenciar ? "<div style='float: left;'>" : "<div>") .
	                       			 $meses[$mesanx8] . "</div>" .
	                       			 ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
	                       		"</td>";                
	                    }
	                 }
	                 // RAP...
	                 $arq =  "08_00_" . $anoarquivoanx8 . ".pdf";
	                 if ( $this->model->arquivo_valido($arq) ) { 
	                    echo "<td style='text-align: center; background-color: #e0f1f6;'>" . 
			                      ($gerenciar ? "<div style='float: left;'>" : "<div>") .
	                    		  "<a href='" . $res102cnjdir . "/" . $arq . "' target='_blank'>RAP</a></div>". 
	                    		  ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, true) . "</div>" : "")   .
	                    	"</td>";
	                 } else {
	                    echo "<td style='text-align: center; background-color: #f8dfdf;'>" .
			                      ($gerenciar ? "<div style='float: left;'>" : "<div>") .
	                    		  "RAP" . "</div>" . 
	                    		  ($gerenciar ? "<div style='float: right;'>" . $btns->acao($arq, false) . "</div>" : "")   .
	                    	 "</td>" ;
	                 }
	            }
	            echo "</tr>" ;
		    ?>
		</tbody>
		</table>
<?php 
if($gerenciar) { 
?>	
		<!-- ****** CAMPOS DEFINIDOS PELO PROCESSAMENTO DO FORMULARIO ********* -->
		
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="arq" value="" />
		<input type="hidden" name="pageYOffset" value="" />
		<input type="hidden" name="pageXOffset" value="" />
	</form>
<?php 
}
?>

<!-- ******* PROCEDIMENTO PARA POSICIONAR A PAGINA NO LOCAL CORRETO ********* -->

<script type="text/javascript">
	function rolar() { 
		window.scrollTo(
			<?php if($pageXOffset) { echo $pageXOffset; } else { echo '0'; } ?>,
			<?php if($pageYOffset) { echo $pageYOffset; } else { echo '0'; } ?>
		);
	}
	window.addEventListener("pageshow", onEventProc, true);
	function onEventProc(aEvent) {
		var win = aEvent.currentTarget;
		var top_doc = win.document;
		var cur_doc = aEvent.target;
		if(top_doc == cur_doc) 
			rolar();
	}
</script>	
