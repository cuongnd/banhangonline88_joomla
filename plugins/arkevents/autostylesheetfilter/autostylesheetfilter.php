<?php
/*------------------------------------------------------------------------
# Copyright (C) 2014-2015 WebxSolution Ltd. All Rights Reserved.
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
 * @subpackage  ArkEvrnts.autostylesheetfilter
 */
class PlgArKEventsAutoStylesheetFilter extends JPlugin
{

	public $app;


	public function onInstanceCreated(&$params)
	{
		
		$filterlists = $params->get('exclude_stylesheets',array('{exclude_stylesheet:[]}'));
		$defaults = $params->get('default_exclude_stylesheet',
			array('modal',
				'bootstrap',
				'frontediting',
				's5_responsive_rtl.css',
				's5_vertex_addons.php',
				'thirdparty.css',
				's5_flex_menu.css',
				's5_responsive_bars.css',
				's5_responsive_hide_classes.css',
				'error.css'
			)
		);
		
		if(empty($filterlists) && empty($defaultFilters))
			return;
		
		$stylesheets = array();
		foreach($filterlists as $filterlist)
		{	
			$list = json_decode($filterlist,true);
			
			for($i = 0; $i < count($list['exclude_stylesheet']); $i++)
			{	
				$stylesheet = $list['exclude_stylesheet'][$i];
				if(!$stylesheet) continue;
				$stylesheets[] = $stylesheet;
			}	
		}
		
		if(empty($stylesheets) && empty($defaults) )
			return;
	
		$stylesheets = array_merge($defaults,$stylesheets);
	
		return "
					editor.on( 'configLoaded', function() {
						editor.config.stylesheets = ". json_encode($stylesheets).";
					});
				";
		
		
	}
}
