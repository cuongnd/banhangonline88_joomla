<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	13 March 2012
 * @file name	:	helpers/mail.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Helper Class for sending Emails (jblance)
 */
defined('_JEXEC') or die('Restricted access');

class EmailHelper {
	
	function getTemplate($tempfor){
		$db = JFactory :: getDbo();
	
		$query = "SELECT * FROM #__jblance_emailtemplate WHERE templatefor = ".$db->Quote($tempfor);
		$db->setQuery($query);
		$template = $db->loadObject();
		return $template;
	}
	
	public static function getSuperAdminEmail(){
		$db = JFactory::getDbo();
	
		/* $query = "SELECT a.name, a.email, a.sendEmail FROM #__users AS a, #__user_usergroup_map AS b ".
				 "WHERE a.id = b.user_id AND b.group_id =".$db->quote(8); */
		// get all admin users
		$query = 'SELECT name, email, sendEmail FROM #__users WHERE sendEmail=1';
		$db->setQuery($query);
		if($db->getErrorNum()){
			JError::raiseError(500, $db->stderr());
		}
		$rows = $db->loadObjectList();
		return $rows;
	}
	
	function getSenderInfo(){
		$app = JFactory::getApplication();
		
		$config = JblanceHelper::getConfig();
		$fromName = $config->mailFromName;
		$fromAddress = $config->mailFromAddress;
		
		if(!$fromName)
			$fromName =  $app->get('fromname');
		
		if(!$fromAddress)
			$fromAddress =  $app->get('mailfrom');
		
		$sitename = $app->get('sitename');
		
		$return['fromname'] = $fromName;
		$return['fromaddress'] = $fromAddress;
		$return['sitename'] = $sitename;
		
		return $return;
	}
	
	function buildCustomFieldValues(){
		$db 	= JFactory::getDbo();
		$return = array();
		$query = "SELECT fv.*, IF(fv.userid > 0, CONCAT(fv.userid,'_PROFILE'), CONCAT(fv.projectid,'_PROJECT')) AS field_type FROM #__jblance_custom_field_value fv
		LEFT JOIN #__jblance_custom_field c ON fv.fieldid=c.id";
		$db->setQuery($query);//echo $query;
		$fieldvalues = $db->loadObjectList();
	
		/* foreach ($fieldvalues as $fv){
		 $return['CUSTOM_'.$fv->fieldid.'_'.$fv->field_type] = $fv->value;
		} */
		return $fieldvalues;
	}
	
	//2.sendRegistrationMail
	function sendRegistrationMail(&$usern, $password, $facebook=false){
	
		$userid		= $usern->get('id');
		$name 		= $usern->get('name');
		$recipient	= $usern->get('email');
		$username 	= $usern->get('username');
	
		$usertype = '';
	
		$jbuser =JblanceHelper::get('helper.user');		// create an instance of the class UserHelper
		$usergroupInfo = $jbuser->getUserGroupInfo($userid);
		$usertype = $usergroupInfo->name;
		$jbrequireApproval = $usergroupInfo->approval;	//require JoomBri Admin approval
	
		$usersConfig 	=JComponentHelper::getParams('com_users');
		$useractivation = $usersConfig->get('useractivation');
		
		$jAdminApproval = ($usersConfig->get('useractivation') == '2') ? 1 : 0;	//require Joomla Admin approval
		
		$requireApproval = $jbrequireApproval | $jAdminApproval;	//approval is required either JoomBri or Joomla require approval
		
		//get sender info
		$senderInfo  = self::getSenderInfo();
		$sitename 	 = $senderInfo['sitename'];
		$fromname 	 = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		$siteURL	 = JURI::base();
		$adminURL 	 = JURI::base().'administrator';
		$actLink 	 = $siteURL.'index.php?option=com_users&task=registration.activate&token='.$usern->get('activation');
	
		/* Send notification to registered user */
		
		//get the email template
		if($requireApproval){
			$template = $this->getTemplate('newuser-pending-approval');
		}
		else {
			if($useractivation == 1){
				$template = $this->getTemplate('newuser-activate');
			}
			else {
				$template = $this->getTemplate('newuser-login');
			}
		}
		
		// If the user is signing up from Facebook, set the template to newuser-login
		if($facebook){
			$template = $this->getTemplate('newuser-facebook-signin');
			$usertype = JText::_('COM_JBLANCE_SIGN_IN_WITH_FACEBOOK');;
		}
		
		//get the status tag
		if($requireApproval)
			$status = JText::_('COM_JBLANCE_PENDING');
		else
			$status = JText::_('COM_JBLANCE_APPROVED');
	
		$tags = array("[NAME]", "[SITENAME]", "[ACTLINK]", "[SITEURL]", "[ADMINURL]", "[USERNAME]", "[PASSWORD]", "[USEREMAIL]", "[USERTYPE]", "[STATUS]");
		$tagsValues = array("$name", "$sitename", "$actLink", "$siteURL", "$adminURL", "$username", "$password", "$recipient", "$usertype", "$status");
	
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
	
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
	
		JFactory::getMailer()->sendMail($fromaddress, $fromname, $recipient, $subject, $message, 1);
	
		/* Send notification to all administrators */
		
		//get all super administrator
		$rows = self::getSuperAdminEmail();
		
		//get the email template
		$template = $this->getTemplate('newuser-details');
	
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
	
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
	
		// get super administrators id
		foreach($rows as $row){
			JFactory::getMailer()->sendMail($fromaddress, $fromname, $row->email, $subject, $message, 1);
		}
	}
	
	//
	function sendUserAccountApproved($userid){
		$user 		= JFactory::getUser($userid);
		$name 		= $user->name;
		$recipient  = $user->email;
		$username 	= $user->username;
		
		//get sender info
		$senderInfo  = self::getSenderInfo();
		$sitename 	 = $senderInfo['sitename'];
		$fromname 	 = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		$siteURL = JURI::root();
		
		$tags = array("[NAME]", "[EMAIL]", "[USERNAME]", "[SITENAME]", "[SITEURL]");
		$tagsValues = array("$name", "$recipient", "$username", "$sitename", "$siteURL");
		
		//get the email template
		$template = $this->getTemplate('newuser-account-approved');
		
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
		
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
		
		// Send email to user
		JFactory::getMailer()->sendMail($fromaddress, $fromname, $recipient, $subject, $message, 1);
	}
	
	//3.Send alert to Admin on new Subscription
	function alertAdminSubscr($subscrid, $userid){
		$user 	= JFactory::getUser($userid);
		$row	= JTable::getInstance('plansubscr', 'Table');
		$row->load($subscrid);
	
		$plan	= JTable::getInstance('plan', 'Table');
		$plan->load($row->plan_id);
		
		//Alert admin based on the plan settings - return if set to 'No'
		if(!$plan->alert_admin)
			return;
	
		$name = $user->name;
		$userEmail = $user->email;
		$username = $user->username;
	
		//get sender info
		$senderInfo  = self::getSenderInfo();
		$sitename 	 = $senderInfo['sitename'];
		$fromname 	 = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		
		$subscrid = $row->id;
		$planname = $plan->name;
		$gateway =JblanceHelper::getGwayName($row->gateway);
	
		if($row->approved)
			$status = JText::_('COM_JBLANCE_APPROVED');
		else
			$status = JText::_('COM_JBLANCE_APPROVAL_PENDING');
	
		$tags = array("[NAME]", "[USERNAME]", "[PLANNAME]", "[SITENAME]", "[SUBSCRID]", "[USEREMAIL]", "[GATEWAY]", "[PLANSTATUS]");
		$tagsValues = array("$name", "$username", "$planname", "$sitename", "$subscrid", "$userEmail", "$gateway", "$status");
	
		//get the email template
		$template = $this->getTemplate('subscr-details');
	
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
	
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
	
		//get all super administrator
		$rows = self::getSuperAdminEmail();
	
		// Send notification to all administrators
		foreach ($rows as $row){
			JFactory::getMailer()->sendMail($fromaddress, $fromname, $row->email, $subject, $message, 1);
		}
	}

