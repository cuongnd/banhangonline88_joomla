<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');
jimport("joomla.filesystem.folder");
jimport("joomla.filesystem.file");

class fsj_transmanControllerAuto extends JControllerLegacy
{
	function translate()
	{
		$tool = "yandex";
		
		$source = JRequest::getVar('source');
		$dest = JRequest::getVar('dest');
		
		$phrases = array();
		
		foreach ($_GET as $var => $value)
		{
			if (substr($var,0,5) == "line_")
			{
				$phrases[substr($var,5)] = urldecode(JRequest::getVar($var,'','','string', JREQUEST_ALLOWRAW));
			}
		}
		
		require_once(JPATH_SITE.DS."administrator".DS."components".DS."com_fsj_transman".DS."plugins".DS."autotranslate".DS.$tool . ".php");
			
		$class = "TM_Auto_" . $tool;
		
		$results = array();
		$t = new $class();
		$rs = $t->Translate($source, $dest, $phrases, $results);
		if ($rs === true)
		{
			//print_r($results);
			$ro = new stdClass();
			$ro->results = $results;
			$ro->status = 1;
						
			/*echo "{\"results\":{";
			
			$bits = array();
			foreach ($results as $key => $value)
			{
				$bits[] = "\"" . $key . "\":\"" . $value . "\"";
			}
			
			echo implode(",", $bits);
			
			echo "},\"status\":1}\n";*/
			
			echo $this->raw_json_encode($ro);
			
			//print_p($ro);
			// damn json_encode aint working with utf-8 characters, so we make our own!
			//echo json_encode($ro, JSON_UNESCAPED_UNICODE );
			exit;
		} else if (is_string($rs)) {
			$out = new stdClass();
			$out->error = $rs;
			$out->status = 0;
			echo json_encode($out);
			exit;	
		}
	}
	
	function raw_json_encode($input) 
	{
		$enc = json_encode($input);

		$enc = preg_replace_callback(
			'/\\\\u([0-9a-zA-Z]{4})/',
			function ($matches) {
				return mb_convert_encoding(pack('H*',$matches[1]),'UTF-8','UTF-16');
			},
			$enc
			);

		return $enc;
	}
}
