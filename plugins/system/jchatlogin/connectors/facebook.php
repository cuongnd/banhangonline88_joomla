<?php
/** 
 * Manage login/logout for social networks connect
 * @package JCHAT::plugins::system
 * @subpackage jchatlogin
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
defined ( '_JEXEC' ) or die ();

/**
 * Facebook connector for the social login
 *
 * @package JCHAT::plugins::system
 * @subpackage jchatlogin
 * @since 2.1
 */
class JChatLoginConnectorFacebook extends JChatLoginConnector {
	/**
	 * Main connector function
	 *
	 * @access public
	 * @return Void
	 */
	public function execute() {
		// Facebook Login - Execute only on self page reload url after Facebook JS SDK login completed
		if ($this->joomlaUserObject->guest && ( bool ) $this->app->input->getInt ( 'fblogin', false )) {
			try {
				$filter = JFilterInput::getInstance ();
	
				// Instantiate main Facebook API library class object, pass in Application ID and secret doce
				$facebookAPI = new JChatFacebook ( array (
						'appId' => $this->appId,
						'secret' => $this->secret
				) );
	
				// Check if SSL peer verification is enabled
				if(!$this->cParams->get('curl_ssl_verifypeer', true)) {
					JChatFacebookBase::$CURL_OPTS[CURLOPT_SSL_VERIFYPEER] = false;
				}
	
				// Retrieve info about current Facebook user using API to get user integer identifier
				$fbUserID = $facebookAPI->getUser ();
				if(!$fbUserID) {
					throw new Exception(JText::_('COM_JCHAT_FBUSERID_NOTFOUND'));
				}
	
				// Retrieve full Facebook user profile informations using Facebook API
				$fbUserProfileArray = $facebookAPI->api ( '/me?fields=id,email,name,first_name,last_name,link,locale,timezone,updated_time,verified,birthday,picture,gender,location' );
	
				// Check if users exists already in the Joomla database using email address as primary key, retrieve user id if exists
				$authType = $this->cParams->get('auth_type', 'id');
				$alreadyCreatedJoomlaUserID = JChatHelpersUsers::getJoomlaId ( $fbUserProfileArray [$authType], $authType );
	
				if (! $alreadyCreatedJoomlaUserID) {
					$name = @$fbUserProfileArray ['name'];
					$username = strtolower ( $filter->clean ( @$fbUserProfileArray ['name'], 'cmd' ) );
					// This Facebook account as no username, for example a Facebook page
					if (! $username) {
						$name = $fbUserProfileArray ['email'];
						$username = $fbUserProfileArray ['email'];
					}
					$password = JUserHelper::genRandomPassword ( 5 );
					$email = $fbUserProfileArray ['email'];
					$fbUserProfileArray ['picture'] = 'https://graph.facebook.com/' .$fbUserProfileArray['id']. '/picture?type=normal';
					
					// Trigger to create a new Joomla user aggregating data from Facebook user profile, pre-populate bind $this->joomlaUserObject
					$this->onNewUser(
							$this->joomlaUserObject,
							$name,
							$username,
							$password,
							$email,
							$fbUserProfileArray,
							$this->cParams
					);
	
					// Do instant Joomla login authentication with new user, aggregated data and random generated password only for login purpouse
					$this->onLogin(
							$this->joomlaUserObject,
							$this->cParams
					);
				} else {
					// get already populated $this->joomlaUserObject
					$this->joomlaUserObject = JFactory::getUser ( $alreadyCreatedJoomlaUserID );
					// Do instant Joomla login authentication with new user, aggregated data and random generated password only for login purpouse
					$this->onLogin(
							$this->joomlaUserObject,
							$this->cParams
					);
				}
			} catch ( Exception $e ) {
				$this->app->enqueueMessage(JText::sprintf('COM_JCHAT_ERROR_FACEBOOK_LOGIN', $e->getMessage ()), 'warning');
			}
		}
	}
	
	/**
	 * Class Constructor
	 * @param Object $cParams
	 * 
	 * @access public
	 */
	public function __construct($cParams) {
		parent::__construct($cParams);
				
		$this->appId = $this->cParams->get('appId', null);
		$this->secret = $this->cParams->get('secret', null);
		
		// Load framework classes without autoloading
		require_once JPATH_ROOT . '/plugins/system/jchatlogin/social/facebook/base.php';
		require_once JPATH_ROOT . '/plugins/system/jchatlogin/social/facebook/facebook.php';
		require_once JPATH_ROOT . '/plugins/system/jchatlogin/social/facebook/exception.php';
	}
}