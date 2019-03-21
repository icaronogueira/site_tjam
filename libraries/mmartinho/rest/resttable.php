<?php
/**
 * @package     MMartinho.REST
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('JPATH_PLATFORM') or die;

jimport('mmartinho.rest.resttableinterface');

/**
 * Abstract REST Table class
 *
 * Parent class to all REST tables.
 *
 */
abstract class JRESTTable extends JObject implements JObservableInterface, JRestTableInterface {

	/**
	 * REST API Classe Instance
	 * @var RESTApi
	 */
	protected $_restApi=null;
	
	/**
	 * REST API Configuration
	 * @var array
	 */
	protected $_restConfig=array();
	
	/**
	 * REST API extra query string
	 */
	protected $_restQueryString='';
	
	/**
	 * Name of the resttable to model.
	 *
	 * @var    string
	 */
	protected $_tbl = '';
	
	/**
	 * Generic observers for this JRESTTable (Used e.g. for tags Processing)
	 *
	 * @var    JObserverUpdater
	 */
	protected $_observers;
	
	/**
	 * Name of the primary key fields in the resttable.
	 *
	 * @var    array
	 */
	protected $_tbl_keys = array();
	
	/**
	 * Indicates that the primary keys autoincrement.
	 *
	 * @var    boolean
	 */
	protected $_autoincrement = true;
	
	/**
	 * An array of key names to be json encoded in the bind function
	 *
	 * @var    array
	 */
	protected $_jsonEncode = array();
	
	/**
	 * Should rows be tracked as ACL assets?
	 *
	 * @var    boolean
	 */
	protected $_trackAssets = false;
	
	/**
	 * Include paths for searching for JRESTTable classes.
	 *
	 * @var    array
	 */
	private static $_includePaths = array();
	
	/**
	 * Object constructor to set resttable and key fields.  In most cases this will
	 * be overridden by child classes to explicitly set the resttable and key fields
	 * for a particular resttable.
	 * 
	 * @author MMartinho (23/07/2017)
	 *
	 * @param   string           $resttable   Name of the resttable to model.
	 * 
	 * @param   mixed            $key    	  Name of the primary key field in the resttable 
	 *                                        or array of field names that compose 
	 *                                        the primary key.
	 * 
	 * @param   array			 $restConfig  REST Configuration 
	 */
	public function __construct($table, $key, $restConfig) {
		// Set internal variables.
		$this->_tbl = $table;
	
		// Set the key to be an array.
		if (is_string($key)) {
			$key = array($key);
		} elseif (is_object($key)) {
			$key = (array) $key;
		}
	
		$this->_tbl_keys = $key;
	
		if (count($key) == 1) {
			$this->_autoincrement = true;
		} else {
			$this->_autoincrement = false;
		}
	
		// Set the singular table key for backwards compatibility.
		$this->_tbl_key = $this->getKeyName();
		
		$this->_restConfig = $restConfig;
		
		// create the RESTApi instance
		if(!empty($this->_restConfig)) {
			if(!empty($this->_restConfig['restusername']) && !empty($this->_restConfig['restpassword'])) {
				if(!empty($this->_restConfig['restserverurl']) && !empty($this->_restConfig['restclass'])) {
					$this->_restApi = new RESTApi($this->_restConfig['restusername'], $this->_restConfig['restpassword']);
				} else {
					throw new RuntimeException('ERROR: REST Server URL or Class are empty');
				}
			} else {
				throw new RuntimeException('ERROR: REST API username or password are empty');
			}
		} else {
			throw new RuntimeException('ERROR: REST Server URL or Class are empty');
		}
		
		// Initialise the table properties.
		$fields = $this->getFields();
	
		if ($fields){
			foreach ($fields as $name => $v) {
				// Add the field if it is not already present.
				if (!property_exists($this, $name)) {
					$this->$name = null;
				}
			}
		}
	
		// If we are tracking assets, make sure an access field exists and initially set the default.
		if (property_exists($this, 'asset_id')) {
			$this->_trackAssets = true;
		}
	
		// If the access property exists, set the default.
		if (property_exists($this, 'access')) {
			$this->access = (int) JFactory::getConfig()->get('access');
		}
	
		// Implement JObservableInterface:
		// Create observer updater and attaches all observers interested by $this class:
		$this->_observers = new JObserverUpdater($this);
		JObserverMapper::attachAllObservers($this);
	}
	
