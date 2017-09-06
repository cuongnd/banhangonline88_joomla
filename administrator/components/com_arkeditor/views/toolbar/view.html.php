<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die();

class ARKViewtoolbar extends JViewLegacy
{
	protected $canDo;
	protected $app;
	protected $user;
	protected $item;
	protected $params;
	protected $form;

	function display( $tpl = null )
	{
		$this->canDo		= ARKHelper::getActions();
		$this->app			= JFactory::getApplication();
		$this->user			= JFactory::getUser();
		$this->item			= '';
		$this->toolbarplugins = array();

		$this->getForm();

		$cid = $this->app->input->get( 'cid', array(), 'array' );
		JArrayHelper::toInteger($cid, array(0));

		if( !count( $cid ) && !$this->canDo->get('core.create') )
		{
			$this->app->redirect( JRoute::_( 'index.php?option=com_arkeditor&view=cpanel', false ), JText::_( 'COM_ARKEDITOR_PLUGIN_PERM_NO_CREATE' ), 'error' );
			return false;
		}
		elseif(!$this->canDo->get('core.edit'))
		{
			$this->app->redirect( JRoute::_( 'index.php?option=com_arkeditor&view=cpanel', false ), JText::_( 'COM_ARKEDITOR_PLUGIN_PERM_NO_EDIT' ), 'error' );
			return false;
		}//end if

	
	
		$lists 	= array();
		$this->item = ARKHelper::getTable('toolbar');

		// load the row from the db table
		$this->item->load( (isset($cid[0]) ? $cid[0] : 0) );

		// fail if checked out not by 'me'
		if ($this->item->isCheckedOut( $this->user->get('id') ))
		{
			$msg = JText::sprintf( 'COM_ARKEDITOR_MSG_BEING_EDITED', JText::_( 'The toolbar' ), $this->item->title );
			$this->app->redirect( JRoute::_( 'index.php?option=com_arkeditor&view=toolbars', false ), $msg, 'error' );
			return false;
		}

		if (isset($cid[0]))
		{
			$this->item->checkout( $this->user->get('id') );
			
			//now lets get default toolbars
			$params = ARKHelper::getEditorPluginConfig();
			$this->default = $params->get('toolbar','back'); 
			$this->defaultFT = $params->get('toolbar_ft','front');

			$this->item->params = new JRegistry($this->item->params);
			$this->form->bind($this->item->params); //bind params options to form

			if(strtolower($this->item->name) == strtolower($this->default) || strtolower($this->item->name) == strtolower($this->defaultFT))
				$this->item->default = true;
			else
				$this->item->default = false;
		} 
		else {
			$this->item->params  = '';
			$this->item->default = false;
		}

		$db = JFactory::getDBO();

		//set the default total number of plugin records
		$total = 0;
		$totalRows = 0;

		if ( isset($cid[0]) )
		{
			$total = 1;

			$config = ARKHelper::getEditorPluginConfig();
			$toolbars = $config->get('toolbars');
			

			if( $toolbars )
			{
				$toolbar = array();
				if(isset($toolbars[$this->item->name]))
					$toolbar = $toolbars[$this->item->name];
				$it = array();
				
			   if(!empty($toolbar))
			   {	
					$it = new RecursiveIteratorIterator(new RecursiveArrayIterator($toolbar));
			   }
			   
			   $pluginTitles = "'0',";
				
				foreach($it as $v) {
					if($v)	
						$pluginTitles .=  $db->quote($v).",";
				}

				$pluginTitles = rtrim($pluginTitles, ",");

		
			
				$sql = $db->getQuery( true );
				$sql->select( 'p.id,p.title,p.icon,p.row,p.name' )
					->from( '#__ark_editor_plugins p' )
					->join( 'LEFT', '#__ark_editor_plugins parent on parent.id = p.parentid' )
					->where( 'p.title NOT IN ('. $pluginTitles .')' )
					->where( 'p.published = 1' )
					->where( 'p.title <> '.$db->quote(''))
					->where( '(p.parentid IS NULL OR parent.published = 1)' )
					//->where( 'p.icon <> '.$db->quote('')) Do not use to check if plugin is a button as Richcomboboxes do not have an icon
					->where( 'p.name <> '.$db->quote('imagemanager'))
					->where( 'p.name <> '.$db->quote('treelink'))
                    ->where( 'p.name <> '.$db->quote('styles'))
                    ->where( 'p.name <> '.$db->quote('arkabout'))
          			->order( 'p.row ASC, p.id ASC' );
				$plugins = $db->setQuery( $sql )->loadObjectList();

         
				
				$sql = $db->getQuery( true );
				$sql->select( 'p.title' )
					->from( '#__ark_editor_plugins p' )
					->join( 'LEFT', '#__ark_editor_plugins parent on parent.id = p.parentid AND parent.published = 1' )
					->where( 'p.title IN ('. $pluginTitles .')' )
					//->where( 'p.icon <> '.$db->quote(''))
					->where( 'p.published = 1' )
                    ->where( 'p.name <> '.$db->quote('styles'))
                    ->where( 'p.name <> '.$db->quote('arkabout'))
					->order( 'p.id ASC' );
				$keys = $db->setQuery( $sql )->loadColumn();	
				
				$sql = $db->getQuery( true );
				$sql->select( 'p.title,p.icon,p.name' )
					->from( '#__ark_editor_plugins p' )
					->join( 'LEFT', '#__ark_editor_plugins parent on parent.id = p.parentid AND parent.published = 1' )
					->where( 'p.title IN ('. $pluginTitles .')' )
					//->where( 'p.icon <> '.$db->quote(''))
					->where( 'p.published = 1' )
                    ->where( 'p.name <> '.$db->quote('styles'))
                    ->where( 'p.name <> '.$db->quote('arkabout'))
					->order( 'p.id ASC' );
				$values = $db->setQuery( $sql )->loadObjectList();	

				$items = array_combine($keys,$values);
				$this->items = $items;
				
				$this->toolbarplugins = $this->_getSortRowToolbars($toolbar );
				$this->assign('plugins',$plugins );
			}
		}

		$params = !isset($cid[0]) ? new JRegistry($this->item->params) :$this->item->params ;
		
		$components = $params->get('components',array());
		
		//$db->setQuery("SELECT element as value, REPLACE(element,'com_','')  as text FROM #__extensions WHERE type = 'component' ORDER BY element ASC");
		
		$query = $db->getQuery(true);
			$query->select('element AS value, element AS text')
				->from('#__extensions')
				->where($db->quoteName('type').' = '. $db->quote('component'))
				->order('element ASC');
		$db->setQuery($query);		
		
		$allcomponents =  $db->loadObjectList();
		
		foreach($allcomponents as $component)
		{
			$component->text = str_replace('com_','',$component->text);
		}
		$lists['components'] = JHTML::_('select.genericlist',  $allcomponents, 'components[]', ' size="10" multiple', 'value', 'text', $components);
				
		$this->assign('lists',	$lists);
		$this->assign('toolbar', $this->item);
		$this->assign('total', $total);
		//$this->assignRef('total', $total);

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		$this->app->input->set('hidemainmenu', true);

		$bar 		= JToolBar::getInstance('toolbar');
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $this->user->get('id'));

