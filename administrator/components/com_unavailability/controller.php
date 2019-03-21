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

/**
 * Register external libraries
 */
JLoader::register('UnavailabilityHelper',JPATH_ADMINISTRATOR.'/components/com_unavailability/helpers/unavailability.php');

/**
 * Component Base Controller.
 * @author Marcus
 *
 */
class UnavailabilityController extends JControllerLegacy {
	/**
	 * The default view for the display method.
	 * Need to be set, since joomla sets 'unavailabilitys' instead.
	 * @var string
	 */
	protected $default_view = 'unavailabilities'; 

	public function display($cachable = false, $urlparams = false) {
		$view   = $this->input->get('view', 'unavailabilities');
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt('id');

		if ($view == 'unavailability' && $layout == 'edit' && !$this->checkEditId('com_unavailability.edit.unavailability', $id)) {
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_unavailability&view=unavailabilities', false));
				
			return false;
		}

		return parent::display($cachable, $urlparams);
	}
}