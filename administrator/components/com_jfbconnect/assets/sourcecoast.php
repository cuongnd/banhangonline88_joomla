<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.html.parameter.element');

class sourceCoastConnect
{

    var $xmlString;
    var $imagePath;

    function __construct($checkName = null, $imagePathDir = null)
    {
        if ($checkName && $imagePathDir)
        {
            $this->xmlString = $this->_getRemoteXML2($checkName);
            $this->imagePath = $imagePathDir;
        }
    }

    function display($extensionName, $installedVersion = null)
    {
        $this->_getExtensionData('joomla', $extensionName);
        if ($installedVersion != null)
            $this->installedVersion = $installedVersion;
        else
            $this->installedVersion = $this->_getJoomlaInstalledVersion($extensionName);

        include_once(dirname(__FILE__) . '/template.php');
    }

    private function _getRemoteXML2($checkName)
    {
        if (!function_exists('curl_init') && !is_callable('curl_init'))
        {
            return "";
        }

        $site = 'www.sourcecoast.com';
        $xml = '/versions/' . $checkName . ".xml";

        $ch = curl_init($site . $xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        $contents = curl_exec($ch);
        curl_close($ch);

        return $contents;
    }

    private function _getRemoteXML($checkName)
    {
        // Get the xml file
        $site = 'www.sourcecoast.com';
        $xml = '/versions/' . $checkName . ".xml";
        $contents = '';

        $handle = fsockopen($site, 80, $errno, $errstr, 30);

        if ($handle)
        {
            $out = "GET /$xml HTTP/1.0\r\n";
            $out .= "Host: $site\r\n";
            $out .= "Connection: Close\r\n\r\n";

            fwrite($handle, $out);

            $body = false;

            while (!feof($handle))
            {
                $return = fgets($handle, 1024);
                if ($body)
                    $contents .= $return;

                if ($return == "\r\n")
                    $body = true;
            }
            fclose($handle);
        }
        return $contents;
    }

    function getExtensionContainer($extensionType)
    {
        if ($extensionType == 'library')
            return 'libraries';
        else
            return $extensionType . "s";
    }

    /**
     * Returns current version number, support link, and reviews for the passed in extension name. Input 1=cms name, input 2=extension name.
     * @return string (or an xmlrpcresp obj instance if call fails)
     */
    function getExtensionData($extensionName, $extensionType)
    {
        if ($this->xmlString)
        {
            $xml = new SimpleXMLElement($this->xmlString);
            $data = new stdClass();
            $extensionContainer = $this->getExtensionContainer($extensionType);
            $element = $xml->xpath('/sourcecoast/' . $extensionContainer . "/" . $extensionType . '[@system="' . $extensionName . '"]');
            if (count($element) > 0)
            {
                $data->currentVersion = $element[0]->version;
                $data->localVersion = $this->getInstalledVersion($extensionName, $extensionType);
                $data->name = $element[0]->name;
                return $data;
            }
        }

        $data->currentVersion = 'unknown';
        $data->localVersion = $this->getInstalledVersion($extensionName, $extensionType);
        $data->name = $extensionName;

        /* $dependencies = $element[0]->dependencies;

          if (count($dependencies) > 0)
          {
          foreach ($dependencies->dependency as $dependency)
          {
          $dep = new stdClass();
          $dep->name = $dependency->name;
          $dep->version = $dependency->version;
          $data->dependencies[] = $dep;
          }
          } */

        return $data;
    }

    function getInstalledVersion($extensionName, $extensionType)
    {
        $version = "Not Installed";

        $extensionType = $this->getExtensionContainer($extensionType);
        $xmlDir = "";
        $xmlFile = "";
        if ($extensionType == "components" || $extensionType == "modules")
        {
            if (JFolder::exists(JPATH_ADMINISTRATOR . '/' . $extensionType . '/' . $extensionName))
            {
                $xmlDir = JPATH_ADMINISTRATOR . '/' . $extensionType . '/' . $extensionName;
                $xmlFile = str_replace("com_", "", $extensionName) . ".xml";
            }
            else if (JFolder::exists(JPATH_SITE . '/' . $extensionType . '/' . $extensionName))
            {
                $xmlDir = JPATH_SITE . '/' . $extensionType . '/' . $extensionName;
                $xmlFile = $extensionName . ".xml";
            }
        }
        else if ($extensionType == "plugins")
        {
            $pluginData = explode(".", $extensionName);

            if (JFile::exists(JPATH_SITE . '/plugins/' . $pluginData[0] . '/' . $pluginData[1] . '/' . $pluginData[1] . '.xml'))
                $xmlDir = JPATH_SITE . '/plugins/' . $pluginData[0] . '/' . $pluginData[1];

            $xmlFile = $pluginData[1] . ".xml";
        }
        else if ($extensionType == "libraries")
        {
            if (JFolder::exists(JPATH_SITE . '/libraries/' . $extensionName))
            {
                $xmlDir = JPATH_SITE . '/libraries/' . $extensionName;
                $xmlFile = $extensionName . '.xml';
            }
        }

        if ($xmlFile == "" || $xmlDir == "")
            return $version;

        if (JFile::exists($xmlDir . '/' . $xmlFile))
        {
            $xmlElement = simplexml_load_file($xmlDir . '/' . $xmlFile);
            if ($xmlElement && isset($xmlElement->version))
            {
                $version = (string)$xmlElement->version;
            }
        }

        return $version;
    }

    function _showVersionInfoRow($extName, $extType)
    {
        $extData = $this->getExtensionData($extName, $extType);
        echo "<tr>";
        echo "<td>" . $extData->name . "</td>";
        if ($extData->localVersion == "Not Installed")
        {
            echo '<td><span style="color:#999999">' . $extData->localVersion . "</span></td>";
            echo '<td>' . $extData->currentVersion . "</span></td>";
        }
        else if ($extData->localVersion != $extData->currentVersion)
        {
            echo '<td><span style="color:#FF0000"><b>' . $extData->localVersion . "</b></span></td>";
            echo '<td>' . $extData->currentVersion . "</span></td>";
        }
        else
        {
            echo '<td><span style="color:#009900">' . $extData->localVersion . "</span></td>";
            echo "<td>" . $extData->currentVersion . "</span></td>";
        }
        if ($extType == "component")
            echo "<td>" . SourceCoastExtensionHelper::checkComponent($extName, $this->imagePath) . "</td>";
        else if ($extType == "library")
            echo "<td>" . SourceCoastExtensionHelper::checkLibrary($extName, $this->imagePath) . "</td>";
        else if ($extType == "module")
            echo "<td>" . SourceCoastExtensionHelper::checkModule($extName, $this->imagePath) . "</td>";
        else if ($extType == "plugin")
            echo "<td>" . SourceCoastExtensionHelper::checkPlugin($extName, $this->imagePath) . "</td>";


        echo "</tr>";
    }

}

class SourceCoastExtensionHelper
{

