<?php
/** 
 * Manage login/logout for social networks connect
 * @package JCHAT::plugins::system
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
defined ( '_JEXEC' ) or die ();

/**
 * Base abstract connector class for social login
 *
 * @package JCHAT::plugins::system
 * @subpackage jchatlogin
 * @since 2.1
 */
abstract class JChatLoginConnector {
	/**
	 * App object
	 *
	 * @access protected
	 * @var Object
	 */
	protected $appId;
	
	/**
	 * App object
	 *
	 * @access protected
	 * @var Object
	 */
	protected $secret;
	
	/**
	 * Component params
	 *
	 * @access protected
	 * @var Object
	 */
	protected $cParams;
	
	/**
	 * Joomla user object
	 *
	 * @access protected
	 * @var Object
	 */
	protected $joomlaUserObject;
	
	/**
	 * App object
	 *
	 * @access protected
	 * @var Object
	 */
	protected $app;
	
	/**
	 * DB object
	 *
	 * @access protected
	 * @var Object
	 */
	protected $db;
	
	/**
	 * Manage the 3PD components integration
	 * 
	 * @access protected
	 * @return void
	 */
	protected function userIntegration($tpdIntegration, $fbUsersTable, $joomlaUserObject) {
		// Manage third party extensions
		require_once JPATH_ADMINISTRATOR . '/components/com_jchat/tables/custom.php';
		switch ($tpdIntegration) {
			case 'jomsocial':
				$tableInstance = new TableCustom('#__community_users', 'id', $this->db);
				$tableInstance->userid = $joomlaUserObject->id;
				$tableInstance->alias = $joomlaUserObject->id . ':' . $joomlaUserObject->username;
				try {
					$tableInstance->store();
				} catch (Exception $e) { }
				break;
	
			case 'easysocial':
				$tableInstance = new TableCustom('#__social_users', 'id', $this->db);
				$tableInstance->user_id = $joomlaUserObject->id;
				$tableInstance->state = 1;
				$tableInstance->type = 'joomla';
				try {
					$tableInstance->store();
				} catch (Exception $e) { }
				$tableInstance = new TableCustom('#__social_profiles_maps', 'id', $this->db);
				$tableInstance->profile_id = 1;
				$tableInstance->user_id = $joomlaUserObject->id;
				$tableInstance->state = 1;
				$tableInstance->created = $fbUsersTable->registered_on;
				try {
					$tableInstance->store();
				} catch (Exception $e) { }
				break;
	
			case 'cbuilder':
				$tableInstance = new TableCustom('#__comprofiler', 'userid', $this->db);
				$tableInstance->id = $tableInstance->user_id = $joomlaUserObject->id;
				$tableInstance->firstname = $fbUsersTable->first_name;
				$tableInstance->lastname = $fbUsersTable->last_name;
				$tableInstance->approved = 1;
				$tableInstance->confirmed = 1;
				try {
					$tableInstance->store();
				} catch (Exception $e) { }
				break;
		}
	}
	
	/**
	 * Get always the redirected 301 URL to the avatar image for Facebook
	 *
	 * @access protected
	 * @param string $url
	 * @return string The redirected URL
	 */
	protected function getFacebookRedirectPage( $url ) {
		// Format the request header array
		$header = array (
				'User-Agent: Google-Bot',
				'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
		);
	
		$ch = curl_init ( $url );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt ( $ch, CURLOPT_HEADER, true);
		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 0 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 30 );
		$result = curl_exec ( $ch );
	
		$info = curl_getinfo ( $ch );
		$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close ( $ch );
	
		// If it's a redirection (3XX) follow the redirect
		if ($httpStatus >= 300 && $httpStatus < 400) {
			$headers = explode("\n", $result);
			// loop through the headers and check for a Location: str
			$j = count($headers);
			for($i = 0; $i < $j; $i++){
				// if we find the Location header strip it and fill the redir var
				if(strpos($headers[$i],"Location:") !== false){
					$redirectionLink = trim(str_replace("Location:","",$headers[$i]));
					break;
				}
			}
	
			return $redirectionLink;
		}
	
