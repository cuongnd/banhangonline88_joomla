<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class FSJ_TM_Helper
{
	static function GetDefaultLanguage($admin = false)
	{
		$client = "site";
		if ($admin)
		$client = "administrator";	
		
		$params = JComponentHelper::getParams('com_languages');
		
		return $params->get($client, 'en-GB');
	}	
	
	static function GetBaseLanguage()
	{
		$base = FSJ_Settings::get('tm_base','baselang');
		if (!$base) $base = 'en-GB';
		return $base;	
	}
	
	static function ParseLine($text)
	{
		$res = new stdClass();
		if (strpos($text, "=") === false)
		{
			return null;	
		}
		list($res->key, $remainder) = explode("=", $text, 2);
		$res->key = trim(strtoupper($res->key));
		
		$res->value = $remainder;
		$res->value =	trim($res->value, " ");
		if (substr($res->value, 0, 1) == "\"")
		$res->value = substr($res->value, 1);
		if (substr($res->value, strlen($res->value)-1,1) == "\"")
		$res->value = substr($res->value, 0, strlen($res->value)-1);
		
		$res->value = str_replace("&quot;", "\"", $res->value);
		$res->value = str_replace("\"_QQ_\"", "\"", $res->value);
		$res->value = str_replace("\"_QUOT_\"", "\"", $res->value);
		
		return $res;		
	}
	
	static function isComment($line)
	{
		if (substr($line, 0, 1) == ";" || substr($line, 0, 1) == "#" || substr($line, 0, 1) == "[")
		return true;
		
		return false;	
	}
	
	static function SanitizeFilename($filename)
	{
		return preg_replace('/[^A-Za-z0-9-._|]/', '', $filename);
	}
	
	static function ParseComment($line)
	{
		$line = trim($line);
		$line = trim($line, ";#[]");
		$line = trim($line);
		return $line;
	}
	
	static function ParseFileName($filename)
	{
		list($client, $tag, $component, $file) = explode("|", $filename);
		
		return array($client, $file, $tag, $component);
	}
	
	static function getPath($client_id, $tag, $component)
	{
		$prefix = substr($component, 0, 1);
		$component = substr($component, 2);
		
		if ($client_id)
		{
			$path = JPATH_ADMINISTRATOR;
		} else {
			$path = JPATH_SITE;
		}

		if ($prefix == "g") // General
		{
			// general folder! do nothing			
		} elseif ($prefix == "c") // component
		{
			$path .= DS . "components" . DS . $component;
		} elseif ($prefix == "m")
		{
			$path .= DS . "modules" . DS . $component;
		} elseif ($prefix == "t")
		{
			$path .= DS . "templates" . DS . $component;
		} elseif ($prefix == "p")
		{
			$path .= DS . "plugins" . DS . str_replace(".", DS, $component);
		}
		
		$path .= DS . "language" . DS . $tag . DS;
		
		return $path;		
	}
	
	static function getBranding()
	{
		return array(	
			'File saved using Freestyle Translation Manager',
			'http://freestyle-joomla.com/products/utilities/transman'
			);
	}
	
	static function getAdditionalHeader()
	{
		return array(	
			'Freestyle Translation Manager Additional Entries'
			);
	}
	static function getIgnoreLines()
	{
		return array_merge (
			self::getBranding(),
			self::getAdditionalHeader()
			);
	}
	
	static function makePackageFilename($item_data, $pubfolder, $filename)
	{
		
		// need to calculate the file target
		$parser = new FSJParser();
		
		foreach($item_data as $var => $value)
		{
			$parser->SetVar($var, $value);
			
			$sanit = str_replace(" ", "_", $value);
			$sanit = strtolower($sanit);
			$sanit = preg_replace('/[^a-zA-Z0-9-_\.]/','', $sanit);
			$parser->SetVar($var."_c", $sanit);
		}
		
		$path = $pubfolder;
		
		$path = str_replace("/", DS, $path);
		$path = str_replace("\\", DS, $path);
		$path = trim($path, DS);
		if (substr($path, -1) != DS) $path .= DS;
		$path = JPATH_ROOT.DS.$path;

		$filename = $filename;
		if (!$filename) $filename = "{title_c}-{langcode}-{ver}";
		
		
		$target = $path . $filename;
		$target = $parser->Parse($target);

		if (strtolower(substr($target, -4)) != ".zip") $target .= ".zip";
		
		return $target;		
	}
} // 