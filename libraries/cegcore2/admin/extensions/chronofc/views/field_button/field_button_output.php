<?php
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
?>
<?php
	$Html = new \G2\H\Html();
	
	$field_class = $this->Field->setup('button', $view, $this->Parser, $Html);
	
	echo $Html->input('button')->field($field_class);