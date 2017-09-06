<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2014 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die();

if (!defined( '_ARK_STATISTIC_MODULE' ))
{
	/** ensure that functions are declared only once */
	define( '_ARK_STATISTIC_MODULE', 1 );

	require_once( JPATH_COMPONENT . DS . 'helper.php' );

	function addStatistic( $label = '', $stat = '', $type = '', $link = '' )
	{
		if( is_numeric( $stat ) )
		{
			$colour		= ( $type && in_array( $type, array( 'success', 'warning', 'important', 'info', 'inverse' ) ) ) ? chr( 32 ) . 'badge-' . $type : '';
			$statistic 	= '<span class="badge' . $colour . '">' . (int)$stat . '</span>';
		}
		else
		{
			$colour		= ( $type && in_array( $type, array( 'muted', 'warning', 'error', 'info', 'success' ) ) ) ? 'text-' . $type : '';
			$statistic 	= '<p class="' . $colour . '"><strong>' . $stat . '</strong></p>';
		}//end if

		if( $link && $label )
		{
			$base		= 'index.php?option=com_arkeditor&view=';
			$label		= '<a href="' . $base . $link . '">' . $label . '</a>';
			$statistic	= '<a href="' . $base . $link . '" class="badge-link">' . $statistic . '</a>';
		}//end if

		// RENDER STAT
		echo '<td>' . $label . '</td>';
		echo '<td>' . $statistic . '</td>';
	}

	$config 		= ARKHelper::getEditorPluginConfig();
	$toolbars 		= ARKHelper::getEditorToolbars( true );
	$plugins 		= ARKHelper::getEditorPlugins( true );
	$toolbars_core 	= JArrayHelper::getColumn( $toolbars, 'iscore' );
	$plugins_core 	= JArrayHelper::getColumn( $plugins, 'iscore' );
	$plugins_state 	= JArrayHelper::getColumn( $plugins, 'published' );

	echo '<div id="arkstats">';
	echo '<table class="table table-striped">';

	echo '<tr>';
		addStatistic( JText::_( 'COM_ARKEDITOR_STATISTIC_ALLTOOLBARS_NAME' ), count( $toolbars ), 'info' );
		addStatistic( JText::_( 'COM_ARKEDITOR_STATISTIC_ALLPLUGINS_NAME' ), count( $plugins ), 'info', 'list&filter_state=*&filter_iscore=*' );
	echo '</tr>';
	echo '<tr>';
		addStatistic( JText::_( 'COM_ARKEDITOR_STATISTIC_CORETOOLBARS_NAME' ), count( array_keys( $toolbars_core, 1 ) ), 'inverse' );
		addStatistic( JText::_( 'COM_ARKEDITOR_STATISTIC_COREPLUGINS_NAME' ), count( array_keys( $plugins_core, 1 ) ), 'inverse', 'list&filter_state=*&filter_iscore=1' );
	echo '</tr>';
	echo '<tr>';
		addStatistic( JText::_( 'COM_ARKEDITOR_STATISTIC_NONCORETOOLBARS_NAME' ), count( array_keys( $toolbars_core, 0 ) ) );
		addStatistic( JText::_( 'COM_ARKEDITOR_STATISTIC_NONCOREPLUGINS_NAME' ), count( array_keys( $plugins_core, 0 ) ), '', 'list&filter_state=*&filter_iscore=0' );
	echo '</tr>';
	echo '<tr>';
		addStatistic( JText::_( 'COM_ARKEDITOR_STATISTIC_FETOOLBAR_NAME' ), $config->get( 'toolbar_ft', JText::_( 'COM_ARKEDITOR_STATISTIC_BLANK' ) ), 'warning' );
		addStatistic( JText::_( 'COM_ARKEDITOR_STATISTIC_PUBPLUGINS_NAME' ), count( array_keys( $plugins_state, 1 ) ), 'success', 'list&filter_state=1&filter_iscore=*' );
	echo '</tr>';
	echo '<tr>';
		addStatistic( JText::_( 'COM_ARKEDITOR_STATISTIC_BETOOLBAR_NAME' ), $config->get( 'toolbar', JText::_( 'COM_ARKEDITOR_STATISTIC_BLANK' ) ), 'warning' );
		addStatistic( JText::_( 'COM_ARKEDITOR_STATISTIC_UNPUBPLUGINS_NAME' ), count( array_keys( $plugins_state, 0 ) ), 'important', 'list&filter_state=0&filter_iscore=*' );
	echo '</tr>';

	echo '</table>';
	echo '</div>';
}