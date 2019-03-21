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

/**
 * @author Marcus
 *
 */
class UnavailabilityControllerUpdunavailability extends JControllerForm {
	/**
	 * @var    string  The prefix to use with controller messages.
	 */
	protected $text_prefix = 'COM_UNAVAILABILITY_UPDUNAVAILABILITY';
	/**
	 * The default view for the display method.
	 * @var string
	 */
	protected $default_view = 'updunavailability';
	/**
	 * The URL view list variable.
	 *
	 * @var string
	 */
	protected $view_list = 'updunavailabilities';
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
		$this->registerTask('updunavailability.add', 'add');
		$this->registerTask('updunavailability.edit', 'edit');
		$this->registerTask('updunavailability.cancelar', 'cancel');
		$this->registerTask('updunavailability.aplicar', 'apply');
		$this->registerTask('updunavailability.salvar', 'save');
	}
	
	/**
	 * {@inheritDoc}
	 * @see JControllerForm::cancel()
	 */
	public function cancel($key=null) {
		$this->task = 'cancel';
		return parent::cancel($key);
	}
	
	/**
	 * {@inheritDoc}
	 * @see JControllerForm::save()
	 */
	public function apply() {
		$this->task = 'apply';
		return parent::save();
	}
	
	/**
	 * {@inheritDoc}
	 * @see JControllerForm::save()
	 */
	public function save($key=null,$urlVar=null) {
		$this->task = 'save';
		return parent::save($key,$urlVar);
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