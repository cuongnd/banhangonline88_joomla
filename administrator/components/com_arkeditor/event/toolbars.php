<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2015 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die;
defined('JPATH_BASE') or die();

jimport( 'joomla.event.event' );

class ARKToolbarsControllerListener extends JEvent
{
	protected $canDo 	= false;
	protected $app 	= false;

	function __construct( &$subject )
	{
		parent::__construct( $subject );

		$this->canDo 	= ARKHelper::getActions();
		$this->app 	= JFactory::getApplication();
	}

	/**
	 * A JParameter object holding the parameters for the plugin
	 *
	 * @var		A JParameter object
	 * @access	public
	 * @since	1.5
	 */
	public function onCopy($original,$copy)
	{
		if( !$this->canDo->get('core.create') )
		{
			$this->app->redirect( JRoute::_( 'index.php?option=com_arkeditor&view=toolbars', false ), JText::_( 'COM_ARKEDITOR_PLUGIN_PERM_NO_COPY' ), 'error' );
			return false;
		}
		
		$config = static::getEditorPluginConfig();
		$toolbars = $config->get('toolbars');

		
		$toolbar[$copy] = $toolbars[$original];
		
		$config->set('toolbars', base64_encode(json_encode($toolbars)));
		$row = JTable::getInstance('extension');
		$row->load(array('folder'=>'editors','element'=>'arkeditor'));
		$row->bind(array('params'=>$config->toArray()));
		if(!$row->store())
			ARKHelper::error( 'Failed to copy toolbar items');
	}
	
 
	public function onSave($name,$oldname,$title,$rows,$isNew)
	{
		$toolbar = new stdclass;
		$toolbar->oldname = $oldname;
		$toolbar->name = $name;
		$toolbar->title = $title;
		

				
		if($isNew) // Also check to see if toolbar file already exists. If so then it is just a simple update
		{
			$this->_createEditorToolbarOption(array($toolbar));
		}	
		else
		{
			$this->_updateEditorToolbarOption(array($toolbar));
			$config = static::getEditorPluginConfig();
			$toolbars = $config->get('toolbars');

			if(empty($rows))
				return ARKHelper::error( 'Failed to retrieve toolbar items');
			
			if(isset($toolbars[$oldname]))
				unset($toolbars[$oldname]);
				
			$toolbars[$name] = $rows;
						
		
			$config->set('toolbars', base64_encode(json_encode($toolbars)));
			$row = JTable::getInstance('extension');
			$row->load(array('folder'=>'editors','element'=>'arkeditor'));
				
			$row->bind(array('params'=>$config->toArray()));

			
			if(!$row->store())
				ARKHelper::error( 'Failed to save toolbar items');
			}	
			
			$config = static::getEditorPluginConfig();
			$toolbars = $config->get('toolbars');
		
	 }
	 
	public function onRemove($names)
	{
		if( !$this->canDo->get('core.delete') )
		{
			$this->app->redirect( JRoute::_( 'index.php?option=com_arkeditor&view=toolbars', false ), JText::_( 'COM_ARKEDITOR_PLUGIN_PERM_NO_DELETE' ), 'error' );
			return false;
		}
		//delete toolbars from database 
		
		
		$config = static::getEditorPluginConfig();
		$toolbars = $config->get('toolbars');

		
		foreach($names as $name)
			unset($toolbars[$name]);
		
		$config->set('toolbars', base64_encode(json_encode($toolbars)));
		$row = JTable::getInstance('extension');
		$row->load(array('folder'=>'editors','element'=>'arkeditor'));
		$row->bind(array('params'=>$config->toArray()));
		if(!$row->store())
			ARKHelper::error( 'Failed to remove toolbars references in the database');
	
		//Delete toolbar files
		$this->_deleteEditorToolbarOption($names);
	}
		
		
		
	private function _createEditorToolbarOption($toolbars)
	{
	   // get editor installfile
		$ARKManifestFile = JPATH_PLUGINS . DS . 'editors' . DS . 'arkeditor' .DS . 'arkeditor.xml';

		$arkeditorXML = ARKHelper::getXMLParser('Simple');
		if(!$arkeditorXML->loadFile($ARKManifestFile)) 
		{
			ARKHelper::error( 'Editor Install: '.JText::_('COM_ARKEDITOR_LAYOUT_MANAGER_NOT_LOAD_ARK_MANIFEST'));
			return;
		}

 		$ARKManifest = $arkeditorXML->document;

 		$paramsElement = $ARKManifest->config[0]->fields[0]->fieldset[0];

		foreach($toolbars as $toolbar)
		{
			foreach ($paramsElement->children() as $param)
			{
		
				if($param->attributes('name') == 'toolbar')
				{
					$child = $param->AddChild('option',array('value' => $toolbar->name));
					$child->setData($toolbar->title);
				}
				if($param->attributes('name') == 'toolbar_ft')
				{
					$child = $param->AddChild('option',array('value' => $toolbar->name));
					$child->setData($toolbar->title);
					break;
				}
			}
		}

		$ARKOutputXMl = $ARKManifest->toString();

		if(!JFile::write($ARKManifestFile,$ARKOutputXMl)) //Write to editor manifest file 
		{
			ARKHelper::error( 'Toolbar Copy: '.JText::_('COM_ARKEDITOR_LAYOUT_MANAGER_NOT_WRITE_ARK_MANIFEST'));
		}
	}

