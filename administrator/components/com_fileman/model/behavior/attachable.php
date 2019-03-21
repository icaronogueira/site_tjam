<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanModelBehaviorAttachable extends ComFilesModelBehaviorAttachable
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()->insert('orphan', 'boolean', false)->insert('before', 'string');
    }

    protected function _beforeFetch(KModelContextInterface $context)
    {
        parent::_beforeFetch($context);

        $state = $context->getState();

        if ($state->orphan === true)
        {
            $context->query
                ->join('fileman_attachments_relations AS rel2', 'rel2.fileman_attachment_id = tbl.fileman_attachment_id AND rel2.row > 0')
                ->where('rel2.fileman_attachment_id IS NULL')
                ->group('tbl.fileman_attachment_id');
        }
    }
}