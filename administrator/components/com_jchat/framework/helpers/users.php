<?php
//namespace components\com_jchat\libraries; 
/** 
 * @package JCHAT::components::com_jchat
 * @subpackage framework
 * @subpackage helpers
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html   
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Utility class for chat users
 * 
 * @package JCHAT::components::com_jchat
 * @subpackage framework
 * @subpackage helpers
 * @since 1.0
 */ 
class JChatHelpersUsers {
	/**
	 * Cross system filemtime no bugged
	 * @access public
	 * @param string $filePath
	 * @return int
	 */
	public static function crossFileMTime($filePath) {
		$time = filemtime($filePath);
	
		$isDST = (date('I', $time) == 1);
		$systemDST = (date('I') == 1);
	
		$adjustment = 0;
	
		if($isDST == false && $systemDST == true)
			$adjustment = 3600;
	
		else if($isDST == true && $systemDST == false)
			$adjustment = -3600;
	
		else
			$adjustment = 0;
	
		return ($time + $adjustment);
	}
	
	/**
	 * Effettua un reverse reduce sul'ID di sessione MD5 per arrivare
	 * ad una stringa da appendere al prefix del name assegnato ai guest users
	 * 
	 * @access public
	 * @static
	 * @param string $sessionID
	 * @param Object $cParams
	 * @return string
	 */
	public static function generateRandomGuestNameSuffix($sessionID, $cParams) {
		static $guestNamesCache = array();
		static $db = null;
		
		// First look if already generated guest name available in cache
		if(array_key_exists($sessionID, $guestNamesCache)) {
			return $guestNamesCache[$sessionID];
		}
		
		// If override guest name enabled and is guest this user and not in cache, try to check if t
		if($cParams->get('allow_guest_overridename', true)) {
			if(!is_object($db)) {
				$db = JFactory::getDbo();
			}
			
			$query = "SELECT ccs.override_name" .
					 "\n FROM #__jchat_sessionstatus AS ccs" .
					 "\n INNER JOIN #__session AS sess" .
					 "\n ON ccs.sessionid = sess.session_id" .
					 "\n WHERE sess.session_id = " . $db->quote($sessionID) .
					 "\n AND sess.client_id = 0 AND sess.guest = 1";
			$overrideNameFound = $db->setQuery($query)->loadResult();
			if($overrideNameFound) {
				$guestNamesCache[$sessionID] = $overrideNameFound;
				return $overrideNameFound;
			}
		}
		
		// Fallback on the random generators algos
		if($cParams->get('guests_name_algo', 'name_based') == 'name_based') {
			$appendHashSuffix = null;
			$guestNamesLength = $cParams->get('guests_name_length', '3');
			$vowels = array("a", "e", "i", "o", "u", "a", "e", "i", "o", "u");
			$consonants = array("b", "c", "d", "v", "g", "t", "f", "m", "r", "s");
			// Get numeric hash substr
			preg_match_all('/\d/i', $sessionID, $matches);

			if(is_array($matches[0]) && count($matches[0])) {
				for($i=0; $i<$guestNamesLength; $i++) {
					if(isset($matches[0][$i])) {
						$appendHashSuffix .= $consonants[$matches[0][$i]];
						$appendHashSuffix .= $vowels[$matches[0][$i]];
					}
				}
			}
			$appendHashSuffix = ucfirst($appendHashSuffix);
		} else {
			// Get numeric hash substr
			preg_match_all('/\d/i', $sessionID, $matches);
		
			if(is_array($matches[0]) && count($matches[0])) {
				$numericHashArray = (float)(implode('', $matches[0]));
			}

			$appendHashSuffix = $numericHashArray;

			// Limitiamo a 4 cifre il numeric suffix
			$appendHashSuffix = $cParams->get('guestprefix', 'Guest') . substr($appendHashSuffix, 0, 4);
		}
		
		// First store in cache for next message
		$guestNamesCache[$sessionID] = $appendHashSuffix;
	
		return $appendHashSuffix;
	}
	
	/**
	 * Get names for users based on current state, logged or guest
	 * 
	 * @access public
	 * @static
	 * @param string $sessionIDFrom
	 * @param string $sessionIDTo
	 * @param Object $componentParams
	 * @return array
	 */
	
