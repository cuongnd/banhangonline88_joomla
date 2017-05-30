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
 * @subpackage  extensions.ARKExtensionsBase
 */
class ARKExtensionsContent extends  ARKExtensionsBase
{

	protected $skipProccessing = false; 

	
	public  function __construct($context, $item, $params)
	{
			//call parent construtor
			 parent::__construct($context, $item, $params);
			 //set allowable context array for inline editing
			 $this->inline_allowed_contexts = array ('com_content.article','com_content.featured','com_content.category');
			 
	     

			if($this->app->input->get('arkoption') != 'com_content')   // if component is masquerading as the content component when firing content plugins lets us bail out
				$this->skipProccessing = true; 
	}	
	
	
	public function prepare()
	{
		
        
        if($this->skipProccessing) // bail out
			return false;
					
       //Are we allowed to edit in this context 
			
		if($this->context != 'com_content.category')  //override base check as we only fire this for category description
		{
			return false;
		}
		
		if(isset($this->item->introtext)) //Ensure this is only fired off for a category
			return false;
		
		//Set Data Item Type
		$this->type = str_replace($this->app->input->get('option').'.','',$this->context);
		  	

        //Permisson Check
        if(!isset($this->id))
        {
		    $id = $this->app->input->get('id',0);
            $this->id = $id ? $id : 0;   
        }
		//Set Asset number 
		 $asset = 'com_content.category.' . $this->id;
		
		$table = JTable::getInstance('category');
		$table->load($this->id);
		$createdBy = $table->created_user_id;
		
		$user = JFactory::getUser();		
		//can user edit item if not then bail
		if (!($user->authorise('core.edit', $asset) || ($user->authorise('core.edit.own', $asset) && $user->id == $createdBy)) )
		{
			return false;
		}	
		
		//need to override base class here for following check for categories
		/*filter article to see if it is being used to load a module if so skip it
		[widgetkit]
		{loadmodule}
		{loadposition}
		{module}
		{modulepos}
		{modulepos}
		{component}
		{article(s)}
		*/

		$text = $table->description;
		$test = preg_match('/\{(?:loadmodule|loadposition|module|modulepos|component|articles?)\s+(.*?)\}/i',$text);
	
		if(!$test)
			$test = preg_match('/\[widgetkit\s+(.*?)\]/i',$text);
		
		if($test)
		{	
			return;
		}
		
        return parent::prepare();	
	}	
	
		
	public function display()
	{
		
       if($this->skipProccessing) // bail out
			return false;
		
		//is this item already proccessed
        
		if(isset($this->item->proccessedInline) && $this->item->proccessedInline)
			return false;	

       		
       	//Are we allowed to edit in this context 
			
		//override base check if needed
	
		//Set Data Item Type
		$this->type = str_replace($this->app->input->get('option').'.','',$this->context);


        $update_introtext = false;
		
		if($this->type == "category" || $this->type == "featured") //we are in blog view so set type to blog
		{
		  $this->type = ($this->type == "category" ? "blog" : $this->type); 		
		   $update_introtext = true;
		}
		
	    //Permisson Check
		
		//Set Asset number 
		$asset = 'com_content.article.'.$this->id;
		$createdBy = $this->item->created_by;
		
		$user = JFactory::getUser();		
		//can user edit item if not then bail
		if (!($user->authorise('core.edit', $asset) || ($user->authorise('core.edit.own', $asset) && $user->id == $createdBy)) )
		{
			return false;
		}		
		

 	    if(isset($this->item->introtext) && $this->item->introtext != $this->item->text)  //make sure we always set item->text
		{	
			if($this->type == "blog")
				$this->item->text = $this->item->introtext;
			$update_introtext = true;
		}	
		
  		if(!parent::display())
			return false;
		

			
		  if($update_introtext)
			    $this->item->introtext = $this->item->text;
  		
		return true; 
	}
}