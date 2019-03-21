<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanJobAttachments extends ComSchedulerJobAbstract
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'frequency'   => ComSchedulerJobInterface::FREQUENCY_DAILY
        ));

        parent::_initialize($config);
    }

    public function run(ComSchedulerJobContextInterface $context)
    {
        $date = $this->getObject('date', array('timezone' => 'UTC'));
        $date->modify('-1 day');

        $query = $this->getObject('lib:database.query.delete')
                      ->table('fileman_attachments_relations')
                      ->where('row < :row')
                      ->where('created_on < :date')
                      ->bind(
                          array(
                              'row'  => 0,
                              'date' => $date->format('Y-m-d H:i:s')
                          ));

        $adapter = $this->getObject('lib:database.adapter.mysqli');

        // Delete old temporary relationships.
        $adapter->delete($query);

        $attachments = $this->getObject('com://admin/fileman.model.attachments')
                            ->limit(100)
                            ->orphan(true)
                            ->fetch();

        // Delete orphaned attachments
        if (!$attachments->isNew()) {
            $attachments->delete();
        }

        return $this->complete();
    }
}