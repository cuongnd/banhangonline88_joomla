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
 
require_once(JPATH_PLUGINS.'/system/inlinecontent/inlinemode.php');
 
class PlgSystemInlineContent extends JPlugin
{

	private $inline_allowed_views = false;
	private $isEnabled = true;
	protected $app;
	protected $db;
	protected $input;
	protected $option;
	protected $context = array('module');
	protected $types = array('module');
	protected $skipProccessing = false;
	protected $_output = '';
	
    
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);
		$this->input = $this->app->input;
	}
	

    public function onAfterRoute()
	{

		//Inline editing is only enabled for frontend editing	
		if ($this->app->isAdmin())
		{
			$this->isEnabled = false;
			return;
		}
      
		if(!JComponentHelper::isInstalled('com_arkeditor'))
		{
			$this->skipProccessing = true;
			$this->isEnabled = false;
			return;
		}
		
	  
		if(!JComponentHelper::isEnabled('com_arkeditor'))
		{	
			$this->skipProccessing = true;
			$this->isEnabled = false;
            $this->app->input->set('ark_inine_enabled',false);	 
			return;
		}
		
		$cParams = JComponentHelper::getParams('com_arkeditor');
		if(!$cParams->get('enable_inline',true))
		{
			$this->skipProccessing = true;
			$this->isEnabled = false;
            $this->app->input->set('ark_inine_enabled',false);	 
			return;
		}	
		
		$componentlist = $cParams->get('component_include_list',array());
		$this->option = $this->input->get('option','');
		
		if(!empty($componentlist))
		{
			if(!in_array($this->option,$componentlist))
			{
				$this->skipProccessing = true;
				$this->isEnabled = false;
				$this->app->input->set('ark_inine_enabled',false);	 
				return;
			}
		}
		
		
		$query = $this->db->getQuery(true);
		

		$query->select(array('element','views','context','types','params'))
			->from('#__ark_editor_inline_views')
			->where('element = '.$this->db->quote($this->option));
			if($query instanceof JDatabaseQueryLimitable)
				$query->setLimit(1);
		$this->db->setQuery($query);	
		$this->inlineElement = $this->db->loadObject();

		
				
	    if(!empty($this->inlineElement))
		{	
			$views = json_decode($this->inlineElement->views);
			if(!empty($views))
				$this->inline_allowed_views = $views;
			
			$types = json_decode($this->inlineElement->types);
			
			//Check to see if we have any children and get types from them
			$query->clear()
				->select('types')
				->from('#__ark_editor_inline_views')
				->where($this->db->quoteName('parent').' = '.$this->db->quote($this->option));
				$this->db->setQuery($query);
				$results = $this->db->loadColumn();
			
			if(!empty($results))
			{
				if(empty($types))
					$types = array();
			
				foreach($results as $result)
					$types = $types + json_decode($result);
			}
			
			if(!empty($types))
				$this->types = array_merge($this->types,$types);
			
			if($this->inlineElement->context)
            	$this->context = array_merge($this->context,array($this->inlineElement->context));
			else //use option instead
				$this->context = array_merge($this->context,array(str_replace('com_','',$this->option)));

             $this->app->input->set('ark_inine_enabled',true);	

	    }
        else
        {
         
             $this->app->input->set('ark_inine_enabled',false);	
        }

		$this->app->input->set('arkoption',$this->option);	 
        
    }

 	public function onAfterDispatch()
	{
          
       //Inline editing is only enabled for frontend editing	
		if ($this->app->isAdmin())
		{
			$this->isEnabled = false;
            $this->app->input->set('ark_inine_enabled',false);	 
			return;
		}
      
        if ($this->skipProccessing)
		{
			$this->isEnabled = false;
            $this->app->input->set('ark_inine_enabled',false);	 
		    return;
		}
        	  
        $user = JFactory::getUser();
                
        //if user is guest lets bail
		if($user->get('guest'))
		{
			$this->isEnabled = false;
            $this->app->input->set('ark_inine_enabled',false);	 
			return;
		}
		  
   		
		
		if(!JPluginHelper::isEnabled('editors','arkeditor'))
        {
           $this->isEnabled = false;
           $this->app->input->set('ark_inine_enabled',false);	 
           return;
        }	
            
		 //Check to see if another editor has been loaded. If so, do not load
        //the editor in inline mode.

        $testEditor = JFactory::getEditor()->get( '_editor' ) ;
        if( !is_null($testEditor ) && !($testEditor instanceof PlgEditorArkEditor ))
             return;
        //end if	
			
		
		$plugin = JPluginHelper::getPlugin('editors','arkeditor');	
	
				
		$view = $this->app->input->get('view');
		

		if($this->option == 'com_ajax') // bail out
			return;
			
		
		if($this->app->input->get('tmpl',false)) //bailout if in a modal or print view 
		{
			$this->isEnabled = false;
			$this->app->input->set('ark_inine_enabled',false);	 
			return;
		}		
		if(isset($plugin->inlineMode) && $plugin->inlineMode  == ArkInlineMode::REPLACE)
		{	
			$this->isEnabled = false;
			$this->app->input->set('ark_inine_enabled',false);	 
			return;
		}	
			
				
		if($view && $this->inline_allowed_views && in_array($view,$this->inline_allowed_views) )    
			$plugin->inlineMode = ArkInlineMode::INLINE;
				
	
		//Use reflection to get loaded method for JModuleHelper
		
		if($this->app->input->get('Itemid',0)) //We are assigned to a menu
		{
			jimport('joomla.filesystem.folder');
			if(JFolder::exists(JPATH_ROOT.'/modules/mod_inlinecustom'))
			{
				$method = '_load';
				
				if(method_exists('JModuleHelper','load'))
					$method = 'load';
				
				$invokeLoad = new ReflectionMethod('JModuleHelper', $method);
				$invokeLoad->setAccessible(true);
				
				$modules = $invokeLoad->invoke(null);
							
				for($i = 0; $i < count($modules); $i++)
				{
					if($modules[$i]->module == "mod_custom")
					{
						if(!isset($plugin->inlineMode) || isset($plugin->inlineMode) && $plugin->inlineMode != ArkInlineMode::INLINE)
							$plugin->inlineMode = ArkInlineMode::INLINE;
						$modules[$i]->module = "mod_inlinecustom";						
					}	
				}
			}
		}
			
		if(!isset($plugin->inlineMode) || isset($plugin->inlineMode) && $plugin->inlineMode != ArkInlineMode::INLINE)
        {
			$this->isEnabled = false;
            $this->app->input->set('ark_inine_enabled',false);	 
        }
        
		$document = JFactory::getDocument();
		if(isset($plugin->inlineMode) && $plugin->inlineMode == ArkInlineMode::INLINE && $document->getType() == 'html')
        {
			$editor = JEditor::getInstance('arkeditor');
			$this->_output = $editor->display('',false,'', '', '', '');
			//$document = JFactory::getDocument();
			//$document->addCustomTag($return);
			$this->isEnabled = true;
	    }
		else
		{			
			$this->isEnabled = false;
			$this->app->input->set('ark_inine_enabled',false);	 
		}

	}


    public function onAfterRender()
	{
	
	
		if($this->option == 'com_ajax') // bail out
			return;

	
		if ($this->app->isSite() && $this->isEnabled)
		{
            $user = JFactory::getUser();
			
		   //if user is guest lets bail
		    if($user->get('guest'))
		    {
			    return;
		    }
         
            // Get the response body
            $body = $this->app->getBody();
			
			if($this->_output)
				$body = preg_replace('/<body([^<\/]*)?>/i','<body$1>'.$this->_output,$body);
			
					
			$body = preg_replace('/="([^"]*?){div class=__ARKQUOTE__editable__ARKQUOTE__ data-id=__ARKQUOTE__(\d*)__ARKQUOTE__ data-context=__ARKQUOTE__(?:'.implode('|',$this->context).')__ARKQUOTE__ data-type=__ARKQUOTE__title__ARKQUOTE__ data-itemtype=__ARKQUOTE__(?:'.implode('|',$this->types).')__ARKQUOTE__ contenteditable=__ARKQUOTE__true__ARKQUOTE__ style=__ARKQUOTE__display:inline;__ARKQUOTE__}([^"]*?){\/div}/',
						'="$1$3',$body);
						
			$body =  str_replace(array('__ARKQUOTE__','{div class="editable"','contenteditable="true" style="display:inline;"}','{/div}'),
			array('"','<div class="editable"','contenteditable="true" style="display:inline;">','</div>'),$body);
			
			$data = JFactory::getDocument()->getHeadData();
			$title = str_replace(array('__ARKQUOTE__','{div class="editable"','contenteditable="true" style="display:inline;"}','{/div}'),
			array('"','<div class="editable"','contenteditable="true" style="display:inline;">','</div>'),$data['title']);
			$title = strip_tags($title);
			$data['title'] = $title;
			
			$body = preg_replace('/<title>([^<>])*?<div.*?<\/div>.*?<\/title>/i','<title>$1'.$title.'</title>',$body);
		  
			$this->app->setBody($body);
		}
		elseif($this->app->isSite() && $this->option == 'com_config')
		{
			// Get the response body
			$body = $this->app->getBody();

			$body = preg_replace('/{div class=__ARKQUOTE__editable__ARKQUOTE__ data-id=__ARKQUOTE__(?:\d*)__ARKQUOTE__ data-context=__ARKQUOTE__(?:'.implode('|',$this->context).')__ARKQUOTE__ data-type=__ARKQUOTE__title__ARKQUOTE__ data-itemtype=__ARKQUOTE__(?:'.implode('|',$this->types).')__ARKQUOTE__ contenteditable=__ARKQUOTE__true__ARKQUOTE__ style=__ARKQUOTE__display:inline;__ARKQUOTE__}([^"]*?){\/div}/','$1',$body);
		
			$this->app->setBody($body);			

		}
	}

}