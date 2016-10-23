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
 * Google connector for the social login
 *
 * @package JCHAT::plugins::system
 * @subpackage jchatlogin
 * @since 2.1
*/
class JChatLoginConnectorGoogle extends JChatLoginConnector {
	/**
	 * Main connector function
	 *
	 * @access public
	 * @return Void
	 */
	public function execute() {
		// Google Plus Login URL generation - Execute only if GPlus login is enabled
		try {
			// Generate GPlus login URL if the user is a guest
			if ($this->joomlaUserObject->guest && ( bool ) !$this->app->input->get->getString ( 'code', false )) {
				$googleAPI = new JChatGoogleLogin($this->appId, $this->secret, JUri::base(), true);
				$gplusLoginURL = $googleAPI->init()->getLoginUrl();
				$doc = JFactory::getDocument();
				$doc->addScriptDeclaration ( "var jchatGPlusLoginURL = '$gplusLoginURL';" );
			}
		} catch ( Exception $e ) {
			$this->app->enqueueMessage(JText::sprintf('COM_JCHAT_ERROR_GPLUS_LOGIN', $e->getMessage ()), 'warning');
		}
		
		// Google Plus Login - Execute only after link click to perform Google login, after Google redirects back
		if ($this->joomlaUserObject->guest && ( bool ) $this->app->input->get->getString ( 'code', false )) {
			try {
				$googleAPI = new JChatGoogleLogin($this->appId, $this->secret, JUri::base(), true);
				$googleUserObject = $googleAPI->init()->loginCallback($this->app->input->get->getString ( 'code' ));
		
				// Check if users exists already in the Joomla database using email address as primary key, retrieve user id if exists
				$authType = $this->cParams->get('auth_type', 'id');
				$joomlaIdentifier = $authType == 'id' ? $googleUserObject->getUid() : $googleUserObject->getEmailAddress();
				$alreadyCreatedJoomlaUserID = JChatHelpersUsers::getJoomlaId ( $joomlaIdentifier, $authType );
		
				if (! $alreadyCreatedJoomlaUserID) {
					// Collect user info
					$name = $googleUserObject->getFirstname() . ' ' . $googleUserObject->getlastname();
					$username = $googleUserObject->getUsername();
					$password = JUserHelper::genRandomPassword ( 5 );
					$email = $googleUserObject->getEmailAddress();
					
					// Normalize info array
					$fbUserProfileArray ['id'] = $googleUserObject->getUid();
					$fbUserProfileArray ['email'] = $email;
					$fbUserProfileArray ['picture'] = $googleUserObject->getPicture();
					$fbUserProfileArray ['first_name'] = $googleUserObject->getFirstname();
					$fbUserProfileArray ['last_name'] = $googleUserObject->getLastname();
					$fbUserProfileArray ['name'] = $name;
					
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
				$this->app->enqueueMessage(JText::sprintf('COM_JCHAT_ERROR_GPLUS_LOGIN', $e->getMessage ()), 'warning');
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

		$this->appId = $this->cParams->get('gplusClientID', null);
		$this->secret = $this->cParams->get('gplusKey', null);

		// Load framework classes without autoloading
		require_once JPATH_ROOT . '/plugins/system/jchatlogin/social/google/login.php';
		require_once JPATH_ROOT . '/plugins/system/jchatlogin/social/google/user.php';
	}
}