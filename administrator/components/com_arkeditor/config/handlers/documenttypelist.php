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

class ARKConfigHandlerDocumentTypeList
{
	function getOptions($key,$value,$default,$node,$params)
	{
		$options = '';
		$value = preg_replace('/"/','\"',$value);
  	   	$options .= "\"$key='".$value."'\"\"";   
		return $options;
	}
    
}