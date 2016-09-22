<?php
/*------------------------------------------------------------------------
# Copyright (C) 2014-2016 WebxSolution Ltd. All Rights Reserved.
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
 * @subpackage  System.arkeditoruser
 */
class PlgSystemArKModal extends JPlugin
{
	public function onBeforeRender()
	{
		if( JFactory::getApplication()->isSite() )
		{
			JHtml::stylesheet(JURI::base() . 'media/editors/arkeditor/css/squeezebox.css');
			JHtml::script(JURI::base() . 'media/editors/arkeditor/js/jquery.easing.min.js');
			JHtml::script(JURI::base() . 'media/editors/arkeditor/js/squeezebox.min.js');

			// Support Image Modals
			JFactory::getDocument()->addScriptDeclaration(
			
				"(function()
				{
					if(typeof jQuery == 'undefined')
						return;
					
					jQuery(function($)
					{
						if($.fn.squeezeBox)
						{
							$( 'a.modal' ).squeezeBox({ parse: 'rel' });
				
							$( 'img.modal' ).each( function( i, el )
							{
								$(el).squeezeBox({
									handler: 'image',
									url: $( el ).attr( 'src' )
								});
							})
						}
						else if(typeof(SqueezeBox) !== 'undefined')
						{
							$( 'img.modal' ).each( function( i, el )
							{
								SqueezeBox.assign( el, 
								{
									handler: 'image',
									url: $( el ).attr( 'src' )
								});
							});
						}
						
						function jModalClose() 
						{
							if(typeof(SqueezeBox) == 'object')
								SqueezeBox.close();
							else
								ARK.squeezeBox.close();
						}
					
					});
				})();"
			);
		}
	}
}