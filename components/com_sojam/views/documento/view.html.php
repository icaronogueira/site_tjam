<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_listing
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * @author Marcus
 *
 */
class ListingViewDocumento extends JViewLegacy {
	/**
	 * @var array
	 */
	protected $params;
	
	/**
	 * {@inheritDoc}
	 * @see JViewLegacy::display()
	 */
	public function display($tpl=null) {
		$app = JFactory::getApplication('site'); // frontend application
		$task = $app->input->get('task');
		$model = $this->getModel();
		if($task == 'download') {
			if(!$model->doDownload()) {
				throw new RuntimeException($model->getError(), 500);
				return false;
			}
		} else {
			throw new RuntimeException('ERROR: Tarefa de documento não encontrada', 500);
			return false;
		}		
	}
}