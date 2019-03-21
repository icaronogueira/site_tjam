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
class SojamControllerMovimentacoes extends JControllerRESTForm {
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
		$this->registerTask('movimentacoes.cancelar', 'cancelar');
		$this->registerTask('movimentacoes.buscar', 'buscar');
	}
	
	/**
	 * {@inheritDoc}
	 * @see JControllerRESTForm::cancel()
	 */
	public function cancelar() { 
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$this->task = 'cancelar';

		$app = JFactory::getApplication('site');
		$context = "$this->option.default.$this->context";
		$model = null;
		$form = null;
		$data = null;
		
		// Clean the session data and redirect.
		$app->setUserState($context.'.data', $data);
		
		// set redirection without data...
		$this->setRedirect(
			JRoute::_(
				'index.php?option=' . $this->option . 
				'&view=movimentacoes', 
				false
			)
		);
		return true;
	}
	
	/**
	 * @return boolean
	 */
	public function buscar() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));	
		$this->task = 'buscar';
		
		$app = JFactory::getApplication('site');
		$context = "$this->option.default.$this->context";
		$model = $this->getModel();
		$form = $model->getForm();
		$data = $this->input->post->get('jform', array(), 'array');
		
		$app->setUserState($context.'.data', $data);
		
		if (!$form) {
			JError::raiseError(500, $model->getError());	
			return false;
		}
		
		// try to validate data...
		if (!$model->validate($form, $data)) {
			$errors = $model->getErrors();
			
			// put each error on app message queue...
			foreach ($errors as $error) {
				$errorMessage = $error;
				if ($error instanceof Exception) {
					$errorMessage = $error->getMessage();
				}
				$app->enqueueMessage($errorMessage, 'error');
			}
			
			// not ok, set redirection without data...
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . 
					'&view=movimentacoes',
					false
				)
			);
			return false;
		}
		
		// everything ok, set redirection with data...
		$this->setRedirect(
			JRoute::_(
				'index.php?option=' . $this->option . 
				'&view=movimentacoes'.
				'&manifestacao_id=' . $data['manifestacao_id'].
				'&manifestacao_cpf='. $data['manifestacao_cpf'], 
				false
			)
		);
		return true;
	}
}