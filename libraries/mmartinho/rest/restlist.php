<?php
/**
 * @package     MMartinho.REST
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('JPATH_PLATFORM') or die;

jimport('mmartinho.rest.restapi');
jimport('mmartinho.rest.resttable');

/**
 * Model class for handling lists of items 
 * doing a REST API request.
 */
class JModelRESTList extends JModelLegacy {
	
	/**
	 * REST API Classe Instance
	 * @var RESTApi
	 */
	protected $restApi=null;
	
	/**
	 * REST API Configuration
	 * @var array
	 */
	protected $restConfig=array();
	
	/**
	 * REST API response data itens
	 * @var StdObj[]
	 */
	protected $restData=null;
	
	/**
	 * REST API response data itens total
	 * @var integer
	 */
	protected $restTotal=null;
	
	/**
	 * REST API Query String to be Added
	 * @var string
	 */
	protected $restQueryString='';
	
	/**
	 * Internal memory based cache array of data.
	 *
	 * @var    array
	 */
	protected $cache = array();

	/**
	 * Context string for the model type.  This is used to handle uniqueness
	 * when dealing with the getStoreId() method and caching data structures.
	 *
	 * @var    string
	 */
	protected $context = null;

	/**
	 * Valid filter fields or ordering.
	 *
	 * @var    array
	 */
	protected $filter_fields = array();

	/**
	 * Name of the filter form to load
	 *
	 * @var    string
	 */
	protected $filterFormName = null;

	/**
	 * Associated HTML form
	 *
	 * @var  string
	 */
	protected $htmlFormName = 'adminForm';

	/**
	 * A blacklist of filter variables to not merge into the model's state
	 *
	 * @var    array
	 */
	protected $filterBlacklist = array();

	/**
	 * A blacklist of list variables to not merge into the model's state
	 *
	 * @var    array
	 */
	protected $listBlacklist = array('select');

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see JModelLegacy
	 */
	public function __construct($config = array()) {
		parent::__construct($config);
		
		// Add the ordering filtering fields whitelist.
		if (isset($config['filter_fields'])) {
			$this->filter_fields = $config['filter_fields'];
		}

		// Guess the context as Option.ModelName.
		if (empty($this->context)) {
			$this->context = strtolower($this->option . '.' . $this->getName());
		}
		
		// create the RESTApi instance
		if(!empty($config['restAPI'])) {
			$this->restConfig = $config['restAPI'];
			if(!empty($this->restConfig['restusername']) && !empty($this->restConfig['restpassword'])) {
				if(!empty($this->restConfig['restserverurl']) && !empty($this->restConfig['restclass'])) {
					$this->restApi = new RESTApi($this->restConfig['restusername'], $this->restConfig['restpassword']);
				} else {
					$this->setError('ERROR: REST Server URL or Class are empty');
				}
			} else {
				$this->setError('ERROR: REST API username or password are empty');
			}
		} else {
			$this->setError('ERROR: REST API configuration is empty');
		}
	}

	/**
	 * Function to get the active filters
	 *
	 * @return  array  Associative array in the format: array('filter_published' => 0)
	 *
	 */
	public function getActiveFilters() {
		$activeFilters = array();

		if (!empty($this->filter_fields)) {
			foreach ($this->filter_fields as $filter) {
				$filterName = 'filter.' . $filter;

				if (property_exists($this->state, $filterName) && (!empty($this->state->{$filterName}) || is_numeric($this->state->{$filterName}))) {
					$activeFilters[$filter] = $this->state->get($filterName);
				}
			}
		}

		return $activeFilters;
	}
	
	/**
	 * REST APT Request Errors.
	 *
	 * @return string
	 */
	private function _errosRequest() {
		return $this->restApi->erro;
	}
	