	/**
	 * Add a filesystem path where JRESTTable should search for resttable class files.
	 *
	 * @author MMartinho (25/07/2017)
	 *
	 * @param   array|string  $path  A filesystem path or array of filesystem paths to add.
	 *
	 * @return  array  An array of filesystem paths to find JRESTTable classes in.
	 */
	public static function addIncludePath($path = null) {
		// If the internal paths have not been initialised, do so with the base resttable path.
		if (empty(self::$_includePaths)) {
			self::$_includePaths = array(__DIR__);
		}
	
		// Convert the passed path(s) to add to an array.
		settype($path, 'array');
	
		// If we have new paths to add, do so.
		if (!empty($path)) {
			// Check and add each individual new path.
			foreach ($path as $dir) {
				// Sanitize path.
				$dir = trim($dir);
	
				// Add to the front of the list so that custom paths are searched first.
				if (!in_array($dir, self::$_includePaths)) {
					array_unshift(self::$_includePaths, $dir);
				}
			}
		}
		return self::$_includePaths;
	}
	
	/**
	 * Static method to get an instance of a JRESTTable class if it can be found 
	 * in the table include paths.
	 *
	 * To add include paths for searching for JRESTTable classes see 
	 * JRESTTable::addIncludePath().
	 *
	 * @author MMartinho (24/07/2017)
	 * 
	 * @param   string  $type        The type (name) of the JRESTTable class to get an 
	 *                               instance of.
	 * @param   string  $prefix      An optional prefix for the table class name.
	 * 
	 * @param   array   $restConfig  REST API configuration 
	 *
	 * @return  JRESTTable|boolean   A JRESTTable object if found or boolean false 
	 *                               on failure.
	 *
	 */
	public static function getInstance($type, $prefix = 'RESTTable', $restConfig) {
		// Sanitize and prepare the table class name.
		$type       = preg_replace('/[^A-Z0-9_\.-]/i', '', $type);
		$tableClass = $prefix . ucfirst($type);
	
		// Only try to load the class if it doesn't already exist.
		if (!class_exists($tableClass)) {
			// Search for the class file in the JRESTTable include paths.
			jimport('joomla.filesystem.path');
			
			$paths = self::addIncludePath();
			$pathIndex = 0;
	
			while (!class_exists($tableClass) && $pathIndex < count($paths)) {
				if ($tryThis = JPath::find($paths[$pathIndex++], strtolower($type) . '.php')) {
					include_once $tryThis;
				}
			}
	
			if (!class_exists($tableClass)) {
				return false;
			}
		}
	
		// Instantiate a new resttable class and return it.
		return new $tableClass($restConfig);
	}
	
	/**
	 * Implement JObservableInterface:
	 * Adds an observer to this instance.
	 * This method will be called fron the constructor of classes 
	 * implementing JObserverInterface which is instanciated by the 
	 * constructor of $this with JObserverMapper::attachAllObservers($this)
	 *
	 * @param   JObserverInterface|JTableObserver  $observer  The observer object
	 *
	 * @return  void
	 *
	 */
	public function attachObserver(JObserverInterface $observer) {
		$this->_observers->attachObserver($observer);
	}
	
	/**
	 * Method to get the resttable name for the class.
	 *
	 * @return  string  The name of the resttable being modeled.
	 *
	 */
	public function getTableName() {
		return $this->_tbl;
	}
	
	/**
	 * Returns the rest API object.
	 * @author 
	 * 
	 * @return RESTApi
	 */
	public function getRestApi() {
		return $this->_restApi;
	}
	
