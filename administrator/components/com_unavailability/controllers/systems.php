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
 * Controller to view 'Systems' 
 * @author Marcus
 */
class UnavailabilityControllerSystems extends JControllerAdmin {
	
	/**
	 * @param array $config
	 */
	public function __construct($config=array()) {
		parent::__construct($config);
		$this->registerTask('systems.unpublish', 'unpublish');
		$this->registerTask('systems.publish', 'publish');
		$this->registerTask('systems.archive', 'archive');
		$this->registerTask('systems.trash', 'trash');
		$this->registerTask('systems.checkin', 'checkin');
		$this->registerTask('systems.delete', 'delete');
		$this->registerTask('systems.saveOrderAjax', 'saveOrderAjax');
	}
	
	/**
	 * Get data from database.
	 * {@inheritDoc}
	 * @see JControllerLegacy::getModel()
	 */
	public function getModel(
			$name = 'System', 
			$prefix = 'UnavailabilityModel', 
			$config = array('ignore_request'=>true) ) {
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