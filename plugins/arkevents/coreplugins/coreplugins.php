<?php
/*------------------------------------------------------------------------
# Copyright (C) 2014-2015 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined('_JEXEC') or die;

/**
 *Ark inline content  System Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  System.inlineContent
 */
class PlgArKEventsCorePlugins extends JPlugin
{

	public $db;


	public function onInstanceCreated(&$params)
	{
		
		$query = $this->db->getQuery(true);
		$query->select('name')
			->from('#__ark_editor_plugins')
			->where('iscore = 1')
			->where('published = 0')
			->where($this->db->quoteName('type').' = '.$this->db->quote('plugin'));

		$this->db->setQuery( $query );
		$plugins = $this->db->loadColumn();
		
		//since all core plugins are loaded in by default we only need to remove core plugins if they are unpublished 
		if($plugins)
			return "
					editor.on( 'configLoaded', function() {

					
					//load in extra plugins
					if(	this.config.removePlugins)
						this.config.removePlugins += ',".implode(',',$plugins)."';
					else
						this.config.removePlugins = '".implode(',',$plugins)."';
			
					});
				";
		return  null;	
	}
}
