<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
if (!defined("DS")) define('DS', DIRECTORY_SEPARATOR);

/**
 * General helper class
 **/

jimport('joomla.utilities.date');
jimport('fsj_core.lib.layout.page');
jimport('fsj_core.lib.utils.settings');
jimport('fsj_core.lib.utils.lang');

// joomla 3 stuff

if (!class_exists("JControllerLegacy"))
{
	jimport( 'joomla.application.component.view');
	jimport( 'joomla.application.component.model');
	jimport( 'joomla.application.component.controller');
	class JControllerLegacy extends JController {}
	class JModelLegacy extends JModel {}
	class JViewLegacy extends JView {}
}

if (!class_exists("FSJ_Helper"))
{
	class FSJ_Helper
	{
		static function IsJ3()
		{
			return version_compare(JVERSION,'3.0.0','>=');
		}
	
		function isFree($com)
		{
			return true;	
		}
	
		function Powered($com, $name)
		{
			$isfree = FSJ_Helper::isFree($com);
			$pro_text = "Pro";
			if ($isfree)
				$pro_text = "Lite";
		
	?>
		<div style="clear:both;"></div>
		<?php if ($isfree) : ?>
			<div align="center" class='fsj_powered' style="">
				<a href="http://www.freestyle-joomla.com/" target="_blank">
					Powered by Freestyle <?php echo $name; ?> <?php echo $pro_text; ?><br>
					<img style="padding-top:2px;" border="0" src="<?php echo JURI::root( false ); ?>/libraries/fsj_core/assets/images/logo_small.png"><br>
				</a>
			</div>
		<?php endif; ?>
	<?php
		}
	
		static function ParseDataFields($text, &$data, $escape = false)
		{
			if (preg_match_all("/%([a-zA-Z_\.]+)%/", $text, $matches))
			{
				foreach ($matches[0] as $offset => $search)
				{
					$field = strtolower($matches[1][$offset]);
					if (is_object($data) && get_class($data) == "JForm")
					{
						if (strpos($field,".") !== FALSE)
						{
							list($group, $field) = explode(".", $field, 2);	
							$replace = $data->getValue($field, $group);
						} else {
							$replace = $data->getValue($field);
						}
						if ($escape)
						{
							$db = JFactory::getDBO();
							$replace = $db->escape($replace);
						}
						$text = str_replace($search, $replace, $text);	
					} else if (is_object($data) && property_exists($data, $field))
					{
						$replace = $data->$field;
						if ($escape)
						{
							$db = JFactory::getDBO();
							$replace = $db->escape($replace);
						}
						$text = str_replace($search, $replace, $text);	
					} else if (is_array($data) && array_key_exists($field, $data))
					{
						$replace = $data[$field];
						if ($escape)
						{
							$db = JFactory::getDBO();
							$replace = $db->escape($replace);
						}
						$text = str_replace($search, $replace, $text);	
					} else {
						echo "Property $field missing<br>";	
					}
				}
			}		
			return $text;
		}
	
		static function ArrayObjSort(&$array, $field, $dir)
		{
			usort($array, array(new FSJ_Array_Obj_Sorter($field, $dir), "compare"));
		}
	
		static function ArrayObjSortMulti(&$array, $sort)
		{
			usort($array, array(new FSJ_Array_Obj_Sorter_Multi($sort), "compare"));
		}
		
		static function base64url_decode($data)
		{
			//return base64_decode(urldecode($data));
			return base64_decode(str_pad(strtr($data, '-_.', '+/='), strlen($data) % 4, '=', STR_PAD_RIGHT));
		}
	
		static function base64url_encode($data) {
			//return urlencode(base64_encode($data));
			return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
		}
	
		static function MakeKey($key)
		{
			$text = preg_replace("/[^A-Za-z0-9\ _]/", "", $key);	
			$text = str_replace(" ","_",$text);
			$text = strtolower($text);
			return $text;
		}

		static function MakeKeyExt($key)
		{
			$text = preg_replace("/[^A-Za-z0-9\ \._\-]/", "", $key);	
			$text = str_replace(" ","_",$text);
			$text = strtolower($text);
			return $text;
		}
		
		static function SplitINIParams(&$params)
		{
			$result = array();
			$bits = explode("\n",$params);
		
			foreach($bits as $bit)
			{
				if (strpos($bit,"=") < 2) continue;
				list($param, $value) = explode("=",$bit,2);
				$param = trim($param);
				if ($param == "") continue;
			
				$result[$param] = $value;
			}	
			return $result;
		}
	
		static function convertPHPSizeToBytes($sSize)  
		{  
			if ( is_numeric( $sSize) ) {
			   return $sSize;
			}
			$sSuffix = substr($sSize, -1);  
			$iValue = substr($sSize, 0, -1);  
			switch(strtoupper($sSuffix)){  
			case 'P':  
				$iValue *= 1024;  
			case 'T':  
				$iValue *= 1024;  
			case 'G':  
				$iValue *= 1024;  
			case 'M':  
				$iValue *= 1024;  
			case 'K':  
				$iValue *= 1024;  
				break;  
			}  
			return $iValue;  
		}  

		static function getMaximumFileUploadSize()  
		{  
			return min(self::convertPHPSizeToBytes(ini_get('post_max_size')), self::convertPHPSizeToBytes(ini_get('upload_max_filesize')));  
		}  

		static function escapeJavaScriptText($string)
		{
			return str_replace("\n", '\n', str_replace('"', '\"', addcslashes(str_replace("\r", '', (string)$string), "\0..\37'\\")));
		}
	
		static function escapeJavaScriptTextForAlert($string)
		{
			if (function_exists("mb_convert_encoding"))
				return mb_convert_encoding(self::escapeJavaScriptText($string), 'UTF-8', 'HTML-ENTITIES');
		
			return self::escapeJavaScriptText($string);
		}
	
		static function noBots()
		{
			// if the current connection is a bot of any kind, redirect back to homepage
			jimport('joomla.environment.browser');
			$doc = JFactory::getDocument();
			$browser = JBrowser::getInstance();
		
			if ($browser->isRobot())
			{
				echo "Bots are not allowed here.";
				exit;	
			}
		
			// add no crawl info to this page
			$doc->addCustomTag( "<meta name=\"robots\" content=\"noindex\" />" );
		}	
	
		static function noCache()
		{
			// we want to disable caching of the page here!
			$cache = JFactory::getCache();
			$cache->setCaching( 0 );
			JResponse::setHeader('Pragma','no-cache');
		}
	}
}