	//4.Send alert to Subscribers on new Subscription
	function alertUserSubscr($subscrid, $userid){
		$user 	= JFactory::getUser($userid);
		$row	= JTable::getInstance('plansubscr', 'Table');
		$row->load($subscrid);
	
		$plan	= JTable::getInstance('plan', 'Table');
		$plan->load($row->plan_id);
		
		$name = $user->name;
		$recipient = $user->email;
		$username = $user->username;
	
		//get sender info
		$senderInfo  = self::getSenderInfo();
		$sitename 	 = $senderInfo['sitename'];
		$fromname 	 = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		$siteURL = JURI::base();
	
		$subscrid = $row->id;
		$planname = $plan->name;
	
		$tags = array("[NAME]", "[PLANNAME]", "[SITENAME]", "[SITEURL]", "[ADMINEMAIL]");
		$tagsValues = array("$name", "$planname", "$sitename", "$siteURL", "$fromaddress");
	
		//get the email template
		if($row->approved){
			$template = $this->getTemplate('subscr-approved-auto');
		}
		else {
			$template = $this->getTemplate('subscr-pending');
		}
	
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
	
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
	
		// Send email to user
		JFactory::getMailer()->sendMail($fromaddress, $fromname, $recipient, $subject, $message, 1);
	}	

	//Send alert to user when the subscription is approved by admin
	function sendSubscrApprovedEmail($subscrid, $userid){
		$row	= JTable::getInstance('plansubscr', 'Table');
		$row->load($subscrid);
	
		$plan	= JTable::getInstance('plan', 'Table');
		$plan->load($row->plan_id);
	
		$data 		= JFactory::getUser($userid);
		$name 		= $data->name;
		$recipient  = $data->email;
		$username 	= $data->username;
	
		//get sender info
		$senderInfo  = self::getSenderInfo();
		$sitename 	 = $senderInfo['sitename'];
		$fromname 	 = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		$siteURL = JURI::root();
	
		$subscrid = $row->id;
		$planname = $plan->name;
	
		$tags = array("[NAME]", "[PLANNAME]", "[SITENAME]", "[SITEURL]");
		$tagsValues = array("$name", "$planname", "$sitename", "$siteURL");
	
		//get the email template
		$template = $this->getTemplate('subscr-approved-admin');
	
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
	
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
	
		// Send email to user
		JFactory::getMailer()->sendMail($fromaddress, $fromname, $recipient, $subject, $message, 1);
	}
	
	//send new project notification to users who can bid for projects
	function sendNewProjectNotification($project_id, $isNewProject){
		$db 	= JFactory::getDbo();
		$jbuser = JblanceHelper::get('helper.user');		// create an instance of the class UserHelper
		
		$config 	  = JblanceHelper::getConfig();
		$currencysym  = $config->currencySymbol;
		$currencycode = $config->currencyCode;
		$dformat	  = $config->dateFormat;
		
		//project details
		$project = JTable::getInstance('project', 'Table');
		$project->load($project_id);
		$projectname	= $project->project_title;
		$categoryname	= JblanceHelper::getCategoryNames($project->id_category);
		$projectType	= $project->project_type;
		$perHr			= ($projectType == 'COM_JBLANCE_HOURLY') ? ' / '.JText::_('COM_JBLANCE_HR') : '';
		$budgetmin 		= JblanceHelper::formatCurrency($project->budgetmin, false).$perHr;
		$budgetmax		= JblanceHelper::formatCurrency($project->budgetmax, false).$perHr;
		$startdate		= JHtml::_('date', $project->start_date, $dformat, false);
		$expires		= $project->expires;
		$projecturl 	= JURI::root().'index.php?option=com_jblance&view=project&layout=detailproject&id='.$project->id;
		$isPvtInvite	= $project->is_private_invite;
		
		//get sender info
		$senderInfo  = self::getSenderInfo();
		$sitename 	 = $senderInfo['sitename'];
		$fromname 	 = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		$siteURL 	 = JURI::root();
		$recipient 	 = $senderInfo['fromaddress'];
		
		$tags = array("[SITENAME]", "[SITEURL]", "[PROJECTNAME]", "[CATEGORYNAME]", "[CURRENCYSYM]", "[CURRENCYCODE]", "[BUDGETMIN]", "[BUDGETMAX]", "[STARTDATE]", "[EXPIRE]", "[PROJECTURL]");
		$tagsValues = array("$sitename", "$siteURL", "$projectname", "$categoryname", "$currencysym", "$currencycode", "$budgetmin", "$budgetmax", "$startdate", "$expires", $projecturl);
		
		/*
		 * if the project is not private invite, get the list of users who can bid and matching the project skills
		 * else get the list of users from the invitees column
		 */
		if(!$isPvtInvite){
			//get the list of usergroups who can bid
			$query = "SELECT * FROM #__jblance_usergroup WHERE published=1";
			$db->setQuery($query);
			$ugs = $db->loadObjectList();
			$canBidUserGroup = array();
			
			foreach($ugs as $ug){
				$info = $jbuser->getUserGroupInfo(null, $ug->id);
				if($info->allowBidProjects)
					$canBidUserGroup[] = $ug->id;
			}
			
			$receivableIds = implode($canBidUserGroup, ',');
			//if there is no user group who can bid, just skip the rest of the code
			if(empty($receivableIds)){
				return false;
			}
			
			//get the email template
			$template = $this->getTemplate('proj-new-notify');
			
			//get subject
			$subject = $template->subject;
			if(!$isNewProject)
				$subject = $subject.' ('.JText::_('COM_JBLANCE_PROJECT_DETAILS_CHANGED').')';
			$subject = str_replace($tags, $tagsValues, $subject);
			$subject = html_entity_decode($subject, ENT_QUOTES);
			
			//get message body
			$message = $template->body;
			$message = str_replace($tags, $tagsValues, $message);
			$message = html_entity_decode($message, ENT_QUOTES);
			
			$where = '';
			$queryStrings[] = "ju.ug_id IN ($receivableIds)";
			$queryStrings[] = "n.notifyNewProject=1";
			$queryStrings[] = "u.block=0";
			
			//get the relevent project for the user id based on the category
			$id_categ = explode(',', $project->id_category);
			if(is_array($id_categ)){
				$miniquery = array();
				foreach($id_categ as $cat){
					$miniquery[] = "FIND_IN_SET($cat, ju.id_category)";
				}
				$querytemp = '('.implode(' OR ', $miniquery).')';
				$queryStrings[] = $querytemp;
			}
			
			//filter projects by matching user locaton with project
			$queryStrings[] = "ju.id_location=".$db->quote($project->id_location);
			
			$where = (count($queryStrings) ? ' WHERE ('.implode(') AND (', $queryStrings).') ' : '');
			
			//get array of user emails
			$query = "SELECT u.email FROM #__jblance_user ju ".
					 "INNER JOIN #__users u ON u.id=ju.user_id ".
					 "LEFT JOIN #__jblance_notify n ON ju.user_id=n.user_id ".
 					  $where;
			$db->setQuery($query);//echo $query;exit;
			$bcc = $db->loadColumn();
		}
		else {
			//get the email template
			$template = $this->getTemplate('proj-private-invite');
			
			//get subject
			$subject = $template->subject;
			$subject = str_replace($tags, $tagsValues, $subject);
			$subject = html_entity_decode($subject, ENT_QUOTES);
			
			//get message body
			$message = $template->body;
			$message = str_replace($tags, $tagsValues, $message);
			$message = html_entity_decode($message, ENT_QUOTES);
			
			//get array of user emails from invitees column
			$invite_user_id = $project->invite_user_id;
			$query = "SELECT DISTINCT u.email FROM #__jblance_user ju ".
					 "INNER JOIN #__users u ON u.id=ju.user_id ".
					 "LEFT JOIN #__jblance_notify n ON ju.user_id=n.user_id ".
					 "WHERE n.notifyNewProject=1 AND u.block=0 AND u.id IN ($invite_user_id)";
			$db->setQuery($query);//echo $query;exit;
			$bcc = $db->loadColumn();
		}
		
		// replace custom field tags
		$message = self::buildCustomFieldTags($message, 0, $project->publisher_userid, $project_id);
		
		// Send email to user
		//if($isNewProject)		//UNCOMMENT '//' this line if you want to send message for new projects only.
		JFactory::getMailer()->sendMail($fromaddress, $fromname, $recipient, $subject, $message, 1, null, $bcc);
	}
	
