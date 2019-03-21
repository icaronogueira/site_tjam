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
 * @author Marcus
 *
 */
class UnavailabilityControllerSystem extends JControllerForm {
	/**
	 * @var    string  The prefix to use with controller messages.
	 */
	protected $text_prefix = 'COM_UNAVAILABILITY_SYSTEM';
	/**
	 * The default view for the display method.
	 * @var string
	 */
	protected $default_view = 'system';
	/**
	 * The URL view list variable.
	 *
	 * @var string
	 */
	protected $view_list = 'systems';
	/**
	 * Hold a JInput object for easier access to the input variables.
	 *
	 * @var    JInput
	 */
	protected $input;
	
	/**
	 * Register no-default tasks
	 * @param array $config
	 */
	public function __construct($config=array()) {
		parent::__construct($config);
		$this->registerTask('system.add', 'add');
		$this->registerTask('system.edit', 'edit');
		$this->registerTask('system.cancel', 'cancel');
		$this->registerTask('system.save', 'save');
		$this->registerTask('system.apply', 'apply');
		$this->registerTask('system.save2new', 'save2new');
		$this->registerTask('system.save2copy', 'save2copy');
	}
	
	/**
	 * 
	 */
	public function save2new() {
		$this->task = 'save2new';
		return parent::save();
	}
	
	/**
	 *
	 */
	public function save2copy() {
		$this->task = 'save2copy';
		return parent::save();
	}
	
	/**
	 * 
	 */
	public function apply() {
		$this->task = 'apply';
		return parent::save();
	}
	
	/**
	 * {@inheritDoc}
	 * @see JControllerLegacy::display()
	 */
	public function display($cachable=false, $urlparams = array()) {
		return parent::display($cachable,$urlparams);
	}
	
	/**
	 * {@inheritDoc}
	 * @see JControllerForm::allowAdd()
	 */
	protected function allowAdd($data = array()) {
		$user = JFactory::getUser();
		$categoryId = Joomla\Utilities\ArrayHelper::getValue($data, 'catid', $this->input->getInt('filter_category_id'), 'int');
				
		if($categoryId) { // if category has been passed in URL...
			// ...check category permission...
			return $user->authorise('core.create', $this->option.'.category.'.$categoryId);
		} 
		return parent::allowAdd($data);			
	}
	
	/**
	 * {@inheritDoc}
	 * @see JControllerForm::allowEdit()
	 */
	protected function allowEdit($data = array(), $key='id'){
		$user = JFactory::getUser();
		
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;
		$categoryId = 0;
		
		if($recordId) {
			$categoryId = (int) $this->getModel()->getItem($recordId)->catid;
		}
		if($categoryId) { // if category has been passed in URL...
			// ...check category permission...
			return $user->authorise('core.edit', $this->option.'.category.'.$categoryId);
		}
		return parent::allowEdit($data, $key);
	}
}