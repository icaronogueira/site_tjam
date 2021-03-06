<?php
/**
 * @version		$Id: listing.php 19343 2017-02-21 10:22:00Z mmartinho $
 * @package		Joomla
 * @author		Marcus Martinho
 * @subpackage	Listing
 * @copyright	Copyright (C) 2017 - 2022 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
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
 * Projeto: Joomla Internet 2017 / Componente de Listagens
 * Tarefa: Construção de componente para mostrar informações do
 *         sistema de Listagens via REST API.
 * Arquivo: Controlador do componente.
 *
 */

defined('_JEXEC') or die;

$user = JFactory::getUser();
$input = JFactory::getApplication()->input;
$task = $input->get('task');

if (!$user->authorise('core.manage', 'com_listing')) { // check authorization to use component
	throw new JAccessExceptionNotallowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
}

$controller	= JControllerLegacy::getInstance('Listing');
$controller->execute($task);
$controller->redirect();