	/**
	 * Returns a success message passed by the REST API,
	 * formated to be shown on view.
	 *
	 * @return string
	 */
	public function successMessage() {
		$msg = '';
		$restResponse = $this->restApi->response();
		if($restResponse <> '') { // se existe resposta...
			if($restResponse->success) { // se não foi bem sucedido...
				if($restResponse->message != '') {
					$msg =
					'<div id="msg_sucesso" style="background-color: green; padding: 5px; color: white;">' . $restResponse->message . '</div>';
				}
			}
		}
		return $msg;
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
		$restResponse = $this->restApi->response(); // API REST response
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
	
	protected function getRestQueryString() {
		return $this->restQueryString;
	}
	
	/**
	 * @param string $class
	 * @param int $start
	 * @param int $limit
	 * @return boolean|array|object
	 */
	protected function _getRESTList() {
		if(count($this->getErrors()) == 0) {
			$qs = $this->getRestQueryString();
			$qsId = $this->getState('filter.id');
			
			$url=
				$this->restConfig['restserverurl']. 								
				'/'.$this->restConfig['restclass'].'/' .
				($qsId ? $qsId.'/' : '').
				'?offset='.$this->getState('list.start') .  						
				'&limit='.$this->getState('list.limit') . 								
				'&filter_order='.$this->getState('list.ordering').
				'&filter_order_dir='.$this->getState('list.direction').
				($qs ? '&'.$qs : '');
			
			$this->restApi->request($url, 'GET'); 			
			$restResponse = $this->restApi->response(); 	
			
			if($restResponse->success) {
				$this->restData = $restResponse->data->{$this->restConfig['restclass']};
				$this->restTotal = (int)$restResponse->data->totalCount;
			} else {
				$this->setError('ERROR: REST Response problems has beem found. '.$restResponse->message);
				$this->restData = null;
				$this->restTotal = null;
			}
			
			return $this->restData;
		} else {
			return false;
		}
	}
	
	/**
	 * 
	 */
	protected function _getRESTListCount() {
		if(is_null($this->restTotal)) {
			$this->_getRESTList();
		}
		return $this->restTotal;
	}
	
	/**
	 * @return boolean
	 */
	protected function _getRESTDownload() {
		if(count($this->getErrors()) == 0) {
			$qs = $this->getRestQueryString();
			$qsId = $this->getState('filter.id');
			
			if(empty($qsId)) {
				$this->setError('ERROR: É necessário fornecer o ID # do documento.');
				$this->restData = null;
				$this->restTotal = null;
				return false; 
			}
				
			$url=
				$this->restConfig['restserverurl'].
				'/'.$this->restConfig['restclass'].'/download/' .
				$qsId.
				($qs ? '&'.$qs : '');
				
			$this->restApi->request($url, 'GET');
			$restResponse = $this->restApi->response();
								
			if($restResponse->success) {
				$this->restData = $restResponse->data;
				$this->restTotal = (int)$restResponse->data->totalCount;
				
				$titulo=$restResponse->data->titulo;
				$descriacao=$restResponse->data->descricao;
				$arquivo=$restResponse->data->arquivo;
				$encoding=$restResponse->data->encoding;
				$fileSize=$restResponse->data->filesize;
				$conteudo=$restResponse->data->conteudo;
					
				ob_start();
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename='.$arquivo);
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				header('Content-Length: ' . $fileSize);
				echo pack("H*", $conteudo);
				ob_end_flush();
				exit;
			} else {
				$this->setError('ERROR: Não foi possível encontrar o DOCUMENTO com ID # ' . $qsId);
				$this->restData = null;
				$this->restTotal = null;
				return false;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * Method to get download.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 */
	public function doDownload() {
		try {
			return $this->_getRESTDownload();
		} catch (RuntimeException $e) {
			$this->setError($e->getMessage());
			return false;
		}
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 */
	public function getItems() {
		// Get a storage key.
		$store = $this->getStoreId();

		// Try to load the data from internal storage.
		if (isset($this->cache[$store])) {
			return $this->cache[$store];
		}

		try {
			// Load the list items and add the items to the internal cache.
			$this->cache[$store] = $this->_getRESTList();
		} catch (RuntimeException $e) {
			$this->setError($e->getMessage());

			return false;
		}

		return $this->cache[$store];
	}

	/**
	 * Method to get a JPagination object for the data set.
	 *
	 * @return  JPagination  A JPagination object for the data set.
	 */
	public function getPagination() {
		// Get a storage key.
		$store = $this->getStoreId('getPagination');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store])) {
			return $this->cache[$store];
		}

		$limit = (int) $this->getState('list.limit') - (int) $this->getState('list.links');

		// Create the pagination object and add the object to the internal cache.
		$this->cache[$store] = new JPagination($this->getTotal(), $this->getStart(), $limit, $this->getPrefix());

		return $this->cache[$store];
	}

	/**
	 * Method to get a store id based on the model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  An identifier string to generate the store id.
	 *
	 * @return  string  A store id.
	 */
	protected function getStoreId($id = '') {
		// Add the list state to the store id.
		$id .= ':' . $this->getState('list.start');
		$id .= ':' . $this->getState('list.limit');
		$id .= ':' . $this->getState('list.ordering');
		$id .= ':' . $this->getState('list.direction');
		$id .= ':' . $this->getState('list.prefix');

		return md5($this->context . ':' . $id);
	}
	
	/**
	 * Method to get the prefix used for request variables.
	 *
	 * @return  string  The prefix used.
	 */
	public function getPrefix() {
		// Get a storage key.
		$store = $this->getStoreId('getPrefix');
		
		// Try to load the data from internal storage.
		if (isset($this->cache[$store])) {
			return $this->cache[$store];
		}
		
		try {
			$this->cache[$store] = $this->getState('list.prefix');
		} catch (RuntimeException $e) {
			$this->setError($e->getMessage());
			
			return false;
		}
		
		return $this->cache[$store];
	}

	/**
	 * Method to get the total number of items for the data set.
	 *
	 * @return  integer  The total number of items available in the data set.
	 */
	public function getTotal() {
		// Get a storage key.
		$store = $this->getStoreId('getTotal');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store])) {
			return $this->cache[$store];
		}

