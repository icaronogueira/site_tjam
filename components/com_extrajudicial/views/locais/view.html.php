<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_extrajudicial
 * @author		Marcus Martinho (marcus.martinho@tjam.jus.br)
 * 
 * @copyright   Copyright (C) 2017 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * @author Marcus
 */
class ExtrajudicialViewLocais extends JViewLegacy {
	/**
	 * Category data
	 *
	 * @var  array
	 */
	protected $categories;
	
	/**
	 * @var object
	 */
	protected $selectedCategory;
	
	/**
	 * Stores the data array from the model.
	 * @var object[]
	 */
	protected $items;
	
	/**
	 * Stores the state from the model.
	 * @var unknown
	 */
	protected $state;
	
	/**
	 * Stores the pagination object from the model.
	 * @var 
	 */
	protected $pagination;
	
	/**
	 * @var unknown
	 */
	protected $params;

	
	/**
	 * Add links to each category to be used by the TOC
	 *
	 * @param Categoria[] $items
	 * @param array $params
	 */
	private function categoriesLinks() {
		$k = 0;
		$count = count($this->categories);
		for($i = 0; $i < $count; $i++) {
			$category =& $this->categories[$i];
			
			// filter a category
			$link = JRoute::_(
				'index.php?option=com_extrajudicial&view=locais&categoryid=' . 
				$category->id
			);
			
			// if category selected active or not
			$class = ( 
				$this->selectedCategory 
				? 
				(	$this->selectedCategory == $category->id 
					? 
					'active' 
					: 
					''
				) 
			    : 
				''
			);
			
			switch ($this->params->get('target')) {
				// cases are slightly different
				case 1:
					// open in a new window
					$category->link = 
						'<a href="'. $link .'" target="_blank" class="'. $class .'">'. 
							$this->escape($category->nome) .' ('. $category->apelido .')'.
						'</a>';
					break;
				case 2:
					// open in a popup window
					$category->link = 
						"<a href=\"#\" onclick=\"javascript: window.open('". $link ."', '', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=780,height=550'); return false\" class=\"$class\">". 
							$this->escape($category->nome) .' ('. $category->apelido .')'.
						"</a>\n";
					break;
				default:
					// formerly case 2
					// open in parent window
					$category->link = 
						'<a href="'. $link .'" class="'. $class .'">'. 
							$this->escape($category->nome) .' ('. $category->apelido .')'.
						'</a>';
					break;
			}
			
			$category->odd		= $k;
			$category->count	= $i;
			$k = 1 - $k;
		}
	}
	
	/**
	 * {@inheritDoc}
	 * @see JViewLegacy::display()
	 */
	public function display($tpl = null) {
		$this->items = $this->get('Items');
		$this->state = $this->get('State');
		$this->pagination = $this->get('Pagination');
		$this->categories = $this->get('CategoryItems');
		$this->selectedCategory = $this->get('SelectedCategory');
		$this->params = JComponentHelper::getParams('com_extrajudicial');
		
		$this->categoriesLinks(); // Inject the categories links
		
		if(count($errors = $this->get('Errors'))) {
			throw new RuntimeException(implode("\n", $errors), 500);
			return false;
		}
		
		return parent::display($tpl);
	}

	/**
	 * Used by the drop-down filter.
	 * @return string[]
	 */
	protected function getSortFields() {
		return array(
			'order'=>JText::_('COM_EXTRAJUDICIAL_FIELD_ORDER_LABEL'),
			'nome'=>JText::_('COM_EXTRAJUDICIAL_FIELD_NOME_LABEL'),
			'apelido'=>JText::_('COM_EXTRAJUDICIAL_FIELD_APELIDO_LABEL'),
			'horarios'=>JText::_('COM_EXTRAJUDICIAL_FIELD_HORARIOS_LABEL'),
			'telefones'=>JText::_('COM_EXTRAJUDICIAL_FIELD_TELEFONES_LABEL'),
			'ativo'=>JText::_('COM_EXTRAJUDICIAL_FIELD_ATIVO_LABEL'),
			'id'=>JText::_('JGRID_HEADING_ID')
		);
	}
}