<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_sojam
 * @author		Marcus Martinho (marcus.martinho@tjam.jus.br)
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('_JEXEC') or die;

jimport('mmartinho.rest.restadmin');
jimport('mmartinho.rest.resttable');

/**
 * Model for Edit view.
 * @author Marcus
 *
 */
class SojamModelNovamanifestacao extends JModelRESTAdmin {
	protected $_params;
	
	/**
	 * The prefix to use with controller messages.
	 * @var string
	 */
	protected $text_prefix = 'COM_SOJAM';
	
	/**
	 * The type alias for this content type.
	 * @var    string
	 */
	public $typeAlias = 'com_sojam.novamanifestacao';
	
	/**
	 * {@inheritDoc}
	 * @see JRESTModel::getRESTTable()
	 */
	public function getRESTTable($type = 'Novamanifestacao', $prefix = 'SojamRESTTable', $restConfig=array()) {		
		$this->_params = JComponentHelper::getParams('com_sojam');
		
		$restConfig = array(
			'restusername'=>$this->_params ? $this->_params->get('restusername') : '',
			'restpassword'=>$this->_params ? $this->_params->get('restpassword') : '',
			'restserverurl'=>$this->_params ? $this->_params->get('restserverurl') : '',
			'restclass'=>'manifestacao',
		);
		
		return JRESTTable::getInstance($type, $prefix, $restConfig);
	}
	
	/**
	 * Get the form object based on XML file where all the
	 * field are defined. It uses the loadFormData() function.
	 * @see SojamModelNovamanifestacao::loadFormData()
	 * {@inheritDoc}
	 * @see JModelRESTForm::getForm()
	 */
	public function getForm($data = array(), $loadData = true) {
		$app = JFactory::getApplication();
	
		$form = $this->loadForm(
			'com_sojam.novamanifestacao',
			'novamanifestacao',
			array('control'=>'jform', 'load_data'=>$loadData)
		);
	
		if(empty($form)) { // check a valid form...
			return false;
		}
	
		$params = JComponentHelper::getParams('com_sojam');
		if(!$params->get('use_captcha')) { // check if captcha is disabled...
			$form->removeField('captcha'); // ...remove captcha definition
		}
	
		return $form;
	}
	
	/**
	 * Loads the data into the form.
	 * {@inheritDoc}
	 * @see JModelRESTForm::loadFormData()
	 */
	protected function loadFormData() {
		$data = JFactory::getApplication()->getUserState(
			'com_sojam.edit.novamanifestacao.data',
			array()
		);
	
		if(empty($data)) {
			$data = $this->getItem();	
			if($data->id == 0) { // new item...
				// ...prime some default values...
				//$data->set('<field>', '<value>');
			}
		}
	
		return $data;
	}
	
	/**
	 * @return mixed
	 */
	public function getTask() {
		return JFactory::getApplication()->getUserState('task');
	}
	
}