		return $url;
	}
	
	/**
	 * Manage the 3PD components integration
	 *
	 * @access protected
	 * @param string $remoteAvatar
	 * @return void
	 */
	protected function importAvatar($remoteAvatar) {
		jimport('joomla.filesystem.stream');
		
		// Manage redirected avatars from Facebook
		if(strpos($remoteAvatar, 'facebook')) {
			$remoteAvatar = $this->getFacebookRedirectPage($remoteAvatar);
		}
		
		// Setup avatar paths
		$tempAvatarName = JPATH_ROOT . '/components/com_jchat/images/avatars/tempavatar' . $this->joomlaUserObject->id;
		$finalAvatarName = JPATH_ROOT . '/components/com_jchat/images/avatars/uidavatar_' . $this->joomlaUserObject->id . '.png';
		
		// Import copy the remote avatar
		$stream = new JStream();
		$stream->copy($remoteAvatar, $tempAvatarName);
		
		// Resize and convert avatar to png
		try {
			$imageHandler = new JImage($tempAvatarName);
			$resizedImage = $imageHandler->resize(32, 32);
			$resizedImage->toFile($finalAvatarName, IMAGETYPE_PNG);
		} catch (Exception $e) { }
		
		// Finally remove the temp image
		$stream->delete($tempAvatarName);
	}
	
	/**
	 * onNewUser handler
	 *
	 * @access protected
	 * @param Object $this->joomlaUserObject
	 * @param string $name
	 * @param string $username
	 * @param string $password
	 * @param string $email
	 * @param array $fbUserProfileArray
	 * @param Object $cParams
	 * @return null
	 */
	protected function onNewUser($joomlaUserObject, $name, $username, $password, $email, $fbUserProfileArray, $cParams) {
		// Execute only on site application
		if (! $this->app->getClientId ()) {
			jimport ( 'joomla.application.component.helper' );
			$config = JComponentHelper::getParams ( 'com_users' );
			// Default to Registered.
			$defaultUserGroup = $config->get ( 'new_usertype', 2 );
	
			$data = array (
					"name" => $name,
					"username" => $username,
					"groups" => array (
							$defaultUserGroup
					),
					"email" => $email
			);
	
			// Write to database
			$this->joomlaUserObject->bind ( $data );
			
			if (! $this->joomlaUserObject->save ()) {
				// Try to load data of existing user using the email address as p.key
				$query = "SELECT *" .
						 "\n FROM #__users" .
						 "\n WHERE " . $this->db->quoteName('email') . " = " . $this->db->quote($email);
				$existingData = $this->db->setQuery($query)->loadAssoc();
				$this->joomlaUserObject->bind($existingData);
			}
	
			// Track auto created users from Facebook connect login into component db table
			JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_jchat/tables');
			$fbUsersTable = JTable::getInstance('Login', 'Table');
			$fbUsersTable->j_uid = $this->joomlaUserObject->id;
			$fbUsersTable->fb_uid = $fbUserProfileArray['id'];
			$fbUsersTable->email = $fbUserProfileArray['email'];
			$fbUsersTable->picture = $fbUserProfileArray['picture'];
			$fbUsersTable->first_name = isset($fbUserProfileArray['first_name']) ? $fbUserProfileArray['first_name'] : $name;
			$fbUsersTable->last_name = isset($fbUserProfileArray['last_name']) ? $fbUserProfileArray['last_name'] : $name;
			$fbUsersTable->name = isset($fbUserProfileArray['name']) ? $fbUserProfileArray['name'] : $name;
	
			$fbUsersTable->store();
	
			// Call 3PD user integration function
			if($tpdIntegration = $cParams->get('3pdintegration', null)) {
				$this->userIntegration ( $tpdIntegration, $fbUsersTable, $this->joomlaUserObject );
			}
			
			// Manage avatar import for the newly created user
			if($fbUsersTable->picture) {
				$this->importAvatar ( $fbUsersTable->picture );
			}
		}
	}
	
	/**
	 * onLogin handler
	 *
	 * @access protected
	 * @param Object $this->joomlaUserObject
	 * @param Object $cParams
	 * @return null
	 */
	protected function onLogin($joomlaUserObject, $cParams) {
		jimport ( 'joomla.user.helper' );
	
		$credentials = array ();
		$credentials ['username'] = $this->joomlaUserObject->username;
			
		// Check if existing user and overwrite mode, save the existing password
		$query = "SELECT " . $this->db->quoteName('password') .
				 "\n FROM #__users" .
				 "\n WHERE " . $this->db->quoteName('id') . " = " . (int)$joomlaUserObject->id;
		$this->db->setQuery ( $query );
		$storedPassword = $this->db->loadResult ();
	
		// Reset a new user registration case
		$joomlaUserObject->password_clear = false;
			
		// If newly created user, we have a password clear already generated
		if($joomlaUserObject->password_clear) {
			$credentials ['password'] = $joomlaUserObject->password_clear;
		} else {
			// Generate and use a temp random password just to login on the fly
			$password = JUserHelper::genRandomPassword();
			$credentials ['password'] = $password;
	
			// Go on to generate a random password for this on the fly login
			$hashedPassword = JUserHelper::getCryptedPassword($password);
			$query = "UPDATE #__users" .
					 "\n SET " . $this->db->quoteName('password') . " = " . $this->db->quote($hashedPassword) .
					 "\n WHERE " . $this->db->quoteName('id') . " = " . (int)$joomlaUserObject->id;
			$this->db->setQuery ( $query );
			$this->db->execute ();
		}
			
		$options = array ();
		$options ['remember'] = true;
		$options ['silent'] = true;
			
		$loggedIn = $this->app->login ( $credentials, $options );
			
		// Check if existing user and overwrite mode, restore the original password
		$query = "UPDATE #__users" .
				 "\n SET " . $this->db->quoteName('password') . " = " . $this->db->quote($storedPassword) .
				 "\n WHERE " . $this->db->quoteName('id') . " = " . (int)$joomlaUserObject->id;
		$this->db->setQuery ( $query );
		$this->db->execute ();
			
		$redirectArray = $loggedIn ? array('msg'=>'', 'status'=>'') : array('msg'=>'COM_JCHAT_ERROR' . '_LOGIN', 'status'=>'error');
			
		$this->app->redirect ( JUri::current(), JText::_($redirectArray['msg']), $redirectArray['status']);
	}
	
	/**
	 * Main connector function
	 *
	 * @abstract
	 * @access public
	 * @return Void
	 */
	abstract public function execute();
	
	/**
	 * Class Constructor
	 * @param Object $cParams
	 * 
	 * @access public
	 */
	public function __construct($cParams) {
		$this->cParams = $cParams;
		$this->joomlaUserObject = JFactory::getUser();
		$this->app = JFactory::getApplication();
		$this->db = JFactory::getDbo();
	}
}