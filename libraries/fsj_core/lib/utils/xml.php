<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

/** 
 * XML Handling class
 **/

if (!class_exists("FSJ_XML"))
{
	class FSJ_XML
	{
		function GetXMLAttribute($xml,$attrname)
		{
			$value = FSJ_XML::findAttribute($xml,$attrname);
			if (!is_null($value))
				return (string)$value;
			
			return null;
		}

		function GetXMLAttributeX($xml,$attrname,&$dest)
		{
			$value = FSJ_XML::findAttribute($xml,$attrname);
			if (!is_null($value))
				$dest = (string)$value;
		}

		function findAttribute($object, $attribute) {
			if (!$object) return null;
			$return = null;
			foreach($object->attributes() as $a => $b) 
			{
				if ($a == $attribute) {
					$return = $b;
				}
			}
			if($return) {
				return $return;
			}
			
			return null;
		} 

		function GetXMLField($xml,$field,&$dest)
		{
			//echo "Getting xml field $field<br>";
			foreach ($xml->children() as $child)
			{
				//echo "Found field " . $child->getName() . "<br>";
				if ($child->getName() != $field) continue;
				$dest = (string)$child;
				return;	
			}	
		}
		
		function XMLtoObject($xml)
		{
			$result = new stdClass();
			foreach($xml->attributes() as $attr => $value)
			{
				$result->$attr = (string)$value;	
			}
			$data = trim((string)$xml);
			if (strlen($data) > 0)
				$result->data = $data;
			return $result;
		}
		
		function ChildrenToArray($xml)
		{
			
			$result = array();
			foreach ($xml->children() as $child)
			{
				$row = array();
				foreach ($child->attributes() as $id => $value)
				{
					$row[$id] = (string)$value;	
				}
				$result[] = $row;
			}	
			
			return $result;
		}
		
		
		static function XMLToClass($xml, $class = null)
		{
			if ($class)
			{
				$obj = new $class();	
			} else {
				$obj = new stdClass();
			}
			
			if (!$xml)
				return (string)$xml;
			
			if (count($xml->attributes()) == 0
				&& count($xml->children()) == 0)
			{
				return (string)$xml;	
			}
			
			foreach ($xml->attributes() as $id => $value)
			{
				$value = trim((string)$value);
				if ($value == "") continue;
				
				$obj->$id = (string)$value;	
			}
			
			foreach ($xml->children() as $child)
			{
				$name = $child->getName();
				if (property_exists($obj, $name))
				{
					if (!is_array($obj->$name))
					{
						$t = $obj->$name;
						$obj->$name = array();
						array_push($obj->$name,$t);	
					}
					array_push($obj->$name,FSJ_XML::XMLToClass($child));
				} else {
					$obj->$name = FSJ_XML::XMLToClass($child);
				}
			}
			
			return $obj;
		}

		static function ArrayToXML($object)
		{
			$output = array();

			foreach ($object as $param => $value)
			{
				if (strpos($value, "<") !== false || strpos($value, ">") !== false)
				{
					$output[] = "<$param><![CDATA[" . $value . "]]></$param>";
				} else {
					$output[] = "<$param>" . htmlspecialchars($value) . "</$param>";
				}
			}

			return implode("\n", $output);
		}
	}
}