	function sendInviteToProjectNotification($project_id, $user_id){
	
		$config 	  = JblanceHelper::getConfig();
		$currencysym  = $config->currencySymbol;
		$currencycode = $config->currencyCode;
		$dformat	  = $config->dateFormat;
	
		//project details
		$project = JTable::getInstance('project', 'Table');
		$project->load($project_id);
		$projectname	= $project->project_title;
		$categoryname	= JblanceHelper::getCategoryNames($project->id_category);
		$projectType	= $project->project_type;
		$perHr			= ($projectType == 'COM_JBLANCE_HOURLY') ? ' / '.JText::_('COM_JBLANCE_HR') : '';
		$budgetmin 		= JblanceHelper::formatCurrency($project->budgetmin, false).$perHr;
		$budgetmax		= JblanceHelper::formatCurrency($project->budgetmax, false).$perHr;
		$startdate		= JHtml::_('date', $project->start_date, $dformat, false);
		$expires		= $project->expires;
		$projecturl 	= JURI::root().'index.php?option=com_jblance&view=project&layout=detailproject&id='.$project->id;
		$isPvtInvite	= $project->is_private_invite;
	
		//get sender info
		$senderInfo  = self::getSenderInfo();
		$sitename 	 = $senderInfo['sitename'];
		$fromname 	 = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		$siteURL 	 = JURI::root();
	
		//get recipient info
		$inviteeInfo = JFactory::getUser($user_id);
		$recipient = $inviteeInfo->email;
	
		$tags = array("[SITENAME]", "[SITEURL]", "[PROJECTNAME]", "[CATEGORYNAME]", "[CURRENCYSYM]", "[CURRENCYCODE]", "[BUDGETMIN]", "[BUDGETMAX]", "[STARTDATE]", "[EXPIRE]", "[PROJECTURL]");
		$tagsValues = array("$sitename", "$siteURL", "$projectname", "$categoryname", "$currencysym", "$currencycode", "$budgetmin", "$budgetmax", "$startdate", "$expires", $projecturl);
	
		//get the email template
		$template = $this->getTemplate('proj-private-invite');
	
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
	
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
	
		JFactory::getMailer()->sendMail($fromaddress, $fromname, $recipient, $subject, $message, 1);
	}

	//send new bid notification
	function sendNewBidNotification($bid_id, $project_id, $isNewBid){
		$db 	= JFactory::getDbo();
		$jbuser = JblanceHelper::get('helper.user');		// create an instance of the class UserHelper
		
		$config 	  = JblanceHelper::getConfig();
		$currencysym  = $config->currencySymbol;
		$currencycode = $config->currencyCode;
		$dformat	  = $config->dateFormat;
		
		$project = JTable::getInstance('project', 'Table');
		$project->load($project_id);
		$projectname	= $project->project_title;
		$categoryname	= JblanceHelper::getCategoryNames($project->id_category);
		$projectType	= $project->project_type;
		$perHr			= ($projectType == 'COM_JBLANCE_HOURLY') ? ' / '.JText::_('COM_JBLANCE_HR') : '';
		$budgetmin 		= JblanceHelper::formatCurrency($project->budgetmin, false).$perHr;
		$budgetmax		= JblanceHelper::formatCurrency($project->budgetmax, false).$perHr;
		$startdate		= JHtml::_('date', $project->start_date, $dformat, false);
		$expires		= $project->expires;
		
		$hrPerInterval = JText::_('COM_JBLANCE_DAYS');
		if($projectType == 'COM_JBLANCE_HOURLY'){
			$commitment = new JRegistry;
			$commitment->loadString($project->commitment);
			$hrPerInterval = JText::_('COM_JBLANCE_HOURS_PER').' '.JText::_($commitment->get('interval'));
		}
		
		//check if the user/bidder has enabled 'new bid' notification. If disabled, return
		$query = "SELECT notifyBidNewAcceptDeny FROM #__jblance_notify WHERE user_id=".$project->publisher_userid;
		$db->setQuery($query);
		$notify = $db->loadResult();
		
		if(!$notify) return;
		
		$buyerinfo = JFactory::getUser($project->publisher_userid);
		$publishername = $buyerinfo->name;
		
		$bid = JTable::getInstance('bid', 'Table');
		$bid->load($bid_id);
		//get  bidder info
		$bidderinfo 	= $jbuser->getUser($bid->user_id);
		$biddername  	= $bidderinfo->name;
		$bidderusername = $bidderinfo->username;
		$bidamount 		= JblanceHelper::formatCurrency($bid->amount, false).$perHr;
		$delivery 		= $bid->delivery.' '.$hrPerInterval;
		
		//get sender info
		$senderInfo  = self::getSenderInfo();
		$sitename 	 = $senderInfo['sitename'];
		$fromname 	 = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		$siteURL 	 = JURI::root();
		$recipient 	 = $buyerinfo->email;
		
		$tags = array("[SITENAME]", "[SITEURL]", "[PUBLISHERNAME]", "[PROJECTNAME]", "[CATEGORYNAME]", "[CURRENCYSYM]", "[CURRENCYCODE]", "[BUDGETMIN]", "[BUDGETMAX]", "[STARTDATE]", "[EXPIRE]", "[BIDDERNAME]", "[BIDDERUSERNAME]", "[BIDAMOUNT]", "[DELIVERY]");
		$tagsValues = array("$sitename", "$siteURL", "$publishername", "$projectname", "$categoryname", "$currencysym", "$currencycode", "$budgetmin", "$budgetmax", "$startdate", "$expires", "$biddername", "$bidderusername", "$bidamount", "$delivery");
		
		//get the email template
		$template = $this->getTemplate('proj-newbid-notify');
		
		//get subject
		$subject = $template->subject;
		if(!$isNewBid)
			$subject = $subject.' ('.JText::_('COM_JBLANCE_BID_CHANGED').')';
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
		
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
		
		// replace custom field tags
		$message = self::buildCustomFieldTags($message, $bid->user_id, $project->publisher_userid, $project_id);
		
		// Send email to user
		//if($isNewBid)		//uncomment this line if you want to send message for new bids only.
			JFactory::getMailer()->sendMail($fromaddress, $fromname, $recipient, $subject, $message, 1);
	}

	//send out bid notification
	function sendOutBidNotification($bid_id, $project_id){
		$db 	= JFactory::getDbo();
		$jbuser = JblanceHelper::get('helper.user');		// create an instance of the class UserHelper
		
		$config 		= JblanceHelper::getConfig();
		$currencysym 	= $config->currencySymbol;
		$currencycode 	= $config->currencyCode;
		$dformat		= $config->dateFormat;
		
		//get sender info
		$senderInfo = self::getSenderInfo();
		$sitename = $senderInfo['sitename'];
		$fromname = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		$siteURL = JURI::root();
		$recipient = $senderInfo['fromaddress'];
		
		$project 		= JTable::getInstance('project', 'Table');
		$project->load($project_id);
		$projectname	= $project->project_title;
		$projectType	= $project->project_type;
		$perHr			= ($projectType == 'COM_JBLANCE_HOURLY') ? ' / '.JText::_('COM_JBLANCE_HR') : '';
		$budgetmin 		= JblanceHelper::formatCurrency($project->budgetmin, false).$perHr;
		$budgetmax		= JblanceHelper::formatCurrency($project->budgetmax, false).$perHr;
		$startdate		= JHtml::_('date', $project->start_date, $dformat, false);
		$expires		= $project->expires;
		
		$hrPerInterval = JText::_('COM_JBLANCE_DAYS');
		if($projectType == 'COM_JBLANCE_HOURLY'){
			$commitment = new JRegistry;
			$commitment->loadString($project->commitment);
			$hrPerInterval = JText::_('COM_JBLANCE_HOURS_PER').' '.JText::_($commitment->get('interval'));
		}
		
		$bid = JTable::getInstance('bid','Table');
		$bid->load($bid_id);
		
		//get  bidder info
		$bidderinfo 	= $jbuser->getUser($bid->user_id);
		$bidderusername = $bidderinfo->username;
		$bidamount 		= JblanceHelper::formatCurrency($bid->amount, false).$perHr;
		$delivery 		= $bid->delivery.' '.$hrPerInterval;
		
		//search for recipient
		$query = "SELECT email FROM #__jblance_bid b ".
				 "INNER JOIN #__users u ON b.user_id = u.id ".
				 "WHERE b.amount > ".$bid->amount." ".
				 "AND  outbid = 1 AND project_id =".$project_id;
		$db->setQuery($query);
		$bcc = $db->loadColumn();
		
		if(count($bcc) > 0){
			$tags = array("[SITENAME]", "[SITEURL]", "[PROJECTNAME]", "[CURRENCYSYM]", "[CURRENCYCODE]", "[BUDGETMIN]", "[BUDGETMAX]", "[STARTDATE]", "[EXPIRE]", "[BIDDERUSERNAME]", "[BIDAMOUNT]", "[DELIVERY]");
			$tagsValues = array("$sitename", "$siteURL", "$projectname", "$currencysym", "$currencycode", "$budgetmin", "$budgetmax", "$startdate", "$expires", "$bidderusername", "$bidamount", "$delivery");
			
			//get the email template
			$template = $this->getTemplate('proj-lowbid-notify');
			
			//get subject
			$subject = $template->subject;
			$subject = str_replace($tags, $tagsValues, $subject);
			$subject = html_entity_decode($subject, ENT_QUOTES);
			
			//get message body
			$message = $template->body;
			$message = str_replace($tags, $tagsValues, $message);
			$message = html_entity_decode($message, ENT_QUOTES);
			
			// replace custom field tags
			$message = self::buildCustomFieldTags($message, $bid->user_id, $project->publisher_userid, $project_id);
			
			// Send email to user
			JFactory::getMailer()->sendMail($fromaddress, $fromname, $recipient, $subject, $message, 1, null, $bcc);
		}
	}