if (!class_exists("FSJ_Array_Obj_Sorter_Multi"))
{
	class FSJ_Array_Obj_Sorter_Multi
	{
		private $sort;

		function __construct( $sort ) {
			$this->sort = $sort;
		
			foreach ($this->sort as &$item)
			{
				if (substr(strtolower($item->dir),0,1) == "d")
				{
					$item->dir = 1;
				} else {
					$item->dir = 0;
				}
			}
		}

		function compare( $a, $b ) {
	 
			foreach ($this->sort as $sort)
			{
				$field = $sort->field;
				$dir = $sort->dir;
			
				// actual compare
				if (!property_exists($a, $field))
					return -1;
		
				if (!property_exists($b, $field))
					return 1;
		
				if ($a->$field == $b->$field)
						continue;

				if (is_numeric($a->$field))
				{		
					if ($dir) // desc
						return $a->$field < $b->$field;
			
					return $a->$field > $b->$field;
				} else {
				
					if ($dir) // desc
						return - strcmp($a->$field, $b->$field);	
		
					return strcmp($a->$field, $b->$field);
				}
			}
		}
	}
}


if (!class_exists("FSJ_Array_Obj_Sorter"))
{
	class FSJ_Array_Obj_Sorter
	{
		private $field;
		private $dir;

		function __construct( $field, $dir ) {
			$this->field = $field;
			$this->dir = 0;
			if (substr(strtolower($dir),0,1) == "d")
				$this->dir = 1;
		}

		function compare( $a, $b ) {
	 
			// actual compare
			if (!property_exists($a, $this->field))
				return -1;
		
			if (!property_exists($b, $this->field))
				return 1;
		
			$field = $this->field;
	
			if (is_numeric($a->$field))
			{
				if ($this->dir) // desc
					return $a->$field < $b->$field;
			
				return $a->$field > $b->$field;
			}
		
			if ($this->dir) // desc
				return - strcmp($a->$field, $b->$field);	
		
			return strcmp($a->$field, $b->$field);
		}
	}
}

// TEMPORARY
if (!function_exists("print_p"))
{
	function print_p(&$data)
	{
		echo "<pre>";
		print_r($data);
		echo "</pre>";
	}
}


// Joomla 2.5, hide strict standards!
if (!FSJ_Helper::IsJ3())
{
	$current_error_reporting = error_reporting();
	if ($current_error_reporting & E_STRICT) 
	{
		error_reporting($current_error_reporting ^ E_STRICT);
	}
}	
