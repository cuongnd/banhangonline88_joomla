<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2014 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die();

if (!defined( '_ARK_VOTE_MODULE' ))
{
	/** ensure that functions are declared only once */
	define( '_ARK_VOTE_MODULE', 1 );

	require_once( JPATH_COMPONENT .DS. 'helper.php' );

	$links = ARKHelper::getExternalLinks();

	echo '<div id="arkvote">';
	echo '<table class="table table-striped">';

	echo '<tr>';
	echo '<td class="muted">' . JText::_( 'COM_ARKEDITOR_VOTE_INTRO_NAME' ) . '</td>';
	echo '</tr>';

	echo '<tr>';
	echo '<td class="buttoncol">';
	echo '<a class="btn btn-success pull-right" href="' . $links['jed-editor'] . '" target="_blank">' . JText::_( 'COM_ARKEDITOR_VOTE_BTN_NAME' ) . '</a>';
	echo '</td>';
	echo '</tr>';

	echo '</table>';
	echo '</div>';
}