	public static function getActualNames($sessionIDFrom, $sessionIDTo, $componentParams) {
		// Load user table
		$userTable = JTable::getInstance('user');
		
		// Load user session table
		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
		$userSessionTable = JTable::getInstance('Session', 'JChatTable');
		
		// Current chosen user field name
		$userFieldName = $componentParams->get('usefullname', 'username');
		
		// Sender actualfrom
		$userSessionTable->load($sessionIDFrom);
		$userTable->load($userSessionTable->userid);
		$actualFrom = $userTable->{$userFieldName};
		if(!$actualFrom) {
			$actualFrom = self::generateRandomGuestNameSuffix($sessionIDFrom, $componentParams);
		}
	
		// Receiver actualto
		$receiverSessionTable = clone($userSessionTable);
		$receiverSessionTable->load($sessionIDTo);
		$userTable->load($receiverSessionTable->userid);
		$actualTo = $userTable->{$userFieldName};
		if(!$actualTo) {
			$actualTo = self::generateRandomGuestNameSuffix($receiverSessionTable->session_id, $componentParams);
		}
		
		$result = array();
		$result['fromActualName'] = $actualFrom;
		$result['toActualName'] = $actualTo;

		return $result;
	}
	
	/**
	 * Return current user session table object with singleton
	 * @access private
	 * @static
	 * @return Object
	 */
	public static function getSessionTable() {
		// Lazy loading user session
		static $userSessionTable;
		
		if(!is_object($userSessionTable)) {
			JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
			$userSessionTable = JTable::getInstance('Session', 'JChatTable');
			$userSessionTable->load(session_id());
		}
	
		return $userSessionTable;
	}
	
	/**
	 * Singleton for session object
	 * @static
	 *
	 * @access private
	 * @return Object
	 */
	public static function getEmptySessionTable() {
		static $sessionTable;
	
		if(!is_object($sessionTable)) {
			JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
			$sessionTable = JTable::getInstance('Session', 'JChatTable');
		}
	
		return $sessionTable;
	}
	
