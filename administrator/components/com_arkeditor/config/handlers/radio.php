<?php
/*------------------------------------------------------------------------
# Copyright (C) 2014-2015 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die;
defined('JPATH_PLATFORM') or die;

class ARKConfigHandlerRadio 
{
	function getOptions($key,$value,$default,$node,$params,$pluginName)
	{
		if(!isset($value))
			$value = $default;
		
		$options = '';
		if($value === '1')
		{
			$value = 'true';
			$options .= "\"$key=$value\",";
		}
		elseif($value === '0')
		{
			$value = 'false';
			$options .= "\"$key=$value\",";
		}
		else
			$options .= "\"$key='".$value."'\",";
		
		return $options;
	}
}





          
          
    