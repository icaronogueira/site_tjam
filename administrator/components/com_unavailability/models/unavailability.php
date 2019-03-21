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
 * Model for Edit view.
 * @author Marcus
 *
 */
class UnavailabilityModelUnavailability extends JModelAdmin {
	
	/**
	 * The prefix to use with controller messages.
	 * @var string
	 */
	protected $text_prefix = 'COM_UNAVAILABILITY';
	
	/**
	 * The type alias for this content type.
	 * @var    string
	 */
	public $typeAlias = 'com_unavailability.unavailability';
	
	/**
	 * Sets the default Table that the model calls.
	 * {@inheritDoc}
	 * @see JModelLegacy::getTable()
	 */
	public function getTable($type = 'Unavailability', $prefix = 'UnavailabilityTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 * Get the systen form object based on XML file where all the
	 * field are defined. Used by the system_dialog_form.
	 * @see view/unavailability/tmpl/edit_system_dialog.php
	 * @param array $data
	 * @param string $loadData
	 * @return boolean|JForm|boolean
	 */
	public function getSystemForm($data = array(), $loadData = true) {
		$app = JFactory::getApplication();
		$form = $this->loadForm(
			'com_unavailability.system',
			'system',
			array('control'=>'jform', 'load_data'=>false)
		);
		if(empty($form)) {
			return false;
		}
		return $form;
	}
	
	/**
	 * Get the form object based on XML file where all the 
	 * field are defined. It uses the loadFormData() function.
	 * @see UnavailabilityModelUnavailability::loadFormData()
	 * {@inheritDoc}
	 * @see JModelForm::getForm()
	 */
	public function getForm($data = array(), $loadData = true) {
		$app = JFactory::getApplication();
		$form = $this->loadForm(
			'com_unavailability.unavailability', 
			'unavailability',
			array('control'=>'jform', 'load_data'=>$loadData)
		);
		if(empty($form)) {
			return false;
		} 
		return $form;
	}
	
	/**
	 * Loads the data into the form.
	 * {@inheritDoc}
	 * @see JModelForm::loadFormData()
	 */
	protected function loadFormData() {
		$data = JFactory::getApplication()->getUserState(
			'com_unavailability.edit.unavailability.data', 
			array()
		);
		
		if(empty($data)) {
			$data = $this->getItem();
			// explode multivalue field data. 
			// @see table store for saving data...
			$data->sistemas = $data->sistemas ? explode(',', $data->sistemas) : array(); 
			
			if($data->id == 0) { // new item...
				$today = JFactory::getDate('now', new DateTimeZone('America/Manaus'));
				$twoHours = date_interval_create_from_date_string('2 hours');
				
				$params = JComponentHelper::getParams('com_unavailability');
				
				// ...prime some default values.
				$data->set('dthr_emissao', $today->format('Y-m-d H:i:s'));
				$data->set('dthr_inicio', $today->format('Y-m-d H:i:s'));
				$data->set('dthr_final', $today->add($twoHours)->format('Y-m-d H:i:s'));
				$data->set('responsavel', $params->get('responsavel_default'));
				$data->set('created_by', JFactory::getUser()->id);
				$data->set('title', JText::_('COM_UNAVAILABILITY_FIELD_TITLE_DEFAULT').' '.$today->format('d/m/Y H:i', true, true));
			}
		}
		return $data;
	}
	
	/**
	 * Transforms some of the data before display.
	 * {@inheritDoc}
	 * @see JModelAdmin::prepareTable()
	 */
	protected function prepareTable($table) {
		$table->title = htmlspecialchars_decode($table->title, ENT_QUOTES);
	}
	
	/**
	 * {@inheritDoc}
	 * @see JModelAdmin::canDelete()
	 */
	protected function canDelete($record) {
		if(!empty($record->id)) {
			if($record->state != -2) { // only trashed can be deleted
				return;
			}
			$user = JFactory::getUser();
			
			if($record->catid) { // if there is a category associated...
				// ...check category permission...
				return $user->authorise('core.delete', 'com_unavailability.category.'.(int)$record->catid);
			} else {
				return parent::canDelete($record);
			}
		}
	}
	
	/**
	 * {@inheritDoc}
	 * @see JModelAdmin::canEditState()
	 */
	protected function canEditState($record) {
		$user = JFactory::getUser();
		
		if(!empty($record->catid)) { // if there is a category associated...
			// ...check category permission...
			return $user->authorise('core.edit.state', 'com_unavailability.category.'.(int)$record->catid);
		} else {
			return parent::canEditState($record);
		}
	}
	
	/**
	 * {@inheritDoc}
	 * @see JModelAdmin::generateNewTitle()
	 */
	protected function generateNewTitle($category_id, $alias, $name) {
		// Alter the title & alias
		$table = $this->getTable();
	
		while ($table->load(array('alias' => $alias, 'catid' => $category_id))) {
			if ($name == $table->title) {
				$name = JString::increment($name);
			}
	
			$alias = JString::increment($alias, 'dash');
		}
	
		return array($name, $alias);
	}
}