	/**
	 * Generate and assign avatars to users
	 * Caching system for $sessionID->avatar associated for the still same request
	 *
	 * @param string $sessionID
	 * @param int $explicitUserId
	 * @return string
	 */
	public static function getAvatar($sessionID, $explicitUserId = null) {
		static $avatarCache = array();
		$integrationType = null;
		
		// Avoid uneeded queries
		if(isset($avatarCache[$sessionID])) {
			return $avatarCache[$sessionID];
		}
		
		$baseURL = JUri::root();
		$cParams = JComponentHelper::getParams('com_jchat');
		$avatarFormat = 'png';
		$avatarSubPath = '/images/avatars/';
		$userGender = null;
	
		// User session object
		$userSessionTable = self::getEmptySessionTable();
		if(!$explicitUserId) {
			$userSessionTable->load($sessionID);
			$userId = $userSessionTable->userid;
		} else {
			$userId = $explicitUserId;
		}
	
		$thirdPartyIntegration = $cParams->get('3pdintegration', false);
		switch ($thirdPartyIntegration) {
			case 'jomsocial':
				$integrationType = 'jomsocial/';
				$userGender = '_male';
				break;

			case 'easysocial':
				$integrationType = 'easysocial/';
				break;

			case 'cbuilder':
				$integrationType = 'cb/';
				break;
		}
		
		// PRIORITY 1 - Try for JomSocial avatar if integration active
		if($thirdPartyIntegration === 'jomsocial' && $userId) {
			$DBO = JFactory::getDBO();
			$sql = 	"SELECT CONCAT('$baseURL', thumb) AS avatar" .
					"\n FROM #__community_users AS cu" .
					"\n INNER JOIN #__users AS u ON cu.userid = u.id" .
					"\n WHERE u.id = " . $DBO->quote($userId) .
					"\n AND cu.thumb != ''";
			$DBO->setQuery($sql);
			if($userAvatar = $DBO->loadResult()) {
				$avatarCache[$sessionID] = $userAvatar;
				return $userAvatar;
			}
			
			// Select the gender of the user if available
			$sql = 	"SELECT v.value AS gender" .
					"\n FROM #__community_fields AS f" .
					"\n LEFT JOIN #__community_fields_values AS v ON f.id = v.field_id" .
					"\n WHERE f.fieldcode = " . $DBO->quote('FIELD_GENDER') .
					"\n AND v.user_id = " . $DBO->quote($userId);
			$DBO->setQuery($sql);
			if($userAvatarGender = $DBO->loadResult()) {
				$userGender = stripos($userAvatarGender, 'female') !== false ? '_female' : '_male';
			}
		}
	
		// PRIORITY 1 - Try for EasySocial avatar if integration active
		if($thirdPartyIntegration === 'easysocial' && $userId) {
			$DBO = JFactory::getDBO();
			$easySocialAvatarPath = $cParams->get('easysocial_avatar_path', 'media/com_easysocial');

			// Check if remote server and specific uploaded avatar by default
			if($cParams->get('easysocial_custom_avatar', 0)) {
				$baseURL = rtrim($cParams->get('easysocial_custom_avatar_path', ''), '/') . '/';
			}
			$sql = 	"SELECT CONCAT('" . $baseURL . "$easySocialAvatarPath/avatars/users/" . $userId . "/', square) AS avatar" .
					"\n FROM #__social_avatars AS cu" .
					"\n INNER JOIN #__users AS u ON cu.uid = u.id" .
					"\n WHERE u.id = " . $DBO->quote($userId) .
					"\n AND cu.square != ''";
			$DBO->setQuery($sql);
			if($userUploadedAvatar = $DBO->loadResult()) {
				$avatarCache[$sessionID] = $userUploadedAvatar;
			} 
			
			// Override by profiles image if any chosen
			$sql = 	"SELECT" .
					"\n sda.small AS avatarname, sda.uid AS profileid" .
					"\n FROM #__social_avatars AS sa" .
					"\n INNER JOIN #__social_default_avatars AS sda ON sa.avatar_id = sda.id" .
					"\n INNER JOIN #__users AS u ON sa.uid = u.id" .
					"\n WHERE u.id = " . $DBO->quote($userId) .
					"\n AND sda.small != ''";
			$DBO->setQuery($sql);
			if($userAvatar = $DBO->loadObject()) {
				$avatarCache[$sessionID] = JUri::root() . $easySocialAvatarPath . '/avatars/defaults/profiles/' .  $userAvatar->profileid . '/' . $userAvatar->avatarname;
			}
			
			// If found a valid avatar by upload or profile avatar return it
			if(isset($avatarCache[$sessionID]) && $avatarCache[$sessionID]) {
				return $avatarCache[$sessionID];
			}
		}
	
		// PRIORITY 1 - Try for CB avatar if integration active
		if($thirdPartyIntegration === 'cbuilder' && $userId) {
			$DBO = JFactory::getDBO();
			$sql = 	"SELECT CONCAT('" . $baseURL . "images/comprofiler/', avatar)" .
					"\n FROM #__comprofiler AS cu" .
					"\n INNER JOIN #__users AS u ON cu.id = u.id" .
					"\n WHERE u.id = " . $DBO->quote($userId) .
					"\n AND cu.avatarapproved = 1 AND cu.avatar != ''";
			$DBO->setQuery($sql);
			if($userAvatar = $DBO->loadResult()) {
				$avatarCache[$sessionID] = $userAvatar;
				return $userAvatar;
			}
		}
	
		// PRIORITY 1 - Try for Kunena avatar if integration active
		if($thirdPartyIntegration === 'kunena' && $userId) {
			$kunenaAvatarSize = $cParams->get('kunena_avatars_resize_format', 'size36');
			$DBO = JFactory::getDBO();
			$sql = 	"SELECT CONCAT('" . $baseURL . "media/kunena/avatars/resized/$kunenaAvatarSize/', avatar) AS avatar" .
					"\n FROM #__kunena_users AS cu" .
					"\n INNER JOIN #__users AS u ON cu.userid = u.id" .
					"\n WHERE u.id = " . $DBO->quote($userId) .
					"\n AND cu.avatar != ''";
			$DBO->setQuery($sql);
			if($userAvatar = $DBO->loadResult()) {
				$avatarCache[$sessionID] = $userAvatar;
				return $userAvatar;
			}
		}
		
		// PRIORITY 1 - Try for EasyProfile avatar if integration active
		if($thirdPartyIntegration === 'easyprofile' && $userId) {
			$DBO = JFactory::getDBO();
			$sql = 	"SELECT CONCAT('" . $baseURL . "', REPLACE(avatar, '_', 'mini_')) AS avatar" .
					"\n FROM #__jsn_users AS cu" .
					"\n INNER JOIN #__users AS u ON cu.id = u.id" .
					"\n WHERE u.id = " . $DBO->quote($userId) .
					"\n AND cu.avatar != ''";
			$DBO->setQuery($sql);
			if($userAvatar = $DBO->loadResult()) {
				$avatarCache[$sessionID] = $userAvatar;
				return $userAvatar;
			}
		}
	
		// Calculate avatar name based on md5 from user id and username
		$calculatedHash = $userId ? 'uidavatar_' . $userId : 'gsidavatar_' . $sessionID;
		$finalName = $calculatedHash . '.' . $avatarFormat;
		$filePath = JPATH_COMPONENT_SITE . $avatarSubPath . $finalName;
	
		// PRIORITY 2 - User uploaded avatar, check if user has uploaded avatar
		if(file_exists($filePath)) {
			$lastModTimeFile = self::crossFileMTime($filePath);
			$userAvatar = JUri::root() . 'components/com_jchat/images/avatars/' . $finalName . '?nocache='.$lastModTimeFile;
			$avatarCache[$sessionID] = $userAvatar;
		} else {
			// PRIORITY 3 - Default avatar image for my and other users
			// Current user session table
			$userSessionTable->load(session_id());
			$am_i = $sessionID == $userSessionTable->session_id ? 'my' : 'other';
			$defaultAvatar = 'default_' . $am_i . $userGender . '.png';
			$userAvatar = JUri::root() . 'components/com_jchat/images/avatars/' . $integrationType . $defaultAvatar ;
			$avatarCache[$sessionID] = $userAvatar;
		}
		
		return $userAvatar;
	}
	