	//send bid won
	function sendBidWonNotification($project_id){
		$db 	= JFactory::getDbo();
		$jbuser = JblanceHelper::get('helper.user');		// create an instance of the class UserHelper
		
		$config 	  = JblanceHelper::getConfig();
		$currencysym  = $config->currencySymbol;
		$currencycode = $config->currencyCode;
		
		$project 		= JTable::getInstance('project', 'Table');
		$project->load($project_id);
		$projectname 	= $project->project_title;
		$projectType	= $project->project_type;
		$perHr			= ($projectType == 'COM_JBLANCE_HOURLY') ? ' / '.JText::_('COM_JBLANCE_HR') : '';
		
		$bidderinfo 	= $jbuser->getUser($project->assigned_userid);
		$biddername 	= $bidderinfo->name;
		$bidderusername = $bidderinfo->username;
		
		//check if the user/bidder has enabled 'bid won' notification. If disabled, return
		$query = "SELECT notifyBidWon FROM #__jblance_notify WHERE user_id=".$project->assigned_userid;
		$db->setQuery($query);
		$notify = $db->loadResult();
		
		if(!$notify) return;
		
		//get bid details
		$query = "SELECT amount,delivery FROM #__jblance_bid WHERE user_id=$project->assigned_userid AND project_id = ".$project_id;
		$db->setQuery($query);
		$bid = $db->loadObject();
		$bidamount = JblanceHelper::formatCurrency($bid->amount, false).$perHr;
		$delivery = $bid->delivery;
		
		//get sender info
		$senderInfo  = self::getSenderInfo();
		$sitename 	 = $senderInfo['sitename'];
		$fromname 	 = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		$siteURL 	 = JURI::root();
		$recipient 	 = $bidderinfo->email;
		
		$tags = array("[SITENAME]", "[SITEURL]", "[PROJECTNAME]", "[BIDDERNAME]", "[BIDDERUSERNAME]", "[CURRENCYSYM]", "[CURRENCYCODE]", "[BIDAMOUNT]", "[DELIVERY]");
		$tagsValues = array("$sitename", "$siteURL", "$projectname", "$biddername", "$bidderusername", "$currencysym", "$currencycode", "$bidamount", "$delivery");
		
		//get the email template
		$template = $this->getTemplate('proj-bidwon-notify');
		
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
		
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
		
		// replace custom field tags
		$message = self::buildCustomFieldTags($message, $project->assigned_userid, $project->publisher_userid, $project_id);
		
		// Send email to user
		JFactory::getMailer()->sendMail($fromaddress, $fromname, $recipient, $subject, $message, 1);
	}
	
	//send when the bidder denied the offer
	function sendProjectDeniedNotification($project_id, $bidder_id){
		
		$db = JFactory::getDbo();
		$jbuser = JblanceHelper::get('helper.user');		// create an instance of the class UserHelper
		
		$project 	 = JTable::getInstance('project', 'Table');
		$project->load($project_id);
		$projectname = $project->project_title;
		
		$buyerinfo		= $jbuser->getUser($project->publisher_userid);
		$publishername 	= $buyerinfo->name;
		
		//check if the publisher has enabled 'bid denied' notification. If disabled, return
		$query = "SELECT notifyBidNewAcceptDeny FROM #__jblance_notify WHERE user_id=".$project->publisher_userid;
		$db->setQuery($query);
		$notify = $db->loadResult();
		
		if(!$notify) return;
		
		$bidderinfo 	= $jbuser->getUser($bidder_id);
		$biddername 	= $bidderinfo->name;
		$bidderusername = $bidderinfo->username;
		
		//get sender info
		$senderInfo  = self::getSenderInfo();
		$sitename 	 = $senderInfo['sitename'];
		$fromname 	 = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		$siteURL 	 = JURI::root();
		$recipient 	 = $buyerinfo->email;
		
		$tags = array("[SITENAME]", "[SITEURL]", "[PROJECTNAME]", "[PUBLISHERNAME]", "[BIDDERNAME]", "[BIDDERUSERNAME]");
		$tagsValues = array("$sitename", "$siteURL", "$projectname", "$publishername", "$biddername", "$bidderusername");
		
		//get the email template
		$template = $this->getTemplate('proj-denied-notify');
		
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
		
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
		
		// replace custom field tags
		$message = self::buildCustomFieldTags($message, $bidder_id, $project->publisher_userid, $project_id);
		
		// Send email to user
		JFactory::getMailer()->sendMail($fromaddress, $fromname, $recipient, $subject, $message, 1);
		
	}
	
	//send project/bid accepted by freelancer
	function sendProjectAcceptedNotification($project_id, $bidder_id){
		
		$db 	= JFactory::getDbo();
		$jbuser = JblanceHelper::get('helper.user');		// create an instance of the class UserHelper
		
		$project = JTable::getInstance('project', 'Table');
		$project->load($project_id);
		$projectname = $project->project_title;
		
		$buyerinfo = $jbuser->getUser($project->publisher_userid);
		$publishername = $buyerinfo->name;
		
		//check if the publisher has enabled 'bid accept' notification. If disabled, return
		$query = "SELECT notifyBidNewAcceptDeny FROM #__jblance_notify WHERE user_id=".$project->publisher_userid;
		$db->setQuery($query);
		$notify = $db->loadResult();
		
		if(!$notify) return;
		
		$bidderinfo = $jbuser->getUser($bidder_id);
		$biddername = $bidderinfo->name;
		$bidderusername = $bidderinfo->username;
		
		//get sender info
		$senderInfo  = self::getSenderInfo();
		$sitename 	 = $senderInfo['sitename'];
		$fromname 	 = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		$siteURL 	 = JURI::root();
		$recipient 	 = $buyerinfo->email;
		
		$tags = array("[SITENAME]", "[SITEURL]", "[PROJECTNAME]", "[PUBLISHERNAME]", "[BIDDERNAME]", "[BIDDERUSERNAME]");
		$tagsValues = array("$sitename", "$siteURL", "$projectname", "$publishername", "$biddername", "$bidderusername");
		
		//get the email template to send to buyer
		//---------------------------------------
		$template = $this->getTemplate('proj-accept-notify');
		
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
		
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
		
		// replace custom field tags
		$message = self::buildCustomFieldTags($message, $bidder_id, $project->publisher_userid, $project_id);
		
		// Send email to buyer
		JFactory::getMailer()->sendMail($fromaddress, $fromname, $recipient, $subject, $message, 1);
		
		//get the email template to send to freelancer
		//--------------------------------------------
		$template = $this->getTemplate('proj-accept-notify-bidder');
		
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
		
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
		
		// replace custom field tags
		$message = self::buildCustomFieldTags($message, $bidder_id, $project->publisher_userid, $project_id);
		
		// Send email to freelancer
		JFactory::getMailer()->sendMail($fromaddress, $fromname, $bidderinfo->email, $subject, $message, 1);
		
		// send email to other bidders who has lost it
		//--------------------------------------------
		$tags = array("[SITENAME]", "[SITEURL]", "[PROJECTNAME]");
		$tagsValues = array("$sitename", "$siteURL", "$projectname");
		
		$template = $this->getTemplate('proj-bid-loosers-notify');
		
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
		
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
		
		$query = "SELECT email FROM #__jblance_bid b
				  LEFT JOIN #__users u ON u.id=b.user_id
				  WHERE project_id=".$db->quote($project_id)." AND user_id NOT IN ($project->assigned_userid)";//echo $query;exit;
		$db->setQuery($query);
		$bccLosers = $db->loadColumn();
		
		// Send email to loosers
		if(count($bccLosers) > 0){
			JFactory::getMailer()->sendMail($fromaddress, $fromname, $fromaddress, $subject, $message, 1, null, $bccLosers);
		}
	}
	
