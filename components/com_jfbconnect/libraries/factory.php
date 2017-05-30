<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */
defined('_JEXEC') or die('Restricted access');

require_once JPATH_SITE . '/components/com_jfbconnect/autoloader.php';
JFBConnectAutoloader::register();

JLoader::register('JFBCOAuth1Client', JPATH_SITE . '/components/com_jfbconnect/libraries/joomla/oauth1/client.php');
JLoader::register('JFBConnectAuthenticationOauth2', JPATH_SITE . '/components/com_jfbconnect/libraries/authentication/oauth2.php');

include_once(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/models/usermap.php');

jimport('joomla.filesystem.folder');

class JFBCFactory
{
    public static function provider($name)
    {
        $name = strtolower($name);

        static $providers = array();
        if (!isset($providers[$name]))
        {
            $className = 'JFBConnectProvider' . ucfirst($name);

            if (class_exists($className))
            {
                // Do not clean this up. It's ugly, but needs to be to prevent recursive loading
                $providers[$name] = new $className();
                $providers[$name]->setupAuthentication();
            }
        }
        if (isset($providers[$name]))
            return $providers[$name];
        else
            return null;
    }


    public static function getAllProviders()
    {
        static $allProviders;
        if (!isset($allProviders))
        {
            $allProviders = array();
            $files = JFolder::files(JPATH_SITE . '/components/com_jfbconnect/libraries/provider/', '\.php$');
            foreach ($files as $file)
            {
                $p = self::provider(str_replace(".php", "", $file));
                if ($p)
                    $allProviders[] = $p;
            }
        }
        return $allProviders;
    }

    public static function library($path)
    {
        static $loaded;
        if (!isset($loaded[$path]))
        {
            $parts = explode('.', $path);
            $path = implode('/', $parts);
            $className = 'JFBConnect' . implode('', array_map('ucfirst', $parts));
            require_once(JPATH_SITE . '/components/com_jfbconnect/libraries/' . $path . '.php');
            $loaded[$path] = new $className();
        }
        return $loaded[$path];
    }

    public static function model($name)
    {
        JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_jfbconnect/models');
        $model = JModelLegacy::getInstance($name, 'JFBConnectModel');
        return $model;
    }

    public static function config()
    {
        static $configModel = null;
        if (!isset($configModel))
        {
            require_once(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/models/config.php');
            $configModel = new JFBConnectModelConfig();
        }
        return $configModel;
    }

    public static function cache()
    {
        static $cache = null;
        if (!isset($cache))
        {
            require_once(JPATH_SITE . '/components/com_jfbconnect/libraries/cache.php');
            $cache = new JFBConnectCache();
        }
        return $cache;
    }

    // Return an instance of the usermap model, always creating it
    public static function usermap()
    {
        $userMapModel = new JFBConnectModelUserMap();
        return $userMapModel;
    }

    public static function widget($providerName, $name, $params = "")
    {
        $className = 'JFBConnectProvider' . ucfirst($providerName) . 'Widget' . ucfirst($name);
        $provider = self::provider($providerName);
        if(class_exists($className))
            $widget = new $className($provider, $params);
        else
            $widget = null;
        return $widget;
    }

    public static function getAllWidgetProviderNames()
    {
        $providers = JFolder::folders(JPATH_SITE . '/components/com_jfbconnect/libraries/provider/');
        return $providers;
    }

    public static function getAllWidgets($provider)
    {
        static $allWidgets;

        if(!$provider || $provider == 'provider')
            return array();

        if (!isset($allWidgets))
            $allWidgets = array();

        if (!isset($allWidgets[$provider]))
        {
            $allWidgets[$provider] = array();

            $widgetFolder = JPATH_SITE . '/components/com_jfbconnect/libraries/provider/' . $provider . '/widget/';
            if(JFolder::exists($widgetFolder))
            {
                $widgetFiles = JFolder::files($widgetFolder, '\.xml$');
                if ($widgetFiles && count($widgetFiles) > 0)
                {
                    foreach ($widgetFiles as $file)
                    {
                        $allWidgets[$provider][] = self::widget($provider, str_replace(".xml", "", $file));
                    }
                }
            }

        }
        return $allWidgets[$provider];
    }

    public static function getLoginButtons($params = null)
    {
        // Any provider can actually be used here. All roads lead to the same login widget
        return JFBCFactory::widget('facebook', 'login', $params)->render();
    }

    public static function getReconnectButtons($params = null)
    {
        $params['show_reconnect'] = 'true';
        return JFBCFactory::widget('facebook', 'login', $params)->render();
    }

    /***
     * Adds a stylesheet to the list of inclusions that need to be added to the page
     * Managed this way as some are added before the page is rendered, and some after
     * @var path to filename relative to /components/com_jfbconnect/
     */
    static protected $css = array();
    public static function addStylesheet($name)
    {
        if (!in_array($name, self::$css))
            self::$css[] = $name;
    }

    public static function getStylesheets()
    {
        return self::$css;
    }

}