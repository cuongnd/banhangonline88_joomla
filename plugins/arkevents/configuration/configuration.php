<?php
/*------------------------------------------------------------------------
# Copyright (C) 2014-2016 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined('_JEXEC') or die;

/**
 *Ark inline content  System Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  System.inlineContent
 */
require_once(JPATH_ADMINISTRATOR.'/components/com_arkeditor/config/handler.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_arkeditor/helper.php');
 
class PlgArKEventsConfiguration extends JPlugin
{

	public $db;


	public function onInstanceCreated(&$registry)
	{
		
		$query = $this->db->getQuery(true);
		$query2 = $this->db->getQuery(true);
		
		$query->select('element AS name,params,0 AS iscore')
			->from('#__extensions')
			->where('folder = '. $this->db->quote('arkeditor'))
			->where('enabled = 1');
		$this->db->setQuery( $query );
		$extPlugins = $this->db->loadObjectList();	
		
		if (!is_array($extPlugins)) {
			ARKHelper::error( $this->db->getErrorMsg() );
		}
		
		
		$query2->select('name,params,iscore')
					->from('#__ark_editor_plugins')
					->where('iscore = 1')
					->where('published = 1');
		$this->db->setQuery( $query2 );
		$corePlugins = $this->db->loadObjectList();

		if (!is_array($corePlugins)) {
			ARKHelper::error( $this->db->getErrorMsg() );
		}
		
		$merged = array_merge($extPlugins,$corePlugins);
		
		$plugins = array_filter($merged, function($obj)
		{
			static $nameList = array();
			if(in_array($obj->name,$nameList)) {
				return false;
			}
			$nameList[] = $obj->name;
			return true;
		});
				
		if(empty($plugins))
			return;
		
		$script = "CKEDITOR.jckplugins = {";
		
		foreach($plugins as $plugin)
		{
			if(empty($plugin->params))
				 $plugin->params == '{}';
        

			if($plugin->iscore)
                $params = new ARKParameter(trim($plugin->params),JPATH_ADMINISTRATOR.'/components/com_arkeditor/editor/plugins/'.$plugin->name.'.xml');
            else
			    $params = new ARKParameter(trim($plugin->params),JPATH_PLUGINS.'/arkeditor/'.$plugin->name.'/'.$plugin->name.'.xml');
			$name = $plugin->name;

            
			$dialogName =  $params->get('dialogname','');
			$title = $params->get('dialogtitle','');
			$height = $params->get('height','');
			$width = $params->get('width','');
			$resizable = $params->get('resizable','');

            if($dialogName)
				$name = $dialogName; // overrwite plugin name with dialogname

			//lets get plugin Joomla configurable options


			if(trim((strtolower($title)) == 'default'))
			 	$title = '';		

			$options = '';
			$optionsXML = $params->getXML();

				
            $filter = isset($optionsXML['options']) ? 'options' : 'advanced';
            
            if (isset($optionsXML[$filter])) 
			{
				foreach ($optionsXML[$filter]->children() as $node)  
				{
					$key = $node->attributes('name');
					$default = $node->attributes('default');
					$value = $params->get($key,$default);

					$handler = ARKConfigHandler::getInstance($node->attributes('type'));
               		$options.= $handler->getOptions($key,$value,$default,$node,$params,$name);
				}
			}

			
			
			
			if($options)
			{
				$options = substr($options, 0, -1);
				$options = '[' . $options  . ']';
			}	
			else
				$options = 'false';

			$script .= "$name:{'title':'$title','height':'$height','width':'$width','resizable':'$resizable','options': $options},";

		}



		if($script != "CKEDITOR.jckplugins = {")
			$script = substr($script, 0, -1);
		$script .= "};" . chr(13);

        $actionscript = "

		CKEDITOR.tools.removeSlashes = function(val)
		{	
			 val = val.replace(/(\\\"|\\\')/g,'');
		     return val;
		}

		CKEDITOR.on( 'dialogDefinition', function( ev )
		{
			// Take the dialog name and its definition from the event
			// data.
			var dialogName = ev.data.name;
			var dialogDefinition = ev.data.definition;

			if(CKEDITOR.jckplugins[dialogName ])
			{
				var jckplugin = CKEDITOR.jckplugins[dialogName ];

				if(jckplugin.title) dialogDefinition.title = jckplugin.title;
				if(jckplugin.height) dialogDefinition.minHeight = jckplugin.height;
				if(jckplugin.width) dialogDefinition.minWidth = jckplugin.width;
				if(jckplugin.resizable) dialogDefinition.resizable = jckplugin.resizable;

				if(jckplugin.options)
				{
					for(var k = 0; k < jckplugin.options.length;k++)
					{
						new Function('instance','instance.config.' + CKEDITOR.tools.removeSlashes(jckplugin.options[k]))(CKEDITOR);
					}
				}
			}
		});

		for(var m in CKEDITOR.jckplugins)
		{  
			var jckplugin = CKEDITOR.jckplugins[m];
			
			if(jckplugin.options)
			{
                for(var n = 0; n < jckplugin.options.length;n++)
				{
					new Function('instance','instance.config.' + CKEDITOR.tools.removeSlashes(jckplugin.options[n]))(editor);
				}
			}
		}
		";

		return $script.$actionscript;
		
		return  null;	
	}
	
	public function onInstanceLoaded(&$registry) 
	{
		return "for(var m in CKEDITOR.jckplugins)
		{  
			var jckplugin = CKEDITOR.jckplugins[m];

			if(jckplugin.options)
			{
				for(var n = 0; n < jckplugin.options.length;n++)
				{
                    new Function('instance','instance.config.' + CKEDITOR.tools.removeSlashes(jckplugin.options[n]))(editor);
				}
			}
		}";
	}
}


class ARKParameter extends JRegistry
{
	protected $_elementPath 	= array();
	protected $_raw 			= false;
	protected $_xml 			= false;

	public function __construct($data = '', $path = '')
	{
		parent::__construct('_default');

		// Set base path.
		$this->_elementPath[] = dirname(__FILE__) . '/parameter/element';

		if (!empty($data) && is_string($data))
		{
			$this->loadString($data);
		}

		if ($path)
		{
			$this->loadSetupFile($path);
		}

		$this->_raw = $data;
	}

	public function getXML()
	{
		return $this->_xml;
	} 

	public function setXML(&$xml)
	{
		if (is_object($xml))
		{
			if ($group = ($xml->attributes('group') ? $xml->attributes('group') : $xml->attributes('name')))
			{
				$this->_xml[$group] = $xml;
			}
			else
			{
				$this->_xml['_default'] = $xml;
			}

			if ($dir = $xml->attributes('addpath'))
			{
				$this->addElementPath(JPATH_ROOT . str_replace('/', DS, $dir));
			}
		}
	}	

	public function loadSetupFile($path)
	{
		$result = false;

	    if ($path)
		{
			$xml = ARKHelper::getXMLParser('Simple');

			if ($xml->loadFile($path))
			{

                if ($params = (isset($xml->document->config) ?  $xml->document->config[0]->fields[0]->fieldset : (isset($xml->document->params) ? $xml->document->params :'')))
				{
                    foreach ($params as $param)
					{
						$this->setXML($param);
						$result = true;
					}
				}
			}
		}
		else
		{
			$result = true;
		}

		return $result;
	}
    
    public function get($path, $default = null)
	{
	    return (isset($this->data->$path) && $this->data->$path !== null && $this->data->$path !== '') ? $this->data->$path : $default;
    }
}