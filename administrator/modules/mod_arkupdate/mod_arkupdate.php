<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2014 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die();

// NOTE - For Non-Ajax Version Call Model Directly (See: administrator/components/com_installer/controllers/update.php)
if (!defined( '_ARK_UPDATE_MODULE' ))
{
	/** ensure that functions are declared only once */
	define( '_ARK_UPDATE_MODULE', 1 );

	require_once( JPATH_COMPONENT .DS. 'helper.php' );

	JHtml::_( 'jquery.framework' );

	$app	= JFactory::getApplication();
	$doc	= JFactory::getDocument();
	$eid 	= ARKHelper::getEID();
	$links 	= ARKHelper::getExternalLinks();
	$names 	= (object)array( 'root' => 'box-arkupdate', 'version' => 'version' );
	$root 	= 'box-arkupdate';

	echo '<div id="arkupdate">';
	echo '<table class="table table-striped">';

	echo '<tr>';
	echo '<td class="muted ' . $names->version . '">' . JText::_( 'COM_ARKEDITOR_UPDATE_INTRO_NAME' ) . '</td>';
	echo '</tr>';

	echo '<tr>';
	echo '<td class="buttoncol">';
	echo '<a class="btn btn-success pull-right" href="' . $links['joomla-update'] . '">' . JText::_( 'COM_ARKEDITOR_UPDATE_UPDATE_NAME' ) . '</a>';
	echo '<a class="btn btn-info pull-right" href="' . $links['ark-update'] . '" target="_blank">' . JText::_( 'COM_ARKEDITOR_UPDATE_MANUAL_NAME' ) . '</a>';
	echo '</td>';
	echo '</tr>';

	echo '</table>';
	echo '</div>';

	$css 	= '.box-arkupdate{ display : none; }';
	$js 	= 'jQuery( document ).ready( function( $ )
			{
				new jQuery.ajax(
				{
					url 	: "' . JURI::base() . 'index.php?option=com_installer&view=update&task=update.ajax&eid=' . $eid . '&skip=700",
					success : function( data )
					{
						if( data && data.length > 2 )
						{
							var result 	= JSON.parse( data )[0];
							var el 		= $( ".' . $names->root . '" );

							if( result && el.get( 0 ) )
							{
								var html = el.find( ".' . $names->version . '" );

								if( html.get( 0 ) && result.version )
								{
									html.html( html.html().replace( "%s", result.version ) );
									el.css( "display", "block" );
								}
							}
						}
					}
				})
			});';

	$doc->addStyleDeclaration( $css );
	$doc->addScriptDeclaration( $js );
}