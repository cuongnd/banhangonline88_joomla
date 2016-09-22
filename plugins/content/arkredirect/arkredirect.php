<?php
/*------------------------------------------------------------------------
# Copyright (C) 2012-2015 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined('_JEXEC') or die;

/**
 * Ark Inline content editing Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  Content.ArkRedirect
 */
class PlgContentArkRedirect extends JPlugin
{
	public $app;
	
	function onContentPrepareData($context, $data)
    {
		if ($context != 'com_plugins.plugin') 
			return true;
     
		
		if($data->folder != 'arkeditor')
			return true;
		
		$pk = isset($data->custom_data) ? $data->custom_data : 0;
		if($pk)
			$this->app->redirect('index.php?option=com_arkeditor&task=list.edit&cid[]='.(int)$pk);	
	}
	
}