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
 *Ark inline context  base class
 *
 * @package     Inlinecontent.Contexts
 * @subpackage  Inlinecontent.contexts.base
 */
error_reporting(E_ERROR & ~E_NOTICE);
 
abstract class ArkContextsBase
{

	protected $table;
	protected $id = null;
	protected $typeAlias = '';
	
	public function __construct($id)
	{
		$this->id = $id;
	}
    public function get()
	{
		if( $this->id == null)
			return array( 'title'=>'','data'=>'');	
		
		$item = null;
			
		$item = $this->table; //Ideally would call content model but cannot call directly in this context

		return array( 'title'=>$item->title,'data'=>$item->articletext);	
	}
		
	
	public function triggerContentPlugins($rawText) {}
	
	public function save($data,$type = 'body'){}
	
	public function version($versionId,$type)
	{
		$text = '';		
		return array( 'data'=>$text);
	}
	
	
	protected function detectPluginTags($text)
	{
		static $detect = false;	
		
		$message = '';
		
		if(preg_match('/\{.*?\}/',$text) && !$detect)
		{	
			$message = "Please note: You may have to refresh this page to see the fully rendered content";
			$detect = true;
		}		
			
		return $message;
	}	
}
