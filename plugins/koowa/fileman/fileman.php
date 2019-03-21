<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * FILEman Koowa Plugin.
 *
 * Handles tasks such as rendering and removal of attachments from supported resources. It also deals with attachments
 * from newly created items (temp attachments ID update).
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Joomlatools\Plugin\Koowa
 */
class PlgKoowaFileman extends PlgKoowaSubscriber
{
    protected $_tables = array('com_content.article' => 'content', 'com_content.form' => 'content');

    protected $_extensions = array('docman' => array('document'), 'content' => array('article'), 'textman' => array('article'));

    protected $_tag = '{attachments}';

    protected $_container;

    public function __construct($dispatcher, $config = array())
    {
        parent::__construct($dispatcher, $config);

        $this->_container = $this->getObject('com:files.model.containers')
                                 ->slug('fileman-attachments')
                                 ->fetch();

        if($dispatcher instanceof JDispatcher || $dispatcher instanceof JEventDispatcher) {
            $dispatcher->attach($this);
        }
    }

    /**
     * Overridden to only run if we have Nooku framework installed
     */
    public function update(&$args)
    {
        $return = null;

        try
        {
            if (class_exists('Koowa') && !$this->_container->isNew())
            {
                $return = parent::update($args);
            }
        }
        catch (Exception $e)
        {
            if (JDEBUG) {
                throw $e;
            }
        }

        return $return;
    }

    protected function _isSupported($context, $all = true)
    {
        $result = false;

        $parts = explode('.', $context);

        if (count($parts) == 2)
        {
            $extension = $parts[0];

            if (strpos($parts[0], 'com_') === 0) {
                $extension = str_replace('com_', '', $parts[0]);
            }

            if (in_array($extension, array_keys($this->_extensions)))
            {
                if ($all)
                {
                    if (in_array($parts[1], $this->_extensions[$extension])) {
                        $result = true;
                    }
                }
                else $result = true;
            }
        }

        return $result;
    }

    /**
     * Content prepare event handler
     *
     * Renders the attachments
     *
     * @param     $context
     * @param     $row
     * @param     $params
     * @param int $page
     */
    public function onContentPrepare($context, &$row, &$params, $page = 0)
    {
        if ($this->_isSupported($context, false))
        {
            $parts = explode('.', $context);

            $extension = str_replace('com_', '', $parts[0]);

            if ($this->_isSupported($context, true))
            {
                $method = sprintf('_prepare%s%s', ucfirst($extension), ucfirst($parts[1]));

                if (method_exists($this, $method)) {
                    $this->{$method($row)}; // Custom prepare
                } else {
                    $row->text = $this->_prepareContent($row->id, $this->_tables[$context], $row->text); // Default prepare
                }
            }
            else
            {
                $method = sprintf('_cleanupContent%s%s', ucfirst($extension), ucfirst($parts[1]));

                if (method_exists($this, $method)) {
                    $this->{$method($row)};
                } else {
                    $row->text = $this->_cleanupContent($row->text);
                }
            }
        }

        if ($context == 'text' && isset($row->text)) {
            $row->text = $this->_cleanupContent($row->text);
        }
    }

    public function onContentAfterDisplay($context, &$article, &$params, $page = 0)
    {
        $html = '';

        if ($context == 'com_content.category')
        {
            $config = $this->getObject('com://admin/fileman.model.configs')->fetch();

            if ($config->attachments_lists) {
                $html = $this->_renderAttachments($article->id, 'content');
            }
        }

        return $html;
    }

    /**
     * Re-route FILEman permalinks
     */
    public function onAfterInitialise()
    {
        $request = $this->getObject('request');

        $path = trim(implode('/', $request->getUrl()->getPath(true)), '/');

        $folder = trim(implode('/', (array) $request->getSiteUrl()->getPath(true)), '/');

        if ($folder && strpos($path, $folder) === 0) {
            $path = trim(substr($path, strlen($folder)), '/');
        }

        $path = explode('/', $path);

        if (isset($path[0]) && $path[0] === 'index.php') {
            array_shift($path); // Remove index.php from begining of path
        }

        if (isset($path[0]) && $path[0] === 'filelink')
        {
            $name = array_pop($path);

            if (isset($path[1]))
            {
                $container = $path[1];

                $folder = array();

                if (isset($path[2]))
                {
                    foreach (array_slice($path, 2) as $key => $value) {
                        $folder[] = $value;
                    }
                }

                $folder = implode('/', $folder);

                $query = array(
                    'option'    => 'com_fileman',
                    'view'      => 'file',
                    'routed'    => 1,
                    'folder'    => $folder,
                    'container' => $container,
                    'name'      => $name
                );

                $user     = $this->getObject('user');
                $response = $this->getObject('response', array('request' => $request, 'user' => $user));

                $request->setQuery($query);

                $controller = $this->getObject('com://site/fileman.controller.file', array(
                    'request'  => $request,
                    'response' => $response
                ));

                $controller->render();

                $response->send();
            }
        }
    }

    /**
     * Content cleanup
     *
     * Removes attachments tags from content
     *
     * @param string $context The content to clean
     * @return string The cleaned up content
     */
    protected function _cleanupContent($content)
    {
        return str_replace($this->_tag, '', $content);
    }

