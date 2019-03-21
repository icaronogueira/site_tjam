<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanControllerUserfolder extends ComFilemanControllerFolder
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('before.render', '_checkUser');
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array('behaviors' => array('ownable'), 'model' => 'com:files.model.folders'));
        parent::_initialize($config);
    }

    protected function _checkUser(KControllerContextInterface $context)
    {
        if (!$context->user->isAuthentic())
        {
            $message = $this->getObject('translator')->translate('You need to be logged in to access your files');

            $url = $this->getObject('lib:dispatcher.router.route', array(
                'url' => $context->getRequest()->getSiteUrl()
            ));

            $redirect = JRoute::_('index.php?option=com_users&view=login&return=' .
                                  base64_encode($url->toString()), false);

            JFactory::getApplication()->redirect($redirect, $message, 'error');
        }
    }
}