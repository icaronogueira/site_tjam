<?php
/**
 * @package     MMartinho.REST
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('JPATH_PLATFORM') or die;

use Joomla\Utilities\ArrayHelper;

jimport('mmartinho.rest.restapi');
jimport('mmartinho.rest.restable');
jimport('mmartinho.rest.restmodel');

/**
 * Prototype form model for REST 
 * API requests.
 *
 * @see    JForm
 * @see    JFormField
 * @see    JFormRule
 */
abstract class JModelRESTForm extends JRESTModel {
	
	/**
	 * Array of form objects.
	 *
	 * @var    JForm[]
	 */
	protected $_forms = array();

	/**
	 * Maps events to plugin groups.
	 *
	 * @var    array
	 */
	protected $events_map = null;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JModelLegacy
	 */
	public function __construct($config = array()) {
		$config['events_map'] = isset($config['events_map']) ? $config['events_map'] : array();

		$this->events_map = array_merge(
			array(
				'validate' => 'content',
			),
			$config['events_map']
		);

		parent::__construct($config);
	}
	
	/**
	 * After API REST response, this message is formated to show
	 * validation messages.
	 *
	 * @return string
	 */
	public function validationErrors() {
		$msg = '';
		// first verify if request was possible...
		if( $this->_errosRequest() <> '') {
			return
				'<div id="msg_erro" style="background-color: red; padding: 5px; color: white;">' .
					$this->_errosRequest() .
				'</div>';
		}
		$restResponse = $this->_restApi->response(); // API REST response
		if($restResponse <> '') { // if a response exists...
			if(!$restResponse->success) { // ...and isnt's succeeded...
				if($restResponse->message->error) {
					$msg = $restResponse->message->error . '<br />'; // base message
				}
				if(!is_null($restResponse->message->validation)) { // if validation errors exist...
					$validacoes = $restResponse->message->validation;
					foreach ($validacoes as $mensagens) { // for each validation error...
						foreach ($mensagens as $mensagem) {
							if($mensagem != '') {
								$msg .= $mensagem . '<br />';
							}
						}
					}
				}
				if($msg) {
					$msg = '<div id="msg_erro"" style="background-color: red; padding: 5px; color: white;">' . $msg . '</div>';
				}
			}
		}
		return $msg;
	}

	/**
	 * Abstract method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm|boolean  A JForm object on success, false on failure
	 */
	abstract public function getForm($data = array(), $loadData = true);

	/**
	 * Method to get a form object.
	 *
	 * @param   string   $name     The name of the form.
	 * @param   string   $source   The form source. Can be XML string if file flag is set to false.
	 * @param   array    $options  Optional array of options for the form creation.
	 * @param   boolean  $clear    Optional argument to force load a new form.
	 * @param   string   $xpath    An optional xpath to search for the fields.
	 *
	 * @return  JForm|boolean  JForm object on success, false on error.
	 *
	 * @see     JForm
	 */
	protected function loadForm($name, $source = null, $options = array(), $clear = false, $xpath = false) {
		// Handle the optional arguments.
		$options['control'] = ArrayHelper::getValue((array) $options, 'control', false);

		// Create a signature hash.
		$hash = md5($source . serialize($options));

		// Check if we can use a previously loaded form.
		if (isset($this->_forms[$hash]) && !$clear) {
			return $this->_forms[$hash];
		}

		// Get the form.
		JForm::addFormPath(JPATH_COMPONENT . '/models/forms');
		JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
		JForm::addFormPath(JPATH_COMPONENT . '/model/form');
		JForm::addFieldPath(JPATH_COMPONENT . '/model/field');

		try {
			$form = JForm::getInstance($name, $source, $options, false, $xpath);

			if (isset($options['load_data']) && $options['load_data']) {
				// Get the data for the form.
				$data = $this->loadFormData();
			} else {
				$data = array();
			}

			// Allow for additional modification of the form, and events to be triggered.
			// We pass the data because plugins may require it.
			$this->preprocessForm($form, $data);

			// Load the data into the form after the plugins have operated.
			$form->bind($data);
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}

		// Store the form for later.
		$this->_forms[$hash] = $form;

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array  The default data is an empty array.
	 */
	protected function loadFormData() {
		return array();
	}

	/**
	 * Method to allow derived classes to preprocess the data.
	 *
	 * @param   string  $context  The context identifier.
	 * @param   mixed   &$data    The data to be processed. It gets altered directly.
	 * @param   string  $group    The name of the plugin group to import (defaults to "content").
	 *
	 * @return  void
	 */
	protected function preprocessData($context, &$data, $group = 'content') {
		// Get the dispatcher and load the users plugins.
		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin($group);

		// Trigger the data preparation event.
		$results = $dispatcher->trigger('onContentPrepareData', array($context, &$data));

		// Check for errors encountered while preparing the data.
		if (count($results) > 0 && in_array(false, $results, true)) {
			$this->setError($dispatcher->getError());
		}
	}

	/**
	 * Method to allow derived classes to preprocess the form.
	 *
	 * @param   JForm   $form   A JForm object.
	 * @param   mixed   $data   The data expected for the form.
	 * @param   string  $group  The name of the plugin group to import (defaults to "content").
	 *
	 * @return  void
	 *
	 * @see     JFormField
	 * @throws  Exception if there is an error in the form event.
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'content') {
		// Import the appropriate plugin group.
		JPluginHelper::importPlugin($group);

		// Get the dispatcher.
		$dispatcher = JEventDispatcher::getInstance();

		// Trigger the form preparation event.
		$results = $dispatcher->trigger('onContentPrepareForm', array($form, $data));

		// Check for errors encountered while preparing the form.
		if (count($results) && in_array(false, $results, true)) {
			// Get the last error.
			$error = $dispatcher->getError();

			if (!($error instanceof Exception)) {
				throw new Exception($error);
			}
		}
	}

	/**
	 * Method to validate the form data.
	 *
	 * @param   JForm   $form   The form to validate against.
	 * @param   array   $data   The data to validate.
	 * @param   string  $group  The name of the field group to validate.
	 *
	 * @return  array|boolean  Array of filtered data if valid, false otherwise.
	 *
	 * @see     JFormRule
	 * @see     JFilterInput
	 */
	public function validate($form, $data, $group = null) {
		// Include the plugins for the delete events.
		JPluginHelper::importPlugin($this->events_map['validate']);

		$dispatcher = JEventDispatcher::getInstance();
		$dispatcher->trigger('onUserBeforeDataValidation', array($form, &$data));

		// Filter and validate the form data.
		$data = $form->filter($data);
		$return = $form->validate($data, $group);

		// Check for an error.
		if ($return instanceof Exception) {
			$this->setError($return->getMessage());
			return false;
		}

		// Check the validation results.
		if ($return === false) {
			// Get the validation messages from the form.
			foreach ($form->getErrors() as $message) {
				$this->setError($message);
			}
			return false;
		}

		// Tags B/C break at 3.1.2
		if (isset($data['metadata']['tags']) && !isset($data['tags'])) {
			$data['tags'] = $data['metadata']['tags'];
		}

		return $data;
	}

}
