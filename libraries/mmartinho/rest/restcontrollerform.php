<?php
/**
 * @package     MMartinho.REST
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('JPATH_PLATFORM') or die;

jimport('mmartinho.rest.restform');
jimport('mmartinho.rest.restcontroller');
jimport('mmartinho.rest.restmodel');

/**
 * Controller tailored to suit most form-based REST API 
 * operations
 */
class JControllerRESTForm extends JControllerRest {
	/**
	 * The context for storing internal data, e.g. record.
	 *
	 * @var    string
	 */
	protected $context;

	/**
	 * The URL option for the component.
	 *
	 * @var    string
	 */
	protected $option;

	/**
	 * The URL view item variable.
	 *
	 * @var    string
	 */
	protected $view_item;

	/**
	 * The URL view list variable.
	 *
	 * @var    string
	 */
	protected $view_list;

	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 */
	protected $text_prefix;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JControllerLegacy
	 * @throws  Exception
	 */
	public function __construct($config = array()) {
		parent::__construct($config);

		// Guess the option as com_NameOfController
		if (empty($this->option)) {
			$this->option = 'com_' . strtolower($this->getName());
		}

		// Guess the JText message prefix. Defaults to the option.
		if (empty($this->text_prefix)) {
			$this->text_prefix = strtoupper($this->option);
		}

		// Guess the context as the suffix, eg: OptionControllerContent.
		if (empty($this->context)) {
			$r = null;
			if (!preg_match('/(.*)Controller(.*)/i', get_class($this), $r)) {
				throw new Exception(JText::_('JLIB_APPLICATION_ERROR_CONTROLLER_GET_NAME'), 500);
			}
			$this->context = strtolower($r[2]);
		}

		// Guess the item view as the context.
		if (empty($this->view_item)) {
			$this->view_item = $this->context;
		}

		// Guess the list view as the plural of the item view.
		if (empty($this->view_list)) {
			// Simple pluralisation based on public domain snippet by Paul Osman
			// For more complex types, just manually set the variable in your class.
			$plural = array(
				array('/(x|ch|ss|sh)$/i', "$1es"),
				array('/([^aeiouy]|qu)y$/i', "$1ies"),
				array('/([^aeiouy]|qu)ies$/i', "$1y"),
				array('/(bu)s$/i', "$1ses"),
				array('/s$/i', 's'),
				array('/$/', 's'),
			);

			// Check for matches using regular expressions
			foreach ($plural as $pattern) {
				if (preg_match($pattern[0], $this->view_item)) {
					$this->view_list = preg_replace($pattern[0], $pattern[1], $this->view_item);
					break;
				}
			}
		}

		// Apply, Save & New, and Save As copy should be standard on forms.
		$this->registerTask('apply', 'save');
		$this->registerTask('save2new', 'save');
		$this->registerTask('save2copy', 'save');
	}

	/**
	 * Method to add a new record.
	 *
	 * @return  boolean  True if the record can be added, false if not.
	 */
	public function add() {
		$context = "$this->option.edit.$this->context";

		// Access check.
		if (!$this->allowAdd()) {
			// Set the internal error and also the redirect error.
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToListAppend(), false
				)
			);

