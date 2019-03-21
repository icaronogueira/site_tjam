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
?>

<?php $fullCalendarLibUrl = JURI::base().'components/com_unavailability/libraries/fullcalendar-3.7.0/'; ?>

<!-- FullCalendar Source -->

<link href="<?php echo $fullCalendarLibUrl;?>fullcalendar.min.css" rel="stylesheet" />
<link href="<?php echo $fullCalendarLibUrl;?>fullcalendar.print.min.css" rel="stylesheet" media="print" />
<script src="<?php echo $fullCalendarLibUrl;?>lib/jquery.min.js"></script>
<script src="<?php echo $fullCalendarLibUrl;?>lib/moment.min.js"></script>
<script src="<?php echo $fullCalendarLibUrl;?>fullcalendar.min.js"></script>
<script src="<?php echo $fullCalendarLibUrl;?>locale-all.js"></script>

<?php 
global $Itemid; // item de menu

$menuid = ($Itemid) ? '&Itemid=' . $Itemid : '';

$defaultDate = date('Y-m-d');
?>

<!-- FullCalendar Configuration, Date events JSON getting and Event Handlers -->

<script>
var $j = jQuery.noConflict();
$j(document).ready(function() {
	$j('#calendar').fullCalendar({
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
		},
		defaultDate: '<?php echo $defaultDate;?>',
		buttonIcons: false, // show the prev/next text
		weekNumbers: false,
		navLinks: true, // can click day/week names to navigate views
		editable: true,
		eventLimit: true, // allow "more" link when too many events
		locale: 'pt-br',	
		events: {
			url: 'index.php?option=com_unavailability&task=events',
			error: function() {
				$j('#error-message').show();
			},
			success: function() {
				$j('#error-message').hide();	
			}
		},	
		eventClick: function(event) {
			// opens events in a popup window
			window.open(event.url, 'UnavailabilityDocument', 'width=700,height=600');
			return false;
		},
		eventMouseover: function(event) {
			this.title = event.title;
		},
		eventMouseout: function(event) {
		},		
		loading: function(bool) {
			$j('#loading').toggle(bool);
		}
	});
	$j(document).ajaxComplete(function() {
		function addClass() {
		  $j(".fc-event-container a").each(function() {
		  	var title = $j(this).text();
		  	console.log(title);
		  	if (title.indexOf('SAJ') > -1) {
		  	  $j(this).addClass("saj");
		  	  console.log("saj");
		  	}
		  	if (title.indexOf('Projudi') > -1) {
		  	  $j(this).addClass("projudi");
		  	  console.log("projudi");
		  	}
		  });
		}
		setTimeout(addClass,500);
		setInterval(addClass,500);
	});
});
</script>

<!-- FullCalendar Style -->

<style>	
	#error-message {
		display: none;
		background: #eee;
		border-bottom: 1px solid #ddd;
		padding: 0 10px;
		line-height: 40px;
		text-align: center;
		font-weight: bold;
		font-size: 12px;
		color: red;
	}
	
	#loading {
		display: none;
		background: #eee;
		border-bottom: 1px solid #ddd;
		padding: 0 10px;
		line-height: 40px;
		text-align: center;
		font-weight: bold;
		font-size: 12px;
	}

	#calendar {
		max-width: 900px;
		margin: 40px auto;
		padding: 0 10px;
	}
	
	.legenda span {
		padding: 3px;
		display: inline-block;
		margin:  0 4px;
		border-radius: 5px;
	}
	
	.fc-event,
	.outros {
		background-color: #004263;
		border: 1px solid #004263;
		color: #fff;
	}
	
	.fc-event.saj,
	.saj {
		background-color: #3aa1ad;
		border: 1px solid #3aa1ad;
		color: #fff;
	}
	
	.fc-event.projudi,
	.projudi {
		background-color: #3a87ae;
		border: 1px solid #3a87ae;
		color: #fff;
	}
	
	.fc-event.saj.projudi,
	.saj.projudi {
		background-color: #559097;
		border: 1px solid #559097;
		color: #fff;
	}
	
	.fc-time {
		display: none;
	}
	
	.fc-center {
		text-transform: capitalize;
	}		
</style>

<!-- Title -->

<div class="page-header"><h2>Calendário de Indisponibilidade de Sistemas</h2></div>

<!-- Intro Text -->

<p>O Tribunal de Justiça do Estado do Amazonas, diante da eventual possibilidade de sistemas 
se tornarem indisponíveis por motivos técnicos, disponibiliza nesta área a consulta ao relatório 
oficial dos períodos em que houve o impedimento do uso de sistemas.</p> 

<p>O TJAM reconhece a validade das informações aqui fornecidas, sendo de competência de cada magistrado 
deliberar sobre a pertinência de eventual pedido de devolução de prazo a partir das informações prestadas,
nos termos do art. 10, § 2º da Lei 11.419/2006.</p>

<small class="legenda">
	Legenda: 
		<span class="saj">Saj</span>
		<span class="projudi">Projudi</span>
		<span class="saj projudi">SAJ e Projudi</span>
		<span class="outros">Outros</span>
</small>

<hr />

<!-- Error Message -->

<div id='error-message'>Erro</div>

<!-- Loading Message -->

<div id='loading'>Carregando...</div>

<!-- FullCalendar Positioning -->

<div id='calendar'></div>

<hr />

<p>
	Veja também: 
	<a href="index.php?option=com_unavailability&view=unavailabilities<?php echo $menuid; ?>">
		Lista de Indisponibilidade de Sistemas
	</a>
</p>

