<?php
/*------------------------------------------------------------------------
# Copyright (C) 2012-2015 WebxSolution Ltd. All Rights Reserved.
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
class PlgSystemInlineHistory extends JPlugin
{

	public $app;
	public $db;

	public function onAfterRoute()
	{
		//Inline editing is only enabled for frontend editing	
	
        $user = JFactory::getUser();
		
		if($this->app->isAdmin())
		{	
			return;
		}	
                
        //if user is guest lets bail
		if($user->get('guest'))
		{
			return;
		}
		
		$cParams = JComponentHelper::getParams('com_arkeditor');
		if(empty($cParams) ||!$cParams->get('enable_inline',true))
		{
			return;
		}	

       if(!JPluginHelper::isEnabled('editors','arkeditor'))
            return;	
		
        if(!JPluginHelper::isEnabled('system','inlinecontent'))
            return;	

        if(!$this->app->input->get('ark_inine_enabled',true))
            return;	
				
		
		$query = $this->db->getQuery(true);		
		$query->select(array('element','context'))
			->from('#__ark_editor_inline_views')
			->where('context != '.$this->db->quote('article'))
			->where($this->db->quoteName('parent'). ' IS NULL');
		
		$this->db->setQuery($query);
		$inlineElements = $this->db->loadObjectList();
		
			
		if(!empty($inlineElements))
		{	
			foreach($inlineElements as $inlineElement)
			{
				$option = $inlineElement->element;
				$typeAlias = $option.'.'. $inlineElement->context;
				$type = JTable::getInstance('Contenttype');
				$type->load(array('type_alias'=>$typeAlias));
				
				if($type->type_id)
				{			
					$params = JComponentHelper::getParams($option);

					$tableInfo = json_decode($type->table);
					$JTableName = ucfirst($tableInfo->special->prefix).ucfirst($tableInfo->special->type);
						
					JObserverMapper::addObserverClassToClass('JTableObserverContenthistory', $JTableName, array('typeAlias' => $typeAlias));
					$component = JComponentHelper::getComponent($option);
					$component->params->set('save_history',1); // Force versioning for inline editing
				
					//load category type as well if it exists
					
					$catTypeAlias = $option.'.category';
					$cattype = JTable::getInstance('Contenttype');
					$cattype->load(array('type_alias'=>$catTypeAlias));
					
					if($cattype->type_id)
					{			
						$tableInfo = json_decode($cattype->table);
						$CatTableName = ucfirst($tableInfo->special->prefix).ucfirst($tableInfo->special->type);
						JObserverMapper::addObserverClassToClass('JTableObserverContenthistory', $CatTableName, array('typeAlias' => $typeAlias));
					}
					
					
				}
			}
		}	
	}
}
