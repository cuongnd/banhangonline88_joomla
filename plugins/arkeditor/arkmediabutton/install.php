<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2015 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * @package		ARK
 * @subpackage	ArkEditor
 * @since		1.0.1
 */

 class plgArkEditorArkMediaButtonInstallerScript
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
	 
	private $_allowed = array
	 (
		'front',
		'inline',
		'back'
	);
 
	private $_pluginTitle = 'Arkmediabutton';
 
	 
	function postflight($type, $parent)
	{
		
		if($type != 'install')
		 return;
		
		
		//update $db
		$db = Jfactory::getDBO();
		
		$query = $db->getQuery(true);
			$query->select('params')
			->from('#__extensions')
			->where('folder = '.$db->quote('editors'))
			->where('element = '.$db->quote('arkeditor'));
				
		$db->setQuery($query);
		$params = $db->loadResult();	
		
		
		if($params === false || !$params)
		{	
				$app->enqueueMessage('Adding ArkMedia Button: Failed to retrieve parameters from Editor');
				return;	
		}
		
		$params = new JRegistry($params);	
		
		$toolbars = json_decode(base64_decode($params->get('toolbars')),true);
		
		
		require_once(JPATH_ADMINISTRATOR.'/components/com_arkeditor/helper.php');
		
		foreach($this->_allowed as $allow)
		{
			$toolbar = $toolbars[$allow];
			
			if(!ARKHelper::in_array($this->_pluginTitle,$toolbar))
					$toolbar[] = array($this->_pluginTitle);
			
			$toolbars[$allow] = $toolbar;
		} 
		

		//update database
		$app = JFactory::getApplication();
		
		$params->set('toolbars', base64_encode(json_encode($toolbars)));
		$row = JTable::getInstance('extension');
		$row->load(array('folder'=>'editors','element'=>'arkeditor'));
		$row->bind(array('params'=>$params->toArray()));

		if(!$row->store())
				$app->enqueueMessage( 'Adding ArkMedia Button: Failed to save Ark Editor\'s parameters');
	
		
	}
	
}
