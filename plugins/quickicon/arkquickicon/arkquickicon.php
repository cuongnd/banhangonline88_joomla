<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined('_JEXEC') or die;

class PlgQuickiconArkEditor extends JPlugin
{
	/**
	 * Returns an icon definition for an icon which looks for extensions updates
	 * via AJAX and displays a notification when such updates are found.
	 *
	 * @param   string  $context  The calling context
	 *
	 * @return  array  A list of icon definition associative arrays, consisting of the
	 *                 keys link, image, text and access.
	 *
	 * @since   2.5
	 */
	public function onGetIcons($context)
	{
		if( $context != $this->params->get( 'context', 'mod_quickicon' ) )
		{
			return;
		}//end if

		JFactory::getLanguage()->load( 'com_arkeditor' );

		return array(
			array(
				'link' => 'index.php?option=com_arkeditor&view=cpanel',
				'image' => 'power-cord',
				'text' => JText::_( 'COM_ARKEDITOR' ),
				'access' => array( 'core.manage', 'com_arkeditor' ),
				'group' => 'MOD_QUICKICON_CONTENT'
			)
		);
	}
}
