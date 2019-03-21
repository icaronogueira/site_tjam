<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanModelEntityScan extends KModelEntityRow
{
    const MAXIMUM_FILE_SIZE = 262144000; // 250 MB

    public static $thumbnail_extensions = [
        'pdf', 'doc', 'docx', 'odt', 'xls', 'xlsx', 'ods', 'ppt', 'pptx', 'odp',
        'bmp', 'gif', 'png', 'tif', 'tiff', 'ai', 'psd', 'svg', 'jpg', 'jpeg', 'html', 'txt'
    ];

    public static $ocr_extensions = [
        'pdf', 'doc', 'docx', 'odt', 'html', 'txt',
        'xls', 'xlsx', 'ods', 'ppt', 'pptx'
    ];

    public function save()
    {
        $result = true;

        $entity = $this->getEntity();

        if ($entity->isNew())
        {
            $result = false;
            $this->setStatusMessage('Invalid scan entity');
        }

        if ($this->thumbnail && !in_array($entity->extension, self::$thumbnail_extensions))
        {
            $result = false;
            $this->setStatusMessage('Invalid scan thumbnail extension');
        }

        if ($this->ocr && !in_array($entity->extension, self::$ocr_extensions))
        {
            $result = false;
            $this->setStatusMessage('Invalid scan ocr extension');
        }

        if ($entity->filesize > self::MAXIMUM_FILE_SIZE)
        {
            $result = false;
            $this->setStatusMessage('Invalid scan entity filesize');
        }

        if ($result) {
            $result = parent::save();
        }

        return $result;
    }

    public function getEntity()
    {
        return $this->getObject('com:files.model.files')
                    ->container($this->container)
                    ->folder($this->folder)
                    ->name($this->name)->fetch();
    }

    public function getPropertyIdentifier()
    {
        $result = null;

        if (!$this->isNew())
        {
            $entity = $this->getEntity();

            $result = sprintf('%s:%s', $entity->container, $entity->path);
        }

        return $result;
    }
}