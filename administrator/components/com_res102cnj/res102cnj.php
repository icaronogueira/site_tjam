<?php
/**
 * @version		$Id: res102cnj.php 19343 2017-12-13 09:12:00Z mmartinho $
 * @package		Joomla
 * @author		Marcus Martinho
 * @subpackage	Res102cnj
 * @copyright	Copyright (C) 2017 - 2022 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * 
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

/**
 * TRIBUNAL DE JUSTIÇA DO ESTADO DO AMAZONAS
 * Divisão da Tecnologia da Informação e Comunicação (DVTIC)
 * Setor de Desenvolvimento de Sistemas (SDS)
 * Projeto: Joomla Internet 2017 / Componente da Res 102 CNJ
 * Tarefa: Construção de Componente que disponibiliza/altera arquivos contendo informações sobre 
 *         dados de transparência orçamentária e de pessoal do órgão.
 * Arquivo: Controlador do componente.
 *
 */

defined('_JEXEC') or die;

$user = JFactory::getUser();
$input = JFactory::getApplication()->input;
$task = $input->get('task');

if (!$user->authorise('core.manage', 'com_res102cnj')) { // check authorization to use component
	throw new JAccessExceptionNotallowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
}

$controller	= JControllerLegacy::getInstance('Res102cnj');
$controller->execute($task);
$controller->redirect();