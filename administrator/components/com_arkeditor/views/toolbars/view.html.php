<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die();

class ARKViewToolbars extends JViewLegacy
{
	protected $canDo;
	protected $app;
	protected $user;
	protected $state;
	protected $items;
	protected $pagination;

	function display( $tpl = null )
	{
		$this->canDo		= ARKHelper::getActions();
		$this->app			= JFactory::getApplication();
		$this->user			= JFactory::getUser();
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if(count($errors = $this->get('Errors')))
		{
			ARKHelper::error( implode("\n", $errors));
			return false;
		}

		// Check if there are no matching items
		if(!count($this->items))
		{
			ARKHelper::error( JText::_('COM_ARKEDITOR_LAYOUT_MANAGER_NO_TOOLBARS_FOUND') );
		}
		
		//now lets get default toolbars
		$editor = JPluginHelper::getPlugin('editors','arkeditor');
		$params =  new JRegistry($editor->params);
		$this->default = $params->get('toolbar','Publisher'); 
		$this->defaultFT = $params->get('toolbar_ft','Basic'); 
		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		$bar = JToolBar::getInstance('toolbar');

		JToolBarHelper::title( JText::_( 'COM_ARKEDITOR_SUBMENU_LAYOUT_NAME' ), 'layout.png' );

		require_once( JPATH_COMPONENT .DS. 'helper.php' );

		$links = ARKHelper::getExternalLinks();

		if($this->canDo->get('core.create'))
		{
			JToolBarHelper::addNew( 'toolbars.add' );
		}

		if($this->canDo->get('core.edit'))
		{
			JToolBarHelper::editList( 'toolbars.edit' );
		}

		if($this->canDo->get('core.create'))
		{
			JToolBarHelper::custom( 'toolbars.copy', 'copy', 'copy', JText::_( 'JLIB_HTML_BATCH_COPY' ), true );
		}

		if($this->canDo->get('core.delete'))
		{
			JToolBarHelper::deleteList( '', 'toolbars.remove' );
		}

		if($this->canDo->get('core.edit.state'))
		{
			JToolbarHelper::checkin('toolbars.checkin');
		}

		JToolBarHelper::help( $this->app->input->get( 'view' ), false, $links['ark-guide'] );
		
		JHtmlSidebar::setAction('index.php?option=com_arkeditor&view=' . JFactory::getApplication()->input->get( 'view', 'toolbars' ) );

		ARKHelper::addSubmenu( $this->app->input->get( 'view' ) );

		$this->sidebar = JHtmlSidebar::render();
	}//end function

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			't.title' => JText::_('JGLOBAL_TITLE'),
			't.name' => JText::_('COM_ARKEDITOR_LAYOUT_MANAGER_NAME'),
			't.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}