<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined( '_JEXEC' ) or die;

jimport('joomla.plugin.plugin');

class plgButtonAttachments extends JPlugin
{
    protected $_extensions = array('docman' => array('document'), 'content' => array('article', 'form'), 'textman' => array('article'));

    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    public function onDisplay($name)
    {
        $button = null;

        $application = JFactory::getApplication();

        if ($data = $this->_getContextData($application))
        {
            $button = new JObject();
            $button->class = 'btn fileman-attachments';

            $is_joomlatools_extension = false;

            try
            {
                if (class_exists('Koowa') && class_exists('KObjectManager')) {
                    $is_joomlatools_extension = (boolean) KObjectManager::getInstance()->isRegistered('dispatcher');
                }
            } catch (Exception $e) {}

            if (!$is_joomlatools_extension)
            {
                // Use Joomla modal
                $button->set('modal', true);
                $button->set('options', "{handler: 'iframe', size: {x: 1000, y: 600}}");

                JHtml::_('stylesheet', 'media/koowa/com_koowa/css/modal-override.css');
            }
            else $button->set('class', 'btn k-js-iframe-modal fileman-attachments'); // Open using MagnificPopup

            $button->set('link', 'index.php?option=com_fileman&amp;view=attachments&amp;e_name=' . $name . '&amp;layout=editor&amp;container=fileman-attachments&amp;table=' . rawurlencode($data->table) . '&amp;row=' . rawurlencode($data->row));
            $button->set('text', JText::sprintf('PLG_ATTACHMENTS_BUTTON_FILE', $data->count));
            $button->set('name', 'attachment fileman-attachments');
        }

        return $button;
    }

    protected function _getContextData($application)
    {
        $data = null;

        $input = $application->input;

        foreach ($this->_extensions as $extension => $views)
        {
            $option = 'com_' . $extension;

            if ($input->get('option') == $option && in_array($input->get('view'), $views))
            {
                $method = sprintf('_getContextData%s', ucfirst($extension));

                if (method_exists($this, $method)) {
                    if ($data = (object) $this->$method($application)) break;
                }
            }
        }

        return $data;
    }

    protected function _getContextDataContent($application)
    {
        $input = $application->input;

        $table = 'content';
        $count = 0;

        if ($application->isAdmin()) {
            $row = $input->getInt('id', 0);
        } else {
            $row = $input->getInt('a_id', 0);
        }

        if (!$row) {
            $row = $this->_getRandomInt() * (-1);
        } else {
            $count = $this->_getAttachmentsCount($row, $table);
        }

        return array('table' => $table, 'row' => $row, 'count' => $count);
    }

    protected function _getContextDataDocman($application)
    {
        $input = $application->input;

        $table = 'docman_documents';
        $count = 0;

        if (JFactory::getApplication()->isSite())
        {
            if ($row = $input->get('slug', '', 'raw'))
            {
                $row = KObjectManager::getInstance()
                                     ->getObject('com://admin/docman.model.documents')
                                     ->slug($row)
                                     ->fetch()->id;
            }

        }
        else $row = $input->getInt('id', 0);

        if (!$row) {
            $row = $this->_getRandomInt() * (-1);
        } else {
            $count = $this->_getAttachmentsCount($row, $table);
        }

        return array('table' => $table, 'row' => $row, 'count' => $count);
    }

    protected function _getContextDataTextman($application)
    {
        $input = $application->input;

        $table = 'content';
        $count = 0;

        $row = $input->getInt('id', 0);

        if (!$row) {
            $row = $this->_getRandomInt() * (-1);
        } else {
            $count = $this->_getAttachmentsCount($row, $table);
        }

        return array('table' => $table, 'row' => $row, 'count' => $count);
    }

    protected function _getRandomInt()
    {
        list($usec, $sec) = explode(' ', microtime());
        $seed = (float) $sec + ((float) $usec * 100000);

        srand($seed);

        // Temporarily assign a random negative int as the row ID
        return rand();
    }

    protected function _getAttachmentsCount($row, $table)
    {
        return KObjectManager::getInstance()
                             ->getObject('com://admin/fileman.model.attachments')
                             ->table($table)
                             ->row($row)
                             ->count();
    }
}