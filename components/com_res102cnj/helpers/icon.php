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

use Joomla\Registry\Registry;

/**
 * Component HTML Helper
 *
 */
abstract class JHtmlIcon {
	
	/**
	 * Method to generate the icons links
	 *
	 * @param   object    $item  	The unavailability information
	 * @param   Registry  $params  	The item parameters
	 * @param   array     $attribs  Optional attributes for the link
	 * @param   boolean   $legacy   True to use legacy images, false to use icomoon based graphic
	 *
	 * @return  string  The HTML markup for the popup link
	 */
	public static function icons($item, $params, $attribs=array(), $legacy=false) {
		return
			'<div class="icons">'.
				'<div class="btn-group pull-right">'.
					'<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">'.
						'<span class="icon-cog"></span>'.
						'<span class="caret"></span>'.
					'</a>'.
					'<ul class="dropdown-menu">'.
						JHtmlIcon::print_popup($item, $params, $attribs, $legacy). 
						JHtmlIcon::pdf_popup($item, $params, $attribs, $legacy).
					'</ul>'.
				'</div>'.
			'</div>';
	}
	
	/**
	 * Method to generate a popup link to print an Unavailability Certificate
	 *
	 * @param   object    $item  	The unavailability information
	 * @param   Registry  $params  	The item parameters
	 * @param   array     $attribs  Optional attributes for the link
	 * @param   boolean   $legacy   True to use legacy images, false to use icomoon based graphic
	 *
	 * @return  string  The HTML markup for the popup link
	 */
	public static function print_popup($item, $params, $attribs=array(), $legacy=false) {
		$app = JFactory::getApplication();
		$input = $app->input;
		$request = $input->request;
	
		$url=
			'index.php?option=com_unavailability&view=unavailability&id='.(int)$item->id.
			'&tmpl=printfriendly&print=1&layout=default&page='.@$request->limitstart;
	
		$status=
			'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,'.
			'resizable=yes,width=640,height=480,directories=no,location=no';
	
		// Checks template image directory for image, if non found default are loaded
		if ($params->get('show_icons')) {
			if ($legacy) {
				$text=JHtml::_('image', 'system/printButton.png', JText::_('COM_UNAVAILABILITY_ICON_PRINT_POPUP'), null, true);
			} else {
				$text='<span class="icon-print"></span>'.JText::_('COM_UNAVAILABILITY_ICON_PRINT_POPUP');
			}
		} else {
			$text=JText::_('COM_UNAVAILABILITY_ICON_PRINT_POPUP');
		}
		
		$attribs = array(
			'title'=>JText::_('COM_UNAVAILABILITY_ICON_PRINT_POPUP').' '.htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'),
			'onclick'=>"window.open(this.href,'win2','" .$status. "'); return false;",
			'rel'=>	'nofollow',
		);
	
		return 
			'<li class="print-icon">'.
				JHtml::_('link', JRoute::_($url), $text, $attribs).
			'</li>';
	}
	
	/**
	 * Method to generate a popup link to export as PDF an Unavailability Certificate
	 *
	 * @param   object    $item  	The unavailability information
	 * @param   Registry  $params  	The item parameters
	 * @param   array     $attribs  Optional attributes for the link
	 * @param   boolean   $legacy   True to use legacy images, false to use icomoon based graphic
	 *
	 * @return  string  The HTML markup for the popup link
	 */
	public static function pdf_popup($item, $params, $attribs=array(), $legacy=false) {
		if (JPluginHelper::isEnabled('phocapdf', 'content')) {
			$app = JFactory::getApplication();
			$input = $app->input;
			$request = $input->request;
		
			$url=
				'index.php?option=com_unavailability&view=unavailability&id='.
				(int)$item->id.'&tmpl=component&format=pdf';
		
			$status=
				'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,'.
				'resizable=yes,width=640,height=480,directories=no,location=no';
			
			// Checks template image directory for image, if non found default are loaded
			if ($params->get('show_icons')) {
				if ($legacy) {
					$text=JHtml::_('image', 'system/printButton.png', JText::_('COM_UNAVAILABILITY_ICON_PDF_POPUP'), null, true);
				} else {
					$text='<span class="glyphicon glyphicon-file icon-file"></span>'.
						JHTML::_('image','media/com_phocapdf/images/pdf_button.png', JText::_('COM_UNAVAILABILITY_ICON_PDF_POPUP'));
				}
			} else {
				$text=JText::_('COM_UNAVAILABILITY_ICON_PDF_POPUP');
			}
		
			$attribs = array(
				'title'=>JText::_('COM_UNAVAILABILITY_ICON_PDF_POPUP').' '.htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'),
				'onclick'=>"window.open(this.href,'win2','".$status."'); return false;",
				'rel'=>	'nofollow',
			);
		
			return
				'<li class="print-icon">'. 
					JHtml::_('link', JRoute::_($url), $text, $attribs).
				'</li>';				
		} else {
			return false;
		}
	}
}