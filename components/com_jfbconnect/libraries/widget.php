<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('sourcecoast.utilities');

abstract class JFBConnectWidget extends JObject
{
    var $fields;
    var $provider;
    var $name;
    var $systemName;
    var $className;
    var $tagName;
    var $examples;

    function __construct($provider, $fields)
    {
        $this->provider = $provider;

        $this->fields = new JRegistry();

        if(is_object($fields) || is_array($fields))
        {
            $this->fields->loadObject($fields);
        }
    }

    public function getSystemName()
    {
        return $this->systemName;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getField($fieldName, $deprecatedName, $type, $defaultValue, $htmlName)
    {
        $value = $this->getParamValueEx($fieldName, $deprecatedName, $type, $defaultValue);

        if($value)
            return ' ' . $htmlName . '="' . $value . '"';
        else
            return '';
    }

    public function getParamValue($fieldName)
    {
        return $this->getParamValueEx($fieldName, null, null, '');
    }

    public function getParamValueEx($fieldName, $deprecatedName, $type, $defaultValue)
    {
        $value = $defaultValue;

        if($this->fields->exists($fieldName))
            $value = $this->fields->get($fieldName);
        else if($this->fields->exists($deprecatedName))
            $value = $this->fields->get($deprecatedName);

        if($value == '')
            $value = $defaultValue;

        if($type == 'boolean')
        {
            $value = strtolower($value);
            if($value == 'false' || $value == '0')
                $value = 'false';
            else if($value == 'true' || $value == '1')
                $value = 'true';
            else
                $value = $defaultValue;
        }

        return $value;
    }

    public function render()
    {
        $tag = $this->getTagHtml();
        $class[] = "sourcecoast";
        $class[] = $this->systemName;

        if($this->provider)
        {
            $class[] = $this->provider->systemName;
            $this->provider->needsJavascript = true;

            if($tag)
                $this->provider->widgetRendered = true;
        }

        if($tag)
        {
            if($this->className)
                $class[] = $this->className;

            $classString = implode(' ', $class);
            $tag = '<div class="'.$classString.'">' . $tag . '</div>';
        }

        return $tag;
    }

    public function getHeadData() {}
    protected function getTagHtml() {}
}