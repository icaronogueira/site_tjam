<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_sojam
 * @author		Marcus Martinho (marcus.martinho@tjam.jus.br)
 *
 * @copyright   Copyright (C) 2016 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

require_once(JPATH_COMPONENT.'/models/'.'tipos.php');

JFormHelper::loadFieldClass('list');

class JFormFieldTipos extends JFormFieldList {
	/**
	 * The form field type.
	 *
	 * @var    string
	 */
	protected $type = 'tipos';

	/**
	 * Get client list in text/value format for a select field
	 *
	 * @return  array
	 */
	public static function getTiposOptions() {
		$options = array();
		try {
			$modelTipos = new SojamModelTipos();
			$items =  $modelTipos->getItems();
			foreach ($items as $item) {
				$options[$item->id] = $item->nome;
			}
		} catch (RuntimeException $e) {
			JError::raiseWarning(500, $e->getMessage());
		}
	
		array_unshift($options, JHtml::_('select.option', '0', JText::_('COM_SOJAM_FIELD_SELECIONE')));
	
		return $options;
	}
	
	/**
	 * {@inheritDoc}
	 * @see JFormFieldList::getInput()
	 */
	public function getInput() {
		return parent::getInput();
	}
	
	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 */
	public function getOptions() {
		return array_merge(parent::getOptions(), self::getTiposOptions());
	}
}
