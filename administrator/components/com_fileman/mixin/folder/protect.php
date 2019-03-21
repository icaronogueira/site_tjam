<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanMixinFolderProtect extends KObjectMixinAbstract
{
    public function protect()
    {
        if (!$this->isProtected())
        {
            $buffer ='DENY FROM ALL';
            file_put_contents($this->_getProtectFile(), $buffer);
        }
    }

    public function unprotect()
    {
        if ($this->isProtected()) {
            unlink($this->_getProtectFile());
        }
    }

    public function isProtected()
    {
        return file_exists($this->_getProtectFile());
    }

    protected function _getProtectFile()
    {
        return $this->fullpath . '/.htaccess';
    }

    public function onMixin(KObjectMixable $mixer)
    {
        if ($mixer instanceof KModelEntityRowset) {
            $mixer->getIterator()->current()->mixin($this); // Make sure we mixin the row as well
        }
    }
}