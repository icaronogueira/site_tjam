<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanModelPages extends KModelAbstract
{
    /**
     * Pages pointing to this component
     *
     * @var array
     */
    protected static $_pages = array();

    /**
     * Constructor
     *
     * @param KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('id', 'int', null, true)
            ->insert('alias', 'cmd', null, true)
            ->insert('language', 'cmd', null)
            ->insert('access', 'int', null) // -1 for no access filter
            ->insert('view', 'cmd')
            ->insert('sort', 'cmd')
            ->insert('direction', 'word', 'asc');
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'identity_key'     => 'id'
        ));

        parent::_initialize($config);
    }

    /**
     * Returns an array of component pages.
     *
     * Each page includes a children property that contains a list of all categories reachable by the page
     *
     * @return array
     */
    protected function _getPages()
    {
        if (!self::$_pages)
        {
            $component = JComponentHelper::getComponent('com_' . $this->getIdentifier()->package);

            $attributes = array('component_id');
            $values     = array($component->id);

            if ($this->getState()->language !== null)
            {
                $attributes[] = 'language';

                if ($this->getState()->language === 'all') {
                    $values[] = JFactory::getDbo()->setQuery('SELECT DISTINCT language FROM #__menu')->loadColumn();
                } else {
                    $values[] = $this->getState()->language;
                }
            }

            if ($this->getState()->access !== null)
            {
                $attributes[] = 'access';
                $values[]     = $this->getState()->access === -1 ? null : $this->getState()->access;
            }


            $items = JApplication::getInstance('site')->getMenu()->getItems($attributes, $values);

            foreach ($items as $item)
            {
                $item           = clone $item;
                $item->children = array();

                if ($item->language === '*') {
                    $item->language = '';
                }

                self::$_pages[$item->id] = $item;
            }

            unset($item);
        }

        return self::$_pages;
    }

    /**
     * Filters pages by view
     *
     * @param array $pages Page list
     * @param array $value Allowed views
     */
    protected function _filterPagesByView(&$pages, array $value)
    {
        foreach ($pages as $i => $page)
        {
            if (!isset($page->query['view']) || !in_array($page->query['view'], $value)) {
                unset($pages[$i]);
            }
        }
    }

    /**
     * Filters pages by a given field
     *
     * @param array  $pages Page list
     * @param string $field Field to filter against
     * @param array  $value Allowed values
     */
    protected function _filterPages(&$pages, $field, array $value)
    {
        foreach ($pages as $i => $page)
        {
            if (!in_array($page->$field, $value)) {
                unset($pages[$i]);
            }
        }
    }

    protected function _actionFetch(KModelContext $context)
    {
        $pages = $this->_getPages();
        $state = $this->getState();

        if ($state->view) {
            $this->_filterPagesByView($pages, (array)$state->view);
        }

        if ($state->id) {
            $this->_filterPages($pages, 'id', (array)$state->id);
        }

        if ($state->alias) {
            $this->_filterPages($pages, 'alias', (array)$state->alias);
        }

        foreach ($pages as &$page) {
            $page = get_object_vars($page);
        }
        unset($page);

        if ($state->sort && count($pages))
        {
            $page = end($pages);
            if (isset($page[$state->sort]))
            {
                $sort      = $state->sort;
                $direction = $state->direction === 'desc' ? 'desc' : 'asc';
                $numeric   = is_numeric($page[$state->sort]);

                usort($pages, function($a, $b) use($numeric, $sort, $direction) {
                    $result = $numeric ? ($a[$sort] - $b[$sort]) : strcasecmp($a[$sort], $b[$sort]);

                    return $direction === 'desc' ? -1 * $result : $result;
                });
            }
        }

        $options = array(
            'status'       => KDatabase::STATUS_FETCHED,
            'data'         => $pages,
            'identity_key' => $context->getIdentityKey()
        );

        $pages = $this->getObject('com://admin/fileman.model.entity.pages', $options);

        return $pages;
    }

    protected function _actionCount(KModelContext $context)
    {
        return count($this->_actionFetch($context));
    }
}
