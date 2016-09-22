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
 *Ark inline content Article Context Class
 *
 * @package     Inlinecontent.Contexts
 * @subpackage  Inlinecontent.contexts.ContentArticle
 */
 
class ARKContextsArticleArticle extends ARKContextsBase
{
	
	

	public function __construct($id)
	{
		$this->table = 	JTable::getInstance('content');
		$this->table->load($id);
		parent::__construct($id);
	}	


    public function get()
	{
		if($this->id == null)
			return parent::get();	
		

		$this->table->articletext = $this->table->introtext;

		if (!empty($this->table->fulltext))
		{
			$this->table->articletext .= '<hr id="system-readmore" />' . $this->table->fulltext;
		}
	
		return parent::get();	
	}
	
	
	
	public function triggerContentPlugins($rawText)
	{
		
		$item = new stdclass;
					
		$text = '';
		
		if (isset($rawText))
		{
			$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
			$tagPos = preg_match($pattern, $rawText);
			
			if ($tagPos == 0)
			{
				
				$text = $rawText;
			}
			else
			{
				list ($text, $rawText) = preg_split($pattern, $rawText, 2);
				$text = $text.$rawText;
			}
		}
		
		$item->id = $this->id;	
		$item->text = $text;
		$params = new JObject;
		$params->set('inline',false);
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('content');
		$dispatcher->trigger('onContentPrepare', array ('com_content.article', &$item, &$params, 0));
			
		return array( 'data'=>$item->text);
	}
			
	public function save($data,$type = 'body')
	{
		if($this->id == null)
			return array( 'title'=>'','data'=>'');	
			
		if($type == 'title')
		{
			$data['title'] = strip_tags($data['title']); 
			$data['title'] = html_entity_decode($data['title']);
		}
		if(isset($data['articletext']))
			$data['articletext'] = base64_decode($data['articletext']);	
		
		
		//Get and set current tags data to stop them from being wiped out
		$this->table->tagsHelper = new JHelperTags;
		$this->table->tagsHelper->tags = (array) explode( ',', $this->table->tagsHelper->getTagIds($this->id, 'com_content.article') );
		
		
		$result = $this->table->save($data);
		//We need to process data as we are sending it back to the client
		
		JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_content/models');
		$model = JModelLegacy::getInstance('article','ContentModel');
		$item = $model->getItem($this->id);
			
	
		if ($item->params->get('show_intro', '1') == '1')
		{
			$item->text = $item->introtext.' '.$item->fulltext;
		}
		elseif ($item->fulltext)
		{
			$item->text = $item->fulltext;
		}
		else
		{
			$item->text = $item->introtext;
		}
		
		//let's detect if any plugin tags are being used 
		//if so let's inform the system to warn the user
		$message = $this->detectPluginTags($item->text);
		
		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('content');
		$item->params->set('inline',false); //set this so inline plugin does not pick this up
		$dispatcher->trigger('onContentPrepare', array ('com_content.article', &$item, &$item->params, 0));
		
		return array( 'title'=>html_entity_decode($item->title),'data'=>$item->text,'message'=>$message);
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
		

		
		$text = $item->introtext;
		if (!empty($item->fulltext))
		{
			$text .= '<hr id="system-readmore" />' . $item->fulltext;
		}
	
	
		return array( 'data'=>$text);
		
	}
	
}
