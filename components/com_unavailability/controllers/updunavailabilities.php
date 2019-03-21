<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_unavailability
 *
 * @copyright   Copyright (C) 2016 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * @author Marcus
 *
 */
class UnavailabilityControllerUpdunavailabilities extends JControllerAdmin {
	
	/**
	 * @param array $config
	 */
	public function __construct($config=array()) {
		parent::__construct($config);
		$this->registerTask('updunavailabilities.saveOrderAjax', 'saveOrderAjax');
		$this->registerTask('updunavailabilities.unpublish', 'unpublish');
		$this->registerTask('updunavailabilities.publish', 'publish');
		$this->registerTask('updunavailabilities.archive', 'archive');
		$this->registerTask('updunavailabilities.trash', 'trash');
		$this->registerTask('updunavailabilities.checkin', 'checkin');
		$this->registerTask('updunavailabilities.delete', 'delete');
	}
	
	/**
	 * Get data from database.
	 * {@inheritDoc}
	 * @see JControllerLegacy::getModel()
	 */
	public function getModel(
		$name='Updunavailability', 
		$prefix='UnavailabilityModel', 
		$config=array('ignore_request'=>true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	/**
	 * {@inheritDoc}
	 * @see JControllerAdmin::publish()
	 */
	public function unpublish() {
		$this->task = 'unpublish';
		parent::publish();
	}
	
	/**
	 * {@inheritDoc}
	 * @see JControllerAdmin::publish()
	 */
	public function publish() {
		$this->task = 'publish';
		parent::publish();
	}
	
	/**
	 * {@inheritDoc}
	 * @see JControllerAdmin::publish()
	 */
	public function archive() {
		$this->task = 'archive';
		parent::publish();
	}
	
	/**
	 * {@inheritDoc}
	 * @see JControllerAdmin::publish()
	 */
	public function trash() {
		$this->task = 'trash';
		parent::publish();
	}
	
	/**
	 * {@inheritDoc}
	 * @see JControllerAdmin::checkin()
	 */
	public function checkin() {
		$this->task = 'checkin';
		parent::checkin();
	}
	
	/**
	 * {@inheritDoc}
	 * @see JControllerAdmin::delete()
	 */
	public function delete() {
		$this->task = 'delete';
		parent::delete();
	}
}