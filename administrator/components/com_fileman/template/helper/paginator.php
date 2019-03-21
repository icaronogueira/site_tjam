<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Paginator Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template\Helper
 */
class ComFilemanTemplateHelperPaginator extends KTemplateHelperPaginator
{
    /**
     * Render item pagination
     *
     * @see     http://developer.yahoo.com/ypatterns/navigation/pagination/
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function pagination($config = array())
    {
        $config = new KObjectConfigJson($config);

        $config->append(array(
            'url' => $this->getObject('request')->getUrl()
        ));

        return parent::pagination($config);
    }

    /**
     * Generates a pagination link
     *
     * @param KObject $page Page object
     * @param string  $title Page title
     * @return string
     */
    protected function _link($page, $title)
    {
        $url = $this->getConfig()->url;

        $query = $url->getQuery(true);

        //For compatibility with Joomla use limitstart instead of offset
        $query['limit']      = $page->limit;
        $query['limitstart'] = $page->offset;

        unset($query['offset']);

        $url->setQuery($query);

        if ($page->active && !$page->current) {
            $html = '<a href="'.$url.'">'.$this->getObject('translator')->translate($title).'</a>';
        } else {
            $html = '<a>'.$this->getObject('translator')->translate($title).'</a>';
        }

        return $html;
    }
}
