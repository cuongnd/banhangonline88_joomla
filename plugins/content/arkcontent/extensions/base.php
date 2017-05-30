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
 * Ark Inline content editing Plugin
 *
 * @package     extensions.ARKExtensions
 * @subpackage  extensions.ARKExtensions
 */
abstract class ARKExtensionsBase
{

	protected $inline_allowed_contexts;

	protected $id;
	
	protected $context;
	
	protected $dataContext;
	
	protected $article;
	
	public $params;
	
	protected $type;
	
	protected $db;
	
	protected $app;
	
	protected static $instance;
	
	protected $enableEditableTitles = true;

    protected static $staticParams = null;
	
		
	public  function __construct( $context, &$item, &$params)
	{
		$this->context = $context;
		$this->item = $item;
		if(isset($this->item->id))
			$this->id = $this->item->id;
		$this->params = $params;
		$this->type;
		$this->db = JFactory::getDBO();
		$this->app = JFactory::getApplication();
		$option = $this->app->input->get('arkoption');
		$query = $this->db->getQuery(true);	
		$query->select('context')
			->from('#__ark_editor_inline_views')
			->where('element = '.$this->db->quote($option));
		if($query instanceof JDatabaseQueryLimitable)
				$query->setLimit(1);	
		$this->db->setQuery($query);	
		$dataContext = $this->db->loadResult();	
		if(!empty($dataContext))
			$this->dataContext = $dataContext;
		else
			$this->dataContext = str_replace('com_','',$option);

	}	
	

	public function prepare()
	{
				
		//Are we allowed to edit in this context 
		
		if(!in_array($this->context,$this->inline_allowed_contexts) )    
		{
			return false;
		}
		
		if(isset($this->id))
		{		
			if(isset($this->item->title))
			{	
				if($this->app->input->get($this->context.'.'.crc32($this->item->title.''.$this->id),0))
					return false;
			}

            if(isset($this->params))
			{
				$this->params->set('show_readmore_title',0);
			}
					
			if(isset($this->item->title) && $this->enableEditableTitles && $this->params->get('enable_editable_titles',1))
			{
				$this->item->title = '{div class=__ARKQUOTE__editable__ARKQUOTE__ data-id=__ARKQUOTE__'.$this->id.'__ARKQUOTE__ data-context=__ARKQUOTE__'.$this->dataContext.'__ARKQUOTE__ data-type=__ARKQUOTE__title__ARKQUOTE__ data-itemtype=__ARKQUOTE__'.$this->type.'__ARKQUOTE__ contenteditable=__ARKQUOTE__true__ARKQUOTE__ style=__ARKQUOTE__display:inline;__ARKQUOTE__}'.$this->item->title.'{/div}'; 
			}	
			
			    //body
			$dispatcher = JEventDispatcher::getInstance();
			JPluginHelper::importPlugin('inline');
			$dispatcher->trigger('editable',array(&$this->item->text, array('id'=>$this->id,'context'=>$this->dataContext,'itemtype'=>$this->type)));
		}
		
		$this->item->procesedInline = true;

        
        if(!is_null(static::$staticParams))
        {
            $this->params = clone static::$staticParams;
        }
		
		return true;
	}	
	
		
	public function display()
	{
		
		//is this item already processed
		if(isset($this->item->procesedInline) && $this->item->procesedInline)
			return false;	
		
		//Are we allowed to edit in this context 
		if(!in_array($this->context,$this->inline_allowed_contexts) )    
		{
			return false;
		}
		
    	if(isset($this->id))
		{
		
			if(isset($this->item->title))
			{	
				if($this->app->input->get($this->context.'.'.crc32($this->item->title.''.$this->id),0))
					return false;
			}

			if(isset($this->params))
			{
				$this->params->set('show_readmore_title',0);
			}

			if(isset($this->item->title) && $this->enableEditableTitles && $this->params->get('enable_editable_titles',1))
			{
				$this->item->title = '{div class=__ARKQUOTE__editable__ARKQUOTE__ data-id=__ARKQUOTE__'.$this->id.'__ARKQUOTE__ data-context=__ARKQUOTE__'.$this->dataContext.'__ARKQUOTE__ data-type=__ARKQUOTE__title__ARKQUOTE__ data-itemtype=__ARKQUOTE__'.$this->type.'__ARKQUOTE__ contenteditable=__ARKQUOTE__true__ARKQUOTE__ style=__ARKQUOTE__display:inline;__ARKQUOTE__}'.$this->item->title.'{/div}'; 
			}	
								
			//body
			$dispatcher = JEventDispatcher::getInstance();
			JPluginHelper::importPlugin('inline');
			$dispatcher->trigger('editable',array(&$this->item->text, array('id'=>$this->id,'context'=>$this->dataContext,'itemtype'=>$this->type)));
		 
            if(!is_null(static::$staticParams))
            {
                $this->params = clone static::$staticParams;
            }
		
	        return true;	
			
		}
	}
}