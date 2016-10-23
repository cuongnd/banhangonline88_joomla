<?php

/*------------------------------------------------------------------------
# com_affiliatetracker - Affiliate Tracker for Joomla
# ------------------------------------------------------------------------
# author				Germinal Camps
# copyright 			Copyright (C) 2014 JoomlaThat.com. All Rights Reserved.
# @license				http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: 			http://www.JoomlaThat.com
# Technical Support:	Forum - http://www.JoomlaThat.com/support
-------------------------------------------------------------------------*/

//no direct access
defined('_JEXEC') or die('Restricted access.');
 
class AffiliateHelper{
	
	static function showATFooter(){
		require_once(JPATH_SITE.DS.'components'.DS.'com_affiliatetracker'.DS.'helpers'.DS.'version.php');
		return ATVersion::show_footer();
	}
	
	static function format($number, $before = "", $after = ""){
		
		$params =JComponentHelper::getParams( 'com_affiliatetracker' );
		
		$before = $params->get('currency_before');
		$after = $params->get('currency_after');
		
		return $before  . number_format($number, 2)  . $after ;
	}
	
	static function getAccountList($user_id = 0){
		
		$user = JFactory::getUser();
		$user_id = $user->id ;
		
		if($user_id) $where = ' WHERE user_id = ' . $user_id ;
		else return array();
		
		$db = JFactory::getDBO();
		$query = 	' SELECT * FROM #__affiliate_tracker_accounts '.
					$where .
					' ORDER BY account_name ' ;
		$db->setQuery( $query );
		$account_list = $db->loadObjectList();

		return $account_list;
	
	}
	
	static function getTypeList($user_id = 0){
		
		$user = JFactory::getUser();
		$user_id = $user->id ;
		
		if($user_id) $where = ' WHERE acc.user_id = ' . $user_id ;
		else return array();
		
		$db = JFactory::getDBO();
		$query = 	' SELECT CONCAT(at.component, ",", at.type) AS id, at.name FROM #__affiliate_tracker_conversions AS at '.
					' LEFT JOIN #__affiliate_tracker_accounts AS acc ON acc.id = at.atid '.
					$where .
					' GROUP BY at.component, at.type '.
					' ORDER BY at.name ' ;
		$db->setQuery( $query );
		$type_list = $db->loadObjectList();
		//print_r($type_list);die;
		return $type_list;
	
	}

