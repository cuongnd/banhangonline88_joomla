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
 *Ark Editor Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  ArkEditor.SaveContent
 */
class PlgArkEditorUIHeader extends JPlugin
{
	
	public $db,$app;
	
	private $allowed_options = array
	(
		'show_versions',
		'show_new',
		'show_save',
		'show_undo',
		'show_redo',
		'show_find',
		'show_close',
		'show_source',
		'show_design',
		'show_maximum'
	);
	
	public function onBeforeInstanceLoaded(&$params)
	{
	
		$toolbar = $params->get('toolbar');
		
		if($this->app->isSite())
		{
			$toolbar = $params->get('toolbar_ft');
		}
			
		$query = $this->db->getQuery(true);
		$query->select('params')
			->from('#__ark_editor_toolbars')
			->where('name = '.$this->db->quote($toolbar));
        $this->db->setQuery( $query );
		$raw = $this->db->loadResult();
		
		$temp = new JRegistry($raw);
			
		$attribs = JComponentHelper::getParams('com_arkeditor');
		
        if($attribs->get('show_versions',false) === false)
        {
             $default_json = '{"show_versions":"1","show_new":"1","show_save":"1","show_undo":"1","show_redo":"1","show_find":"1","show_close":"1","show_source":"1","show_design":"1","show_maximum":"1","components":[]}';
             $default = new JRegistry($default_json);
             $attribs->merge($default); //set default values for Global
        }
               
        $attribs->merge($temp); // override Global values with toolbar specific values
		




		$data  = $attribs->toArray();
		$buttonStates = array();
		

		foreach ($data as $k => $v)
		{
			if (in_array($k,$this->allowed_options))
			{
				$buttonStates[$k] = $v ? 'cke_show' : 'cke_hide';
				
			}
		}
		
		$option = $this->app->input->get('option'); 
		
		if(!in_array($option,array('com_content','com_categories')))
				$buttonStates['show_versions'] = "cke_hide";
	
		if(empty($buttonStates))
			return;

		return " 	
				editor.on( 'configLoaded', function()
				{
					editor.config.headerButtons_states = ".json_encode($buttonStates,JSON_FORCE_OBJECT).";
				});";	
	}
	
	public function onInstanceLoaded(&$params){}
		
}