	function buildCustomFieldTags($message, $bidder_id = 0, $publisher_id = 0, $project_id = 0){
		
		//apend custom field
		$fields = self::buildCustomFieldValues();
		$tagKeys = array();
		$tagValues = array();
		foreach ($fields as $field){
			if($field->userid > 0 && $field->userid == $bidder_id){
				$tagKeys[] = "[CUSTOM_".$field->fieldid."_BIDDER]";
				$tagValues[] = empty($field->value) ? '-' : nl2br($field->value);
			}
			if($field->userid > 0 && $field->userid == $publisher_id){
				$tagKeys[] = "[CUSTOM_".$field->fieldid."_PUBLISHER]";
				$tagValues[] = empty($field->value) ? '-' : nl2br($field->value);
			}
			if($field->projectid > 0 && $field->projectid == $project_id){
				$tagKeys[] = "[CUSTOM_".$field->fieldid."_PROJECT]";
				$tagValues[] = empty($field->value) ? '-' : nl2br($field->value);
			}
		}
		$message = str_replace($tagKeys, $tagValues, $message);
		return $message;
	}

	//send project pending approval to admin
	function sendAdminProjectPendingApproval($project_id){
		
		$project = JTable::getInstance('project', 'Table');
		$project->load($project_id);
		$projectname = $project->project_title;
		
		$buyerinfo = JFactory::getUser($project->publisher_userid);
		$publisherusername = $buyerinfo->username;
		
		//get sender info
		$senderInfo  = self::getSenderInfo();
		$sitename 	 = $senderInfo['sitename'];
		$fromname 	 = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		$adminURL 	 = JURI::base().'administrator';
		
		$tags = array("[SITENAME]", "[ADMINURL]", "[PROJECTNAME]", "[PUBLISHERUSERNAME]");
		$tagsValues = array("$sitename", "$adminURL", "$projectname", "$publisherusername");
		
		//get the email template
		$template = $this->getTemplate('proj-pending-approval');
		
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
		
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
		
		//get all email address eligible to receive system emails
		$rows = self::getSuperAdminEmail();
		
		// replace custom field tags
		$message = self::buildCustomFieldTags($message, 0, $project->publisher_userid, $project_id);
		
		// Send notification to all administrators
		foreach($rows as $row){
			JFactory::getMailer()->sendMail($fromaddress, $fromname, $row->email, $subject, $message, 1);
		}
	}
	
	//send project approved to publisher
	function sendPublisherProjectApproved($project_id){
		
		$project = JTable::getInstance('project', 'Table');
		$project->load($project_id);
		$projectname = $project->project_title;
		$projecturl = JURI::root().'index.php?option=com_jblance&view=project&layout=detailproject&id='.$project->id;
		
		//get publisher info
		$publisher = JFactory::getUser($project->publisher_userid);
		$publishername = $publisher->name;
		
		//get sender info
		$senderInfo  = self::getSenderInfo();
		$sitename 	 = $senderInfo['sitename'];
		$fromname 	 = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		$recipient 	 = $publisher->email;
		
		$tags = array("[SITENAME]", "[PROJECTURL]", "[PROJECTNAME]", "[PUBLISHERNAME]");
		$tagsValues = array("$sitename", "$projecturl", "$projectname", "$publishername");
		
		//get the email template
		$template = $this->getTemplate('proj-approved');
		
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
		
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
		
		// replace custom field tags
		$message = self::buildCustomFieldTags($message, 0, $project->publisher_userid, $project_id);
		
		// Send email to user
		JFactory::getMailer()->sendMail($fromaddress, $fromname, $recipient, $subject, $message, 1);
	}
	
	//send send project payment complete notification
	function sendProjectPaymentCompleteNotification($project_id, $marker_id){
		$project = JTable::getInstance('project', 'Table');
		$project->load($project_id);
		$projectname = $project->project_title;
		
		//get sender info
		$senderInfo  = self::getSenderInfo();
		$sitename 	 = $senderInfo['sitename'];
		$siteURL 	 = JURI::root();
		$fromname 	 = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		
		//get marker information
		$marker = JFactory::getUser($marker_id);
		$markerUsername = $marker->username;
		
		//logic to find the recipient
		$publisher_userid = $project->publisher_userid;
		$assigned_userid = $project->assigned_userid;
		if($marker_id == $publisher_userid){
			$recipient = JFactory::getUser($assigned_userid);
			$recipientUsername = $recipient->username;
			$recipientEmail = $recipient->email;
		}
		elseif($marker_id == $assigned_userid){
			$recipient = JFactory::getUser($publisher_userid);
			$recipientUsername = $recipient->username;
			$recipientEmail = $recipient->email;
		}
			
		$tags = array("[SITENAME]", "[SITEURL]", "[PROJECTNAME]", "[RECIPIENT_USERNAME]", "[MARKEDBY_USERNAME]");
		$tagsValues = array("$sitename", "$siteURL", "$projectname", "$recipientUsername", "$markerUsername");
		
		//get the email template
		$template = $this->getTemplate('proj-payment-complete');
		
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
		
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
		
		// Send email to user
		JFactory::getMailer()->sendMail($fromaddress, $fromname, $recipientEmail, $subject, $message, 1);
	}
	
	//send project progress notification to buyer
	function sendProjectProgressNotification($bid_id, $project_id){
		$project = JTable::getInstance('project', 'Table');
		$project->load($project_id);
		$projectName 	 = $project->project_title;
	
		$bid = JTable::getInstance('bid', 'Table');
		$bid->load($bid_id);
		$status = JText::_($bid->p_status);
		$percent = $bid->p_percent.' %';
	
		//get recipient
		$buyer = JFactory::getUser($project->publisher_userid);	//recipient is the buyer
		$buyerUsername = $buyer->username;
	
		//get sender info
		$senderInfo  = self::getSenderInfo();
		$sitename 	 = $senderInfo['sitename'];
		$fromname 	 = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		$siteURL 	 = JURI::root();
	
		$tags 		= array("[SITENAME]", "[SITEURL]", "[BUYER_USERNAME]", "[PROJECTNAME]", "[PROJECTID]", "[STATUS]", "[PERCENT]");
		$tagsValues = array("$sitename", "$siteURL", "$buyerUsername", "$projectName", "$project_id", "$status", "$percent");
	
		//get the email template
		$template = $this->getTemplate('proj-progress-notify');
	
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
	
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
	
		// replace custom field tags
		//$message = self::buildCustomFieldTags($message, $bid->user_id, $project->publisher_userid, $project_id);
	
		// Send email to user
		JFactory::getMailer()->sendMail($fromaddress, $fromname, $buyer->email, $subject, $message, 1);
	
	}
	
