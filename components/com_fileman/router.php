<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

function FilemanBuildRoute(&$query)
{
	$segments = array();

	if (empty($query['Itemid']) || (isset($query['view']) && $query['view'] === 'filelink'))
	{
		if (isset($query['folder'])) $query['folder'] = str_replace('%2F', '/', rawurlencode($query['folder']));

        if (isset($query['name'])) $query['name'] = rawurlencode($query['name']);

        return $segments;
	}

	$menu_query = JFactory::getApplication()->getMenu()->getItem($query['Itemid'])->query;
	
	if ($menu_query['view'] === 'submit') {
        unset($query['view']);
	    
	    return $segments;
	}

	if (isset($query['view']) && $query['view'] === 'file') {
		$segments[] = 'file';
	}
	unset($query['view']);

	if (isset($query['layout']) && isset($menu_query['layout']) && $query['layout'] === $menu_query['layout']) {
		unset($query['layout']);
	}

	if (isset($query['folder']))
	{
		if (!empty($menu_query['folder']))
		{
			if (strpos($query['folder'], $menu_query['folder']) === 0)
			{
				$relative   = substr(trim($query['folder'], '/'), strlen($menu_query['folder']) + 1);
				$segments[] = str_replace('%2F', '/', rawurlencode($relative));
			}
			else $segments[] = str_replace('%2F', '/', rawurlencode($query['folder']));
		}
		else $segments[] = str_replace('%2F', '/', rawurlencode($query['folder']));

		unset($query['folder']);
	}

	if (isset($query['name']))
	{
		$name = $query['name'];
		$segments[] = rawurlencode($name);
		unset($query['name']);
	}

	return $segments;
}

function FilemanParseRoute($segments)
{
	$vars = array();

	// Circumvent Joomla's auto encoding
	foreach ($segments as &$segment)
	{
		$pos = strpos($segment, ':');

		if ($pos !== false) {
			$segment[$pos] = '-';
		}
	}

	$item = JFactory::getApplication()->getMenu()->getActive();

	if ($segments[0] === 'file')
	{ // file view
		$vars['view']    = array_shift($segments);
		$vars['name']    = array_pop($segments);
		$vars['folder']  = !empty($item->query['folder']) ? $item->query['folder'].'/' : '';
		$vars['folder'] .= implode('/', $segments);
	}
	else
	{ // folder view
		$vars['view']   = $item->query['view'];
		$vars['layout'] = $item->query['layout'];
		$vars['folder'] = !empty($item->query['folder']) ? $item->query['folder'] . '/' : '';
		$vars['folder'] .= implode('/', $segments);
	}

	$vars['folder'] = str_replace('%2E', '.', trim($vars['folder'], '/'));

	return $vars;
}