    static function checkExtension($path, $query, $imagePath)
    {
        if (!is_dir($path) && !is_file($path))
        {
            $alt = "This extension does not appear to be installed.";
            return '<img title="' . $alt . '" alt="' . $alt . '" src="' . $imagePath . 'icon-16-deny.png" width="10" height="10" />';
        }
        else
        {
            $dbo = JFactory::getDBO();
            $dbo->setQuery($query);
            $instance = $dbo->loadObject();

            if ($instance == null)
            {
                $alt = "This extension is installed but not published.";
                return '<img title="' . $alt . '" alt="' . $alt . '" src="' . $imagePath . 'icon-16-notice-note.png" width="10" height="10" />';
            }
            else
            {
                $alt = "This extension is installed and published.";
                return '<img title="' . $alt . '" alt="' . $alt . '" src="' . $imagePath . 'icon-16-allow.png" width="10" height="10" />';
            }
        }
    }

    static function checkComponent($name, $imagePath)
    {
        //Don't need to perform check since this is called from component code
        $alt = "This extension is installed and published.";
        return '<img title="' . $alt . '" alt="' . $alt . '" src="' . $imagePath . 'icon-16-allow.png" width="10" height="10" />';
    }

    static function checkLibrary($name, $imagePath)
    {
        $dbo = JFactory::getDBO();

        return SourceCoastExtensionHelper::checkExtension(
            JPATH_ROOT . "/libraries/" . $name,
                "SELECT extension_id " .
                "FROM #__extensions " .
                "WHERE element = " . $dbo->quote($name) .
                "   AND type = 'library'" .
                "	AND enabled = 1",
            $imagePath
        );
    }

    static function checkModule($name, $imagePath)
    {
        $dbo = JFactory::getDBO();

        return SourceCoastExtensionHelper::checkExtension(
            JPATH_ROOT . "/modules/" . $name,
                "SELECT id " .
                "FROM #__modules " .
                "WHERE module = " . $dbo->quote($name) . " " .
                "	AND published = 1",
            $imagePath
        );
    }

    static function checkPlugin($name, $imagePath)
    {
        $pluginParts = explode(".", $name);
        $dbo = JFactory::getDBO();

        return SourceCoastExtensionHelper::checkExtension(
            JPATH_ROOT . "/plugins/" . $pluginParts[0] . '/' . $pluginParts[1] . '/' . $pluginParts[1] . ".php",
                "SELECT extension_id " .
                "FROM #__extensions " .
                "WHERE folder = " . $dbo->quote($pluginParts[0]) . " " .
                "AND element = " . $dbo->quote($pluginParts[1]) . " " .
                "AND enabled = 1 AND access = 1",
            $imagePath
        );
    }
}