	//send new forum message notification
	function sendForumMessageNotification($post){
		$db 	= JFactory::getDbo();
	
		//get message info from the post variable
		$poster_id 	   	= $post['user_id'];
		$project_id	   	= $post['project_id'];
		$project_title 	= $post['project_title'];
		$message 		= $post['message'];
		$projecturl 	= JURI::root().'index.php?option=com_jblance&view=project&layout=detailproject&id='.$project_id;
		$publisher_userid = $post['publisher_userid'];
	
		//get sender info
		$senderInfo  = self::getSenderInfo();
		$sitename 	 = $senderInfo['sitename'];
		$fromname 	 = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		$siteURL 	 = JURI::root();
		$recipient 	 = $senderInfo['fromaddress'];
	
		$posterInfo = JFactory::getUser($poster_id);
		$publisherInfo = JFactory::getUser($publisher_userid);
	
		//get the recipient list. Do not send to poster and users not receive notification
		$query = "SELECT DISTINCT u.email FROM #__jblance_forum f ".
				 "INNER JOIN #__users u ON u.id=f.user_id ".
				 "LEFT JOIN #__jblance_notify n ON f.user_id=n.user_id ".
				 "WHERE f.project_id=".$db->quote($project_id)." AND f.user_id !=".$db->quote($poster_id);
		$db->setQuery($query);
		$bcc = $db->loadColumn();
		
		if($publisher_userid != $poster_id)
			$bcc[] = $publisherInfo->email;		// always add project author email but if he is adding message, leave him
		
		$bcc = array_unique($bcc);			// make the array unique
		
		if(count($bcc) > 0){
			$tags = array("[SITENAME]", "[SITEURL]", "[POSTERUSERNAME]", "[PROJECTNAME]", "[PROJECTURL]", "[FORUMMESSAGE]");
			$tagsValues = array("$sitename", "$siteURL", "$posterInfo->username", "$project_title", "$projecturl", "$message");
		
			//get the email template
			$template = $this->getTemplate('proj-newforum-notify');
		
			//get subject
			$subject = $template->subject;
			$subject = str_replace($tags, $tagsValues, $subject);
			$subject = html_entity_decode($subject, ENT_QUOTES);
		
			//get message body
			$message = $template->body;
			$message = str_replace($tags, $tagsValues, $message);
			$message = html_entity_decode($message, ENT_QUOTES);
		
			// Send email to user
			JFactory::getMailer()->sendMail($fromaddress, $fromname, $recipient, $subject, $message, 1, null, $bcc);
		}
	}
	
	//send service order notification to seller
	function sendServiceOrderNotification($order_id, $service_id){
		
		$service = JTable::getInstance('service', 'Table');
		$service->load($service_id);
		$serviceName 	 = $service->service_title;
		$servicePrice 	 = JblanceHelper::formatCurrency($service->price, true, true);
		$serviceDuration = $service->duration.' '.JText::_('COM_JBLANCE_DAYS');
		$serviceUrl 	 = JURI::root().'index.php?option=com_jblance&view=service&layout=viewservice&id='.$service_id;
		
		$order = JTable::getInstance('serviceorder', 'Table');
		$order->load($order_id);
		$totalPrice    = JblanceHelper::formatCurrency($order->price, true, true);
		$totalDuration = $order->duration.' '.JText::_('COM_JBLANCE_DAYS');
		
		$config 	  = JblanceHelper::getConfig();
		$currencysym  = $config->currencySymbol;
		$currencycode = $config->currencyCode;
		
		//get recipient
		$seller = JFactory::getUser($service->user_id);	//recipient is the freelancer or seller
		$sellerUsername = $seller->username;
		
		//get sender info
		$senderInfo  = self::getSenderInfo();
		$sitename 	 = $senderInfo['sitename'];
		$fromname 	 = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		$siteURL 	 = JURI::root();
		
		$tags 		= array("[SITENAME]", "[SITEURL]", "[SELLER_USERNAME]", "[SERVICENAME]", "[SERVICEPRICE]", "[SERVICEDURATION]", "[TOTALPRICE]", "[TOTALDURATION]", "[SERVICEURL]");
		$tagsValues = array("$sitename", "$siteURL", "$sellerUsername", "$serviceName", "$servicePrice", "$serviceDuration", "$totalPrice", "$totalDuration", "$serviceUrl");
		
		//get the email template
		$template = $this->getTemplate('svc-neworder-notify');
		
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
		
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
		
		// replace custom field tags
		//$message = self::buildCustomFieldTags($message, $bid->user_id, $project->publisher_userid, $project_id);
		
		// Send email to user
		JFactory::getMailer()->sendMail($fromaddress, $fromname, $seller->email, $subject, $message, 1);
	}
	
	//send service progress notification to buyer
	function sendServiceProgressNotification($order_id, $service_id){
		$service = JTable::getInstance('service', 'Table');
		$service->load($service_id);
		$serviceName 	 = $service->service_title;
		
		$order = JTable::getInstance('serviceorder', 'Table');
		$order->load($order_id);
		$status = JText::_($order->p_status);
		$percent = $order->p_percent.' %';
		
		//get recipient
		$buyer = JFactory::getUser($order->user_id);	//recipient is the freelancer or seller
		$buyerUsername = $buyer->username;
		
		//get sender info
		$senderInfo  = self::getSenderInfo();
		$sitename 	 = $senderInfo['sitename'];
		$fromname 	 = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		$siteURL 	 = JURI::root();
		
		$tags 		= array("[SITENAME]", "[SITEURL]", "[BUYER_USERNAME]", "[SERVICENAME]", "[ORDERID]", "[STATUS]", "[PERCENT]");
		$tagsValues = array("$sitename", "$siteURL", "$buyerUsername", "$serviceName", "$order_id", "$status", "$percent");
		
		//get the email template
		$template = $this->getTemplate('svc-progress-notify');
		
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
		
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
		
		// replace custom field tags
		//$message = self::buildCustomFieldTags($message, $bid->user_id, $project->publisher_userid, $project_id);
		
		// Send email to user
		JFactory::getMailer()->sendMail($fromaddress, $fromname, $buyer->email, $subject, $message, 1);
	}
	
	//send service pending approval to admin
	function sendAdminServicePendingApproval($service_id){
	
		$service = JTable::getInstance('service', 'Table');
		$service->load($service_id);
		$serviceName = $service->service_title;
	
		$sellerInfo = JFactory::getUser($service->user_id);
		$sellerUsername = $sellerInfo->username;
	
		//get sender info
		$senderInfo  = self::getSenderInfo();
		$sitename 	 = $senderInfo['sitename'];
		$fromname 	 = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		$adminURL 	 = JURI::base().'administrator';
	
		$tags = array("[SITENAME]", "[ADMINURL]", "[SERVICENAME]", "[SELLER_USERNAME]");
		$tagsValues = array("$sitename", "$adminURL", "$serviceName", "$sellerUsername");
	
		//get the email template
		$template = $this->getTemplate('svc-pending-approval');
	
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
	
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
	
		//get all email address eligible to receive system emails
		$rows = self::getSuperAdminEmail();
	
		// Send notification to all administrators
		foreach($rows as $row){
			JFactory::getMailer()->sendMail($fromaddress, $fromname, $row->email, $subject, $message, 1);
		}
	}

	//send seller service approval status
	function sendSellerServiceApprovalStatus($service_id){
	
		$service = JTable::getInstance('service', 'Table');
		$service->load($service_id);
		$serviceName = $service->service_title;
		$serviceUrl = JURI::root().'index.php?option=com_jblance&view=service&layout=viewservice&id='.$service->id;
	
		//get seller info
		$sellerInfo = JFactory::getUser($service->user_id);
		$sellerUsername = $sellerInfo->username;
		
		//get approval status and message
		$approved = $service->approved;
		if($approved){
			$approvalStatus = JText::_('COM_JBLANCE_APPROVED');
			$approvalMessage = JText::_('COM_JBLANCE_YOUR_SERVICE_IS_APPROVED');
			
		}
		else {
			$approvalStatus = JText::_('COM_JBLANCE_NEEDS_REVISION');
			$approvalMessage = $service->disapprove_reason;
		}
	
		//get sender info
		$senderInfo  = self::getSenderInfo();
		$sitename 	 = $senderInfo['sitename'];
		$fromname 	 = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		$recipient 	 = $sellerInfo->email;
	
		$tags = array("[SITENAME]", "[SERVICEURL]", "[SERVICENAME]", "[SELLER_USERNAME]", "[APPROVAL_STATUS]", "[APPROVAL_MESSAGE]");
		$tagsValues = array("$sitename", "$serviceUrl", "$serviceName", "$sellerUsername", "$approvalStatus", "$approvalMessage");
	
		//get the email template
		$template = $this->getTemplate('svc-approval_status');
	
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
	
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
	
		// Send email to user
		JFactory::getMailer()->sendMail($fromaddress, $fromname, $recipient, $subject, $message, 1);
	}
	
