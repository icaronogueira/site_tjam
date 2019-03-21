<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanControllerBehaviorContentable extends KControllerBehaviorAbstract
{
    protected function _afterDelete(KControllerContextInterface $context)
    {
        if ($entities = $context->result)
        {
            $model = $this->getObject('com://admin/fileman.model.contents');

            foreach ($entities as $entity)
            {
                $content = $model->path($entity->path)->container($entity->container)->fetch();

                if (!$content->isNew()) $content->delete();
            }
        }
    }
}