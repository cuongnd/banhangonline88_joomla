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
class PlgArKEventsAutoCSSFilter extends JPlugin
{

	public $app;


	public function onInstanceCreated(&$params)
	{
		
		$filterlists = $params->get('exclude_selectors',array('{exclude_selector:[]}'));
		$defaultfilters = $params->get('default_exclude_selectors',
			array(	'body','cke_','__','sbox','input','textarea','button','select','form','fieldset',
				'.modal-backdrop','div.modal','.dropdown-backdrop','.chzn','.uk-slideshow-swipe','.uk-position',
				'.fa-','.fa.','#jcemediabox-','.jcemediabox-','.pweb-','.uk-accordion-icon-title')
		);
		
		if(empty($filterlists) && empty($defaultFilters))
			return;
		
		$defaults = array();
		foreach($defaultfilters as $defaultlist)
			$defaults[] = '^'.str_replace(array('\\','.'),array('','\.'), $defaultlist);    
		
		$selectors = array();
		foreach($filterlists as $filterlist)
		{	
			$list = json_decode($filterlist,true);
			
			for($i = 0; $i < count($list['exclude_selector']); $i++)
			{	
				$selector = $list['exclude_selector'][$i];
				if(!$selector) continue;
				$selectors[] = '^'.str_replace(array('\\','.','-'),array('','\.','\-'), $selector);
			}
		}			
			
			
		if(empty($selectors) && empty($defaults) )
			return;
	
		$selectors = array_merge($defaults,$selectors);
		
		
		return "
					editor.on( 'configLoaded', function() {
						this.config.stylesheetParser_skipSelectors = /(". implode('|',$selectors) . ")/;
					});
				";
	}
}