	/**
	 * Method to load a row from the REST Server by primary key and bind the 
	 * fields to the JRESTTable instance properties.
	 * 
	 * @author MMartinho (24/07/2017)
	 * 
	 * @param   mixed    $keys   An optional primary key value to load the row by, 
	 *                           or an array of fields to match.
	 *                           If not set the instance property value is used.
	 * @param   boolean  $reset  True to reset the default values before loading 
	 *                           the new row.
	 *
	 * @return  boolean  True if successful. False if row not found.
	 *
	 * @throws  InvalidArgumentException
	 * @throws  RuntimeException
	 * @throws  UnexpectedValueException
	 */
	public function load($keys = null, $reset = true) {
		// Implement JObservableInterface: Pre-processing by observers
		$this->_observers->update('onBeforeLoad', array($keys, $reset));
	
		if (empty($keys)) {
			$empty = true;
			$keys  = array();
	
			// If empty, use the value of the current key
			foreach ($this->_tbl_keys as $key) {
				$empty      = $empty && empty($this->$key);
				$keys[$key] = $this->$key;
			}
	
			// If empty primary key there's is no need to load anything
			if ($empty) {
				return true;
			}
		} elseif (!is_array($keys)) {
			// Load by primary key.
			$keyCount = count($this->_tbl_keys);
			if ($keyCount) {
				if ($keyCount > 1) {
					throw new InvalidArgumentException('Table has multiple primary keys specified, only one primary key value provided.');
				}
				$keys = array($this->getKeyName() => $keys);
			} else {
				throw new RuntimeException('No table keys defined.');
			}
		}
	
		if ($reset) {
			$this->reset();
		}
		
		$url=$this->_restConfig['restserverurl'].'/'.$this->_restConfig['restclass'];
			
		$fields = array_keys($this->getProperties());
		foreach ($keys as $field => $value) {
			// Check that $field is in the table.
			if (!in_array($field, $fields)) {
				throw new UnexpectedValueException(sprintf('Missing field in resttable: %s &#160; %s.', get_class($this), $field));
			}
			// Add the search to the url.
			$url = $url.'/'.$value;
		}
	
		$this->_restApi->request($url, 'GET');
		$restResponse = $this->_restApi->response();
			
		if($restResponse->success) {
			$row = $restResponse->data->{$this->_restConfig['restclass']};
		} else {
			throw new RuntimeException('ERROR: REST Response problems has beem found. '.$restResponse->message);
		}
			
		// Check that we have a result.
		if (empty($row)) {
			$result = false;
		} else {
			// Bind the object with the row and return.
			$result = $this->bind($row);
		}
	
		// Implement JObservableInterface: Post-processing by observers
		$this->_observers->update('onAfterLoad', array(&$result, $row));
	
		return $result;
	}
	
	/**
	 * Method to perform sanity checks on the JRESTTable instance properties to ensure 
	 * they are safe to store in the REST Server database.
	 *
	 * Child classes should override this method to make sure the data they are storing 
	 * in the REST Server database is safe and as expected before storage.
	 *
	 * @return  boolean  True if the instance is sane and able to be stored in the 
	 *                   REST Server database.
	 *
	 */
	public function check() {
		return true;
	}
	
	/**
	 * Method to bind an associative array or object to the JTable instance.This
	 * method only binds properties that are publicly accessible and optionally
	 * takes an array of properties to ignore when binding.
	 * 
	 * @author MMartinho (24/07/2017)
	 *
	 * @param   array|object  $src     An associative array or object to bind to the 
	 *                                 JTable instance.
	 * @param   array|string  $ignore  An optional array or space separated list of 
	 *                                 properties to ignore while binding.
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws  InvalidArgumentException
	 */
	public function bind($src, $ignore = array()) {
		// JSON encode any fields required
		if (!empty($this->_jsonEncode)) {
			foreach ($this->_jsonEncode as $field) {
				if (isset($src[$field]) && is_array($src[$field])) {
					$src[$field] = json_encode($src[$field]);
				}
			}
		}
	
		// Check if the source value is an array or object
		if (!is_object($src) && !is_array($src)) {
			throw new InvalidArgumentException(
				sprintf(
					'Could not bind the data source in %1$s::bind(), '.
					'the source must be an array or object but a "%2$s" was given.',
						get_class($this),
						gettype($src)
					)
				);
		}
	
		// If the source value is an object, get its accessible properties.
		if (is_object($src)) {
			$src = get_object_vars($src);
		}
	
		// If the ignore value is a string, explode it over spaces.
		if (!is_array($ignore)) {
			$ignore = explode(' ', $ignore);
		}
	
		// Bind the source value, excluding the ignored fields.
		foreach ($this->getProperties() as $k => $v) {
			// Only process fields not in the ignore array.
			if (!in_array($k, $ignore)) {
				if (isset($src[$k])) {
					$this->$k = $src[$k];
				}
			}
		}
	
		return true;
	}
	
	/**
	 * Method to reset class properties to the defaults set in the class
	 * definition. It will ignore the primary key as well as any private class
	 * properties (except $_errors).
	 *
	 * @return  void
	 */
	public function reset() {
		// Get the default values for the class from the table.
		$fields = $this->getFields();
		foreach ($fields as $k => $v) {
			// If the property is not the primary key or private, reset it.
			if (!in_array($k, $this->_tbl_keys) && (strpos($k, '_') !== 0)) {
				$this->$k = null;
			}
		}
	
		// Reset table errors
		$this->_errors = array();
	}
	
