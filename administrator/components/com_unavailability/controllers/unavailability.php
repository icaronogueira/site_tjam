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
class UnavailabilityControllerUnavailability extends JControllerForm {
	/**
	 * @var    string  The prefix to use with controller messages.
	 */
	protected $text_prefix = 'COM_UNAVAILABILITY_UNAVAILABILITY';
	/**
	 * The default view for the display method.
	 * @var string
	 */
	protected $default_view = 'unavailability';
	/**
	 * The URL view list variable.
	 *
	 * @var string
	 */
	protected $view_list = 'unavailabilities';
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
		$this->registerTask('unavailability.add', 'add');
		$this->registerTask('unavailability.edit', 'edit');
		$this->registerTask('unavailability.cancel', 'cancel');
		$this->registerTask('unavailability.save', 'save');
		$this->registerTask('unavailability.apply', 'apply');
		$this->registerTask('unavailability.save2new', 'save2new');
		$this->registerTask('unavailability.save2copy', 'save2copy');
		$this->registerTask('unavailability.saveNewSystemAjax', 'saveNewSystemAjax');
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
	
	/**
	 * Method to save a new System submitted 
	 * POST data using AJAX, returning a JSON 
	 * response with an selectbox option 
	 *
	 * @return  void
	 */
	public function saveNewSystemAjax() {
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		
		if(!JSession::checkToken()) { echo 0; $app->close(); }
		
		$data  = $this->input->post->get('jform', array(), 'array');
		$system = $this->getModel('System','UnavailabilityModel');
		$systems = $this->getModel('Systems','UnavailabilityModel'); 
		$table = $system->getTable();
		$checkin = property_exists($table, 'checked_out');
		$context = "$this->option.edit.$this->context";
		
		// Determine the name of the primary key for the System data.
		$key = $table->getKeyName();
		
		// Access check...
		$canCreate = $user->authorise('core.create', $this->option) || 
			count($user->getAuthorisedCategories($this->option, 'core.create'));
		if (!$canCreate) {
			echo JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'); 
			$app->close();
		}
		
		// Validate the posted data.
		$form = $system->getForm($data, false);
		if(!$form) {
			foreach ($system->getErrors() as $error) {
				$errors .= $error .'<br />';
			}
			echo $errors; $app->close();
		}
		
		// Test whether the data is valid...
		$validData = $system->validate($form, $data);
		if ($validData === false) {
			// Get the validation messages...
			foreach ($system->getErrors() as $error) {
				$errors .= $error .'<br />';
			}
			echo $errors; $app->close();
		}
		
		// Attempt to save the data...
		if (!$system->save($validData)) {
			// Get the save attempt errors messages.
			foreach ($system->getErrors() as $error) {
				$errors .= $error .'<br />';
			}
			echo $errors; $app->close();
		}
		
		$item = $system->getItem($system->getDbo()->insertid()); // item recently added
		
		$optionToAppend = '<option value="'.$item->id. '">'.$item->title.'</option>';
		
		// prepare array response...
		$response = array(
			'msg'=>JText::_('COM_UNAVAILABILITY_NEW_SYSTEM_ADDED'),
			'option'=>$optionToAppend,
			'id'=>$item->id,
		);
		
		echo json_encode($response); // format array response to JSON 
		
		$app->close();
	}
}