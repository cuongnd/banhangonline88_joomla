<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die();

class ARKModelEditPlugin extends JModelForm
{
	protected $item;

	public function getItem( $pk = null )
	{
		$user		= JFactory::getUser();
		$app		= JFactory::getApplication();
		$cid 		= $app->input->get( 'cid', array(), 'array' );
		$id 		= current( $cid );
		$item 		= ARKHelper::getTable('plugin');

		// load the row from the db table
		$item->load( $id );
		
		// Hide CK's plugin
		if( !$item || in_array( $item->name, ARKHelper::getHiddenPlugins() ) )
		{
			$app->redirect( 'index.php?option=com_arkeditor&view=list', 'Could Not Load Plugin.', 'error' );
			return false;		
		}

		// fail if checked out not by 'me'
		if ($item->isCheckedOut( $user->get('id') ))
		{
			$msg = JText::sprintf( 'COM_ARKEDITOR_MSG_BEING_EDITED', JText::_( 'The plugin' ), ($item->title ?: $item->name) );
			$app->redirect( JRoute::_( 'index.php?option=com_arkeditor&view=list', false ), $msg, 'error' );
			return false;
		}

		// TOOLBARS
		$toolbars = $this->getToolbarList();
		$item->selections = $this->getSelectedToolbarList();

		if( !$item->selections )
		{
			$item->toolbars = 'none';
		}
		elseif( count( $item->selections ) == count( $toolbars ) )
		{
			$item->toolbars = 'all';
		}
		else
		{
			$item->toolbars = 'select';
		}

		// GROUPS
		$groups 		= $this->getUserGroupList();
		$allowedGroups 	= array();
		
		// re-order groups to match acl col
		foreach( $groups as $group )
		{
			$allowedGroups[] = $group->value;
		}

		if( !is_null( $item->acl ))
		{
			$allowedGroups = json_decode($item->acl);
		}

		if($item->acl == '[]')
		{
			$item->group = 'special';
		} 
		elseif(count($allowedGroups) == count($groups)) 
		{
			$item->group = 'all';
		} 
		else 
		{
			$item->group = 'select';
		}

		$item->groups	= $allowedGroups;
		$xmlPath = '';

		if($item->iscore) //AW get path for core plugins XML file
		{
			$path		= JPATH_COMPONENT.DS.'editor'.DS.'plugins';
			$xmlPath 	= $path .DS. $item->name .'.xml';
	    }
		else
		{
			$path		= JPATH_PLUGINS .DS.'editors'.DS.'arkeditor'.DS.'plugins'.DS.$item->name;
			$xmlPath 	= $path .DS. $item->name .'.xml';
		}

		if($id)
		{
			$item->checkout( $user->get('id') );
   
            if(JFile::exists($xmlPath ))
			{
	            $data =  simplexml_load_file( $xmlPath );
          		$item->description = (string) $data->description;
			}
			else
			{
				$item->description = '';
			}
		} else {
			$item->type 		= 'plugin';
			$item->published 	= 1;
			$item->description 	= 'From XML install file';
			$item->icon 		= '';
			$item->params		= '';
		}

		$this->item = $item;

		return $this->item;
	}

	function getForm( $data = array(), $loadData = true )
	{
		  
        $form = $this->loadForm('com_arkeditor.editplugin', 'editplugin', array('control' => 'jform', 'load_data' => $loadData));


        return ( empty( $form ) ) ? false : $form;
	}

	// Not yet in use.... (swap out for what the view is doing someday?
	function getPluginForm( $data = false, $loadData = true )
	{
		
         $form = $this->loadForm('com_arkeditor.plugin', $data, array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	function getSelectedToolbarList()
	{
		return JModelLegacy::getInstance( 'list', 'ARKModel' )->getSelectedToolbarList();
	}

	function getToolbarList()
	{
		$rows = array();
		arkimport('helper');
		$toolbars = ARKHelper::getEditorToolbars();

		if(!empty($toolbars))
		{
			foreach($toolbars as $toolbar)
			{
				$row = new stdclass;
				$row->text = ucfirst($toolbar); 
				$row->value = $toolbar;
				$rows[] = $row;
			}
		}
		return $rows;
	}
	

	function getUserGroupList()
	{
		return JModelLegacy::getInstance( 'list', 'ARKModel' )->getUserGroupList();
	}
}