		try {
			// Load the total and add the total to the internal cache.
			$this->cache[$store] = (int) $this->_getRESTListCount();
		} catch (RuntimeException $e) {
			$this->setError($e->getMessage());

			return false;
		}

		return $this->cache[$store];
	}

	/**
	 * Method to get the starting number of items for the data set.
	 *
	 * @return  integer  The starting number of items available in the data set.
	 */
	public function getStart() {
		$store = $this->getStoreId('getstart');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store])) {
			return $this->cache[$store];
		}

		$start = $this->getState('list.start');

		if ($start > 0) {
			$limit = $this->getState('list.limit');
			$total = $this->getTotal();

			if ($start > $total - $limit) {
				$start = max(0, (int) (ceil($total / $limit) - 1) * $limit);
			}
		}

		// Add the total to the internal cache.
		$this->cache[$store] = $start;

		return $this->cache[$store];
	}

	/**
	 * Get the filter form
	 *
	 * @param   array    $data      data
	 * @param   boolean  $loadData  load current data
	 *
	 * @return  JForm|boolean  The JForm object or false on error
	 */
	public function getFilterForm($data = array(), $loadData = true) {
		$form = null;

		// Try to locate the filter form automatically. Example: ContentModelArticles => "filter_articles"
		if (empty($this->filterFormName)) {
			$classNameParts = explode('Model', get_called_class());

			if (count($classNameParts) == 2) {
				$this->filterFormName = 'filter_' . strtolower($classNameParts[1]);
			}
		}

		if (!empty($this->filterFormName)) {
			// Get the form.
			$form = $this->loadForm($this->context . '.filter', $this->filterFormName, array('control' => '', 'load_data' => $loadData));
		}

		return $form;
	}

	/**
	 * Method to get a form object.
	 *
	 * @param   string   $name     The name of the form.
	 * @param   string   $source   The form source. Can be XML string if file flag is set to false.
	 * @param   array    $options  Optional array of options for the form creation.
	 * @param   boolean  $clear    Optional argument to force load a new form.
	 * @param   string   $xpath    An optional xpath to search for the fields.
	 *
	 * @return  JForm|boolean  JForm object on success, False on error.
	 *
	 * @see     JForm
	 */
	protected function loadForm($name, $source = null, $options = array(), $clear = false, $xpath = false) {
		// Handle the optional arguments.
		$options['control'] = JArrayHelper::getValue($options, 'control', false);

		// Create a signature hash.
		$hash = md5($source . serialize($options));

		// Check if we can use a previously loaded form.
		if (isset($this->_forms[$hash]) && !$clear) {
			return $this->_forms[$hash];
		}

		// Get the form.
		JForm::addFormPath(JPATH_COMPONENT . '/models/forms');
		JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');

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
	 * @return	mixed	The data for the form.
	 */
	protected function loadFormData() {
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState($this->context, new stdClass);

		// Pre-fill the list options
		if (!property_exists($data, 'list')) {
			$data->list = array(
				'direction' => $this->getState('list.direction'),
				'limit'     => $this->getState('list.limit'),
				'ordering'  => $this->getState('list.ordering'),
				'start'     => $this->getState('list.start'),
				'prefix'    => $this->getState('list.prefix'),
			);
		}

		return $data;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null) {
		// If the context is set, assume that stateful lists are used.
		if ($this->context) {
			$app         = JFactory::getApplication();
			$inputFilter = JFilterInput::getInstance();

			// Receive & set filters
			if ($filters = $app->getUserStateFromRequest($this->context . '.filter', 'filter', array(), 'array')) {
				foreach ($filters as $name => $value) {
					// Exclude if blacklisted
					if (!in_array($name, $this->filterBlacklist)) {
						$this->setState('filter.' . $name, $value);
					}
				}
			}

			$limit = 0;

			// Receive & set list options
			if ($list = $app->getUserStateFromRequest($this->context . '.list', 'list', array(), 'array')) {
				foreach ($list as $name => $value) {
					// Exclude if blacklisted
					if (!in_array($name, $this->listBlacklist)) {
						// Extra validations
						switch ($name) {
							case 'fullordering':
								$orderingParts = explode(' ', $value);

								if (count($orderingParts) >= 2) {
									// Latest part will be considered the direction
									$fullDirection = end($orderingParts);

									if (in_array(strtoupper($fullDirection), array('ASC', 'DESC', ''))) {
										$this->setState('list.direction', $fullDirection);
									}

									unset($orderingParts[count($orderingParts) - 1]);

									// The rest will be the ordering
									$fullOrdering = implode(' ', $orderingParts);

									if (in_array($fullOrdering, $this->filter_fields)) {
										$this->setState('list.ordering', $fullOrdering);
									}
								} else {
									$this->setState('list.ordering', $ordering);
									$this->setState('list.direction', $direction);
								}
								break;
							case 'ordering':
								if (!in_array($value, $this->filter_fields)) {
									$value = $ordering;
								}
								break;
							case 'direction':
								if (!in_array(strtoupper($value), array('ASC', 'DESC', ''))) {
									$value = $direction;
								}
								break;
							case 'limit':
								$value = $inputFilter->clean($value, 'int');
								$limit = $value;
								break;
							case 'select':
								$explodedValue = explode(',', $value);
								foreach ($explodedValue as &$field) {
									$field = $inputFilter->clean($field, 'cmd');
								}
								$value = implode(',', $explodedValue);
								break;
							case 'prefix':
								break;
						}
						$this->setState('list.' . $name, $value);
					}
				}
			} else { // Keep B/C for components previous to jform forms for filters
				// Pre-fill the limits
				$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'uint');
				$this->setState('list.limit', $limit);

				// Check if the ordering field is in the whitelist, otherwise use the incoming value.
				$value = $app->getUserStateFromRequest($this->context . '.ordercol', 'filter_order', $ordering);

				if (!in_array($value, $this->filter_fields)) {
					$value = $ordering;
					$app->setUserState($this->context . '.ordercol', $value);
				}

				$this->setState('list.ordering', $value);

				// Check if the ordering direction is valid, otherwise use the incoming value.
				$value = $app->getUserStateFromRequest($this->context . '.orderdirn', 'filter_order_Dir', $direction);

				if (!in_array(strtoupper($value), array('ASC', 'DESC', ''))) {
					$value = $direction;
					$app->setUserState($this->context . '.orderdirn', $value);
				}
				$this->setState('list.direction', $value);
			}

			// Support old ordering field
			$oldOrdering = $app->input->get('filter_order');

			if (!empty($oldOrdering) && in_array($oldOrdering, $this->filter_fields)) {
				$this->setState('list.ordering', $oldOrdering);
			}

			// Support old direction field
			$oldDirection = $app->input->get('filter_order_Dir');
			if (!empty($oldDirection) && in_array(strtoupper($oldDirection), array('ASC', 'DESC', ''))) {
				$this->setState('list.direction', $oldDirection);
			}
			$value = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0, 'int');
			$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
			$this->setState('list.start', $limitstart);
		} else {
			$this->setState('list.start', 0);
			$this->setState('list.limit', 0);
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
	 * @throws  Exception if there is an error in the form event.
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'content') {
		// Import the appropriate plugin group.
		JPluginHelper::importPlugin($group);

		// Get the dispatcher.
		$dispatcher = JDispatcher::getInstance();

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
	 * Gets the value of a user state variable and sets it in the session
	 *
	 * This is the same as the method in JApplication except that this also can optionally
	 * force you back to the first page when a filter has changed
	 *
	 * @param   string   $key        The key of the user state variable.
	 * @param   string   $request    The name of the variable passed in a request.
	 * @param   string   $default    The default value for the variable if not found. Optional.
	 * @param   string   $type       Filter for the variable, for valid values see {@link JFilterInput::clean()}. Optional.
	 * @param   boolean  $resetPage  If true, the limitstart in request is set to zero
	 *
	 * @return  mixed  The request user state.
	 */
	public function getUserStateFromRequest($key, $request, $default = null, $type = 'none', $resetPage = true) {
		$app       = JFactory::getApplication();
		$input     = $app->input;
		$old_state = $app->getUserState($key);
		$cur_state = (!is_null($old_state)) ? $old_state : $default;
		$new_state = $input->get($request, null, $type);

		// BC for Search Tools which uses different naming
		if ($new_state === null && strpos($request, 'filter_') === 0) {
			$name    = substr($request, 7);
			$filters = $app->input->get('filter', array(), 'array');

			if (isset($filters[$name])) {
				$new_state = $filters[$name];
			}
		}

		if (($cur_state != $new_state) && $new_state !== null && ($resetPage)) {
			$input->set('limitstart', 0);
		}

		// Save the new value only if it is set in this request.
		if ($new_state !== null) {
			$app->setUserState($key, $new_state);
		} else {
			$new_state = $cur_state;
		}

		return $new_state;
	}

	/**
	 * Parse and transform the search string into a string fit for regex-ing arbitrary strings against
	 *
	 * @param   string  $search          The search string
	 * @param   string  $regexDelimiter  The regex delimiter to use for the quoting
	 *
	 * @return  string  Search string escaped for regex
	 */
	protected function refineSearchStringToRegex($search, $regexDelimiter = '/') {
		$searchArr = explode('|', trim($search, ' |'));

		foreach ($searchArr as $key => $searchString) {
			if (strlen(trim($searchString)) == 0) {
				unset($searchArr[$key]);
				continue;
			}
			$searchArr[$key] = str_replace(' ', '.*', preg_quote(trim($searchString), $regexDelimiter));
		}

		return implode('|', $searchArr);
	}
	
	/**
	 * @return RESTApi
	 */
	public function getRESTApi() {
		return $this->restApi;
	}
	
	/**
	 * Method to get a resttable object, load it if necessary.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 *
	 * @return  JRESTTable  A JRESTTable object
	 * 
	 * @throws  Exception
	 */
	public function getRESTTable($name = '', $prefix = 'RESTTable') {
		if (empty($name)) {
			$name = $this->getName();
		}
	
		if ($resttable = $this->_createRESTTable($name, $prefix)) {
			return $resttable;
		}
	
		throw new Exception(JText::sprintf('JLIB_APPLICATION_ERROR_TABLE_NAME_NOT_SUPPORTED', $name), 0);
	}
	
	/**
	 * Method to load and return a model object.
	 *
	 * @param   string  $name    The name of the view
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration settings to pass to JTable::getInstance
	 *
	 * @return  JRESTTable|boolean  RESTTable object or boolean false if failed
	 *
	 * @see     JRESTTable::getInstance()
	 */
	protected function _createRESTTable($name, $prefix = 'RESTTable') {
		// Clean the model name
		$name = preg_replace('/[^A-Z0-9_]/i', '', $name);
		$prefix = preg_replace('/[^A-Z0-9_]/i', '', $prefix);
	
		return JRESTTable::getInstance($name, $prefix);
	}
}
