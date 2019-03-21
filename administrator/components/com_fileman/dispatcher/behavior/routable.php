<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Routes requests marked with routed=1 through com_files
 *
 */
class ComFilemanDispatcherBehaviorRoutable extends KControllerBehaviorAbstract
{
    public function isSupported()
    {
        // Overridden to coexists with attachable behavior.
        return $this->getMixer()->getRequest()->getQuery()->container != 'fileman-attachments';
    }

    protected function _setContainer(KControllerContextInterface $context)
    {
        $query = $context->getRequest()->getQuery();

        if (!$query->container) {
            $query->container = 'fileman-files';
        }
    }

    protected function _attachBehaviors(KControllerContextInterface $context)
    {
        $loader = $this->getObject('manager')->getClassLoader();

        $domain = $this->getMixer()->getIdentifier()->getDomain();

        if($path = $this->getObject('object.bootstrapper')->getApplicationPath($domain)) {
            $loader->setBasePath($path);
        }

        $permission = 'ComFilemanControllerPermissionAbstract';

        if (!$loader->isDeclared($permission)) {
            $loader->load($permission);
        }

        $controllers = array(
            'file'      => array(
                'com://site/fileman.controller.behavior.notifiable',
                'com://site/fileman.controller.behavior.contentable'
            ),
            'node'      => array(),
            'folder'    => array(),
            'thumbnail' => array()
        );

        // Append global behaviors
        foreach (array('file', 'folder', 'node', 'thumbnail') as $controller)
        {
            $behaviors = array(
                'com:files.controller.behavior.cacheable' => array(
                    'group' => 'com_fileman.files',
                    'only_clear' => true
                ),
                'permissible'                             => array(
                    'permission' => sprintf('com://%s/fileman.controller.permission.file', $domain)
                )
            );

            $controllers[$controller] = array_merge($controllers[$controller], $behaviors);
        }

        // Inject behaviors
        foreach ($controllers as $controller => $behaviors)
        {
            $this->getIdentifier('com:files.controller.' . $controller)->getConfig()->append(array(
                'behaviors' => $behaviors
            ));
        }
    }

    protected function _beforeDispatch(KControllerContextInterface $context)
    {
        $query  = $context->request->query;
        $layout = $query->layout ?: 'default';
        $view   = $query->view;

        if ($query->routed
                || ($view === 'filelink' && $layout === 'default')
                || ($view === 'files' && in_array($layout, array('default', 'select')))
        ) {
            $tmpl   = $query->tmpl;

            $this->_setContainer($context);
            $this->_attachBehaviors($context);

            $config = array(
                'can_upload' => (bool) $context->getUser()->authorise('core.create', 'com_fileman'),
                'grid' => array(
                    'layout' => 'compact'
                )
            );

            if ($layout === 'select' || $view === 'filelink')
            {
                $query->tmpl = 'joomla';

                $query->layout = 'compact';

                if (!$query->types) {
                    $query->types = array('file', 'image');
                }

                if ($view === 'filelink')
                {
                    $query->editor = $query->e_name;
                    $query->view   = 'files';

                    $params = JComponentHelper::getComponent('com_fileman')->params;

                    if (JFactory::getApplication()->isSite() && $params->get('userfolder', false))
                    {
                        /*$behavior = $this->getObject('com://site/fileman.controller.behavior.ownable');

                        if ($pages = $behavior->getUserPages())
                        {
                            $query->root = $behavior->setUserPage(array_shift($pages))->getFolder();
                            //$query->revalidate_cache = 1;
                        }*/

                        $query->container = 'fileman-user-files';

                        $query->root = $this->getObject('com://site/fileman.controller.behavior.ownable')->getFolder();
                    }
                }
            }
            else
            {
                $config['grid']['layout'] = 'icons';
                $query->layout = 'com://admin/fileman.files.files';
            }

            $query->config = $config;

            $context->param = 'com:files.dispatcher.http';
            $this->getMixer()->execute('forward', $context);

            $query->layout = $layout;
            $query->view   = $view;
            $query->tmpl   = $tmpl;

            unset($context->param);

            if ($query->routed)
            {
                // Work-around the bug here: http://joomlacode.org/gf/project/joomla/tracker/?action=TrackerItemEdit&tracker_item_id=28249
                JFactory::getSession()->set('com.files.fix.the.session.bug', microtime(true));

                $this->send();
            }
        }
    }
}
