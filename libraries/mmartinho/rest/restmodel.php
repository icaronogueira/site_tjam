<?php
/**
 * @package     MMartinho.Rest
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('JPATH_PLATFORM') or die;

use Joomla\Utilities\ArrayHelper;
use Imagine\Exception\Exception;
use Imagine\Exception\RuntimeException;

/**
 * Base class for a Joomla REST Model
 *
 * Acts as a Factory class for application specific objects and provides many supporting API functions.
 *
 */
abstract class JRESTModel extends JObject {
	/**
	 * REST API Class instance
	 *
	 * @var RESTApi
	 */
	protected $_restApi;
	
	/**
	 * REST API config
	 *
	 * @var array
	 */
	protected $_restConfig;
	
	/**
	 * REST API response data itens
	 * @var StdObj[]
	 */
	protected $_restData=null;
	
	/**
	 * REST API response data itens total
	 * @var integer
	 */
	protected $_restTotal=null;
	
	/**
	 * REST API Query String to be Added
	 * @var string
	 */
	protected $_restQueryString='';
	
	/**
	 * Indicates if the internal state has been set
	 *
	 * @var    boolean
	 */
	protected $__state_set = null;

	/**
	 * The model (base) name
	 *
	 * @var    string
	 */
	protected $name;

	/**
	 * The URL option for the component.
	 *
	 * @var    string
	 */
	protected $option = null;

	/**
	 * A state object
	 *
	 * @var    JObject
	 */
	protected $state;

	/**
	 * The event to trigger when cleaning cache.
	 *
	 * @var    string
	 */
	protected $event_clean_cache = null;
	
	/**
	 * Constructor
	 * 
	 * @author MMartinho (20/07/2017)
	 *
	 * @param   array  $config  An array of configuration options
	 *                          (name, state, restApi, table_path, ignore_request).
	 *
	 * @throws  Exception
	 */
	public function __construct($config = array()) {
		// Guess the option from the class name (Option)Model(View).
		if (empty($this->option)) {
			$r = null;
	
			if (!preg_match('/(.*)Model/i', get_class($this), $r)) {
				throw new Exception(JText::_('JLIB_APPLICATION_ERROR_MODEL_GET_NAME'), 500);
			}
	
			$this->option = 'com_' . strtolower($r[1]);
		}
	
		// Set the view name
		if (empty($this->name)) {
			if (array_key_exists('name', $config)) {
				$this->name = $config['name'];
			} else {
				$this->name = $this->getName();
			}
		}
	
		// Set the model state
		if (array_key_exists('state', $config)) {
			$this->state = $config['state'];
		} else {
			$this->state = new JObject;
		}
		
		// Set the model restApi
		if (array_key_exists('restAPI', $config)) {
			$this->_restConfig = $config['restAPI'];
			if(!empty($this->_restConfig['restusername']) && !empty($this->_restConfig['restpassword'])) {
				if(!empty($this->_restConfig['restserverurl'])&& !empty($this->_restConfig['restclass'])) {
					$this->_restApi = new RESTApi($this->_restConfig['restusername'], $this->_restConfig['restpassword']);
				} else {
					$this->setError('ERROR: REST Server URL or Class are empty');
				}
			} else {
				$this->setError('ERROR: REST API username or password are empty');
			}
		}
	
		// Set the default view search path
		if (array_key_exists('table_path', $config)) {
			$this->addTablePath($config['table_path']);
		}
		// @codeCoverageIgnoreStart
		elseif (defined('JPATH_COMPONENT_ADMINISTRATOR')) {
			$this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
			$this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR . '/table');
		}
	
		// @codeCoverageIgnoreEnd
	
		// Set the internal state marker - used to ignore setting state from the request
		if (!empty($config['ignore_request'])) {
			$this->__state_set = true;
		}
	
