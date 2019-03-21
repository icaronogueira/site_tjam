<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_sojam
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('mmartinho.rest.restcontrollerform');

/**
 * @author Marcus
 *
 */
class SojamControllerNovamanifestacao extends JControllerRESTForm {
	/**
	 * Hold a JInput object for easier access to the input variables.
	 *
	 * @var    JInput
	 */
	protected $input;
	/**
	 * The default view for the display method.
	 * @var string
	 */
	protected $default_view = 'novamanifestacao';
	
	/**
	 * @var string
	 */
	protected $view_list = 'novamanifestacao';
	
	/**
	 * Register no-default tasks
	 * @param array $config
	 */
	public function __construct($config=array()) {
		parent::__construct($config);
		$this->registerTask('novamanifestacao.add', 'add');
		$this->registerTask('novamanifestacao.cancelar', 'cancel');
		$this->registerTask('novamanifestacao.salvar', 'save');
	}
	
	/**
	 * {@inheritDoc}
	 * @see JControllerRESTForm::getRedirectToListAppend()
	 */
	protected function getRedirectToListAppend() {
		$append = '';
		if ($show = $this->input->get('show', '', 'string')) {
			$append .= '&show=' . $show;
		}
		$append .= parent::getRedirectToListAppend();
		return $append;
	}
	
	/**
	 * {@inheritDoc}
	 * @see JControllerRESTForm::getRedirectToItemAppend()
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id') {
		$append = '';
		if ($show = $this->input->get('show', '', 'string'))  {
			$append .= '&show=' . $show;
		}
		$append .= parent::getRedirectToItemAppend($recordId, $urlVar);
		return $append;
	}
	
	/**
	 * {@inheritDoc}
	 * @see JControllerRESTForm::cancel()
	 */
	public function cancel($key=null) {
		$this->task = 'cancel';
		$this->input->set('show', 'cancelamento');
		return parent::cancel($key);
	}
	
	/**
	 * {@inheritDoc}
	 * @see JControllerRESTForm::save()
	 */
	public function save($key=null,$urlVar=null) {
		$this->task = 'apply2show';
		$this->input->set('show', 'protocolo');
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
	 * @see JControllerRESTForm::allowAdd()
	 */
	protected function allowAdd($data = array()) {
		return true;
	}
	
	/**
	 * {@inheritDoc}
	 * @see JControllerRESTForm::allowEdit()
	 */
	protected function allowEdit($data = array(), $key='id'){
		return true;
	}
}