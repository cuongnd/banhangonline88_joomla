<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die;
defined('JPATH_PLATFORM') or die;

class ARKConfigHandlerSmileyPath 
{
	function getOptions($key,$value,$default,$node,$params)
	{
		$options = '';
				  
		if($value)
			$value = str_replace('/administrator','',JURI::base(true)).'/'.$value;		  
				  
	   	$options .= "\"$key='".$value."'\",";   
		
		return $options;
	}
}