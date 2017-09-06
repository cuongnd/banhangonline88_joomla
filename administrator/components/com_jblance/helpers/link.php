<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	07 June 2012
 * @file name	:	helpers/link.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Helper Class for sending Emails (jblance)
 */
defined('_JEXEC') or die('Restricted access');

class LinkHelper {
	
	// Basic universal href link
	public static function GetHrefLink($link, $name, $title = '', $rel = 'nofollow', $class = '', $anker = '', $attr = '') {
		return '<a ' . ($class ? 'class="' . $class . '" ' : '') . 'href="' . $link . ($anker ? ('#' . $anker) : '') . '" title="' . $title . '"' . ($rel ? ' rel="' . $rel . '"' : '') . ($attr ? ' ' . $attr : '') . '>' . $name . '</a>';
	}
	
	public static function GetProfileLink($userid, $name = null, $title ='', $rel = 'nofollow', $class = '') {
		if(!$name){
			$config 		= JblanceHelper::getConfig();
			$showUsername 	= $config->showUsername;
			$nameOrUsername = ($showUsername) ? 'username' : 'name';
			$profile = JFactory::getUser($userid);
			$name = htmlspecialchars($profile->$nameOrUsername, ENT_COMPAT, 'UTF-8');
		}
		if($userid > 0){
			$link = self::GetProfileURL($userid);
			if(!empty ($link))
				return self::GetHrefLink($link, $name, $title, $rel, $class);
		}
		return "<span class=\"{$class}\">{$name}</span>";
	}
	
	public static function GetProfileURL($userid, $xhtml = true) {
		$profile = JblanceHelper::getProfile();
		return $profile->getProfileURL($userid, '', $xhtml);
	}
	
	public static function getDownloadLink($type, $id, $task = '', $class = ''){
		$fileInfo = JBMediaHelper::getFileInfo($type, $id);
	
		$filePath = $fileInfo['filePath'];
		$fileUrl = $fileInfo['fileUrl'];
		$showName = $fileInfo['showName'];
		
		$directDownloadLink = false;
	
		if(!$directDownloadLink){
			if($type == 'nda'){
				//$showName = '<img src="components/com_jblance/images/nda.png" width="20px" title="'.JText::_('COM_JBLANCE_NDA_SIGNED').'"/>';
				$showName = JText::_('COM_JBLANCE_NDA_SHORT_SIGNED');
			}
			$link = JRoute::_('index.php?option=com_jblance&task='.$task.'&type='.$type.'&id='.$id.'&'.JSession::getFormToken().'=1');
		}
		else {
			if($type == 'nda'){
				$showName = '<img src="components/com_jblance/images/nda.png" width="20px" title="'.JText::_('COM_JBLANCE_NDA_SIGNED').'"/>';
			}
			$link = $fileUrl;
		}
	
		return self::GetHrefLink($link, $showName, $title ='', $rel = 'nofollow', $class, '','target=_blank');
	}
	
	public static function getPortfolioDownloadLink($type, $id, $task='', $attachmentColumnNum){
		$db		= JFactory::getDbo();
		$fileInfo = array();
		
		$fileInfo = JBMediaHelper::getPorfolioFileInfo($type, $id, $attachmentColumnNum);
	
		$filePath = $fileInfo['filePath'];
		$fileUrl = $fileInfo['fileUrl'];
		$showName = $fileInfo['showName'];
	
		$directDownloadLink = false;
	
		if(!$directDownloadLink){
			$link = JRoute::_('index.php?option=com_jblance&task='.$task.'&type='.$type.'&id='.$id.'&attachment='.$attachmentColumnNum.'&'.JSession::getFormToken().'=1');
		}
		else {
			$link = $fileUrl;
		}
	
		return self::GetHrefLink($link, $showName, $title ='', $rel = 'nofollow', $class = '', '','target=_blank');
	}
	
	public static function getProjectLink($project_id, $name = ''){
		if(empty($name)){
			$project	= JTable::getInstance('project', 'Table');
			$project->load($project_id);
			$name = $project->project_title;
		}
		$link = JRoute::_('index.php?option=com_jblance&view=project&layout=detailproject&id='.$project_id);
		//$link = JRoute::_('index.php?option=com_jblance&view=project&layout=detailproject&id='.$project_id.'&Itemid=107');	//uncomment this line if you want to include itemid	
		return self::GetHrefLink($link, $name);
	}
}