<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * @package		ARK
 * @subpackage	arkeditor
 * @since		1.0.1
 */

class plgEditorsArkeditorInstallerScript
{
	/**
	 * Post-flight extension installer method.
	 *
	 * This method runs after all other installation code.
	 *
	 * @param	$type
	 * @param	$parent
	 *
	 * @return	void
	 * @since	1.0.3
	 */
	 
	function postflight($type, $parent)
	{
		// Display a move files and folders to parent.
		
			
		jimport('joomla.filesystem.folder');
			
		$srcBase = JPATH_PLUGINS.'/editors/arkeditor/layouts/joomla/'; 
		$dstBase = JPATH_SITE.'/layouts/joomla/';
		
		$folders = JFolder::folders($srcBase);
		
		$manifest = $parent->getParent()->getManifest();	
		$attributes = $manifest->attributes();	
				
		$method = ($attributes->method ? (string)$attributes->method : false); 
		
	
		foreach($folders as $folder)
		{
		
			if($method !='upgrade')
			{
				if(JFolder::exists($dstBase.$folder))
					JFolder::delete($dstBase.$folder);
			}
			
			JFolder::copy($srcBase.$folder,$dstBase.$folder,null, true);
		}
		
		if($type == 'install')
		{
			//update $db
			$db = Jfactory::getDBO();
			
			$toolbars = base64_encode('{"back":[[ "Templates" ],[ "Cut","Copy","Paste","PasteText","PasteFromWord" ] ,["SelectAll","Scayt" ] ,["Bold","Italic","Underline","Strike","Subscript","Superscript","-","RemoveFormat" ] ,[ "NumberedList","BulletedList","Outdent","Indent","-","Blockquote","CreateDiv","JustifyLeft","JustifyCenter","JustifyRight","JustifyBlock","BidiLtr","BidiRtl" ],[ "Link","Document","Unlink","Anchor","Email" ],	[ "Image","Flash","Table","Smiley","SpecialChar","PageBreak","Iframe" ],[ "Styles","Format","Font","FontSize" ],[ "TextColor","BGColor" ],[ "Maximize", "ShowBlocks","-","About" ]],"front":[["Source","ShowBlocks","Scayt"],["Undo","Redo","Cut","PasteText","PasteFromWord"],["Bold","Italic","Underline","Blockquote","RemoveFormat"],["NumberedList","BulletedList"],["JustifyLeft","JustifyRight","JustifyBlock"],["Anchor","Email","Unlink","Link","Document"],["Styles","Format","Image","Templates","Table","CreateDiv","About"]],"inline" :[["Sourcedialog","Bold","NumberedList","BulletedList"],["PasteText","Image","Link","Document"],["Format"],["Readmore"],["Save"],["Versions"],["Close"] ],"title":[["Save"],["Cut","Copy","PasteText"],["Undo"],["Close"]],"image":[["Save"],["Image"],["Link","Document"],["Close"]],"mobile":[["Bold"],["Link"],["Image"],["Save"],["Versions"],["Close"]]}');

			$query = $db->getQuery(true);
			$query->select('params')
			->from('#__extensions')
			->where('folder = '.$db->quote('editors'))
			->where('element = '.$db->quote('arkeditor'));
				
			$db->setQuery($query);
			$params = $db->loadResult();	
			
			if($params === false)
				throw new Exception('Failed to retrieve parameters from Editor');

			if(!$params)
				$params = '{}';
			
			$params = new JRegistry($params);
			
			$params->set('toolbars',$toolbars);
				
			$query->clear()
			->update('#__extensions')
			->set('params= '.$db->quote($params->toString()))
			->where('folder = '.$db->quote('editors'))
			->where('element = '.$db->quote('arkeditor'));
				
			$db->setQuery($query);
			if(!$db->query())
				throw new Exception('Failed to update parameters for Editor');
			
		}
	}
	
	function uninstall($parent) 
	{
		jimport('joomla.filesystem.folder');
		
		$app = JFactory::getApplication();
		
		$db = JFactory::getDBO();

		$folder =  JPATH_SITE.'/layouts/joomla/arkeditor';
		
		if(JFolder::exists($folder) && !JFolder::delete($folder)) {
			$app->enqueueMessage( JText::_('Unable to delete Arkeditor Layouts') );
		}
		
	}
		
}