	//send withdraw request to admin
	function sendWithdrawFundRequest($withdraw_id){
		
		$withdraw = JTable::getInstance('withdraw', 'Table');
		$withdraw->load($withdraw_id);
		
		$invoiceNo = $withdraw->invoiceNo;
		$user_id   = $withdraw->user_id;
		$gateway   = JblanceHelper::getGwayName($withdraw->gateway);
		
		//get requestor info
		$requestor 	 = JFactory::getUser($user_id);
		$name		 = $fromname = $requestor->name;
		$username 	 = $requestor->username;
		$fromaddress = $requestor->email;
		$adminURL 	 = JURI::base().'administrator';
		
		$tags = array("[NAME]", "[USERNAME]", "[INVOICENO]", "[ADMINURL]", "[GATEWAY]");
		$tagsValues = array("$name", "$username", "$invoiceNo", "$adminURL", "$gateway");
		
		//get the email template
		$template = $this->getTemplate('fin-witdrw-request');
		
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
		
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
		
		//get all super administrator
		$rows = self::getSuperAdminEmail();
		
		// Send notification to all administrators
		foreach($rows as $row){
			JFactory::getMailer()->sendMail($fromaddress, $fromname, $row->email, $subject, $message, 1);
		}
	}
	
	//send withdraw request approved to user
	function sendWithdrawRequestApproved($withdraw_id){
		
		$withdraw = JTable::getInstance('withdraw', 'Table');
		$withdraw->load($withdraw_id);
		
		$invoiceNo 	= $withdraw->invoiceNo;
		$user_id 	= $withdraw->user_id;
		$amount		= JblanceHelper::formatCurrency($withdraw->amount, false);
		
		//get requestor info
		$requestor = JFactory::getUser($user_id);
		$name = $requestor->name;
		
		$config 	  = JblanceHelper::getConfig();
		$currencysym  = $config->currencySymbol;
		$currencycode = $config->currencyCode;
		
		//get sender info
		$senderInfo  = self::getSenderInfo();
		$sitename 	 = $senderInfo['sitename'];
		$fromname 	 = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		$siteURL 	 = JURI::root();
		$recipient 	 = $requestor->email;
		
		$tags = array("[NAME]", "[CURRENCYSYM]", "[AMOUNT]", "[INVOICENO]", "[SITENAME]", "[SITEURL]");
		$tagsValues = array("$name", "$currencysym", "$amount", "$invoiceNo", "$sitename", "$siteURL");
		
		//get the email template
		$template = $this->getTemplate('fin-witdrw-approved');
		
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
		
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
		
		// Send email to user
		JFactory::getMailer()->sendMail($fromaddress, $fromname, $recipient, $subject, $message, 1);
	}

	//send escrow payment received to the bid winner
	function sendEscrowPaymentReleased($escrow_id){
		$escrow	= JTable::getInstance('escrow', 'Table');
		$escrow->load($escrow_id);
		
		$config 	 = JblanceHelper::getConfig();
		$currencysym = $config->currencySymbol;
		$dformat	 = $config->dateFormat;
		
		$receiver = JFactory::getUser($escrow->to_id);
		$sender = JFactory::getUser($escrow->from_id);
		
		//get project name
		$project	= JTable::getInstance('project', 'Table');
		$project->load($escrow->project_id);
		$projectname = $project->project_title;
		
		if(empty($projectname))
			$projectname = JText::_('COM_JBLANCE_NA');
		
		$senderUsername  = $sender->username;
		$receiveUsername = $receiver->username;
		$releaseDate	 = JHtml::_('date', $escrow->date_release, $dformat, false);
		$amount 		 = JblanceHelper::formatCurrency($escrow->amount, false);;
		$note 			 = $escrow->note;
		
		//get sender info
		$senderInfo  = self::getSenderInfo();
		$sitename 	 = $senderInfo['sitename'];
		$fromname 	 = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		$siteURL 	 = JURI::root();
		$recipient 	 = $receiver->email;
		
		$tags = array("[SITENAME]", "[SITEURL]", "[PROJECTNAME]", "[SENDERUSERNAME]", "[RECEIVEUSERNAME]", "[RELEASEDATE]", "[CURRENCYSYM]", "[AMOUNT]", "[NOTE]");
		$tagsValues = array("$sitename", "$siteURL", "$projectname", "$senderUsername", "$receiveUsername", "$releaseDate", "$currencysym", "$amount", "$note");
		
		//get the email template
		$template = $this->getTemplate('fin-escrow-released');
		
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
		
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
		
		// Send email to user
		JFactory::getMailer()->sendMail($fromaddress, $fromname, $recipient, $subject, $message, 1);
	}
	
	//send escrow pymt accepted to sender
	function sendEscrowPaymentAccepted($escrow_id){
		$escrow	= JTable::getInstance('escrow', 'Table');
		$escrow->load($escrow_id);
		
		$config 	 = JblanceHelper::getConfig();
		$currencysym = $config->currencySymbol;
		$dformat	 = $config->dateFormat;
		
		$receiver = JFactory::getUser($escrow->to_id);
		$sender   = JFactory::getUser($escrow->from_id);
		
		//get project name
		$project	= JTable::getInstance('project', 'Table');
		$project->load($escrow->project_id);
		$projectname = $project->project_title;
		
		if(empty($projectname))
			$projectname = JText::_('COM_JBLANCE_NA');
		
		$senderUsername  = $sender->username;
		$receiveUsername = $receiver->username;
		$releaseDate	 = JHtml::_('date', $escrow->date_release, $dformat, false);
		$amount 		 = JblanceHelper::formatCurrency($escrow->amount, false);
		$note 			 = $escrow->note;
		
		//get sender info
		$senderInfo  = self::getSenderInfo();
		$sitename 	 = $senderInfo['sitename'];
		$fromname 	 = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		$siteURL 	 = JURI::root();
		$recipient 	 = $sender->email;
		
		$tags = array("[SITENAME]", "[SITEURL]", "[PROJECTNAME]", "[SENDERUSERNAME]", "[RECEIVEUSERNAME]", "[RELEASEDATE]", "[CURRENCYSYM]", "[AMOUNT]", "[NOTE]");
		$tagsValues = array("$sitename", "$siteURL", "$projectname", "$senderUsername", "$receiveUsername", "$releaseDate", "$currencysym", "$amount", "$note");
		
		//get the email template
		$template = $this->getTemplate('fin-escrow-accepted');
		
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
		
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
		
		// Send email to user
		JFactory::getMailer()->sendMail($fromaddress, $fromname, $recipient, $subject, $message, 1);
	}
	
	//send deposit fund alert to admin
	function sendAdminDepositFund($deposit_id){
	
		$config 	  = JblanceHelper::getConfig();
		$currencysym  = $config->currencySymbol;
		
		$deposit = JTable::getInstance('deposit', 'Table');
		$deposit->load($deposit_id);
	
		$invoiceNo = $deposit->invoiceNo;
		$user_id   = $deposit->user_id;
		$gateway   = JblanceHelper::getGwayName($deposit->gateway);
		$amount	   = JblanceHelper::formatCurrency($deposit->amount, false);
	
		//get depositor info
		$depositor 	 = JFactory::getUser($user_id);
		$name		 = $fromname = $depositor->name;
		$username 	 = $depositor->username;
		$fromaddress = $depositor->email;
		$adminURL 	 = JURI::base().'administrator';
		
		if($deposit->approved)
			$status = JText::_('COM_JBLANCE_APPROVED');
		else
			$status = JText::_('COM_JBLANCE_PENDING');
	
		$tags = array("[NAME]", "[USERNAME]", "[INVOICENO]", "[ADMINURL]", "[GATEWAY]", "[STATUS]", "[AMOUNT]", "[CURRENCYSYM]");
		$tagsValues = array("$name", "$username", "$invoiceNo", "$adminURL", "$gateway", "$status", $amount, $currencysym);
	
		//get the email template
		$template = $this->getTemplate('fin-deposit-alert');
	
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
	
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
	
		//get all super administrator
		$rows = self::getSuperAdminEmail();
	
		// Send notification to all administrators
		foreach($rows as $row){
			JFactory::getMailer()->sendMail($fromaddress, $fromname, $row->email, $subject, $message, 1);
		}
	}

