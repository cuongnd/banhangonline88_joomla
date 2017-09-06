<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die();

class ARKViewList extends JViewLegacy
{
	protected $app;
	protected $user;
	protected $state;
	protected $items;
	protected $pagination;
	
	function display( $tpl = null )
	{
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
			ARKHelper::error( JText::_('COM_ARKEDITOR_PLUGIN_NO_PLUGINS_FOUND') );
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		$bar 	= JToolBar::getInstance('toolbar');
		$canDo 	= ARKHelper::getActions();

		JToolBarHelper::title( JText::_( 'COM_ARKEDITOR_SUBMENU_PLUGIN_NAME' ), 'puzzle' );

		require_once( JPATH_COMPONENT .DS. 'helper.php' );

		$links = ARKHelper::getExternalLinks();
		
		if($canDo->get('core.edit'))
		{
			JToolBarHelper::editList('list.edit');
		}//end if

		if($canDo->get('core.edit.state'))
		{
			JToolBarHelper::publishList('list.publish');
			JToolBarHelper::unpublishList('list.unpublish');
			JToolbarHelper::checkin('list.checkin');
		}//end if

		JToolBarHelper::help( $this->app->input->get( 'view' ), false, $links['ark-guide'] );

		ARKHelper::addSubmenu( $this->app->input->get( 'view' ) );

		JHtmlSidebar::setAction('index.php?option=com_arkeditor&view=list');

		// FILTERS
		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_state',
			JHtml::_('select.options', ARKHelper::getStateOptions(), 'value', 'text', $this->state->get('filter.state'))
		);

		JHtmlSidebar::addFilter(
			JText::_('- Select Core Type -'),
			'filter_iscore',
			JHtml::_('select.options', array( JHtml::_('select.option', '1', 'Core Plugins'), JHtml::_('select.option', '0', 'Not Core Plugins') ), 'value', 'text', $this->state->get('filter.iscore'))
		);

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
			'p.title' => JText::_('JGLOBAL_TITLE'),
			'p.published' => JText::_('JSTATUS'),
			'p.name' => JText::_('COM_ARKEDITOR_PLUGIN_LIST_NAME'),
			'p.icon' => JText::_('COM_ARKEDITOR_PLUGIN_LIST_ICON'),
			'p.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}//end class