	/**
	 * @param $data. The data of the conversion
	 * @param $userid. The affiliate user id
	 */
	static function create_conversion($data, $userid){
		$mainframe = JFactory::getApplication();
		$params = JComponentHelper::getParams( 'com_affiliatetracker' );

		$userid = (int)$userid ;
		
		$model = AffiliateHelper::getConversionAdminModel();
		require_once( JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_affiliatetracker'.DS.'tables'.DS.'conversion.php' );
		AffiliateHelper::log_data($data);		

		if($data['atid']){
			$atid = $data['atid'] ;
		}	
		elseif(isset($_COOKIE["atid"]) && $mainframe->isSite()){
		 	//there is a COOKIE and we get the atid from it
			$the_cookie = unserialize(base64_decode($_COOKIE["atid"]));
			$atid = $the_cookie["atid"] ;
		}
		elseif ($userid) {
			//there is no COOKIE, we try to retrieve the atid from the user id
			$atid = AffiliateHelper::get_atid_from_userid($userid);
		}

	 	if(!empty($atid)){
			$data['id'] = 0;
			$data['atid'] = $atid;
			$data['user_id'] = $userid;
			AffiliateHelper::log_data($data);

			$commissionToBeApplied = null;
			$commissionTypeToBeApplied = null;
			$account = self::getAccountWithCommissionsObject($atid);
			if (!empty($data['component'])) {
				//Search the specific variable commission for this component
				$found = false; $i = 0;
				while (!$found && $i < sizeof($account->variable_comissions)) {
					$compName = $account->variable_comissions[$i]->extension;
					if ($compName == $data['component']) {
						$commissionToBeApplied = $account->variable_comissions[$i]->commission;
						$commissionTypeToBeApplied = $account->variable_comissions[$i]->type;
						$found = true;
					}
					$i++;
				}
			}

			if (empty($commissionToBeApplied)) {
				$commissionToBeApplied = $account->comission;
				$commissionTypeToBeApplied = $account->type;
			}

			switch($commissionTypeToBeApplied){
				case "percent":
					$data['comission'] = $data['value'] * $commissionToBeApplied / 100;
					break;
				case "flat":
					$data['comission'] = $commissionToBeApplied;
					break;
			}

			if ($model->store($data)) {
				$msg = JText::_( 'CONVERSION_CREATED' );
				$type = "message" ;
				
			} else {
				$msg = JText::_( 'ERROR_CREATING_CONVERSION' );
				$type = "error" ;
			}
		}
	}

	/**
	 * Given a name of a component and an affiliate id, returns the account object for that affiliate with the variable commissions field in object format
	 *
	 * @param $atid. Affiliate id
	 * @return stdClass. The account object
	 */
	static function getAccountWithCommissionsObject($atid) {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select('a.*');
		$query->from($db->quoteName('#__affiliate_tracker_accounts', 'a'));
		$query->where($db->quoteName('a.id') . ' = ' . $db->quote($atid));
		$db->setQuery($query);

		$account = $db->loadObject();
		$account->variable_comissions =  json_decode($account->variable_comissions);

		return $account;

	}

	static function get_atid_from_userid($userid){
		$db = JFactory::getDBO();
		$query = " SELECT atid FROM #__affiliate_tracker_logs WHERE user_id = " . $userid ." AND atid <> 0 " ;
		$db->setQuery($query);
		$atid = $db->loadResult();

		return $atid;
	}
	
	static function log_data($data)
	{
		$f = fopen(JPATH_SITE . DS . 'cache' . DS . 'affiliates.txt', 'a');
		fwrite($f, "\n" . date('F j, Y, g:i a') . "\n");
		fwrite($f, print_r($data, true));
		fclose($f);
	}
	
	static function getConversionAdminModel()
	{
		if (!class_exists( 'ConversionsModelConversion' ))
		{
			// Build the path to the model based upon a supplied base path
			$path = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_affiliatetracker'.DS.'models'.DS.'conversion.php';
			$false = false;

			// If the model file exists include it and try to instantiate the object
			if (file_exists( $path )) {
				require_once( $path );
				if (!class_exists( 'ConversionsModelConversion' )) {
					JError::raiseWarning( 0, 'View class ConversionsModelConversion not found in file.' );
					return $false;
				}
			} else {
				JError::raiseWarning( 0, 'View ConversionsModelConversion not supported. File not found.' );
				return $false;
			}
		}

		$model = new ConversionsModelConversion();
		return $model;
	}

	static function getUserRegistrationModel()
	{
		if (!class_exists( 'UsersModelRegistration' ))
		{
			// Build the path to the model based upon a supplied base path
			$path = JPATH_SITE.DS.'components'.DS.'com_users'.DS.'models'.DS.'registration.php';
			$false = false;

			// If the model file exists include it and try to instantiate the object
			if (file_exists( $path )) {
				require_once( $path );
				if (!class_exists( 'UsersModelRegistration' )) {
					JError::raiseWarning( 0, 'Model class UsersModelRegistration not found in file.' );
					return $false;
				}
			} else {
				JError::raiseWarning( 0, 'Model UsersModelRegistration not supported. File not found.' );
				return $false;
			}
		}

		$model = new UsersModelRegistration();
		return $model;
	}
	
	static function getStatus(){
		$status = array();
		
		$status[0] = "NOT_APPROVED";
		$status[1] = "APPROVED";	
		
		return $status;
	}
	
	static function nav_tabs(){
		
		$params =JComponentHelper::getParams( 'com_affiliatetracker' );
		$itemid = $params->get('itemid');
		if($itemid != "") $itemid = "&Itemid=" . $itemid;
		
		$link_conversions = JRoute::_("index.php?option=com_affiliatetracker&view=conversions" . $itemid) ;
		$link_logs = JRoute::_("index.php?option=com_affiliatetracker&view=logs" . $itemid) ;
		$link_account = JRoute::_("index.php?option=com_affiliatetracker&view=accounts" . $itemid) ;
		$link_payments = JRoute::_("index.php?option=com_affiliatetracker&view=payments" . $itemid) ;
		$link_marketings = JRoute::_("index.php?option=com_affiliatetracker&view=marketings" . $itemid) ;
		
		$view = JRequest::getVar('view');
		
		$class_conversions = "" ;
		$class_logs = "" ;
		$class_account = "" ;
		$class_payments = "" ;
		$class_marketings = "";
		
		switch($view){
			case "conversions":
				$class_conversions = "class='active'" ;
			break;
			case "logs":
				$class_logs = "class='active'" ;
			break;
			case "accounts":
				$class_account = "class='active'" ;
			break;
			case "payments":
				$class_payments = "class='active'" ;
			break;
			case "marketings":
				$class_marketings = "class='active'" ;
		}
		
		$return = "<ul class='nav nav-tabs'>
		  <li ".$class_conversions."><a href='".$link_conversions."'>". JText::_('CONVERSIONS') ."</a></li>
		  <li ".$class_logs."><a href='".$link_logs."'>". JText::_('TRAFFIC_LOGS') ."</a></li>
		  <li ".$class_account."><a href='".$link_account."'>". JText::_('MY_ACCOUNT') ."</a></li>
		  <li ".$class_payments."><a href='".$link_payments."'>". JText::_('PAYMENTS') ."</a></li>
		  <li ".$class_marketings."><a href='".$link_marketings."'>". JText::_('MARKETINGS') ."</a></li>
		</ul>";
        
		return $return ;
        
	}
	
	static function time_options(){
		
		$return = "";
		$thetext = "";
		
		$mainframe = JFactory::getApplication();
		
		$params =JComponentHelper::getParams( 'com_affiliatetracker' );
		$itemid = $params->get('itemid');
		if($itemid != "") $itemid = "&Itemid=" . $itemid;
		
		$timeoptions[0] = new stdClass();
		$timeoptions[0]->name = 'LAST_TODAY_DAYS' ;
		$timeoptions[0]->link = 'TODAY' ;
		
		$timeoptions[1] = new stdClass();
		$timeoptions[1]->name = 'LAST_7_DAYS' ;
		$timeoptions[1]->link = '7' ;
		
		$timeoptions[2] = new stdClass();
		$timeoptions[2]->name = 'LAST_15_DAYS' ;
		$timeoptions[2]->link = '15' ;
		
		$timeoptions[3] = new stdClass();
		$timeoptions[3]->name = 'LAST_30_DAYS' ;
		$timeoptions[3]->link = '30' ;
		  
		$timeoptions[4] = new stdClass();
		$timeoptions[4]->name = 'LAST_60_DAYS' ;
		$timeoptions[4]->link = '60' ;
		
		$timeoptions[5] = new stdClass();
		$timeoptions[5]->name = 'LAST_ALLTIME_DAYS' ;
		$timeoptions[5]->link = 'ALLTIME' ;
		
		$view = JRequest::getVar('view');
		$current = $mainframe->getUserStateFromRequest('affiliate.timespan','timespan',$params->get('days',30),'timespan');
		
		foreach($timeoptions as $option){
			if($option->link == $current) $thetext = $option->name ;
			$return .= "<li><a href='".JRoute::_("index.php?option=com_affiliatetracker&view=".$view."&timespan=".$option->link.$itemid)."'>". JText::_($option->name)."</a></li>"	;
		}
		
		$return = '
			<div class="btn-group pull-right time-options dropdown">
				<a class="btn  btn-small dropdown-toggle" data-toggle="dropdown" href="#"> '.JText::_($thetext).' <span class="caret"></span> </a>
				<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
			  		'.$return.'
				</ul>
		  	</div>
		  	<script type="text/javascript">jQuery(".dropdown-toggle").dropdown();</script>
		';
		  
		  return $return ;
  
	}

	static function get_account_link($account_id = null, $ref_word = null){

		$uri = JFactory::getURI();
		$app = JFactory::getApplication();

		if (empty($account_id)) $account_id = self::getCurrentUserAtid();
		if (empty($ref_word)) $ref_word = self::getRefWordFromAtid($account_id);

		if(!empty($ref_word)) {
			$return = $uri->base() . $ref_word ;
		} else {
			$return = $uri->base() . "?atid=". $account_id ;
		}

		if(!$app->isSite()){
			$return = str_replace("/administrator", "", $return);
		}

		return $return; 
	}

	static function account_status($account){

		$uri = JFactory::getURI();
		$app = JFactory::getApplication();

		switch($account->publish){
			case 1:
				$status = 'ACTIVE';
				$class = "success";
			break;
			case 0:
				$status = 'PENDING';
				$class = "warning";
			break;
		}
		$return = '<span class="label label-'.$class.'">'.JText::_($status).'</span>' ;


		return $return; 
	}

	static function payment_status($payment){

		$uri = JFactory::getURI();
		$app = JFactory::getApplication();

		switch($payment->payment_status){
			case 1:
				$status = 'PAID';
				$class = "success";
			break;
			case 0:
				$status = 'PENDING';
				$class = "warning";
			break;
		}
		$return = '<span class="label label-'.$class.'">'.JText::_($status).'</span>' ;


		return $return; 
	}

	static function getStatusPaymentFilters(){
		$status = array();
		
		$status['unpaid'] = JText::_('UNPAID_SIMPLE');
		$status['paid'] = JText::_('PAID');
		$status['pending'] = JText::_('PENDING_VALIDATION');
		
		return $status;
	}

	static function getPaymentStatus(){
		$status = array();
		
		$status[0] = JText::_('UNPAID');
		$status[1] = JText::_('PAID');
		$status[2] = JText::_('UNPAID_ONTIME');
		$status[3] = JText::_('PENDING_VALIDATION');
		
		return $status;
	}

	static function getPaymentData($payment_id){
		
		$db = JFactory::getDBO();
		
		$query = ' SELECT pa.* FROM #__affiliate_tracker_payments AS pa WHERE pa.id = '.$payment_id;
		$db->setQuery($query);
		$payment = $db->loadObject();
		
		return $payment;
		
	}

	static function getUserPaymentOptions($user_id){
		
		$db = JFactory::getDBO();
		$query = " SELECT payment_options FROM #__affiliate_tracker_accounts WHERE payment_options != '' AND user_id = ".$user_id ;
		$db->setQuery($query);
		$payment_options = $db->loadResult();

		$payment_options = json_decode($payment_options);
		
		return $payment_options;
	}

	static function hasAccounts($user_id){

		$db = JFactory::getDBO();
		$query = " SELECT id FROM #__affiliate_tracker_accounts WHERE user_id != 0 AND user_id = ".$user_id ;
		$db->setQuery($query);
		$hasaccount = $db->loadResult();
		
		return $hasaccount;
	}

	static function versionBox() {
		$db = JFactory::getDBO();
		$query = 	' SELECT manifest_cache FROM #__extensions WHERE element = "com_affiliatetracker" AND type = "component" ';
		$db->setQuery( $query );
		$extenison_info = $db->loadObject();
		$extenison_info = json_decode($extenison_info->manifest_cache);
		$installed_version = $extenison_info->version;


		$query = 	' SELECT manifest_cache FROM #__extensions WHERE element = "affiliate_tracker" AND type = "plugin" AND folder = "system" ';
		$db->setQuery( $query );
		$plugin_info = $db->loadObject();
		$plugin_info = json_decode($plugin_info->manifest_cache);
		$installed_plugin_version = $plugin_info->version;


		$versionBox = '';
		$versionBox .= "<div class='row-fluid version-box'><div class='well well-small'><div class='container-fluid'><div class='row-fluid sys-info-title'><span>".JText::_('SYSTEM_INFO')."</span></div><div class='row-fluid'><span>".JText::sprintf('INSTALLED_VERSION', $installed_version)."</span></div><div class='row-fluid'><span>".JText::_('LATEST_VERSION')."</span><span id='latest-version'>".$installed_version."</span></div><div class='plugin-version-row row-fluid'><span>".JText::sprintf('PLUGIN_INSTALLED_VERSION', $installed_plugin_version)."</span></div><div class='row-fluid'><span>".JText::_('PLUGIN_LATEST_VERSION')."</span><span id='latest-plugin-version'>".$installed_plugin_version."</span></div><div class='row-fluid'><div id='update-info'>".JText::_('SEARCHING_UPDATES')."</div></div></div></div></div>";

		return $versionBox;
	}

	static function getVariableCommisions($user_id) {

		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select($db->quoteName('variable_comissions'));
		$query->from($db->quoteName('#__affiliate_tracker_accounts'));
		$query->where($db->quoteName('variable_comissions') . ' != '. $db->quote('\'\'') . ' AND ' . $db->quoteName('user_id') . ' = ' . $user_id);

		$db->setQuery($query);

		$payment_options = $db->loadResult();

		return json_decode($payment_options);
	}

	public static function getInstalledPlugins($withParameters = false) {
		jimport( 'joomla.registry.registry' );
		$plugins = array();

		//J2Store
		if (JPluginHelper::isEnabled('j2store', 'affiliatetracker')) {
			if (!$withParameters) array_push($plugins, 'com_j2store');
			else {
				$plugin = JPluginHelper::getPlugin('j2store', 'affiliatetracker');
				$params = new JRegistry($plugin->params);

				$returnParams = new stdClass();
				$returnParams->name = 'com_j2store';
				$returnParams->type = $params->get('defaultType','percent');
				$returnParams->commissions = explode(",", str_replace(" ", "", $params->get('defaultLevels', '')));
				array_push($plugins, $returnParams);
			}
		}

		//Akeeba Subscriptions
		if (JPluginHelper::isEnabled('akeebasubs', 'affiliatetracker')) {
			if (!$withParameters) array_push($plugins, 'com_akeebasubs');
			else {
				$plugin = JPluginHelper::getPlugin('akeebasubs', 'affiliatetracker');
				$params = new JRegistry($plugin->params);

				$returnParams = new stdClass();
				$returnParams->name = 'com_akeebasubs';
				$returnParams->type = $params->get('defaultType','percent');
				$returnParams->commissions = explode(",", str_replace(" ", "", $params->get('defaultLevels', '')));
				array_push($plugins, $returnParams);
			}
		}

		//OS Membership pro
		if (JPluginHelper::isEnabled('osmembership', 'affiliates')) {
			if (!$withParameters) array_push($plugins, 'com_osmembership');
			else {
				$plugin = JPluginHelper::getPlugin('osmembership', 'affiliates');
				$params = new JRegistry($plugin->params);

				$returnParams = new stdClass();
				$returnParams->name = 'com_osmembership';
				$returnParams->type = $params->get('defaultType','percent');
				$returnParams->commissions = explode(",", str_replace(" ", "", $params->get('defaultLevels', '')));
				array_push($plugins, $returnParams);
			}
		}

		//Emerald
		if (JPluginHelper::isEnabled('system', 'affiliates_emerald')) {
			if (!$withParameters) array_push($plugins, 'com_emerald');
			else {
				$plugin = JPluginHelper::getPlugin('system', 'affiliates_emerald');
				$params = new JRegistry($plugin->params);

				$returnParams = new stdClass();
				$returnParams->name = 'com_emerald';
				$returnParams->type = $params->get('defaultType','percent');
				$returnParams->commissions = explode(",", str_replace(" ", "", $params->get('defaultLevels', '')));
				array_push($plugins, $returnParams);
			}
		}

		//K2 Store
		if (JPluginHelper::isEnabled('k2store', 'affiliatetracker')) {
			if (!$withParameters) array_push($plugins, 'com_k2store');
			else {
				$plugin = JPluginHelper::getPlugin('k2store', 'affiliatetracker');
				$params = new JRegistry($plugin->params);

				$returnParams = new stdClass();
				$returnParams->name = 'com_k2store';
				$returnParams->type = $params->get('defaultType','percent');
				$returnParams->commissions = explode(",", str_replace(" ", "", $params->get('defaultLevels', '')));
				array_push($plugins, $returnParams);
			}
		}

		//Redshop
		if (JPluginHelper::isEnabled('system', 'affiliates_redshop')) {
			if (!$withParameters) array_push($plugins, 'com_redshop');
			else {
				$plugin = JPluginHelper::getPlugin('system', 'affiliates_redshop');
				$params = new JRegistry($plugin->params);

				$returnParams = new stdClass();
				$returnParams->name = 'com_redshop';
				$returnParams->type = $params->get('defaultType','percent');
				$returnParams->commissions = explode(",", str_replace(" ", "", $params->get('defaultLevels', '')));
				array_push($plugins, $returnParams);
			}
		}

		//HikaShop
		if (JPluginHelper::isEnabled('hikashop', 'affiliatetracker')) {
			if (!$withParameters) array_push($plugins, 'com_hikashop');
			else {
				$plugin = JPluginHelper::getPlugin('hikashop', 'affiliatetracker');
				$params = new JRegistry($plugin->params);

				$returnParams = new stdClass();
				$returnParams->name = 'com_hikashop';
				$returnParams->type = $params->get('defaultType','percent');
				$returnParams->commissions = explode(",", str_replace(" ", "", $params->get('defaultLevels', '')));
				array_push($plugins, $returnParams);
			}
		}

		//VirtueMart
		if (JPluginHelper::isEnabled('system', 'affiliates_virtuemart')) {
			if (!$withParameters) array_push($plugins, 'com_virtuemart');
			else {
				$plugin = JPluginHelper::getPlugin('system', 'affiliates_virtuemart');
				$params = new JRegistry($plugin->params);

				$returnParams = new stdClass();
				$returnParams->name = 'com_virtuemart';
				$returnParams->type = $params->get('defaultType','percent');
				$returnParams->commissions = explode(",", str_replace(" ", "", $params->get('defaultLevels', '')));
				array_push($plugins, $returnParams);
			}
		}

		//Invoice Manager
		if (JPluginHelper::isEnabled('invoices', 'affiliatetracker')) {
			if (!$withParameters) array_push($plugins, 'com_invoices');
			else {
				$plugin = JPluginHelper::getPlugin('invoices', 'affiliatetracker');
				$params = new JRegistry($plugin->params);

				$returnParams = new stdClass();
				$returnParams->name = 'com_invoices';
				$returnParams->type = $params->get('defaultType','percent');
				$returnParams->commissions = explode(",", str_replace(" ", "", $params->get('defaultLevels', '')));
				array_push($plugins, $returnParams);
			}
		}

		//Joomla users
		if (JPluginHelper::isEnabled('user', 'affiliates')) {
			if (!$withParameters) array_push($plugins, 'com_users');
			else {
				$plugin = JPluginHelper::getPlugin('user', 'affiliates');
				$params = new JRegistry($plugin->params);

				$returnParams = new stdClass();
				$returnParams->name = 'com_users';
				$returnParams->type = $params->get('defaultType','percent');
				$returnParams->commissions = explode(",", str_replace(" ", "", $params->get('defaultLevels', '')));
				array_push($plugins, $returnParams);
			}
		}

		//Payplans
		if (JPluginHelper::isEnabled('payplans', 'affiliatetracker')) {
			if (!$withParameters) array_push($plugins, 'com_payplans');
			else {
				$plugin = JPluginHelper::getPlugin('payplans', 'affiliatetracker');
				$params = new JRegistry($plugin->params);

				$returnParams = new stdClass();
				$returnParams->name = 'com_payplans';
				$returnParams->type = $params->get('defaultType','percent');
				$returnParams->commissions = explode(",", str_replace(" ", "", $params->get('defaultLevels', '')));
				array_push($plugins, $returnParams);
			}
		}

		return $plugins;
	}

	public static function getVariableCommissionByExtensionName($userVariableCommissions, $ext_name) {
		for ($i = 0; $i < sizeof($userVariableCommissions); $i++) {
			if (empty($userVariableCommissions[$i]->extension)) return new stdClass();
			if ($userVariableCommissions[$i]->extension == $ext_name) {
				return $userVariableCommissions[$i];
			}
		}
		return new stdClass();
	}

	public static function getRefWordFromAtid($atid) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('ref_word'));
		$query->from($db->quoteName('#__affiliate_tracker_accounts'));
		$query->where($db->quoteName('id') . ' = ' . $atid);

