/**
 * @package     Joomla.Administrator
 * @subpackage  com_unavailability
 * @author		Marcus Martinho (marcus.martinho@tjam.jus.br)
 *
 * @copyright   Copyright (C) 2016 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * system_dialog events and configuration
 */
jQuery(document).ready( // when document is totaly loaded...
	function() {
		// array to setup system_dialog configuration to show as modal...
		jQuery('#system_dialog').dialog({autoOpen:false, modal:true});  
		// event attached to new-system span tag...
		jQuery('#new-system-button').on('click', 
			function() {
				jQuery('#system_dialog').dialog('open'); // open dialog
			} 
		);
		// event onclick attached to new-system-add span tag...
		jQuery('#new-system-add-button').on('click',
			function() {
				var values = jQuery('#system_dialog_form').serialize(); // serialize all dialog form data
				// assyncronous json ajax function called
				jQuery.ajax(
					{ 
						type:'POST',
						dataType:'json',
						cache:false,
						url:'index.php?option=com_unavailability&task=unavailability.saveNewSystemAjax&tmpl=component',
						data: values,
						success: function(response) {
							if(typeof response === 'object') {
								jQuery('#system_dialog').dialog('close'); // close the system_dialog
								jQuery('#jform_sistemas').append(response.option); // add the option to select input box
								jQuery('#jform_sistemas > option[value="'+response.id+'"]').prop('selected', true); // select the option added
							}
						}
					}
				);
			}
		);
	}
);