	private function _deleteEditorToolbarOption($names)
	{
	   // get editor installfile
		$ARKManifestFile = JPATH_PLUGINS .'/editors/arkeditor/arkeditor.xml';
		
		$arkeditorXML = ARKHelper::getXMLParser('Simple');
		
		if(!$arkeditorXML->loadFile($ARKManifestFile)) 
		{
			ARKHelper::error( 'Editor Install: '.JText::_('COM_ARKEDITOR_LAYOUT_MANAGER_NOT_LOAD_ARK_MANIFEST'));
			return;
		}
 
 		$ARKManifest = $arkeditorXML->document;
		
		$paramsElement = $ARKManifest->config[0]->fields[0]->fieldset[0];
		
		foreach($names as $name)
		{
			foreach ($paramsElement->children() as $param)
			{
				if($param->attributes('name') == 'toolbar')
				{
					foreach($param->children() as $child)
					{
					   if($child->attributes('value') == $name)
					   {
							$param->removeChild($child); 
							break;
						}
					}
				}
				if($param->attributes('name') == 'toolbar_ft')
				{
					foreach($param->children() as $child)
					{
					   if($child->attributes('value') == $name)
					   {
							$param->removeChild($child); 
							break;
						}
					}
					break;
				}
			}
		}

		$ARKOutputXMl = $ARKManifest->toString();

		if(!JFile::write($ARKManifestFile,$ARKOutputXMl)) //Write to editor manifest file 
		{
			ARKHelper::error( 'Toolbar Delete: '.JText::_('COM_ARKEDITOR_LAYOUT_MANAGER_NOT_WRITE_ARK_MANIFEST'));
		}
	}

	private function _updateEditorToolbarOption($toolbars)
	{
	   // get editor installfile
		$ARKManifestFile = JPATH_PLUGINS . DS . 'editors' . DS . 'arkeditor' . DS . 'arkeditor.xml';
		
		$arkeditorXML = ARKHelper::getXMLParser('Simple');
		if(!$arkeditorXML->loadFile($ARKManifestFile)) 
		{
			ARKHelper::error( 'Editor Install: '.JText::_('COM_ARKEDITOR_LAYOUT_MANAGER_NOT_LOAD_ARK_MANIFEST'));
			return;
		}

 		$ARKManifest = $arkeditorXML->document;

 		$paramsElement = $ARKManifest->config[0]->fields[0]->fieldset[0];

		foreach($toolbars as $toolbar)
		{
			foreach ($paramsElement->children() as $param)
			{
				if($param->attributes('name') == 'toolbar')
				{
					foreach($param->children() as $child)
					{
					   if($child->attributes('value') == $toolbar->oldname)
					   {
							$child->removeAttribute('value');
							$child->addAttribute('value',$toolbar->name);
							$child->setData($toolbar->title); 
							break;
						}
					}
				}
				if($param->attributes('name') == 'toolbar_ft')
				{
					foreach($param->children() as $child)
					{
					   if($child->attributes('value') == $toolbar->oldname)
					   {
							$child->removeAttribute('value');
							$child->addAttribute('value',$toolbar->name);
							$child->setData($toolbar->title); 
							break;
						}
					}
					break;
				}
			}
		}

		$ARKOutputXMl = $ARKManifest->toString();

		if(!JFile::write($ARKManifestFile,$ARKOutputXMl)) //Write to editor manifest file 
		{
			ARKHelper::error( 'Toolbar Update: '.JText::_('COM_ARKEDITOR_LAYOUT_MANAGER_NOT_WRITE_ARK_MANIFEST'));
		}
	}
	
	public function onApply($id,$name,$oldname,$title,$isNew)
	{
	 	$this->onSave($id,$name,$oldname,$title,$isNew);
	}
	
	static function getEditorPluginConfig()
	{
	    static $config;
		       
        if(!isset($config))
        {
			$plugin = JPluginHelper::getPlugin('editors','arkeditor');
            if(!$plugin)
                return new JObject;						
		   	$config = new JRegistry($plugin->params);
			//lets decode parameters
			$toolbars =  base64_decode($config->get('toolbars'));
			$config->set('toolbars',json_decode($toolbars,true));
	  	 
			return 	$config;
        }  
        
        return $config;
    }
	
}