	/**
	 * Method to get the primary key field name for the resttable.
	 *
	 * @param   boolean  $multiple  True to return all primary keys 
	 *                              (as an array) or false to return just the 
	 *                              first one (as a string).
	 *
	 * @return  mixed  Array of primary key field names or string 
	 *                 containing the first primary key field.
	 *
	 */
	public function getKeyName($multiple = false) {
		// Count the number of keys
		if (count($this->_tbl_keys)) {
			if ($multiple) {
				// If we want multiple keys, return the raw array.
				return $this->_tbl_keys;
			} else {
				// If we want the standard method, just return the first key.
				return $this->_tbl_keys[0];
			}
		}
	
		return '';
	}
	
	/**
	 * Get all fields/values to be updated, based on RESTTable 
	 * instance scalars properties (not internal fields).
	 * 
	 * @author MMartinho (24/07/2017)
	 * 
	 * @param bool $updateNulls If null values will be considered
	 * 
	 * @return mixed[]
	 */
	public function getFieldsValues($updateNulls=false) {
		$fields = array();
		// Iterate over the object variables to build the fields/value pairs.
		$object_vars = get_object_vars($this);
		foreach ($object_vars as $k => $v) {
			// Only process scalars that are not internal fields and not null...
			if($k[0] == '_' || is_array($v) || is_object($v) || is_null($v)) {
				continue;
			}
			// Add the field to be updated.
			$fields[$k] = $v;
		}
		return $fields;
	}
	
	/**
	 * Extra query string to the REST request.
	 * This function is overidden by the extended component class.
	 * 
	 * @author MMartinho (24/07/2017)
	 * 
	 * @return string
	 */
	protected function getRestQueryString() {
		return $this->_restQueryString;
	}
	
	/**
	 * Writes the data to the REST Class instance when user submit the
	 * form. Check for new records to set default values,
	 * update existing ones.
	 * 
	 * @param bool $updateNulls If null value should be consider
	 * 
	 * @author MMartinho (created: 24/07/2017, updated: 07/08/2017)
	 * 
	 */
	public function store($updateNulls=false) {
		$result = true;
		
		$this->_restApi->setData($this->getFieldsValues($updateNulls));
	
		$qs = $this->getRestQueryString();
		
		$this->_restApi->request($this->_restConfig['restserverurl'] . '/'.$this->_restConfig['restclass'].'/?offset=0' . ($qs ? '&'.$qs : ''), 'POST'); // realiza a requisição do tipo POST
	
		$restResponse = $this->_restApi->response(); // response data
		if($restResponse->success && ((int)$restResponse->data->totalCount >= 1)) { // CODE 200 and has data...
			// try to bind local attributes with response data...
			$bind = $this->bind((array)$restResponse->data->{$this->_restConfig['restclass']}[0]); 
			if($bind) { // everything is ok...
				$this->_observers->update('onAfterStore', array(&$result)); // Implement JObservableInterface: Post-processing by observers
			} else { 
				$result = false;
				throw new RuntimeException('ERROR: Não foi possível realizar o BIND ');
			}
			return $result;
		} else {
			$restAPIErrorMessage = '';
			// there are multiple error messages structures... 
			if(is_object($restResponse->message)) {
				$restAPIErrorMessage .= $restResponse->message->error ; // concat the error
				if(is_object($restResponse->message->validation)) { // there is a validation error?
					$properties = get_object_vars($restResponse->message->validation); 
					foreach ($properties as $property=>$validation_errors) { // each property has its own error...
						foreach($validation_errors as $validation_error) { // each error has its own message...
							$restAPIErrorMessage .= $validation_error; // concat everything to error message
						}
					}
				}	
				throw new RuntimeException('ERROR: REST API: ' .$restAPIErrorMessage);
			} else { 
				throw new RuntimeException('ERROR: REST API: ' .$restResponse->message);
			}
			return false;
		}
	}
	
	/**
	 * Get the columns and relations from REST Class checking cache first.
	 *
	 * @author MMartinho (24/07/2017)
	 * 
	 * @param   bool  $reload  flag to reload cache
	 * 
	 * @return  mixed  An array of the field names, or false if an error occurs.
	 * 
	 * @throws  UnexpectedValueException
	 */
	public function getFields($reload = false) {
		static $cache = null;
	
		if ($cache === null || $reload) {
			// Lookup the fields for this table only once.
			$fields = $this->getColumns();
	
			if (empty($fields)) {
				throw new UnexpectedValueException(sprintf('No columns found for %s class', $name));
			}
	
			$cache = $fields;
		}
	
		return $cache;
	}

