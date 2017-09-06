<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2014 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die();

if (!defined( '_ARK_TIP_MODULE' ))
{
	/** ensure that functions are declared only once */
	define( '_ARK_TIP_MODULE', 1 );

	require_once( JPATH_COMPONENT .DS. 'helper.php' );

	

	echo '<div id="arktip">';
	echo '<table class="table table-striped">';

	echo '<tr>';
	echo '<td class="muted">' . JText::_( 'COM_ARKEDITOR_TIP_OF_THE_DAY' ) . '</td>';
	echo '</tr>';
	echo '</table>';
	echo '</div>';
}