		// Set the clean cache event
		if (isset($config['event_clean_cache'])) {
			$this->event_clean_cache = $config['event_clean_cache'];
		} elseif (empty($this->event_clean_cache)) {
			$this->event_clean_cache = 'onContentCleanCache';
		}
	}

	/**
	 * REST APT Request Errors.
	 *
	 * @return string
	 */
	protected function _errosRequest() {
		return $this->_restApi->erro;
	}
	
	/**
	 * Returns a success message passed by the REST API,
	 * formated to be shown on view.
	 *
	 * @return string
	 */
	public function successMessage() {
		$msg = '';
		$restResponse = $this->_restApi->response();
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
	 * Add a directory where JRESTModel should search for models. You may
	 * either pass a string or an array of directories.
	 *
	 * @param   mixed   $path    A path or array[string] of paths to search.
	 * @param   string  $prefix  A prefix for models.
	 *
	 * @return  array  An array with directory elements. If prefix is equal to '', 
	 *                 all directories are returned.
	 *
	 */
	public static function addIncludePath($path = '', $prefix = '') {
		static $paths;

		if (!isset($paths)) {
			$paths = array();
		}

		if (!isset($paths[$prefix])) {
			$paths[$prefix] = array();
		}

		if (!isset($paths[''])) {
			$paths[''] = array();
		}

		if (!empty($path)) {
			jimport('joomla.filesystem.path');

			foreach ((array) $path as $includePath) {
				if (!in_array($includePath, $paths[$prefix])) {
					array_unshift($paths[$prefix], JPath::clean($includePath));
				}

				if (!in_array($includePath, $paths[''])) {
					array_unshift($paths[''], JPath::clean($includePath));
				}
			}
		}

		return $paths[$prefix];
	}

	/**
	 * Adds to the stack of model table paths in LIFO order.
	 *
	 * @author MMartinho (20/07/2017)
	 *
	 * @param   mixed  $path  The directory as a string or directories as an array to add.
	 *
	 * @return  void
	 */
	public static function addTablePath($path) {
		JRESTTable::addIncludePath($path);
	}

	/**
	 * Create the filename for a resource
	 *
	 * @param   string  $type   The resource type to create the filename for.
	 * @param   array   $parts  An associative array of filename information.
	 *
	 * @return  string  The filename
	 * 
	 */
	protected static function _createFileName($type, $parts = array()) {
		$filename = '';

		switch ($type) {
			case 'model':
				$filename = strtolower($parts['name']) . '.php';
				break;
		}

		return $filename;
	}

	/**
	 * Returns a Model object, always creating it
	 *
	 * @param   string  $type    The model type to instantiate
	 * @param   string  $prefix  Prefix for the model class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JRESTModel|boolean   A JRESTModel instance or false on failure
	 *
	 */
	public static function getInstance($type, $prefix = '', $config = array()) {
		$type = preg_replace('/[^A-Z0-9_\.-]/i', '', $type);
		$modelClass = $prefix . ucfirst($type);

		if (!class_exists($modelClass)) {
			jimport('joomla.filesystem.path');
			
			$path = JPath::find(self::addIncludePath(null, $prefix), self::_createFileName('model', array('name' => $type)));
			
			if (!$path) {
				$path = JPath::find(self::addIncludePath(null, ''), self::_createFileName('model', array('name' => $type)));
			}
			
			if(!$path) {
				$path = JPath::find(self::addIncludePath(JPATH_COMPONENT.'/models', $prefix), self::_createFileName('model', array('name' => $type)));
			}

			if (!$path) {
				return false;
			}

			require_once $path;

			if (!class_exists($modelClass)) {
				JLog::add(JText::sprintf('JLIB_APPLICATION_ERROR_MODELCLASS_NOT_FOUND', $modelClass), JLog::WARNING, 'jerror');

				return false;
			}
		}

		return new $modelClass($config);
	}
	
	/**
	 * @return string
	 */
	protected function getRestQueryString() {
		return $this->_restQueryString;
	}

	/**
	 * Gets an array of objects/object from the results of REST API query.
	 *
	 * @param   integer  $limitstart  Offset.
	 * @param   integer  $limit       The number of records.
	 *
	 * @return  object[] | object  An array of results.
	 *
	 * @throws  RuntimeException
	 */
	protected function _getList($limitstart = 0, $limit = 0) {
		if(count($this->getErrors()) == 0) {
			$qs = $this->getRestQueryString();
			$qsId = $this->getState('filter.id');
			
			$url=
				$this->_restConfig['restserverurl'].
				'/'.$this->_restConfig['restclass'].'/' .
				($qsId ? $qsId.'/' : '').
				'?offset='.($limitstart > 0 ? $limitstart : $this->getState('list.start')) .
				'&limit='.($limit > 0 ? $limit : $this->getState('list.limit')).
				'&filter_order='.$this->getState('list.ordering').
				'&filter_order_dir='.$this->getState('list.direction').
				($qs ? '&'.$qs : '');
			
			$this->_restApi->request($url, 'GET');
			$restResponse = $this->_restApi->response();
			
			if($restResponse->success) {
				$this->_restData = $restResponse->data->{$this->_restConfig['restclass']};
				$this->_restTotal = (int)$restResponse->data->totalCount;
			} else {
				$this->setError('ERROR: REST Response problems has beem found. '.$restResponse->message);
				$this->_restData = null;
				$this->_restTotal = null;
			}
			
			return $this->_restData;
		} else {
			throw new RuntimeException(implode("\n", $this->getErrors()), 500);
		}
	}

	/**
	 * Returns a record count for the REST query.
	 *
	 * @return  integer  Number of rows for the REST query.
	 * 
	 */
	protected function _getListCount() {
		if(is_null($this->_restTotal)) {
			$this->_getList();
		}
		return $this->_restTotal;
	}

	/**
	 * Method to get the model name
	 *
	 * The model name. By default parsed using the classname or it can be set
	 * by passing a $config['name'] in the class constructor
	 *
	 * @return  string  The name of the model
	 * 
	 * @throws  Exception
	 */
	public function getName() {
		if (empty($this->name)) {
			$r = null;

			if (!preg_match('/Model(.*)/i', get_class($this), $r)) {
				throw new Exception(JText::_('JLIB_APPLICATION_ERROR_MODEL_GET_NAME'), 500);
			}

			$this->name = strtolower($r[1]);
		}

		return $this->name;
	}
	
	/**
	 * @throws Exception
	 * @return RESTApi
	 */
	public function getRESTApi() {
		if($this->_restApi) {
			return $this->_restApi;
		} else { 
			throw new Exception(JText::_('REST_LIB_ERROR_MODEL_GET_RESTAPI'), 500);
		}
	}
	
	/**
	 * @throws Exception
	 * @return array
	 */
	public function getRESTConfig() {
		if(!empty($this->_restConfig)) {
			return $this->_restConfig;
		} else {
			throw new Exception(JText::_('REST_LIB_ERROR_MODEL_GET_RESTCONFIG'), 500);
		}
	}

	/**
	 * Method to get model state variables
	 *
	 * @param   string  $property  Optional parameter name
	 * @param   mixed   $default   Optional default value
	 *
	 * @return  mixed  The property where specified, the state object where omitted
	 * 
	 */
	public function getState($property = null, $default = null) {
		if (!$this->__state_set) {
			// Protected method to auto-populate the model state.
			$this->populateState();

			// Set the model state set flag to true.
			$this->__state_set = true;
		}

		return $property === null ? $this->state : $this->state->get($property, $default);
	}

	/**
	 * Method to get a resttable object, load it if necessary.
	 *
	 * @param   string  $name        The table name. Optional.
	 * @param   string  $prefix      The class prefix. Optional.
	 * @param   array   $restConfig  The REST Configuration
	 *
	 * @return  JRESTTable  A JRESTTable object
	 *
	 * @throws  Exception
	 */
	public function getRESTTable($name = '', $prefix = 'RESTTable', $restConfig=array()) {
		if (empty($name)) {
			$name = $this->getName();
		}
		
		if(empty($restConfig)) {
			$restConfig = $this->getRESTConfig();
		}

		if ($table = $this->_createRESTTable($name, $prefix, $restConfig)) {
			return $table;
		}

		throw new Exception(JText::sprintf('RESTLIB_APPLICATION_ERROR_RESTTABLE_NAME_NOT_SUPPORTED', $name), 0);
	}
	
	/**
	 * Method to load and return a model object.
	 *
	 * @author MMartinho (20/07/2017)
	 * 
	 * @param   string  $name    The name of the view
	 * 
	 * @param   string  $prefix  The class prefix. Optional.
	 * 
	 * @param   array   $config  Configuration settings to pass to JRESTTable::getInstance
	 *
	 * @return  JRESTTable|boolean  RestTable object or boolean false if failed
	 *
	 * @see     JRESTTable::getInstance()
	 */
	protected function _createRESTTable($name, $prefix = 'RESTTable', $restConfig) {
		// Clean the model name
		$name = preg_replace('/[^A-Z0-9_]/i', '', $name);
		$prefix = preg_replace('/[^A-Z0-9_]/i', '', $prefix);
	
		return JRESTTable::getInstance($name, $prefix, $restConfig);
	}

	/**
	 * Method to load a row for editing from the version history table.
	 *
	 * @author MMartinho (20/07/2017)
	 * 
	 * @param   integer  $version_id        Key to the version history table.
	 * 
	 * @param   JRESTTable   &$resttable    Content table object being loaded.
	 *
	 * @return  boolean  False on failure or error, true otherwise.
	 *
	 */
	public function loadHistory($version_id, JRESTTable &$resttable) {
		// Only attempt to check the row in if it exists, otherwise do an early exit.
		if (!$version_id) {
			return false;
		}

		// Get an instance of the row to checkout.
		$historyTable = JTable::getInstance('Contenthistory');

		if (!$historyTable->load($version_id)) {
			$this->setError($historyTable->getError());
			return false;
		}

		$rowArray = ArrayHelper::fromObject(json_decode($historyTable->version_data));
		$typeId   = JTable::getInstance('Contenttype')->getTypeId($this->typeAlias);

		if ($historyTable->ucm_type_id != $typeId) {
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_HISTORY_ID_MISMATCH'));

			$key = $resttable->getKeyName();

			if (isset($rowArray[$key])) {
				$resttable->checkIn($rowArray[$key]);
			}

			return false;
		}

		$this->setState('save_date', $historyTable->save_date);
		$this->setState('version_note', $historyTable->version_note);

		return $resttable->bind($rowArray);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @return  void
	 *
	 * @note    Calling getState in this method will result in recursion.
	 */
	protected function populateState() {
	}

	/**
	 * Method to set model state variables
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $value     The value of the property to set or null.
	 *
	 * @return  mixed  The previous value of the property or null if not set.
	 * 
	 */
	public function setState($property, $value = null) {
		return $this->state->set($property, $value);
	}

	/**
	 * Clean the cache
	 *
	 * @param   string   $group      The cache group
	 * @param   integer  $client_id  The ID of the client
	 *
	 * @return  void
	 *
	 */
	protected function cleanCache($group = null, $client_id = 0) {
		$conf = JFactory::getConfig();

		$options = array(
			'defaultgroup' => ($group) ? $group : (isset($this->option) ? $this->option : JFactory::getApplication()->input->get('option')),
			'cachebase' => ($client_id) ? JPATH_ADMINISTRATOR . '/cache' : $conf->get('cache_path', JPATH_SITE . '/cache'),
			'result' => true,
		);

		try {
			/** @var JCacheControllerCallback $cache */
			$cache = JCache::getInstance('callback', $options);
			$cache->clean();
		} catch (JCacheException $exception) {
			$options['result'] = false;
		}

		// Trigger the onContentCleanCache event.
		JEventDispatcher::getInstance()->trigger($this->event_clean_cache, $options);
	}
}
