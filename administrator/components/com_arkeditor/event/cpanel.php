<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die;
defined('JPATH_BASE') or die();

jimport( 'joomla.event.event' );

class ARKCpanelControllerListener extends JEvent
{
	protected $canDo 	= false;
	protected $app 		= false;

	function __construct( &$subject )
	{
		parent::__construct( $subject );

		$this->canDo 	= ARKHelper::getActions();
		$this->app 		= JFactory::getApplication();
	}

	/**
	 * A JParameter object holding the parameters for the plugin
	 *
	 * @var		A JParameter object
	 * @access	public
	 * @since	1.5
	 */
	 public function onCheck()
	 {
	 }//end function	

	public function onSync()
	{
	}//end function	

	public function onImport()
	{
	}//end function	

	public function onExport()
	{
	}//end function
	//end function
}//end class