		JToolBarHelper::title( JText::_( 'COM_ARKEDITOR_SUBMENU_LAYOUT_NAME' ) .':' . chr( 32 ) . JText::_($this->item->name), 'list-view' );

		if( $this->canDo->get('core.create') && !$checkedOut )
		{
			JToolBarHelper::apply( 'toolbars.apply' );
			JToolBarHelper::save( 'toolbars.save' );
		}//end if

    	JToolBarHelper::cancel( 'toolbars.cancel', 'JTOOLBAR_CLOSE' );

		ARKHelper::addSubmenu( $this->app->input->get( 'view' ) );

		$this->sidebar = JHtmlSidebar::render();
	}//end function
		
	function _getSortRowToolbars($toolbar)
	{
		$out = array();
		$outToolbars = array();
		
		for($i = 0; $i < count($toolbar);$i++)
		{
			$item = $toolbar[$i];
			if(is_array($item))
			{
				$out[] = $item;	
			}
			elseif($item == '/')
			{
				$outToolbars[] = $out;
				$out = array();	
			}
			else
				continue; //ignore
		}		

		if(!empty($out))
		  $outToolbars[] = $out;	

		return $outToolbars;
	}
	
	
	function getItem($title)
	{
		if(isset($this->items[$title]))
			return $this->items[$title];
		return null;
	}
	
	
	
	public function getForm()
	{
		// Get the form.
		if (!is_object($this->form))
		{
			jimport('joomla.form.form');
			$app = JFactory::getApplication();
			JForm::addFormPath(JPATH_COMPONENT . '/models/forms');
			JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
			$form = JForm::getInstance('com_arkeditor.toolbar', 'toolbar', array('load_data' => true,'control'=>'params'));
			$this->form = ( empty( $form ) ) ? false : $form;
		}	

		 return $this->form;
	}
	
}