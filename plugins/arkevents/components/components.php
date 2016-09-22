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
 * @subpackage  ArkEditor.components
 */

class PlgArKEventsComponents extends JPlugin
{

	public $app,$db;


	public function onBeforeLoadToolbar(&$params)
	{
		
		$query = $this->db->getQuery(true);
		$query->select('name')
			->from('#__ark_editor_plugins')
			->where('iscore = 0')
			->where('published = 0');

		$this->db->setQuery( $query );
		$plugins = $this->db->loadColumn();
		
		$defaults = array(strtolower($params->get('toolbar','full')),strtolower($params->get('toolbar_ft','full')) );

		$this->db = JFactory::getDBO();
		$query = $this->db->getQuery(true);
		$query->select('name,params')
				->from('#__ark_editor_toolbars')
				->where('published = 1')
				->where('LOWER(name) NOT IN('.$this->db->quote(implode($this->db->quote(','),$defaults)).')')
				->order('id DESC');
		$this->db->setQuery($query);
		$toolbars = $this->db->loadObjectList();
		
		if(empty($toolbars))
			return;
			
		$component = $this->app->input->get('option','');
			
		foreach($toolbars as $toolbar)
		{
				$tparams = new JRegistry($toolbar->params);
				$components = $tparams->get('components',array(0));
				
				if(in_array($component,$components,true))
				{
					$name = $toolbar->name;
					$params->set('toolbar',$name);
					$params->set('toolbar_ft',$name);
					break;
				}

		}		
		return  null;	
	}
}