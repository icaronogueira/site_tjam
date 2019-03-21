<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanModelEntityPage extends KModelEntityAbstract
{
    public function getPropertyFolder()
    {
        $result = null;

        if (!$this->isNew() && $query = $this->_getQuery())
        {
            if (isset($query['folder'])) {
                $result = $query['folder'];
            }
        }

        return $result;
    }

    public function getPropertyContainer()
    {
        $result = null;

        if (!$this->isNew() && $query = $this->_getQuery())
        {
            if (isset($query['container'])) {
                $result = $query['container'];
            }
        }

        return $result;
    }

    protected function _getQuery()
    {
        $query = null;

        if (!$this->isNew())
        {
            $link = $this->link;

            $url = parse_url($link);

            if (isset($url['query'])) {
                parse_str($url['query'], $query);
            }
        }

        return $query;
    }
}
