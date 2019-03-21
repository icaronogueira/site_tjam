<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Activity Router Route Class.
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Joomlatools\Component\LOGman
 */
class ComLogmanActivityRouterRoute extends KDispatcherRouterRoute
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array('application' => JFactory::getApplication()->getName()));

        parent::_initialize($config);
    }

    public function toString($parts = self::FULL, $escape = null)
    {
        $query  = $this->getQuery(true);
        $escape = isset($escape) ? $escape : $this->_escape;

        //Add the option to the query for compatibility with the Joomla router
        if(isset($query['component']))
        {
            if(!isset($query['option'])) {
                $query['option'] = 'com_'.$query['component'];
            }

            unset($query['component']);
        }

        //Push option and view to the beginning of the array for easy to read URLs
        $query = array_merge(array('option' => null, 'view'   => null), $query);

        $route = $this->_getRoute($query);

        if ($escape) {
            $route = htmlspecialchars($route);
        }

        // We had to change the format in the URL above so that .htaccess file can catch it
        if (isset($append_format)) {
            $route .= (strpos($route, '?') !== false ? '&' : '?').'format='.$append_format;
        }

        //Create a fully qualified route
        if(!empty($this->host) && !empty($this->scheme)) {
            $route = parent::toString(self::AUTHORITY) . '/' . ltrim($route, '/');
        }

        return $route;
    }

    /**
     * Route getter.
     *
     * @param array $query An array containing query variables.
     *
     * @return string The route.
     */
    protected function _getRoute($query)
    {
        $current = JFactory::getApplication();

        $application = JApplicationCms::getInstance($this->getConfig()->application);

        // Force route application during route build.
        JFactory::$application = $application;

        // Get the router.
        $router = $application->getRouter();

        $url = 'index.php?'.http_build_query($query, '', '&');

        // Build route.
        $route = $router->build($url);

        // Revert application change.
        JFactory::$application = $current;

        $route = $route->toString(array('path', 'query', 'fragment'));

        // Check if we need to remove "administrator" from the path
        if ($current->isAdmin() && $application->getName() == 'site')
        {
            $base        = JUri::base('true');

            $replacement = explode('/', $base);
            array_pop($replacement);
            $replacement = implode('/', $replacement);

            $base        = str_replace('/', '\/', $base);

            $route       = preg_replace('/^' . $base . '/', $replacement, $route);
        }

        // Replace spaces.
        $route = preg_replace('/\s/u', '%20', $route);

        return $route;
    }
}