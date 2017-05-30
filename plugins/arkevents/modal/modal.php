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

class PlgArKEventsModal extends JPlugin
{
	public function onInstanceCreated(&$params)
	{
		return "editor.on('beforeContentUpdate',function(event)
		{
            //Are there any links that need to be added to SqueezeBox
			if( event.data.data[0].data.indexOf( 'modal' ) != -1 )
            {
                //Has squeezebox been loaded?
                if( typeof(SqueezeBox) === 'undefined' && !jQuery.fn.squeezeBox)
                {
                    //Doesn't appear so, so lets call it.
		            jQuery.getScript('".JURI::root()."media/editors/arkeditor/js/squeezebox.min.js');
					jQuery.getScript('".JURI::root()."media/editors/arkeditor/js/jquery.easing.min.js');
                }

            }//end if
			
			
		});
        
        editor.on('destroy',function(event)
		{
            //Are there any links that need to be added to SqueezeBox
			if( editor.getData().indexOf( 'modal' ) != -1 )
            {
				if(jQuery.fn.squeezeBox)
				{
					jQuery( this.container.$ ).find( 'a.modal' ).squeezeBox({ parse: 'rel' });
			
					jQuery( this.container.$ ).find( 'img.modal' ).each( function( i, el )
					{
						jQuery(el).squeezeBox({
							handler: 'image',
							url: jQuery( el ).attr( 'src' )
						});
					})
				}
				else if(typeof SqueezeBox === 'object')
				{
					SqueezeBox.assign( jQuery( this.container.$ ).find( 'a.modal' ).get(), { parse: 'rel' });	
				   
					jQuery( this.container.$ ).find( 'img.modal' ).each( function( i, el )
					{
						SqueezeBox.assign( el, 
						{
							handler: 'image',
							url: jQuery( el ).attr( 'src' )
						})
					});
				}
            }//end if	
		});";
	}
}