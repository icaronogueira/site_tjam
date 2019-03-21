<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanTemplateHelperPlayer extends ComFilesTemplateHelperPlayer
{
    /**
     * @param array $config
     * @return string html
     */
    public function render($config = [])
    {
        $config = new KObjectConfigJson($config);
        $config->append([
            'url' => null
        ]);

        $url = $config->url;
        $html = '';

        if ($this->_isAudio($url)) {
            $html = $this->_renderAudio($url);
        }

        if ($this->_isVideo($url)) {
            $html = $this->_renderVideo($url);
        }

        return $html;
    }

    /**
     * @param string $url
     * @return bool
     */
    protected function _isVideo($url)
    {
        $name = $this->_getName($url);
        $extension = $this->_getExtension($name);

        if (in_array($extension, self::$_SUPPORTED_FORMATS['video'])) {
            return true;
        }

        return false;
    }

    /**
     * @param string $url
     * @return string
     */
    protected function _renderVideo($url)
    {
        $name = $this->_getName($url);
        $extension = $this->_getExtension($name);

        $html = $this->getTemplate()
                     ->loadFile('com://site/fileman.file.player_video.html')
                     ->render(array('url' => $url, 'extension' => $extension, 'name' => $name));

        return $html;
    }

    /**
     * @param string $url
     * @return bool
     */
    protected function _isAudio($url)
    {
        $name = $this->_getName($url);
        $extension = $this->_getExtension($name);

        if (in_array($extension, self::$_SUPPORTED_FORMATS['audio'])) {
            return true;
        }

        return false;
    }

    /**
     * @param string $url
     * @return string
     */
    protected function _renderAudio($url)
    {
        $name = $this->_getName($url);
        $extension = $this->_getExtension($name);

        $html = $this->getTemplate()
                     ->loadFile('com://site/fileman.file.player_audio.html')
                     ->render(array('url' => $url, 'extension' => $extension, 'name' => $name));

        return $html;
    }

    /**
     * @param string $name
     * @return string
     */
    protected function _getExtension($name)
    {
        if (! empty($name)) {
            $name = pathinfo($name);
            return $name['extension'];
        }

        return '';
    }

    /**
     * @param string $url
     * @return string
     */
    protected function _getName($url)
    {
        if (substr($url, 0, 1) != '?') {
            $url = '?'.$url;
        }

        $query = parse_url($url, PHP_URL_QUERY);
        parse_str(str_replace('&amp;', '&', $query), $query);

        return $query['name'];
    }
}