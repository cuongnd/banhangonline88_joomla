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
 * @subpackage  System.inlineContent
 */
class PlgSystemArkVersions extends JPlugin
{

	public $app;
	
	public function onBeforeCompileHead()
	{
				
		//if not media manager bail out
		if ($this->app->input->get('option','') != 'com_contenthistory' )
		{
			return;
		}
		
		if(!$this->app->input->get('editor',''))
			return;
		
			
		$user = JFactory::getUser();
		
		//if user is guest lets bail
		if($user->get('guest'))
		{
			return;
		}
		
		
		if ($this->app->isSite())
		{
			
			if(!$this->app->input->get('inline',0) ||  !$this->app->input->get('editor',''))
				return;
		
			$head = JFactory::getDocument()->getHeadData();
        		
			$script = "
			(function ($){
				$(document).ready(function (){
										
					function getQueryValue(key) {
						return decodeURIComponent((new RegExp('[?|&]' + key + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,\"\"])[1].replace(/\+/g, '%20'))||null;
					}
					
					$('#toolbar-load').off('click').click(function() {
						var ids = $('input[id*=\'cb\']:checked');
						if (ids.length == 1) {
							if (window.parent) {
								window.parent.jLoadVersion(ids[0].value,getQueryValue('editor'));
								window.parent.jModalClose && window.parent.jModalClose();
							}
						}
						else
						{
							alert('Please select one version.');
						}
					});
				
				});
			})(jQuery);
			";
			
			$head['script']['text/javascript'] .= chr(13) . $script;
			JFactory::getDocument()->setHeadData($head);
			
		}
		
	}
}