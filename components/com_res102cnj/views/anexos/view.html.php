<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_res102cnj
 * @author		Marcus Martinho (marcus.martinho@tjam.jus.br)
 *
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * View Attached PDFs documents
 * @author Marcus
 */
class Res102cnjViewAnexos extends JViewLegacy {	

	/**
	 * @var boolean
	 */
	protected $podeacessar=false;
	
	/**
	 * @var object
	 */
	protected $user;
	
	/**
	 * @var boolean
	 */
	protected $gerenciar=false;
	
	/**
	 * @var int
	 */
	protected $pageYOffset=null;
	
	/**
	 * @var int
	 */
	protected $pageXOffset=null;
	
	/**
	 * @var object
	 */
	protected $model=null;
	
	/**
	 * @var object
	 */
	protected $btns=null;
	
	/**
	 * @var string
	 */
	protected $erro='';
	
	/**
	 * {@inheritDoc}
	 * @see JViewLegacy::display()
	 */
	public function display($tpl=null) {
		$input = JFactory::getApplication()->input;
		
		$this->model = $this->getModel();
		$this->btns = new JHtmlBtns();
		
		$task = $input->get('task');
		$arq =  $input->get('arq', null);
		$this->pageYOffset =  $input->get('pageYOffset', null);
		$this->pageXOffset =  $input->get('pageXOffset', null);
		
		$remotehost = new JHtmlRemoteHost();
		$this->podeacessar = $remotehost->isPermited();
		$this->user = JFactory::getUser();  // loggedin user
		
		if($this->user && $this->podeacessar) {
			$this->gerenciar = $this->user->authorise('core.manage');
		}
		
		// Decide o que fazer ao submeter o formulário...
		if($task && $arq && $this->gerenciar) {
			switch ($task) {
				case 'delete' : {
					$this->erro = $this->model->arquivo_excluir($arq);
					break;
				}
				case 'upload' : {
					$this->erro = $this->model->arquivo_enviar($_FILES['arqenvio'],$arq);
					break;
				}
			}
		}

		if(count($errors = $this->get('Errors'))) {
			throw new RuntimeException(implode("\n", $errors), 500);
			return false;
		}
		
		return parent::display($tpl);
	}
}