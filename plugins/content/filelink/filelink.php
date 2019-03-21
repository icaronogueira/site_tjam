<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class PlgContentFilelink extends JPlugin
{
    /**
     * Overridden to only run if we have Nooku framework installed
     */
    public function update(&$args)
    {
        $return = null;

        try
        {
            if (class_exists('Koowa')) {
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

    public function onContentPrepare($context, &$row, &$params, $page = 0)
    {
        if ($context == 'com_finder.indexer') {
            return;
        }

        if ($links = $this->_getLinks($row->text)) {
            $this->_enrichLinks($links, $row->text);
        }

        if ($links = $this->_getLinks($row->text)) {
            $this->_replaceLinksWithPlayers($links, $row->text);
        }
    }

    /**
     * Overrides fulltext and into image buttons on backend content form so that the FILElink file picker is used
     * instead of the default media component.
     *
     * @param $form
     * @param $data
     */
    /*public function onContentPrepareForm($form, $data)
    {
        if (JFactory::getApplication()->isAdmin() && $form->getName() == 'com_content.article')
        {
            $document = JFactory::getDocument();

            $intro_route    = JRoute::_('index.php?option=com_fileman&view=filelink&callback=filelinkIntroCallback&types=image&tmpl=koowa&_=' .
                                        time(), false);
            $fulltext_route = JRoute::_('index.php?option=com_fileman&view=filelink&callback=filelinkFullCallback&types=image&tmpl=koowa&_=' .
                                        time(), false);

            $document->addScriptDeclaration("
                jQuery(function(\$)
                {                    
                    var fileLinkCallback = function(id, link) {         
                        $(id).val(link);
                    };
                    
                    var buttons = {'#jform_images_image_intro': '{$intro_route}', '#jform_images_image_fulltext': '{$fulltext_route}'};
                    
                    if (" . (version_compare(JVERSION, '3.7', '<') ? 1 : 0) . ")
                    {                    
                        filelinkIntroCallback = function(link) {
                            fileLinkCallback('#jform_images_image_intro', link);
                        };
                        
                        filelinkFullCallback = function(link) {
                            fileLinkCallback('#jform_images_image_fulltext', link);
                        };
                                        
                        $.each(buttons, function(id, route)
                        {
                            var button = \$(id), parent;
                                                                                                                         
                            if (button.length && (parent = button.parent()))
                            {
                                var link = parent.find('a.modal, a.button-select');
                                
                                if (link.length) {
                                    link.attr('href', route);
                                }
                            }
                        });
                    }
                    else
                    {
                        $('.field-media-wrapper').each(function()
                        {                           
                            var el = $(this);
                                                      
                            if (el.data('fieldMedia'))
                            {
                                var field = el.data('fieldMedia');
                                                           
                                $.each(buttons, function(id, route)
                                {
                                    if (el.find(id).length) {
                                        field.options.url = route;
                                    }
                                });
                            }  
                        });
                        
                        var handleMediaField = function(id)
                        {
                            var field = $(id).closest('.field-media-wrapper');
                            
                            if (field.length && field.data('fieldMedia'))
                            {
                                field = field.data('fieldMedia');
                                
                                $(id).trigger('change');
                                field.updatePreview();
                                
                                field.modalClose();
                            }      
                        }                                                                
                        
                        filelinkIntroCallback = function(link)
                        {
                            var id = '#jform_images_image_intro';
                        
                            fileLinkCallback(id, link);
                            
                            handleMediaField(id);                                                                                                    
                        };
                        
                        filelinkFullCallback = function(link)
                        {
                            var id = '#jform_images_image_fulltext';
                        
                            fileLinkCallback(id, link);
                            
                            handleMediaField(id);
                        };
                    }
                });"
            );
        }
    }*/

    /**
     * Replaces audio/video playable links with html5 players
     *
     * @param array  $links   The links to enrich.
     * @param string $content The content containing the links.
     */
    protected function _replaceLinksWithPlayers(&$links, &$content)
    {
        foreach ($links as &$link)
        {
            if (isset($link->url))
            {
                $url = $link->url;

                $manager = KObjectManager::getInstance();

                $helper = $manager->getObject('com://site/fileman.template.helper.player');

                $helper->load();

                $player = $helper->render(array('url' => $url));

                if (! empty($player)) {
                    $content = str_replace($link->full, $player, $content);
                }
            }
        }
    }

    /**
     * Enrich links.
     *
     * @param array  $links   The links to enrich.
     * @param string $content The content containing the links.
     */
    protected function _enrichLinks(&$links, &$content)
    {
        $manager = KObjectManager::getInstance();
        $helper  = $manager->getObject('com://admin/fileman.template.helper.route');

        foreach ($links as &$link)
        {
            $filelink = $link->full;
            $html     = '';

            if  ($link->type == 'image')
            {
                if ($source = $link->source)
                {
                    $parts = $manager->getObject('com:files.model.state.parser.url')->parse($source);

                    $container = $parts->container;
                    $path      = $parts->path;

                    $folder = str_replace('&amp;', '&', dirname($path));
                    $name   = basename($path);

                    if (in_array($folder, array('.', '/'))) {
                        $folder = '';
                    }

                    $controller = $manager->getObject('com:files.controller.file');
                    $controller->getRequest()->getQuery()->thumbnails = true;

                    $manager->getIdentifier('com:files.model.entity.thumbnail')
                         ->getConfig()
                         ->append(array('behaviors' => array('com://admin/fileman.database.behavior.scannable')));

                    $file = $controller->container($container)->folder($folder)->name($name)->browse();

                    if (!$file->isNew())
                    {
                        if (!isset($link->attributes['width']))
                        {
                            if (isset($link->attributes['style']) && preg_match('#(?<!-)width:\s*(\d+)#i', $link->attributes['style'], $result)) {
                                $link->attributes['width'] = $result[1]; // Set width from style value
                            }
                            elseif (($metadata = $file->metadata) && isset($metadata['image']['width'])) {
                                $link->attributes['width'] = $metadata['image']['width'];
                            }
                        }

                        $original = JRoute::_(sprintf('index.php?option=com_fileman&view=file&routed=1&folder=%s&name=%s&container=%s', rawurlencode($folder), rawurlencode($name), $file->container));

                        $srcset = array();

                        if (isset($link->attributes['width']) && ($thumbnails = $file->thumbnail))
                        {
                            $container = $manager->getObject('com:files.model.containers')->slug('fileman-thumbnails')->fetch();

                            foreach ($container->getParameters()->versions as $label => $config)
                            {
                                if ($thumbnail = $thumbnails->find($label))
                                {
                                    $path = sprintf('%s/%s', JURI::root(), $this->_getCleanBasePath($thumbnail->relative_path));

                                    $srcset[$config->dimension->width] = sprintf('%s %sw', $path, $config->dimension->width);
                                }
                            }

                            // Add source
                            if (($metadata = $file->metadata) && isset($metadata['image']['width'])) {
                                $srcset[$metadata['image']['width']] = sprintf('%s %sw', $original, $metadata['image']['width']);
                            }
                        }

                        // Cleanup style attribute

                        $styles_ignore = array('width', 'height');

                        if (isset($link->attributes['style']))
                        {
                            $styles = explode(';', $link->attributes['style']);

                            $valid_styles = array();

                            foreach ($styles as $style)
                            {
                                $parts = explode(':', $style);

                                if (!in_array(trim($parts[0]), $styles_ignore)) {
                                    $valid_styles[] = $style;
                                }
                            }

                            if ($valid_styles) {
                                $link->attributes['style'] = implode(';', $valid_styles);
                            } else {
                                unset($link->attributes['style']);
                            }
                        }

                        $attributes = array();

                        foreach (array('alt', 'title', 'class', 'align', 'width', 'style') as $attribute) {
                            if (isset($link->attributes[$attribute])) $attributes[] = sprintf('%s="%s"', $attribute, $link->attributes[$attribute]);
                        }

                        $attributes = implode(' ', $attributes);

                        if (count($srcset))
                        {
                            ksort($srcset, SORT_NUMERIC);

                            $format = '<img %s src="%s" srcset="%s" sizes="100vw">';

                            $html .= sprintf($format, $attributes, $original, implode(', ', array_values(array_reverse($srcset, true))));

                        }
                        else
                        {
                            $format = '<img %s src="%s">';

                            $html .= sprintf($format, $attributes, $original);
                        }
                    }
                }
            }

            if ($html) {
                $content = str_replace($filelink, $html, $content);
            }
        }
    }

    protected function _getCleanBasePath($path)
    {
        $folders = explode('/', $path);

        foreach ($folders as &$folder) {
            $folder = rawurlencode($folder);
        }

        return implode('/', $folders);
    }

    /**
     * Replaces a link by its enriched version.
     *
     * @param object $link    The link object.
     * @param string $html    The enriched link.
     * @param string $content The content text.
     */
    protected function _replaceLink($link, $html, &$content)
    {
        $content = str_replace($link->full, $html, $content);
    }

    /**
     * Links getter.
     *
     * Returns a list of links.
     *
     * @param string $content The content.
     * @return array The links.
     */
    protected function _getLinks(&$content)
    {
        $matches = array();
        $pattern = '~(?:<a.*</a>|<img.*>)~isU';

        if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER))
        {
            foreach ($matches as &$match)
            {
                $in_double_quote = false;
                $in_single_quote = false;

                for ($i = 0, $j = strlen($match[0]); $i < $j; $i++)
                {
                    $char = $match[0][$i];

                    if ($char === '"' && !$in_single_quote) {
                        $in_double_quote = !$in_double_quote;
                    }
                    elseif ($char === '\'' && !$in_double_quote) {
                        $in_single_quote = !$in_single_quote;
                    }
                    elseif ($char === '>' && !$in_single_quote && !$in_double_quote)
                    {
                        if (strpos($match[0], '<a') === 0)
                        {
                            $match['attributes'] = substr($match[0], 2, $i-2);
                            $match['text'] = substr($match[0], $i+1, $j-$i-1-4);
                        }
                        else
                        {
                            if ($match[0][$i-1] == '/') {
                                $k = 1;
                            } else {
                                $k = 0;
                            }

                            $match['attributes'] = substr($match[0], 4, $i-$k-4);
                        }

                        continue 2;
                    }
                }
            }
        }

        $links = array();

        foreach ($matches as $i => &$match)
        {
            $match['full']  = $match[0];
            unset($match[0]);

            $match = (object) $match;

            // Parse attributes
            if (preg_match_all('/([\w:-]+)[\s]?=[\s]?"([^"]*)"/i', $match->attributes, $attr, PREG_SET_ORDER))
            {
                $result = array();
                foreach ($attr as $a) {
                    $result[$a[1]] = $a[2];
                }

                $match->attributes = $result;
            }
            else continue;

            $attributes = $match->attributes;

            if (isset($attributes['href']) && strpos($attributes['href'], 'option=com_fileman') !== false)
            {
                $match->url = $attributes['href'];

                $query = parse_url($match->url, PHP_URL_QUERY);
                parse_str(str_replace('&amp;', '&', $query), $query);

                $match->query = $query;
                $match->type  = 'file';

                if (isset($query['view']) && $query['view'] == 'file') {
                    $links[] = $match;
                }
            }
            elseif(isset($attributes['src']))
            {
                if (isset($attributes['class']) && $attributes['class'] == 'filelink')
                {
                    $match->type   = 'image';

                    if (isset($attributes['data-source']))
                    {
                        $source = $attributes['data-source'];

                        $manager = KObjectManager::getInstance();

                        $parts = $manager->getObject('com:files.model.state.parser.url')->parse($source);

                        // Handle legacy sources
                        if (($scheme = $parts->scheme) && strpos($scheme,'fileman-') === 0)
                        {
                            $path = $parts->path;

                            if ($container = $parts->container) {
                                $path = $container . $path;
                            }

                            $source = sprintf('file://%s/%s', $scheme, $path);
                        }

                        $match->source = $source;
                    }

                    $links[] = $match;
                }
            }
        }

        return $links;
    }
}