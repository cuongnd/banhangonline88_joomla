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
 *Ark inline content  Ajax Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  Ajax.inlineContent
 */
error_reporting(E_ERROR & ~E_NOTICE);

JLoader::registerPrefix('ARK', JPATH_PLUGINS . '/ajax/inlinecontent');
 
class PlgAjaxInlineContent extends JPlugin
{
	
	public function onAjaxInlineContent()
	{

		$app = JFactory::getApplication();

		// JInput object
		$input = $app->input;
		$id = $input->get('id');
		$mode = $input->get('mode');
		$context = $input->get('context');
		$itemtype = $input->get('itemtype');
		$type = $input->get('type');
		$data = $input->get( 'data', '', 'raw');
				
		$classname = 'ARKContexts'.ucfirst($context).ucfirst($itemtype);
		
		$instance = new $classname($id);
				
		if($mode == 'get')
			return $instance->get();
		elseif($mode == 'process')
		{
			$html = base64_decode($data);
			return $instance->triggerContentPlugins($html);
		}
		elseif($mode == 'version')
		{
			return $instance->version($id,$type);
		}			
		else
		{
			$data = $input->get('data',array(),'raw');
			$input->set('jform', array('version_note'=>''));
			return $instance->save($data,$type);
		}	
			
	}
}
