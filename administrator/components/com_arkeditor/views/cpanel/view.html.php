<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die();

jimport('joomla.html.sliders');
jimport('joomla.application.module.helper');

class ARKViewCpanel extends JViewLegacy
{
	protected $canDo;
	protected $app;
	protected $left;
	protected $right;
	protected $bottom;
	
	function display( $tpl = null )
	{
		$this->canDo	= ARKHelper::getActions();
		$this->app		= JFactory::getApplication();
		$lang 			= JFactory::getLanguage();		
		$this->left		= ARKModuleHelper::getModules( 'ark_icon' );
		$this->right	= ARKModuleHelper::getModules( 'ark_cpanel' );
		$this->bottom	= ARKModuleHelper::getModules( 'ark_footer' );

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		$bar = JToolBar::getInstance('toolbar');

		JToolBarHelper::title( JText::_( 'COM_ARKEDITOR' ), 'arkeditor' );

		require_once( JPATH_COMPONENT .DS. 'helper.php' );

		$links = ARKHelper::getExternalLinks();

		// TODO: Plumb In
		// JToolbarHelper::custom( $links['ark-guide'], 'help', 'help', 'Help' );
		// JToolbarHelper::custom( $links['ark'], 'warning', 'warning', 'Report a Bug' );
		JToolBarHelper::help( $this->app->input->get( 'view' ), false, $links['ark-guide'] );

		if ($this->canDo->get('core.admin'))
		{
			JToolbarHelper::preferences( 'com_arkeditor' );
		}

		ARKHelper::addSubmenu( $this->app->input->get( 'view' ) );

		$this->sidebar = JHtmlSidebar::render();
	}//end function
}