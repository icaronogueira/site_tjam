<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanDispatcherBehaviorExpirable extends KControllerBehaviorAbstract
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array('priority' => KCommandHandlerInterface::PRIORITY_HIGH));

        parent::_initialize($config);
    }

    /**
     * Checks for expiration tokens on routed file requests for protecting file downloads from being leeched.
     *
     * @param KDispatcherContextInterface $context
     * @return bool
     */
    protected function _beforeDispatch(KDispatcherContextInterface $context)
    {
        $result = true;

        $request = $context->getRequest();
        $query = $request->getQuery();

        if ($query->view == 'file' && $query->routed && $request->getFormat() == 'html')
        {
            $file = $this->getObject('com:files.model.files')
                         ->name($query->name)
                         ->folder($query->folder)
                         ->container($query->container)
                         ->fetch();

            if (!$file->isNew())
            {
                // Bypass FILElink images as these need to render on editors
                if ($query->container == 'fileman-attachments' || !$file->isImage())
                {
                    if (isset($query->exp_token))
                    {
                        $token  = $this->getObject('lib:http.token')->fromString($query->exp_token);
                        $secret = JFactory::getConfig()->get('secret');

                        if (!$token->verify($secret) || $token->isExpired())
                        {
                            $query->tmpl = 'koowa'; // Do not handle response back to Joomla
                            $context->getResponse()->setStatus(KHttpResponse::UNAUTHORIZED)->send();
                        }
                    }
                    else
                    {
                        // Routed queries must contain an expiration token
                        $query->tmpl = 'koowa'; // Do not handle response back to Joomla
                        $context->getResponse()->setStatus(KHttpResponse::BAD_REQUEST)->send();
                    }
                }
            }
            else
            {
                $query->tmpl = 'koowa'; // Do not handle response back to Joomla
                $context->getResponse()->setStatus(KHttpResponse::NOT_FOUND)->send();
            }
        }

        return $result;
    }
}