	//send approved deposit fund to depositor
	function sendUserDepositFundApproved($deposit_id){
		$config 	  = JblanceHelper::getConfig();
		$currencysym  = $config->currencySymbol;
		
		$deposit = JTable::getInstance('deposit', 'Table');
		$deposit->load($deposit_id);
		
		$invoiceNo = $deposit->invoiceNo;
		$user_id   = $deposit->user_id;
		$gateway   = JblanceHelper::getGwayName($deposit->gateway);
		$amount	   = JblanceHelper::formatCurrency($deposit->amount, false);
		
		//get depositor info
		$depositor 	 = JFactory::getUser($user_id);
		$name		 = $depositor->name;
		$username 	 = $depositor->username;
		
		//get sender info
		$senderInfo  = self::getSenderInfo();
		$sitename 	 = $senderInfo['sitename'];
		$fromname 	 = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		$siteURL 	 = JURI::root();
		$recipient 	 = $depositor->email;
		
		$tags = array("[NAME]", "[CURRENCYSYM]", "[AMOUNT]", "[INVOICENO]", "[SITENAME]", "[SITEURL]");
		$tagsValues = array("$name", "$currencysym", "$amount", "$invoiceNo", "$sitename", "$siteURL");
		
		//get the email template
		$template = $this->getTemplate('fin-deposit-approved');
		
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
		
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
		
		// Send email to user
		JFactory::getMailer()->sendMail($fromaddress, $fromname, $recipient, $subject, $message, 1);
	}

	//send PM notification to the recipient
	function sendMessageNotification($msg_id){
		$db 	= JFactory::getDbo();
		
		$message = JTable::getInstance('message', 'Table');
		$message->load($msg_id);
		
		//get message info
		$sender_id 	  = $message->idFrom;
		$recipient_id = $message->idTo;
		$msg_subject  = $message->subject;
		$msg_body 	  = $message->message;
		
		//get sender info
		$senderInfo  = self::getSenderInfo();
		$sitename 	 = $senderInfo['sitename'];
		$fromname 	 = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		$siteURL 	 = JURI::root();
		
		$msgSenderInfo = JFactory::getUser($sender_id);
		$msgRecipientInfo = JFactory::getUser($recipient_id);
		
		//check if the recipient has enabled 'new message' notification. If disabled, return
		$query = "SELECT notifyNewMessage FROM #__jblance_notify WHERE user_id=".$recipient_id;
		$db->setQuery($query);
		$notify = $db->loadResult();
		
		if(!$notify) return;
		
		$tags = array("[RECIPIENT_USERNAME]", "[SENDER_USERNAME]", "[MSG_SUBJECT]", "[MSG_BODY]", "[SITENAME]", "[SITEURL]");
		$tagsValues = array("$msgRecipientInfo->username", "$msgSenderInfo->username", "$msg_subject", "$msg_body", "$sitename", "$siteURL");
		
		//get the email template
		$template = $this->getTemplate('pm-new-notify');
		
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
		
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
		
		// Send email to user
		JFactory::getMailer()->sendMail($fromaddress, $fromname, $msgRecipientInfo->email, $subject, $message, 1);
	}
	
	//send message pending approval to admin
	function sendAdminMessagePendingApproval($msg_id){
	
		$message = JTable::getInstance('message', 'Table');
		$message->load($msg_id);
		
		//get message info
		$sender_id 	  = $message->idFrom;
		$recipient_id = $message->idTo;
		$msg_subject  = $message->subject;
		$msg_body 	  = $message->message;
		
		$msgSenderInfo = JFactory::getUser($sender_id);
		$msgRecipientInfo = JFactory::getUser($recipient_id);
	
		//get sender info
		$senderInfo  = self::getSenderInfo();
		$sitename 	 = $senderInfo['sitename'];
		$fromname 	 = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		$siteURL 	 = JURI::root();
		$adminURL 	 = JURI::base().'administrator';
		
		$tags = array("[RECIPIENT_USERNAME]", "[SENDER_USERNAME]", "[MSG_SUBJECT]", "[MSG_BODY]", "[SITENAME]", "[SITEURL]", "[ADMINURL]");
		$tagsValues = array("$msgRecipientInfo->username", "$msgSenderInfo->username", "$msg_subject", "$msg_body", "$sitename", "$siteURL", "$adminURL");
	
		//get the email template
		$template = $this->getTemplate('pm-pending-approval');
	
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
	
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
	
		//get all email address eligible to receive system emails
		$rows = self::getSuperAdminEmail();
	
		// Send notification to all administrators
		foreach($rows as $row){
			JFactory::getMailer()->sendMail($fromaddress, $fromname, $row->email, $subject, $message, 1);
		}
	}
	
	function sendReportingDefaultAction($report, $result){
		
		//get sender info
		$senderInfo  = self::getSenderInfo();
		$sitename 	 = $senderInfo['sitename'];
		$fromname 	 = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		
		$type = $result['type'];
		$count = $report->getReportersCount();
		$action = $result['action'];
		$itemlink = $report->link;
		
	
		$tags = array("[TYPE]", "[COUNT]", "[ACTION]", "[ITEMLINK]", "[SITENAME]");
		$tagsValues = array("$type", "$count", "$action", "$itemlink", "$sitename");
	
		//get the email template
		$template = $this->getTemplate('report-default-action');
	
		//get subject
		$subject = $template->subject;
		$subject = str_replace($tags, $tagsValues, $subject);
		$subject = html_entity_decode($subject, ENT_QUOTES);
	
		//get message body
		$message = $template->body;
		$message = str_replace($tags, $tagsValues, $message);
		$message = html_entity_decode($message, ENT_QUOTES);
	
		//get all super administrator
		$rows = self::getSuperAdminEmail();
	
		// Send notification to all administrators
		foreach($rows as $row){
			JFactory::getMailer()->sendMail($fromaddress, $fromname, $row->email, $subject, $message, 1);
		}
	}
	
	function sendExpiryEmail($email, $uname, $type, $subscr_project_id){
		//get sender info
		$senderInfo  = self::getSenderInfo();
		$sitename 	 = $senderInfo['sitename'];
		$fromname 	 = $senderInfo['fromname'];
		$fromaddress = $senderInfo['fromaddress'];
		$siteURL	 = JURI::base();
		
		$config 	  = JblanceHelper::getConfig();
		$dformat	  = $config->dateFormat;
		
		//get the subscription details
		/* if($type == 'subscr'){
			$row	= JTable::getInstance('plansubscr', 'Table');
			$row->load($subscr_project_id);
			$expireDate = JHtml::_('date', $row->date_expire, $dformat, false);
		
			$query = "SELECT * FROM #__jblance_plan WHERE id = ".$row->plan_id;
			$db->setQuery($query);
			$plan = $db->loadObject();
			
			$tags = array("[SITENAME]", "[SITEURL]", "[PROJECTNAME]", "[PUBLISHERNAME]", "[PROJECTEXPIRYDATE]");
			$tagsValues = array("$sitename", "$siteURL", "$action", "$itemlink", "$sitename");
			
			//get the email template
			$template = $this->getTemplate('report-default-action');
			
			//get subject
			$subject = $template->subject;
			$subject = str_replace($tags, $tagsValues, $subject);
			$subject = html_entity_decode($subject, ENT_QUOTES);
			
			//get message body
			$message = $template->body;
			$message = str_replace($tags, $tagsValues, $message);
			$message = html_entity_decode($message, ENT_QUOTES);
		
			// Send email to user
			JFactory::getMailer()->sendMail($fromaddress, $fromname, $email, $subject, $message, 1);
		}
		else */if($type == 'project'){
			$row	= JTable::getInstance('project', 'Table');
			$row->load($subscr_project_id);
			$expiredate = JFactory::getDate($row->start_date);
			$expiredate->modify("+$row->expires days");
			$expireDate = JHtml::_('date', $expiredate, $dformat, false);
		
			$tags = array("[SITENAME]", "[SITEURL]", "[PROJECTNAME]", "[PUBLISHERNAME]", "[PROJECTEXPIRYDATE]");
			$tagsValues = array("$sitename", "$siteURL", "$row->project_title", "$uname", "$expireDate");
			
			//get the email template
			$template = $this->getTemplate('proj-expiry-reminder');
			
			//get subject
			$subject = $template->subject;
			$subject = str_replace($tags, $tagsValues, $subject);
			$subject = html_entity_decode($subject, ENT_QUOTES);
			
			//get message body
			$message = $template->body;
			$message = str_replace($tags, $tagsValues, $message);
			$message = html_entity_decode($message, ENT_QUOTES);
		
			// Send email to user
			JFactory::getMailer()->sendMail($fromaddress, $fromname, $email, $subject, $message, 1);
		}
	}
}
?>