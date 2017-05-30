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
 * @subpackage  Content.ArkContent
 */
class PlgContentArkFilter extends JPlugin
{
	public $app;

	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{

		//Inline editing is only enabled for frontend editing	
		if ($this->app->isAdmin())
		{
			return;
		}
		
		
		$user = JFactory::getUser();
		
		//if user is guest lets bail
		if($user->get('guest'))
		{
			return;
		}

        $cParams = JComponentHelper::isInstalled('com_arkeditor') ? JComponentHelper::getParams('com_arkeditor') : false;
		if(!empty($cParams) && !$cParams->get('enable_inline',true))
		{
			$this->isEnabled = false;
			return;
		}	
        		
        if(!JPluginHelper::isEnabled('editors','arkeditor'))
            return;	
		
        if(!JPluginHelper::isEnabled('system','inlinecontent'))
            return;	
		
		if(empty($params))
			return;
		
		if(is_array($params)) //wrong format lets bail out
			return;
		
		if(!($params instanceOf JRegistry))
			return;
		
		if($params->get('inline') === false )
		{
			return;
		}

		if(!isset($article->id) || !isset($article->title)) 
			return;
		
		$text = isset($article->text) ? $article->text : ''; 
		
		if(isset($article->introtext) && !isset($article->text))
			$text = $article->introtext;
		
		if(!$text)
			return;
		
		//check if not allowed content is present	
		
		/*filter article to see if it is being used to load a module if so skip it
		[widgetkit]
		{loadmodule}
		{loadposition}
		{module}
		{modulepos}
		{modulepos}
		{component}
		{article(s)}
		*/
		
		$test = preg_match('/\{(?:loadmodule|loadposition|module|modulepos|component|articles?)\s+(.*?)\}/i',$text);
		
		if(!$test)
			$test = preg_match('/\[widgetkit\s+(.*?)\]/i',$article->text);
			
		if($test)
		{	
			$uniqueid = crc32($article->title.$article->id);
			$this->app->input->set($context.'.'.$uniqueid,1);
		}	
	}	
}