		$db->setQuery($query);

		return $db->loadResult();
	}

	public static function getCurrentUserAtid() {
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__affiliate_tracker_accounts'));
		$query->where($db->quoteName('user_id') . ' = ' . $user->id);

		$db->setQuery($query);

		return $db->loadResult();
	}

	public static function getParentUsernameFromParentId($parent_id) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query
				->select('u.username')
				->from($db->quoteName('#__users', 'u'))
				->join('LEFT', $db->quoteName('#__affiliate_tracker_accounts', 'a') . ' ON (' . $db->quoteName('u.id') . ' = ' . $db->quoteName('a.user_id') . ')')
				->where($db->quoteName('a.id') . ' = ' . $db->quote($parent_id));

		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * @param $userId. Joomla user id.
	 * @return bool. True if the user already has the maximum number of accounts created.
	 */
	public static function maxNumAccountsReached($userId) {
		
		if(!$userId) return false;

		$params = JComponentHelper::getParams( 'com_affiliatetracker' );
		$maxAccounts = $params->get('numaccountsuser', '');

		if ($maxAccounts == '') return false;

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select('count(*)')
			->from($db->quoteName('#__affiliate_tracker_accounts'))
			->where($db->quoteName('user_id') . ' = ' . $db->quote($userId));

		$db->setQuery($query);

		$numAccounts = $db->loadResult();

		if ($numAccounts >= $maxAccounts) return true;
		return false;

	}

}