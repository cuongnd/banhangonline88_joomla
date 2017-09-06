<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die();

class ARKControllerCpanel extends ARKController
{
	protected $canDo = false;

	function __construct( $default = array())
	{
		parent::__construct( $default );

		$this->canDo = ARKHelper::getActions();
	}

	public function editor()
	{
		$app = JFactory::getApplication();
		$dbo = JFactory::getDBO();
		$sql = $dbo->getQuery( true );
		$sql->select( 'extension_id' )->from( '#__extensions' )->where( 'type = "plugin"' )->where( 'folder = "editors"' )->where( 'element = "arkeditor"' );
		$dbo->setQuery( $sql );
		$plg = $dbo->loadresult();

		if( $plg )
		{
			$app->redirect( 'index.php?option=com_plugins&task=plugin.edit&extension_id=' . $plg );
		}
		else
		{
			$app->redirect( 'index.php?option=com_arkeditor&view=cpanel', JText::_( 'COM_ARKEDITOR_MSG_NOEDITOR' ), 'warning' );
		}
	}
}