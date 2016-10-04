<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.view');

require_once(JPATH_BASE."/components/com_adsmanager/helpers/field.php");

/**
 * @package		Joomla
 * @subpackage	Contacts
 */
class AdsmanagerViewEdit extends TView
{
	function display($tpl = null)
	{
		$app = JFactory::getApplication();

		$user		= JFactory::getUser();
		$app	= JFactory::getApplication();
		$pathway = $app->getPathway();
		$document	= JFactory::getDocument();
		
		$configurationmodel	= $this->getModel("configuration");
		$fieldmodel		= $this->getModel("field");
		$contentmodel	= $this->getModel("content");
		$positionmodel	= $this->getModel("position");
		$catmodel		= $this->getModel("category");
		$usermodel		= $this->getModel("user");
		
		// Get the parameters of the active menu item
		$menus	= $app->getMenu();
		$menu    = $menus->getActive();
		
		$conf = $configurationmodel->getConfiguration();
        
        if(!isset($conf->single_category_selection_type))
            $conf->single_category_selection_type = 'normal';
		
        $this->assignRef('conf',$conf);	
		
		$fields = $fieldmodel->getFields(true,null,null,"fieldid","ASC",true,'write');	
		$this->assignRef('fields',$fields);
		
		$positions = $positionmodel->getPositions('edit');
		$this->assignRef('positions',$positions);
		
		$fieldsByPosition = $fieldmodel->getFieldsbyPositions(true,true,'write','edit');
		$this->assignRef('fieldsByPosition',$fieldsByPosition);
		
		$field_values = $fieldmodel->getFieldValues();
		foreach($fields as $field)
		{
			if ($field->cbfieldvalues != "-1")
			{
				/*get CB value fields */
				$cbfieldvalues = $fieldmodel->getCBFieldValues($field->cbfieldvalues);
				$field_values[$field->fieldid] = $cbfieldvalues;
			}
		}
		$this->assignRef('field_values',$field_values);
		
		$plugins = $fieldmodel->getPlugins();
		$field = new JHTMLAdsmanagerField($conf,$field_values,"1",$plugins);
		
		$this->assignRef('field',$field);
		
		$errorMsg = JRequest::getString( 'errorMsg',	"" );
		$this->assignRef('errorMsg',$errorMsg);	
		
		/* No need to user query, if errorMsg */
		if ($errorMsg == "")
		{
			if ($conf->comprofiler == 0)
			{	
				$profile = $usermodel->getProfile($user->id);
			}
			else if (COMMUNITY_BUILDER == 1)
			{
				$profile = $usermodel->getCBProfile($user->id);
			} else {
				$profile = new stdClass();
			}
			$this->assignRef('default',$profile);
		}
		
		$contentid = JRequest::getInt( 'id', 0 );
		
		// Update Ad ?
		if ($contentid > 0)
		{ // edit ad	
			$content = $contentmodel->getContent($contentid,false,1);
			//$content->ad_text = str_replace ('<br/>',"\r\n",$content->ad_text);
            
            if(!isset($this->isDuplicated))
                $this->isDuplicated = 0;
            
            if ($user->id == 0) {
                $app->redirect( TRoute::_('index.php?option=com_adsmanager') );
            }
            else if ($content->userid == $user->id)
            {
                if($this->isDuplicated == 1){
                    $content->images =array();
                    $content->id = 0;
                    $isUpdateMode = 0;
                } else {
                    $isUpdateMode = 1;
                }
            }
            else
            {
                $app->redirect(TLink::getMyAdsLink());
            }
			
		}
		else { // insert
			$isUpdateMode = 0;	
		}
		
		$this->assignRef('content',$content);
        
        if(isset($content->pendingdata->new_ad) && $content->pendingdata->new_ad == true) {
            $isUpdateMode = 0;
        }
        
		$this->assignRef('isUpdateMode',$isUpdateMode);
		
		$catid = JRequest::getInt('catid',0);
		
		// If Root cat is not allowed we must check that catid is correct
		$submit_allow = 1;
		if (($catid != 0)&&($conf->root_allowed == 0)) {
			$submit_allow = !$catmodel->isRootCategory($catid);
		}
		$this->assignRef('submit_allow',$submit_allow);
		
		if ($catid != 0) {
			if ($catmodel->isPublishedCategory($catid) == false) {
				$app->redirect( TRoute::_('index.php?option=com_adsmanager'),"Invalid Category selection","message");
				return;
			}
		}
        
		if (($catid == 0)&&($isUpdateMode == 1))
		{
			$catid = $content->cats[0]->catid;
		}
		
		$rootid = JRequest::getInt('rootid',0);
		$this->assignRef('rootid',$rootid);
		
		if ($catid != "0") {
			$category = $catmodel->getCategory($catid);
			$category->img = TTools::getCatImageUrl($catid,true);
		}
		else
		{
			$category = new stdClass();
			$category->name = JText::_("");
			$category->description = "";
			$category->img = "";
		}
		$this->assignRef('category',$category);
		$this->assignRef('catid',$catid);
		
		if (isset($content))
			$extra = " - ".$content->ad_headline;
		else
			$extra = "";
		$document->setTitle( JText::_('ADSMANAGER_PAGE_TITLE')." ".JText::_($category->name).$extra);
		
		$nbcats = $conf->nbcats;
		if (function_exists("getMaxCats"))
		{
			$nbcats = getMaxCats($conf->nbcats);
		}
		$this->assignRef('nbcats',$nbcats);
		
		if ($nbcats > 1) {
			$cats = $catmodel->getFlatTree(true, false, $nbContents, 'write',$rootid);
		} else {
			$rootid = JRequest::getInt('rootid',0);
			switch(@$conf->single_category_selection_type) {
				default:
				case 'normal':
				case 'color':
				case 'combobox':
					$cats = $catmodel->getFlatTree(true, false, $nbContents, 'write',$rootid);
					break;
				case 'cascade':
					$cats = $catmodel->getCategoriesPerLevel(true, false, $nbContents, 'write',$rootid);
					break;
			}
		}
        
        if(empty($cats)){
			$app->redirect($_SERVER['HTTP_REFERER'], JText::_('ADSMANAGER_NO_WRITE_RIGHT'),'message' );
		}
        
        $this->assignRef('cats',$cats);
		
		if ($errorMsg != "") {
			//$post = (object) $_POST;
			$post = JRequest::get( 'post' ); 
			$this->assignRef('default',$post);
		}
			
		if (($conf->submission_type == 2)&&($user->id == "0"))
		{
			$txt = JText::_('ADSMANAGER_WARNING_NEW_AD_NO_ACCOUNT');
			$this->assignRef('warning_text',$txt);
		}
		
		switch($errorMsg)
		{
			case "bad_password":
				$txt = JText::_('ADSMANAGER_BAD_PASSWORD');
				$this->assignRef('error_text',$txt);
				break;
			case "email_already_used":
				$txt = JText::_('ADSMANAGER_EMAIL_ALREADY_USED');
				$this->assignRef('error_text',$txt);
				break;
			case "file_too_big":
				$txt = JText::_('ADSMANAGER_FILE_TOO_BIG');
				$this->assignRef('error_text',$txt);
				break;
			default:
				if ($errorMsg != "") {
					$txt = $errorMsg;
					$this->assignRef('error_text',$txt);
				}
		}
		
		if (($conf->submission_type == 0)&&($user->id == 0))
		{
			$account_creation = 1;
		}
		else
			$account_creation = 0;
		
		$this->assignRef('account_creation',$account_creation);
		
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('adsmanagercontent');
		
		$event = new stdClass();
		$results = $dispatcher->trigger('ADSonContentAfterForm', array ($content));
		$event->onContentAfterForm = trim(implode("\n", $results));
		$this->assignRef('event',$event);
		
		if (PAIDSYSTEM) {
			if(isset($content->id)) {
				$db = JFactory::getDbo();
				$db->setQuery( "SELECT * FROM #__paidsystem_ads WHERE id=".(int)$content->id );
				$adext = $db->loadObject();
			} else {
				$adext = new stdClass();
				$adext->images = 0;
			}
		} else {
			$adext = new stdClass();
			$adext->images = 0;
		}
		$this->assignRef('adext',$adext);
        
		parent::display($tpl);
	}
}