			return false;
		}

		// Clear the record edit information from the session.
		JFactory::getApplication()->setUserState($context . '.data', null);

		// Redirect to the edit screen.
		$this->setRedirect(
			JRoute::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_item
				. $this->getRedirectToItemAppend(), false
			)
		);

		return true;
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean
	 */
	protected function allowAdd($data = array()){
		$user = JFactory::getUser();
		return $user->authorise('core.create', $this->option) || count($user->getAuthorisedCategories($this->option, 'core.create'));
	}

	/**
	 * Method to check if you can edit an existing record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 */
	protected function allowEdit($data = array(), $key = 'id') {
		return JFactory::getUser()->authorise('core.edit', $this->option);
	}

	/**
	 * Method to check if you can save a new or existing record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 *
	 * @return  boolean
	 */
	protected function allowSave($data, $key = 'id') {
		$recordId = isset($data[$key]) ? $data[$key] : '0';

		if ($recordId) {
			return $this->allowEdit($data, $key);
		} else {
			return $this->allowAdd($data);
		}
	}

	/**
	 * Method to run batch operations.
	 *
	 * @param   JModelLegacy  $model  The model of the component being processed.
	 *
	 * @return	boolean	 True if successful, false otherwise and internal error is set.
	 */
	public function batch($model) {
		$vars = $this->input->post->get('batch', array(), 'array');
		$cid  = $this->input->post->get('cid', array(), 'array');

		// Build an array of item contexts to check
		$contexts = array();

		foreach ($cid as $id) {
			// If we're coming from com_categories, we need to use extension vs. option
			if (isset($this->extension)) {
				$option = $this->extension;
			} else {
				$option = $this->option;
			}

			$contexts[$id] = $option . '.' . $this->context . '.' . $id;
		}

		// Attempt to run the batch operation.
		if ($model->batch($vars, $cid, $contexts)) {
			$this->setMessage(JText::_('JLIB_APPLICATION_SUCCESS_BATCH'));
			return true;
		} else {
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_FAILED', $model->getError()), 'warning');
			return false;
		}
	}

	/**
	 * Method to cancel an edit.
	 *
	 * @param   string  $key  The name of the primary key of the URL variable.
	 *
	 * @return  boolean  True if access level checks pass, false otherwise.
	 */
	public function cancel($key = null) {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$model = $this->getModel();
		$resttable = $model->getRESTTable();
		$context = "$this->option.edit.$this->context";

		if (empty($key)) {
			$key = $resttable->getKeyName();
		}

		$recordId = $this->input->getInt($key);

		// Clean the session data and redirect.
		$this->releaseEditId($context, $recordId);
		JFactory::getApplication()->setUserState($context . '.data', null);

		$this->setRedirect(
			JRoute::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_list
				. $this->getRedirectToListAppend(), false
			)
		);
		return true;
	}

	/**
	 * Method to edit an existing record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key
	 *                           (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if access level check and checkout passes, false otherwise.
	 */
	public function edit($key = null, $urlVar = null) {
		// Do not cache the response to this, its a redirect, and mod_expires and google chrome browser bugs cache it forever!
		JFactory::getApplication()->allowCache(false);

		$model = $this->getModel();
		$resttable = $model->getRESTTable();
		$cid   = $this->input->post->get('cid', array(), 'array');
		$context = "$this->option.edit.$this->context";

		// Determine the name of the primary key for the data.
		if (empty($key)) {
			$key = $resttable->getKeyName();
		}

		// To avoid data collisions the urlVar may be different from the primary key.
		if (empty($urlVar)) {
			$urlVar = $key;
		}

		// Get the previous record id (if any) and the current record id.
		$recordId = (int) (count($cid) ? $cid[0] : $this->input->getInt($urlVar));

		// Access check.
		if (!$this->allowEdit(array($key => $recordId), $key)) {
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToListAppend(), false
				)
			);

			return false;
		}

		// push the new record id into the session.
		$this->holdEditId($context, $recordId);
		JFactory::getApplication()->setUserState($context . '.data', null);

		$this->setRedirect(
			JRoute::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_item
				. $this->getRedirectToItemAppend($recordId, $urlVar), false
			)
		);
		
		return true;	
	}
	
	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JModelRESTForm|boolean  Model object on success; otherwise false on failure.
	 *
	 */
	public function getModel($name = '', $prefix = '', $config = array('ignore_request'=>true)) {
		if (empty($name)) {
			$name = $this->context;
		}
	
		if (empty($prefix)) {
			$prefix = $this->model_prefix;
		}
	
		if ($model = $this->createRESTModel($name, $prefix, $config)) {
			// Task is a reserved state
			$model->setState('task', $this->task);
	
			// Let's get the application object and set menu information if it's available
			$menu = JFactory::getApplication()->getMenu();
	
			if (is_object($menu)) {
				if ($item = $menu->getActive()) {
					$params = $menu->getParams($item->id);
	
					// Set default state data
					$model->setState('parameters.menu', $params);
				}
			}
		}
	
		return $model;
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id') {
		$append = '';

		// Setup redirect info.
		if ($tmpl = $this->input->get('tmpl', '', 'string')) {
			$append .= '&tmpl=' . $tmpl;
		}

		if ($layout = $this->input->get('layout', 'edit', 'string')) {
			$append .= '&layout=' . $layout;
		}

		if ($forcedLanguage = $this->input->get('forcedLanguage', '', 'cmd')) {
			$append .= '&forcedLanguage=' . $forcedLanguage;
		}

		if ($recordId) {
			$append .= '&' . $urlVar . '=' . $recordId;
		}

		return $append;
	}

	/**
	 * Gets the URL arguments to append to a list redirect.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 */
	protected function getRedirectToListAppend() {
		$append = '';

		// Setup redirect info.
		if ($tmpl = $this->input->get('tmpl', '', 'string')) {
			$append .= '&tmpl=' . $tmpl;
		}
		
		if ($layout = $this->input->get('layout', '', 'string')) {
			$append .= '&layout=' . $layout;
		}

		if ($forcedLanguage = $this->input->get('forcedLanguage', '', 'cmd')) {
			$append .= '&forcedLanguage=' . $forcedLanguage;
		}

		return $append;
	}

	/**
	 * Function that allows child controller access to model data
	 * after the data has been saved.
	 *
	 * @param   JModelLegacy  $model      The data model object.
	 * @param   array         $validData  The validated data.
	 *
	 * @return  void
	 */
	protected function postSaveHook(JRESTModel $model, $validData = array()) {
	}

	/**
	 * Method to load a row from version history
	 *
	 * @return  mixed  True if the record can be added, an error object if not.
	 */
	public function loadhistory() {
		$model = $this->getModel();
		$table = $model->getTable();
		$historyId = $this->input->getInt('version_id', null);

		if (!$model->loadhistory($historyId, $table)) {
			$this->setMessage($model->getError(), 'error');

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToListAppend(), false
				)
			);

			return false;
		}

		// Determine the name of the primary key for the data.
		if (empty($key)) {
			$key = $table->getKeyName();
		}

		$recordId = $table->$key;

		// To avoid data collisions the urlVar may be different from the primary key.
		$urlVar = empty($this->urlVar) ? $key : $this->urlVar;

		// Access check.
		if (!$this->allowEdit(array($key => $recordId), $key)) {
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToListAppend(), false
				)
			);
			$table->checkin();

			return false;
		}

		$table->store();
		$this->setRedirect(
			JRoute::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_item
				. $this->getRedirectToItemAppend($recordId, $urlVar), false
			)
		);

		$this->setMessage(
			JText::sprintf(
				'JLIB_APPLICATION_SUCCESS_LOAD_HISTORY', $model->getState('save_date'), $model->getState('version_note')
			)
		);

		// Invoke the postSave method to allow for the child class to access the model.
		$this->postSaveHook($model);

		return true;
	}

	/**
	 * Method to save a record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 */
	public function save($key = null, $urlVar = null) {
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app   = JFactory::getApplication();
		$model = $this->getModel();
		$table = $model->getRESTTable();
		$data  = $this->input->post->get('jform', array(), 'array');
		$context = "$this->option.edit.$this->context";
		$task = $this->getTask();

		// Determine the name of the primary key for the data.
		if (empty($key)) {
			$key = $table->getKeyName();
		}

		// To avoid data collisions the urlVar may be different from the primary key.
		if (empty($urlVar)) {
			$urlVar = $key;
		}

		$recordId = $this->input->getInt($urlVar);

		// Populate the row id from the session.
		$data[$key] = $recordId;

		// The save2copy task needs to be handled slightly differently.
		if ($task == 'save2copy') {
			// Reset the ID, the multilingual associations and then treat the request as for Apply.
			$data[$key] = 0;
			$data['associations'] = array();
			$task = 'apply';
		}

		// Access check.
		if (!$this->allowSave($data, $key)) {
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToListAppend(), false
				)
			);

			return false;
		}

		// Validate the posted data.
		// Sometimes the form needs some posted data, such as for plugins and modules.
		$form = $model->getForm($data, false);

		if (!$form) {
			$app->enqueueMessage($model->getError(), 'error');

			return false;
		}

		// Test whether the data is valid.
		$validData = $model->validate($form, $data);

		// Check for validation errors.
		if ($validData === false) {
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if ($errors[$i] instanceof Exception) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState($context . '.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($recordId, $urlVar), false
				)
			);

			return false;
		}

		if (!isset($validData['tags'])) {
			$validData['tags'] = null;
		}

		// Attempt to save the data.
		if (!$model->save($validData)) {
			// Save the data in the session.
			$app->setUserState($context . '.data', $validData);

			// Redirect back to the edit screen.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($recordId, $urlVar), false
				)
			);

			return false;
		}

		$langKey = $this->text_prefix . ($recordId == 0 && $app->isClient('site') ? '_SUBMIT' : '') . '_SAVE_SUCCESS';
		$prefix  = JFactory::getLanguage()->hasKey($langKey) ? $this->text_prefix : 'JLIB_APPLICATION';

		$this->setMessage(JText::_($prefix . ($recordId == 0 && $app->isClient('site') ? '_SUBMIT' : '') . '_SAVE_SUCCESS'));

		// Redirect the user and adjust session state based on the chosen task.
		switch ($task) {
			case 'apply':
				// Set the record data in the session.
				$recordId = $model->getState($this->context . '.id');
				$this->holdEditId($context, $recordId);
				$app->setUserState($context . '.data', null);

				// Redirect back to the edit screen.
				$this->setRedirect(
					JRoute::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_item
						. $this->getRedirectToItemAppend($recordId, $urlVar), false
					)
				);
				break;
			case 'apply2show':
				// Set the record data in the session.
				$recordId = $model->getState($this->context . '.id');
				$this->holdEditId($context, $recordId);
				$app->setUserState($context . '.data', null);
			
				// Redirect back to the edit screen.
				$this->setRedirect(
					JRoute::_(
							'index.php?option=' . $this->option . '&view=' . $this->view_item
							. $this->getRedirectToItemAppend($recordId, $urlVar), false
						)
					);
				break;
			case 'save2new':
				// Clear the record id and data from the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState($context . '.data', null);

				// Redirect back to the edit screen.
				$this->setRedirect(
					JRoute::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_item
						. $this->getRedirectToItemAppend(null, $urlVar), false
					)
				);
				break;

			default:
				// Clear the record id and data from the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState($context . '.data', null);

				// Redirect to the list screen.
				$this->setRedirect(
					JRoute::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_list
						. $this->getRedirectToListAppend(), false
					)
				);
				break;
		}

		// Invoke the postSave method to allow for the child class to access the model.
		$this->postSaveHook($model, $validData);

		return true;
	}
	
	/**
	 * Method to load and return a model object.
	 *
	 * @param   string  $name    The name of the model.
	 * @param   string  $prefix  Optional model prefix.
	 * @param   array   $config  Configuration array for the model. Optional.
	 *
	 * @return  JModelREST|boolean   Model object on success; otherwise false on failure.
	 *
	 */
	protected function createRESTModel($name, $prefix = '', $config = array()) {
		// Clean the model name
		$modelName = preg_replace('/[^A-Z0-9_]/i', '', $name);
		$classPrefix = preg_replace('/[^A-Z0-9_]/i', '', $prefix);
	
		return JRESTModel::getInstance($modelName, $classPrefix, $config);
	}
}
