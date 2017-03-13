<?php

/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

class JFBConnectAutoloader
{
    public static function register($prepend = false)
    {
        if (version_compare(phpversion(), '5.3.0', '>=')) {
            spl_autoload_register(array(__CLASS__, 'autoload'), true, $prepend);
        } else {
            spl_autoload_register(array(__CLASS__, 'autoload'));
        }
    }

    /**
     * Handles autoloading of classes.
     *
     * @param string $class A class name.
     */
    public static function autoload($class)
    {
        if (0 !== strpos($class, 'JFBConnect')) {
            return;
        }

        $class = str_replace("JFBConnect" , "", $class);
        $parts = preg_split('/(?=[A-Z])/',$class);
        unset($parts[0]);

        // Let Joomla handle loading these
        if ($parts[1] == "Model")
            return;

        if (isset($parts[2]) && $parts[2] == "Widget")
            unset($parts[1]); // Special case where widget isn't in the /provider/ folder

        $file = JPATH_SITE . '/components/com_jfbconnect/libraries';
        foreach ($parts as $p)
        {
            $path = str_replace(array('/', '\\', '.', "\0"), array('', '', '', ''), $p); // basic sanitization
            $file .= '/' . strtolower($path);
        }
        $file .= '.php';
        if (is_file($file)) {
            require $file;
        }
    }
}