    /**
     * DOCman render event handler.
     *
     * Renders attachments on DOCman content.
     *
     * @param KEventInterface $event The event.
     */
    public function onBeforeDocmanHtmlViewRender($event)
    {
        $view = $event->getTarget();

        if (JFactory::getApplication()->isSite() && $view->getName() == 'document' && $view->getLayout() == 'default')
        {
            $document = $view->getModel()->fetch();

            if (!$document->isNew()) {
                $document->setProperty('description', $this->_prepareContent($document->id, 'docman_documents', $document->description), false);
            }
        }
    }

    /**
     * Default content prepare
     *
     * Adds attachments to the content
     *
     * @param mixed  $id    The resource identifier
     * @param string $table The resource table name
     * @param string $text  The content
     * @return string The prepared content
     */
    protected function _prepareContent($id, $table, $content)
    {
        if ($id)
        {
            if ($output = $this->_renderAttachments($id, $table))
            {
                if (strstr($content, $this->_tag) !== false) {
                    $content = str_replace($this->_tag, $output, $content); // Replace tags with attachments
                } else {
                    $content .= $output;
                }
            }
            else $content = $this->_cleanupContent($content);
        }

        return $content;
    }

    protected function _renderAttachments($id, $table)
    {
        $output = '';

        $attachments = $this->getObject('com://admin/fileman.model.attachments')
                            ->table($table)
                            ->row($id)
                            ->container($this->_container->id)
                            ->fetch();

        if ($attachments->count())
        {
            $manager = KObjectManager::getInstance();

            $request = $manager->getObject('request');

            $view = $manager->getObject('com://site/fileman.view.default.html', array('url' => $request->getUrl()));

            $params = $this->getObject('com://admin/fileman.model.configs')->fetch();

            $layout     = $params->attachments_layout;
            $show_icons = $params->attachments_icons;
            $show_info  = $params->attachments_info;

            // Make thumbnails scannable.
            $this->getIdentifier('com:files.model.entity.thumbnail')
                 ->getConfig()
                 ->append(array(
                     'behaviors' => array(
                         'com://admin/fileman.database.behavior.scannable'
                     )
                 ));

            if ($this->getObject('com://admin/fileman.job.scans')->isEnabled())
            {
                // Make document files thumbnailable when connect is supported
                $this->getIdentifier('com:files.database.behavior.thumbnailable')
                     ->getConfig()
                     ->append(array(
                         'thumbnailable_extensions' => ComFilemanModelEntityScan::$thumbnail_extensions
                     ));
            }

            $template = $view->getTemplate()
                             ->addFilter('style')
                             ->addFilter('script')
                             ->loadFile("com://site/fileman.attachments.{$layout}.html");

            $output = $template->render(array('attachments' => $attachments, 'show_icon' => $show_icons, 'show_info' => $show_info));
        }

        return $output;
    }

    /**
     * After content save event handler.
     *
     * Updates the temporary attachments relations records for new content items.
     *
     * @param $context
     * @param $item
     * @param $isNew
     */
    public function onContentAfterSave($context, $item, $isNew)
    {
        if ($isNew && in_array($context, array_keys($this->_tables)))
        {
            $request = $this->getObject('request');

            if (($row = (int) $request->getData()->fileman_attachment_row) && $row < 0) {
                $this->_updateAttachments($item->id, $row);
            }
        }
    }

    /**
     * After content delete event handler.
     *
     * Deletes attachment entries when deleting items.
     *
     * @param string $context The context
     * @param object $item The item
     */
    public function onContentAfterDelete($context, $item)
    {
        if (in_array($context, array_keys($this->_tables))) {
            $this->_deleteAttachments($item->id, $this->_tables[$context]);
        }
    }

    /**
     * After DOCman document add event handler.
     *
     * Updates the temporary attachments relations records for new documents.
     *
     * @param KEventInterface $event The event
     */
    public function onAfterDocmanDocumentControllerAdd($event)
    {
        $data = $event->getTarget()->getRequest()->getData();

        if (($row = (int) $data->fileman_attachment_row) && $row < 0) {
            $this->_updateAttachments($event->result->id, $row);
        }
    }

    /**
     * After DOCman document delete event handler.
     *
     * Deletes attachments from the deleted document.
     *
     * @param KEventInterface $event The event
     */
    public function onAfterDocmanDocumentControllerDelete($event)
    {
        if ($event->result->getStatus() === KDatabase::STATUS_DELETED) {
            $this->_deleteAttachments($event->result->id, 'docman_documents');
        }
    }

    /**
     * Update attachments
     *
     * Updates the temporary attachments relations records for new resources
     *
     * @param mixed $id The resource ID
     * @param mixed $temp_id The temporary resource ID
     */
    protected function _updateAttachments($id, $temp_id)
    {
        $table = $this->getObject('com://admin/fileman.model.attachments')->getRelationsModel()->getTable();

        $query = $this->getObject('lib:database.query.update')->table($table->getBase())->values(array('row = :id'))
                      ->where('row = :row')->bind(array('id' => $id, 'row' => $temp_id));

        $table->getAdapter()->update($query);
    }

    /**
     * Delete attachments
     *
     * Removes attachments from a given resource
     *
     * @param mixed $id The resource identifier
     * @param string $table The resource table
     */
    protected function _deleteAttachments($id, $table)
    {
        $controller  = $this->getObject('com://admin/fileman.controller.attachment')
                            ->container($this->_container->id);

        $attachments = $this->getObject('com://admin/fileman.model.attachments')
                            ->table($table)
                            ->row($id)
                            ->fetch();

        foreach ($attachments as $attachment)
        {
            $controller->name($attachment->name);
            $controller->detach(array('table' => $table, 'row' => $id));
        }
    }
}