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

class ARKListControllerListener extends JEvent
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
	 function onSave($plugin,$pluginToolbarnames)
	 {
		if( !$this->canDo->get('core.edit') )
		{
			$this->app->redirect( JRoute::_( 'index.php?option=com_arkeditor&view=list', false ), JText::_( 'COM_ARKEDITOR_PLUGIN_PERM_NO_SAVE' ), 'error' );
			return false;
		}

		arkimport('helper');
	    $toolbarnames = ARKHelper::getEditorToolbars();

		$config = ARKHelper::getEditorPluginConfig();
		$toolbars = $config->get('toolbars');

		
		if(!empty( $toolbarnames))
		{
			 foreach($toolbarnames as $toolbarname)
			 {
				$toolbar = $toolbars[$toolbarname];
				
				if(in_array($toolbarname, $pluginToolbarnames))
				{
			
					if(!ARKHelper::in_array($plugin->title,$toolbar))
						$toolbar[] = array($plugin->title);
				}
				else
				{
					$key = ARKHelper::in_array($plugin->title,$toolbar);
					if(ARKHelper::in_array($plugin->title,$toolbar))
					{
						foreach($toolbar as $key => $elements)
						{
							$found = false;
							foreach($elements as $k => $value)
							{
								if($value == $plugin->title)
								{
									unset($toolbar[$key][$k]);
									$found = true;
									break;
								}	
							}
							if($found)
								break;
						}
					}	
				}
				$toolbars[$toolbarname] = $toolbar;
			} 
			
			$config->set('toolbars', base64_encode(json_encode($toolbars)));
			$row = JTable::getInstance('extension');
			$row->load(array('folder'=>'editors','element'=>'arkeditor'));
			$row->bind(array('params'=>$config->toArray()));
				
			if(!$row->store())
				ARKHelper::error( 'Failed to save Ark Editor\'s parameters');

		}	
		//Publish or unpblish plugin
		$this->onPublish(array($plugin->id),(int) $plugin->published);
		
		//Checkin extension plugin
		$row = JTable::getInstance('extension');
		$row->load(array('custom_data'=>$plugin->id));
		
		if($row->extension_id)
		{
			if(!$row->checkin())
				ARKHelper::error( 'Failed to check in in extension');
		}
	}

	function onPublish($cid,$value)
	{
		
		if( !$this->canDo->get('core.edit.state') )
		{
			$this->app->redirect( JRoute::_( 'index.php?option=com_arkeditor&view=list', false ), JText::_( 'COM_ARKEDITOR_PLUGIN_PERM_NO_PUB' ), 'error' );
			return false;
		}
	
		$db =  JFactory::getDBO(); 
		$user = JFactory::getUser();
		$cids = implode( ',', $cid );
		
		$query = $db->getQuery(true);
		$query->select('extension_id')
			->from('#__extensions')
			->where('folder= '.$db->quote('arkeditor'))
			->where('custom_data IN ('.$cids.')');
			
		$db->setQuery( $query );
		$extensions_ids = $db->loadColumn();
		
		if(!empty($extensions_ids))
		{
			$query = $db->getQuery(true);
			$query->update('#__extensions')
				->set('enabled='. (int) $value)
				->where('extension_id IN ('. implode( ',', $extensions_ids ).')');
			$db->setQuery( $query );
			$db->query();
		}
	}

	function onApply($plugin,$pluginToolbarnames)
	{
		if( !$this->canDo->get('core.edit') )
		{
			$this->app->redirect( JRoute::_( 'index.php?option=com_arkeditor&view=list', false ), JText::_( 'COM_ARKEDITOR_PLUGIN_PERM_NO_SAVE' ), 'error' );
			return false;
		}
		$this->onSave($plugin,$pluginToolbarnames);
	}

	function onUnpublish($cid,$value)
	{
		if( !$this->canDo->get('core.edit.state') )
		{
			$this->app->redirect( JRoute::_( 'index.php?option=com_arkeditor&view=list', false ), JText::_( 'COM_ARKEDITOR_PLUGIN_PERM_NO_UNPUB' ), 'error' );
			return false;
		}

		$this->onPublish($cid,0);
	}
	
	function onCancel($id)
	{
		if(!$id) //nothing to do do so bail out
			return;
			
		//Checkin extension plugin
		$row = JTable::getInstance('extension');
		$row->load(array('custom_data'=>$id));
		
		if($row->extension_id)
		{
			if(!$row->checkin())
				ARKHelper::error( 'Failed to check in in extension');
		}
	}
}