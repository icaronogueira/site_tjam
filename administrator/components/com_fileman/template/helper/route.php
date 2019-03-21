<?php
/**
 * @category    FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanTemplateHelperRoute extends KTemplateHelperAbstract
{
    public function token($config = array())
    {
        $config = new KObjectConfig($config);

        $config->append(array('expire' => '+24 hours'));

        $secret = JFactory::getConfig()->get('secret');

        $token = $this->getObject('lib:http.token');

        $date = new DateTime('now');

        $token->setExpireTime($date->modify($config->expire));

        return $token->sign($secret);
    }

    public function permalink($config = array())
    {
        $config = new KObjectConfig($config);

        $config->append(array('request' => $this->getObject('request')));

        if (!$config->file instanceof KModelEntityInterface) {
            throw new RuntimeException('File is missing');
        }

        $file = $config->file;

        $request = $config->request;

        $path = trim(implode('/', $request->getUrl()->getPath(true)), '/');

        $folder = trim(implode('/', $request->getSiteUrl()->getPath(true)), '/');

        if ($folder && strpos($path, $folder) === 0) {
            $path = trim(substr($path, strlen($folder)), '/');
        }

        $path = explode('/', $path);

        $url = $config->request->getSiteUrl();

        if (isset($path[0]) && $path[0] === 'index.php') {
            $permalink = sprintf('%s/index.php/filelink/%s', $url, rawurlencode($file->container));
        } else {
            $permalink = sprintf('%s/filelink/%s', $url, rawurlencode($file->container));
        }

        if ($file->folder)
        {
            $path = explode('/', $file->folder);

            foreach ($path as $folder) {
                $permalink .= '/' . rawurlencode($folder);
            }
        }

        $permalink .= '/' . rawurlencode($file->name);

        return $permalink;
    }
}