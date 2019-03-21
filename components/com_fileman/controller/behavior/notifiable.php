<?php
/**
* @package     FILEman
* @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
* @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link        http://www.joomlatools.com
*/

class ComFilemanControllerBehaviorNotifiable extends KControllerBehaviorAbstract
{
    protected function _afterAdd(KControllerContextInterface $context)
    {
        if ($context->getResponse()->getStatusCode() !== 201) {
            return;
        }

        $emails = $this->getObject('com://admin/fileman.model.configs')->fetch()->notification_emails;

        if (empty($emails) || !is_array($emails)) {
            return;
        }

        $translator = $this->getObject('translator');

        // language string exists on site
        $translator->load('com://site/fileman');

        $config  	= JFactory::getConfig();
        $from_name  = $config->get('fromname');
        $mail_from  = $config->get('mailfrom');
        $sitename   = $config->get('sitename');
        $subject    = $translator->translate('A new file was submitted for you to review on {sitename}', array(
            'sitename' => $sitename));

        $file = $context->result;

        $container = $file->getContainer();

        $file_url   = sprintf('%sadministrator/index.php?option=com_fileman&view=file&routed=1&container=%s&folder=%s&name=%s', JURI::root(), $container->slug, rawurlencode($file->folder), rawurlencode($file->name));
        $folder_url = sprintf('%sadministrator/index.php?option=com_fileman&view=files&container=%s&folder=%s', JURI::root(), $container->slug, rawurlencode($file->folder));

        $template = $this->getObject('com:koowa.view.html')->getTemplate();

        foreach ($emails as $email)
        {
            $template->loadFile('com://site/fileman.email.upload.html', 'php');

            $body = $template->render(array(
                'email'      => $email,
                'file'       => $file,
                'sitename'   => $sitename,
                'folder_url' => $folder_url,
                'file_url'   => $file_url,
                'user'       => $this->getObject('user')
            ));

            JFactory::getMailer()->sendMail($mail_from, $from_name, $email, $subject, $body, true);
        }
    }
}