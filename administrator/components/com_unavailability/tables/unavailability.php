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

use Joomla\Utilities\ArrayHelper;
use function PasswordCompat\binary\check;

class UnavailabilityTableUnavailability extends JTable {
	/**
	 * Defines the database table name.
	 * @param object $db
	 */
	public function __construct(&$db) {
		parent::__construct('#__unavailability', 'id', $db);
	}
	
	/**
	 * Prepared the data immediately before it is saved to 
	 * the database.
	 * {@inheritDoc}
	 * @see JTable::bind()
	 */
	public function bind($array, $ignore = '') {
		return parent::bind($array, $ignore);
	}
	
	/**
	 * Writes the data to the database when user submit the
	 * form. Check for new records to set default values, 
	 * update existing ones.
	 * {@inheritDoc}
	 * @see JTable::store()
	 */
	public function store($updateNulls=false) {
		$date   = JFactory::getDate()->toSql();
		$userId = JFactory::getUser()->id;
		
		$this->modified = $date;
		
		if ($this->id) {
			// Existing item
			$this->modified_by = $userId;
		} else {
			// New Certificate. A Certificate document created and created_by field can be set by the user,
			// so we don't touch either of these if they are set.
			if (!(int) $this->created) {
				$this->created = $date;
			}
		
			if (empty($this->created_by)) {
				$this->created_by = $userId;
			}
		}
		
		// Set publish_up to null date if not set
		if (!$this->publish_up) {
			$this->publish_up = $this->_db->getNullDate();
		}
		
		// Set publish_down to null date if not set
		if (!$this->publish_down) {
			$this->publish_down = $this->_db->getNullDate();
		}
		
		if(is_array($this->sistemas) && count($this->sistemas) > 0 && ($this->sistemas[0] != '0')) {
			// implode the multivalue field data. 
			// @see model loadingFormData for loading the data...
			$this->sistemas = implode(',', $this->sistemas); 
		} else {
			$this->sistemas = ''; // no system selected
		}
		
		// Verify that the alias is unique
		$table = JTable::getInstance('Unavailability', 'UnavailabilityTable');		
		if ($table->load(array('alias'=>$this->alias, 'catid' => $this->catid)) && ($table->id != $this->id || $this->id == 0)) {
			$this->setError(JText::_('COM_UNAVAILABILITY_ERROR_UNIQUE_ALIAS'));
		
			return false;
		}
		
		return parent::store(true);
	}
	
	/**
	 * Set row(s) state. Called by the model class.
	 * {@inheritDoc}
	 * @see JTable::publish()
	 */
	public function publish($pks=array(), $state=1, $userId=0) {
		$k = $this->_tbl_key; // primary key name
		
		ArrayHelper::toInteger($pks);
		
		$state = (int)$state;
		
		if(empty($pks)) { // no primary keys selected? 
			$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
			return false;
		}
		
		// query preparation...
		$where = $k.'='.implode(' OR '.$k.'=', $pks); // where clause with all pks selected
		$table = $this->_db->quoteName($this->_tbl);
		$conditions = $this->_db->quoteName('state') .'='.(int)$state;
		
		$query = $this->_db->getQuery(true)
			->update($table)
			->set($conditions)
			->where($where);
		$this->_db->setQuery($query);
		
		//try the query...
		try {
			$this->_db->execute();
		} catch (RuntimeException $e) {
			$this->setError($e->getMessage());
			return false;
		}
		
		$this->setError('');
		
		return true;
	}
	
	/**
	 * Validation procedure before store. 
	 * {@inheritDoc}
	 * @see JTable::check()
	 */
	public function check() {
		// Check for valid title
		if (trim($this->title) == '') {
			$this->setError(JText::_('COM_UNAVAILABILITY_WARNING_PROVIDE_VALID_TITLE'));
		
			return false;
		}
		
		// Generate a valid alias
		$this->generateAlias();
		
		// Check for valid category
		if (trim($this->catid) == '') {
			$this->setError(JText::_('COM_UNAVAILABILITY_WARNING_CATEGORY'));
		
			return false;
		}
		
		// Check the publish down date is not earlier than publish up.
		if ((int) $this->publish_down > 0 && $this->publish_down < $this->publish_up) {
			$this->setError(JText::_('JGLOBAL_START_PUBLISH_AFTER_FINISH'));
		
			return false;
		}
		
		/*
		 * Clean up keywords -- eliminate extra spaces between phrases
		 * and cr (\r) and lf (\n) characters from string.
		 * Only process if not empty.
		 */
		if (!empty($this->metakey)) {
			// Array of characters to remove.
			$bad_characters = array("\n", "\r", "\"", "<", ">");
		
			// Remove bad characters.
			$after_clean = JString::str_ireplace($bad_characters, "", $this->metakey);
		
			// Create array using commas as delimiter.
			$keys = explode(',', $after_clean);
			$clean_keys = array();
		
			foreach ($keys as $key) {
				// Ignore blank keywords.
				if (trim($key)) {
					$clean_keys[] = trim($key);
				}
			}
		
			// Put array back together delimited by ", "
			$this->metakey = implode(", ", $clean_keys);
		}
		
		// Clean up description -- eliminate quotes and <> brackets
		if (!empty($this->metadesc)) {
			// Only process if not empty
			$bad_characters = array("\"", "<", ">");
			$this->metadesc = JString::str_ireplace($bad_characters, "", $this->metadesc);
		}
		
		return true;
	}
	
	/**
	 * Generate a valid alias from title / date.
	 * Remains public to be able to check for duplicated alias before saving
	 *
	 * @return  string
	 */
	public function generateAlias() {
		if (empty($this->alias)) {
			$this->alias = $this->title;
		}
	
		$this->alias = JApplicationHelper::stringURLSafe($this->alias, $this->language);
	
		if (trim(str_replace('-', '', $this->alias)) == '') {
			$this->alias = JFactory::getDate()->format("Y-m-d-H-i-s");
		}
	
		return $this->alias;
	}
	
}