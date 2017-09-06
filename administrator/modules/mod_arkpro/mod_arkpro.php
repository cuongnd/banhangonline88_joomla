<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2014 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die();

if (!defined( '_ARK_PRO_MODULE' ))
{
	/** ensure that functions are declared only once */
	define( '_ARK_PRO_MODULE', 1 );

	require_once( JPATH_COMPONENT .DS. 'helper.php' );

	$links 	= ARKHelper::getExternalLinks();
	$lang 	= JFactory::getLanguage();
	$html 	= array();
	$i 		= 1;

	function addItem( $label = '', $type = 'ok' )
	{
		$links 	= ARKHelper::getExternalLinks();
		$html 	= array();
		$html[] = '<td width="6%"><i class="icon-' . $type . '"></i></td>';
		$html[] = '<td width="44%" class="stat">';
		$html[] = '<span>' . $label . '</span>';
		$html[] = '<a href="' . $links['ark-pro'] . '" target="_blank">' . JText::_( 'COM_ARKEDITOR_PRO_MORE_NAME' ) . '</a>';
		$html[] = '</td>';

		return implode( "\n", $html );
	}

	while ( $lang->hasKey( 'COM_ARKEDITOR_PRO_ROW' . $i . '_NAME' ) )
	{
		$html[] = ( ( $i & 1 ) ) ? '<tr>' : '';
		$html[] = addItem( JText::_( 'COM_ARKEDITOR_PRO_ROW' . $i . '_NAME' ) );
		$html[] = ( !( $i & 1 ) ) ? '</tr>' : '';

		$i++;
	}//end while

	echo '<div id="arkpro">';
	echo '<table class="table table-striped">';

	echo '<tr>';
	echo '<td colspan="4" class="muted">' . JText::sprintf( 'COM_ARKEDITOR_PRO_REASONS_NAME', --$i ) . '</td>';
	echo '</tr>';

	echo implode( "\n", $html );

	echo '<tr>';
	echo '<td colspan="4" class="buttoncol">';
	echo '<a class="btn btn-success pull-right" href="' . $links['ark-pro'] . '" target="_blank">' . JText::_( 'COM_ARKEDITOR_PRO_GO_NAME' ) . '</a>';
	echo '</td>';
	echo '</tr>';

	echo '</table>';
	echo '</div>';
}