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
 *Ark inline content Module Context Class
 *
 * @package     Inlinecontent.Contexts
 * @subpackage  Inlinecontent.contexts.Module
 */
 
class ARKContextsModuleModule extends ARKContextsBase
{


	public function __construct($id)
	{
		$this->table = 	JTable::getInstance('module');
        $this->table->load($id);
        parent::__construct($id);	
	}	


    public function get()
	{
		if($this->id == null)
			return parent::get();	
		
	    $this->table->articletext = $this->table->content;

		return parent::get();	
	}
	
	
	
	public function triggerContentPlugins($rawText)
	{
		
		$item = new stdclass;
					
		$item->text = '';
		if(isset($rawText))
		{
			$item->text = $rawText;
			$params = new JObject;
			$params->set('inline',false); //set this so that we don't trigger the inline content plugins
			$dispatcher	= JEventDispatcher::getInstance();
			JPluginHelper::importPlugin('content');
			$dispatcher->trigger('onContentPrepare', array ('mod_custom.content', &$item, &$params, 0));
		}	
			
		return array( 'data'=>$item->text);
	}
			
	public function save($data,$type = 'body')
	{
		if($this->id == null)
			return array( 'title'=>'','data'=>'');	


		if(isset($data['articletext']))
				$data['content'] = base64_decode($data['articletext']);
			else
				$data['title'] = strip_tags($data['title']); 
		
		$this->table->save($data);
		
		//We need to process data as we are sending it back to the client
		
		$item = $this->table;
		$item->text = $item->content;
		
		//let's detect if any plugin tags are beig used 
		//if so let's inform the system to warn the user
		$message = $this->detectPluginTags($item->text);
		
		$params = new JObject;
		$params->set('inline',false); //set this so that we don't trigger the inline content plugins
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('content');
		$dispatcher->trigger('onContentPrepare', array ('mod_custom.content', &$item, &$params, 0));
												
		return array( 'title'=>$item->title,'data'=>$item->text,'message'=>$message);	
	}
	
	public function version($versionId,$type)
	{
				
		$historyTable = JTable::getInstance('Contenthistory');
		$historyTable->load($versionId);
		$rowArray = JArrayHelper::fromObject(json_decode($historyTable->version_data));
			
		$item = $this->table;

		$item->bind($rowArray);	
		if($type == 'title')
		{
			return array( 'data'=>$item->title);
		}
		$text = '';
		
		$text = $item->content;
	
		return array( 'data'=>$text);
		
	}
	
}
