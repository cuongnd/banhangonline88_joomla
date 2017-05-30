<?php
/*------------------------------------------------------------------------
# Copyright (C) 2012-2015 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined('_JEXEC') or die('Restricted access');

class pkg_arkeditorInstallerScript
{
	
	private $ajax_plugins = array
	(
		'arktreelink',
		'inlinecontent',
		'arktypography',
		'arktemplates',
		'inlinemodestatelistener',
		'arkbootstrap',
		'arkimageupload',
		'arkimageresize'
	);
	
	private $arkeditor_plugins = array
	(
		'link',
		'sourcedialog',
		'codemirror',
		'xml',
		'ajax',
		'stylesoverride',
		'autostylesheetparser',
		'imagemanager',
		'document',
		'savecontent',
		'versions',
		'treelink',
		'pagebreak2',
		'readmore',
		'arkabout',
		'uiheader',
		'arkmedia',
		'tabledefinition',
		'autosave',
		'preloader',
		'styles',
		'quicktable',
		'xmltemplates',
		'toolbarswitcher',
		'close',
		'paragraphdataprocessor',
		'email',
		'browsebrowser',
		'browsepopup',
		'video',
		'audio',
		'magicline',
		'widget',
		'lineutils',
		'contentscss',
		'filetools',
		'notification',
		'notificationaggregator',
		'uploadwidget',
		'uploadimage',
		'focusmanager',
		'dndhandler',
		'sefresourceprocessor',
		'arkmediabutton',
		'corecss',
		'imageresize',
		'uploadinline'
	);
	
	private $content_plugins = array
	(
		'arkredirect',
		'arkcontent',
		'arkfilter'
	);
	
	private $arkevents_plugins = array
	(
		'acl',
		'components',
		'configuration',
		'coreplugins',
		'extraplugins',
		'mobile',
		'pastefromword',
		'modal',
		'autostylesheetfilter',
		'autocssfilter',
		'magicline',
		'env',
		'element'
	);
	
	
	private $editors = array
	(
		'arkeditor'
	);
	
	
	private $extension_plugins = array
	(
		'arkeditor',
		'inline'
	);
	
	private $installer_plugins = array
	(
		'arkeditor'
	);

	private $quickicon_plugins = array
	(
		'arkquickicon'
	);

	private $system_plugins = array
	(
		'imageeditor',
		'arkeditoruser',
		'arkmedia',
		'inlinecontent',
		'arktypography',
		'arkversions',
		'modulehistory',
		'sefbase',
		'arkmodal',
		'k2extrafields',
		'inlinehistory',
		'arkbootstrap',
		'arkmediaobserver'
	);

	private $inline_plugins = array
	(
		'arkeditor'
	);
	
	
	public function preflight($type, $parent)
	{
		//Workaround I shouldn't have to do
		$app 	= JFactory::getApplication();
		$db 	= JFactory::getDBO();
		
		$sql = $db->getQuery( true );
		$sql->select( '1' )
			->from('#__extensions')
			->where( 'name = '.$db->quote('com_arkeditor') );
			
		if( !$db->setQuery( $sql )->loadResult() )
		{
			
	
			$sql = $db->getQuery( true ); //Delete if we still need to
			$sql->delete( '#__menu' )
				->where( 'title like '.$db->quote('COM_ARKEDITOR%') );
			$db->setQuery( $sql )->query();
			
			//delete any files
			
			if(JFolder::exists(JPATH_SITE.'/components/com_arkeditor'))
				JFolder::delete(JPATH_SITE.'/components/com_arkeditor');
			if(JFolder::exists(JPATH_ADMINISTRATOR.'/components/com_arkeditor'))	
				JFolder::delete(JPATH_ADMINISTRATOR.'/components/com_arkeditor');
			
			
			//reset to install path
			$prop =  new ReflectionProperty('JInstallerAdapterPackage', 'route');
			$prop->setAccessible(true);
			$prop->setValue($parent,'install');

		}
		
	}
	
	public function update($parent) 
    {
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		
		$query = $db->getQuery(true)
			->select('update_site_id')
			->from('#__update_sites')
			->where($db->quoteName('name') . ' = ' . $db->quote('Ark Editor'))
			->where($db->quoteName('type') .' = ' . $db->quote('collection'));
			
		$db->setQuery($query);
		$update_site_id = (int) $db->loadResult();

		// If it does exist, change type as the Joomla extension plugin will not do this for us.
		if ($update_site_id)
		{
			//Update update_sites
			$query = $db->getQuery(true)
				->update('#__update_sites')
				->set($db->quoteName('type') . ' = ' . $db->quote('extension'))
				->where('update_site_id = ' .(int) $update_site_id);
			$db->setQuery($query);
			$db->query();
		}
	}
	
	public function postflight($parent)
	{
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();

		//Publish ajax plugins

		$query = $db->getQuery(true);
		$query->update('#__extensions')
			->set('enabled = 1')
			->where('folder = '.$db->quote('ajax'))
			->where('element IN ('. $db->quote(implode($db->quote(','),$this->ajax_plugins),false).')');
		
		$db->setQuery($query);

		if(!$db->query())
		{
			$app->enqueueMessage( 'Failed to publish ajax plugins for ARK' );
		}

		//Publish arkevents plugins

		$query = $db->getQuery(true);

		$query->update('#__extensions')
			->set('enabled = 1')
			->where('folder = '.$db->quote('arkevents'))
			->where('element IN ('. $db->quote(implode($db->quote(','),$this->arkevents_plugins),false).')');

		$db->setQuery($query);
		if(!$db->query())
			$app->enqueueMessage( 'Failed to publish arkeditor plugins for ARK' );		

		//Publish arkeditor plugins

		//post processing of arkeditor plugins
		foreach($this->arkeditor_plugins as $name)
		{
			/*
			 * ---------------------------------------------------------------------------------------------
			 * Database Processing Section
			 * ---------------------------------------------------------------------------------------------
			 */

			$row = JTable::getInstance('extension');
			$row->load(array('folder'=>'arkeditor','element'=>$name));
			$row->enabled = 1;
			
			//skip if we cannot find plugin
			if(!$row->extension_id)
				continue;	
				
			//Skip if plugin has not been installed
			if(!JFolder::exists(JPATH_PLUGINS.'/arkeditor/'.$name))
				continue;	
			
			if(!$row->store())
				throw new Exception('Failed to publish plugin');



					
			/*
			*---------------------------------------------------------------------------------------------------
			* Integrate with Ark Component
			*---------------------------------------------------------------------------------------------------
			*/

			$manifest = simplexml_load_file(JPATH_PLUGINS.'/arkeditor/'.$name.'/'.$name.'.xml');

			if(!$manifest)
				throw new Exception('Failed to find manifest for '.$name.' plugin in arkeditor group');

			require_once JPATH_ADMINISTRATOR.'/components/com_arkeditor/tables/plugin.php';

			$jckRow = JTable::getInstance('plugin','ARKTable');
			$jckRow->load(array('name'=>$name));
			$icon 				= $manifest->icon;
			$title				= ucFirst($name);
			$jckRow->title 		= (!empty($icon)  ? (string) $title : '');
			$jckRow->name		= $name;
			$jckRow->type 		= 'plugin';
			$jckRow->row	 	= 4;
			$jckRow->published 	= 1;
			$jckRow->editable 	= 1;
			$jckRow->icon 		= (!empty($icon) ? (string) $icon : '');
			$jckRow->iscore 	= 0;
			$jckRow->params 	= $this->getParams($manifest);

			
			try{
			
				if(!$jckRow->save(array('editable'=>1)))
					throw new Exception('Failed to insert record into JCK Plugins table');	

			
			}
			catch(Exception $e)
			{
				throw new Exception($e);
				//throw new Exception('Failed to insert record into JCK Plugins table due to unexpected error');	
				
			}
			
			//Update Extension table with reference to this new record
						
			$row->custom_data = $jckRow->id;
			if(!$row->store())
				throw Exception('Failed to add plugin reference to extension record');
		}

		//Publish content plugins

		$query = $db->getQuery(true);
		$query->update('#__extensions')
			->set('enabled = 1')
			->set('ordering = -1')
			->where('folder = '.$db->quote('content'))
			->where('element IN ('. $db->quote(implode($db->quote(','),$this->content_plugins),false).')');	

		$db->setQuery($query);

		if(!$db->query())
			$app->enqueueMessage( 'Failed to publish content plugins for ARK' );
		
		
		$query = $db->getQuery(true);
		$query->update('#__extensions')
			->set('ordering = 9999')
			->where('folder = '.$db->quote('content'))
			->where('element = '.$db->quote('arkcontent'));	

		$db->setQuery($query);

		if(!$db->query())
			$app->enqueueMessage( 'Failed to update ordering field for arkcontent plugins for ARK' );
		
		//Publish editor plugins

		$query = $db->getQuery(true);
		$query->update('#__extensions')
			->set('enabled = 1')
			->where('folder = '.$db->quote('editors'))
			->where('element IN ('. $db->quote(implode($db->quote(','),$this->editors),false).')');	

		$db->setQuery($query);

		if(!$db->query())
			$app->enqueueMessage( 'Failed to publish editor plugins for ARK' );	

		//Publish extension plugins

		$query = $db->getQuery(true);
		$query->update('#__extensions')
			->set('enabled = 1')
			->where('folder = '.$db->quote('extension'))
			->where('element IN ('. $db->quote(implode($db->quote(','),$this->extension_plugins),false).')');
			
		$db->setQuery($query);

		if(!$db->query())
			$app->enqueueMessage( 'Failed to publish extension plugins for ARK' );

		//Publish installer plugins

		$query = $db->getQuery(true);
		$query->update('#__extensions')
			->set('enabled = 1')
			->where('folder = '.$db->quote('installer'))
			->where('element IN ('. $db->quote(implode($db->quote(','),$this->installer_plugins),false).')');
			
		$db->setQuery($query);	
		
		if(!$db->query())
			$app->enqueueMessage( 'Failed to publish installer plugins for ARK' );

		//Publish QuickIcon plugins

		$query = $db->getQuery(true);
		$query->update('#__extensions')
			->set('enabled = 1')
			->where('folder = '.$db->quote('quickicon'))
			->where('element IN ('. $db->quote(implode($db->quote(','),$this->quickicon_plugins),false).')');
			
		$db->setQuery($query);	
		
		if(!$db->query())
			$app->enqueueMessage( 'Failed to publish system plugins for ARK' );	

		//Publish system plugins

		$query = $db->getQuery(true);
		$query->update('#__extensions')
			->set('enabled = 1')
			->where('folder = '.$db->quote('system'))
			->where('element IN ('. $db->quote(implode($db->quote(','),$this->system_plugins),false).')');

		$db->setQuery($query);

		if(!$db->query())
			$app->enqueueMessage( 'Failed to publish system plugins for ARK' );
		
		//Publish inline plugins

		$query = $db->getQuery(true);
		$query->update('#__extensions')
			->set('enabled = 1')
			->where('folder = '.$db->quote('inline'))
			->where('element IN ('. $db->quote(implode($db->quote(','),$this->inline_plugins),false).')');

		$db->setQuery($query);

		if(!$db->query())
			$app->enqueueMessage( 'Failed to publish inline plugins for ARK' );
		
		// @bug J3 > Doesn't Allow for Adding Files to the Package Installer for Adding Features on Update/Upgrade so Suppress Messages
		$queue = $app->getMessageQueue();

		if( count( $queue ) )
		{
			foreach( $queue as $key => $message )
			{
				// If We Find a Message Referring to the Above Bug
				if( JText::_( 'JLIB_INSTALLER_ERROR_NOTFINDXMLSETUPFILE' ) === $message['message'] && $message['type'] === 'warning' )
				{
					// Can't Get Hold of Queue Thanks to JLogger so Hide All Warning Messages (no unique css class)
					echo '<style>
							#system-message-container .alert { display : none; }
							#system-message-container .alert-block,
							#system-message-container .alert-success,
							#system-message-container .alert-info,
							#system-message-container .alert-danger,
							#system-message-container .alert-error { display : block; }
						</style>';

					break;
				}//end if
			}//end foreach
		}//end if
	}
	
	private function getParams($manifest)
	{
		// Validate that we have a fieldset to use
		if (!isset($manifest->config->fields->fieldset))
		{
			return '{}';
		}
		// Getting the fieldset tags
		$fieldsets = $manifest->config->fields->fieldset;

		// Creating the data collection variable:
		$ini = array();

		// Iterating through the fieldsets:
		foreach ($fieldsets as $fieldset)
		{
			if (!count($fieldset->children()))
			{
				// Either the tag does not exist or has no children therefore we return zero files processed.
				return null;
			}

			// Iterating through the fields and collecting the name/default values:
			foreach ($fieldset as $field)
			{
				// Check against the null value since otherwise default values like "0"
				// cause entire parameters to be skipped.

				if (($name = $field->attributes()->name) === null)
				{
					continue;
				}

				if (($value = $field->attributes()->default) === null)
				{
					continue;
				}

				$ini[(string) $name] = (string) $value;
			}
		}

		return json_encode($ini);
	}

}