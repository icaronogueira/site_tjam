<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Listbox Template Helper.
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Joomlatools\Component\LOGman
 */
class ComLogmanTemplateHelperListbox extends ComKoowaTemplateHelperListbox
{
    public function packages($config = array())
    {
        $config = new KObjectConfig($config);

        $adapter = $this->getObject('lib:database.adapter.mysqli');

        $query = $this->getObject('lib:database.query.select')->table('logman_activities')->columns('package')
            ->distinct();

        $packages = $adapter->select($query, KDatabase::FETCH_FIELD_LIST);

        $options = array();

        if ($packages)
        {
            foreach($packages as $package) {
                $options[] = $this->option(array('label' => ucfirst($package), 'value' => $package));
            }
        }

        $config->append(array('options' => $options));

        return $this->optionlist($config);
    }
}