	/**
	 * Query a remote REST Class its columns information and relations. 
	 * 
	 * @author MMartinho (24/07/2017)
	 * 
	 * @return StdClass[]|boolean
	 */
	public function getColumns() {
		$url=
			$this->_restConfig['restserverurl'].
			'/'.$this->_restConfig['restclass'].'/?columns=true&relations=true';
		
		$this->_restApi->request($url, 'GET');
		$restResponse = $this->_restApi->response();
			
		if($restResponse->success) {
			$data = $restResponse->data->{$this->_restConfig['restclass']};
		} else {
			$this->setError('ERROR: REST Response problems has beem found. '.$restResponse->message);
			$data = false;
		}
		if($data) {
			return $data;
		} else {
			return false;
		}
	}
	
	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form table_name.id
	 * where id is the value of the primary key of the table.
	 *
	 * @return  string
	 *
	 */
	protected function _getAssetName() {
		$keys = array();
		foreach ($this->_tbl_keys as $k) {
			$keys[] = (int) $this->$k;
		}
		return $this->_tbl . '.' . implode('.', $keys);
	}
	
	/**
	 * Method to provide a shortcut to binding, checking and storing a JRESTTable instance 
	 * to the REST Server database table.
	 *
	 * The method will check a row in once the data has been stored and if an ordering 
	 * filter is present will attempt to reorder the table rows based on the filter.  
	 * The ordering filter is an instance property name.  The rows that will be reordered
	 * are those whose value matches the JTable instance for the property specified.
	 *
	 * @author MMartinho (23/07/2017)
	 * @param   array|object  $src             An associative array or object to bind to the $this 
	 *                                         JRESTTable instance.
	 * @param   string        $orderingFilter  Filter for the order updating
	 * @param   array|string  $ignore          An optional array or space separated list of properties to 
	 *                                         ignore while binding.
	 *
	 * @return  boolean  True on success.
	 *
	 */
	public function save($src, $orderingFilter = '', $ignore = '') {
		// Attempt to bind the source to the instance.
		if (!$this->bind($src, $ignore)) {
			return false;
		}
	
		// Attempt to store the properties to the database table.
		if (!$this->store()) {
			return false;
		}
	
		// Set the error to empty and return true.
		$this->setError('');
	
		return true;
	}
	
	/**
	 * Method to delete a row from the RESTTable by primary key value.
	 *
	 * @author MMartinho (24/07/2017)
	 * 
	 * @param   mixed  $pk  An optional primary key value to delete.  
	 *                      If not set the instance property value is used.
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws  UnexpectedValueException
	 */
	public function delete($pk = null) {
		if (is_null($pk)) {
			$pk = array();
			foreach ($this->_tbl_keys as $key) {
				$pk[$key] = $this->$key;
			}
		} elseif (!is_array($pk)) {
			$pk = array($this->_tbl_key => $pk);
		}
	
		foreach ($this->_tbl_keys as $key) {
			$pk[$key] = is_null($pk[$key]) ? $this->$key : $pk[$key];
			if ($pk[$key] === null) {
				throw new UnexpectedValueException('Null primary key not allowed.');
			}
			$this->$key = $pk[$key];
		}
	
		// Implement JObservableInterface: Pre-processing by observers
		$this->_observers->update('onBeforeDelete', array($pk));
	
		// If tracking assets, remove the asset first.
		if ($this->_trackAssets) {
			// Get the asset name
			$name  = $this->_getAssetName();
			/** @var JTableAsset $asset */
			$asset = self::getInstance('Asset');
	
			if ($asset->loadByName($name)){
				if (!$asset->delete()) {
					$this->setError($asset->getError());
					return false;
				}
			}
		}
	
		// Delete the row by primary key.
		$url=$this->_restConfig['restserverurl'].'/'.$this->_restConfig['restclass'];
			
		$fields = array_keys($this->getProperties());
		foreach ($keys as $field => $value) {
			// Check that $field is in the table.
			if (!in_array($field, $fields)) {
				throw new UnexpectedValueException(sprintf('Missing field in resttable: %s &#160; %s.', get_class($this), $field));
			}
			// Add the search to the url.
			$url = $url.'/'.$value;
		}
		
		$this->_restApi->request($url, 'DELETE');
		$restResponse = $this->_restApi->response();
			
		if(!$restResponse->success) {
			$this->setError($restResponse->message);
			return false;
		}
	
		// Implement JObservableInterface: Post-processing by observers
		$this->_observers->update('onAfterDelete', array($pk));
	
		return true;
	}
}