	/**
	 * Get children user groups required for ACL permissions check
	 *
	 * @static
	 * @access public
	 * @param Object $db
	 * @param int $parentGroupId
	 * @return array
	 */
	public static function getChildGroups($db, $parentGroupId) {
		// Find all the child groups given a parent group
		try {
			$query = $db->getQuery(true)
						->select('DISTINCT(ug2.id)')
						->from('#__usergroups as ug1')
						->join('INNER', '#__usergroups AS ug2 ON ug2.lft > ug1.lft AND ug2.rgt < ug1.rgt')
						->where('ug1.id=' . $db->quote($parentGroupId));

			$db->setQuery($query);
			$result = $db->loadColumn();
		} catch (Exception $e) {
			return array();
		}

		return $result;
	}
	
	/**
	 * Get the total access levels registered in Joomla
	 *
	 * @static
	 * @access public
	 * @param Object $db
	 * @param int $parentGroupId
	 * @return array
	 */
	public static function getTotalAccessLevels($db) {
		// Find all the access levels registered in Joomla
		try {
			$query = $db->getQuery(true)
						->select($db->quoteName('a.id', 'value') . ', ' . $db->quoteName('a.title', 'text'))
						->from($db->quoteName('#__viewlevels', 'a'))
						->group($db->quoteName(array('a.id', 'a.title', 'a.ordering')))
						->order($db->quoteName('a.ordering') . ' ASC')
						->order($db->quoteName('title') . ' ASC');

			// Get the options.
			$db->setQuery($query);
			$result = $db->loadObjectList();
		} catch (Exception $e) {
			return array();
		}

		return $result;
	}
	
	/**
	 * Find if the FB user id is already registered in this Joomla system
	 * and in this case return the Joomla user id assigned
	 *
	 * @param string $userIdentifier
	 *        	It can be facebook app scoped user id or email address
	 * @param string $email
	 * @return int
	 */
	public static function getJoomlaId($userIdentifier, $authType = 'id') {
		$db = JFactory::getDbo ();
		// Mapping for auth types
		$mapping = array('id'=>'fb_uid', 'email'=>'email');
		$authType = $mapping[$authType];
	
		$query = "SELECT login.j_uid" .
				 "\n FROM #__jchat_login AS login" .
				 "\n INNER JOIN #__users AS users" .
				 "\n ON login.j_uid = users.id" .
				 "\n WHERE login." . $authType . " = " . $db->quote ( $userIdentifier );
		$db->setQuery ( $query );
		$userExistsID = $db->loadResult ();
	
		return $userExistsID;
	}
}