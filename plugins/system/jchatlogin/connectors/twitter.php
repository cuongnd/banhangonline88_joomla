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
 * Twitter connector for the social login
 *
 * @package JCHAT::plugins::system
 * @subpackage jchatlogin
 * @since 2.1
*/
class JChatLoginConnectorTwitter extends JChatLoginConnector {
	/**
	 * Main connector function
	 *
	 * @access public
	 * @return Void
	 */
	public function execute() {
		// Twitter Login URL generation - Execute only if Twitter login is enabled and user is not logged in
		try {
			// Generate Twitter login URL if the user is a guest
			if ($this->joomlaUserObject->guest && ( bool ) !$this->app->input->get->getString ( 'oauth_token', false )) {
				$connection = new JChatTwitterLogin($this->appId, $this->secret);
		
				$request_token = $connection->getRequestToken ( JUri::current() ); // get Request Token
		
				if ($request_token && isset($request_token ['oauth_token'])) {
					$token = $request_token ['oauth_token'];
					$_SESSION ['jchat_request_token'] = $token;
					$_SESSION ['jchat_request_token_secret'] = $request_token ['oauth_token_secret'];
						
					switch ($connection->http_code) {
						case 200 :
							$twitterLoginURL = $connection->getAuthorizeURL ( $token );
							$doc = JFactory::getDocument();
							$doc->addScriptDeclaration ( "var jchatTwitterLoginURL = '$twitterLoginURL';" );
							break;
					}
				}
			}
		} catch ( Exception $e ) {
			$this->app->enqueueMessage(JText::sprintf('COM_JCHAT_ERROR_TWITTER_LOGIN', $e->getMessage ()), 'warning');
		}
		
		// Twitter Login - Execute only after link click to perform Twitter login, after Twitter redirects back
		if ($this->joomlaUserObject->guest && ( bool ) $this->app->input->get->getString ( 'oauth_token', false ) && ( bool ) $this->app->input->get->getString ( 'oauth_verifier', false )) {
			try {
				$connection = new JChatTwitterLogin($this->appId, $this->secret, $_SESSION['jchat_request_token'], $_SESSION['jchat_request_token_secret']);
				$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
				if(isset($access_token['oauth_token'])) {
					$connection = new JChatTwitterLogin($this->appId, $this->secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
					$twitterParams =array();
					$twitterParams['include_entities']='false';
					$twitterUserObject = $connection->get('account/verify_credentials', $twitterParams);
				} else {
					return;
				}
		
				// Check if users exists already in the Joomla database using email address as primary key, retrieve user id if exists
				$authType = 'id';
				$joomlaIdentifier = 'tw' . $twitterUserObject->id_str;
				$alreadyCreatedJoomlaUserID = JChatHelpersUsers::getJoomlaId ( $joomlaIdentifier, $authType );
		
				if (! $alreadyCreatedJoomlaUserID) {
					// Collect user info
					$name = $twitterUserObject->name;
					$username = $twitterUserObject->screen_name;
					$password = JUserHelper::genRandomPassword ( 5 );
					$email = $twitterUserObject->email = $joomlaIdentifier . '@twitter.com';
					
					// Normalize info array
					$fbUserProfileArray ['id'] = $joomlaIdentifier;
					$fbUserProfileArray ['email'] = $twitterUserObject->email;
					$fbUserProfileArray ['picture'] = $twitterUserObject->profile_image_url_https;
					$fbUserProfileArray ['first_name'] = $twitterUserObject->name;
					$fbUserProfileArray ['last_name'] = $twitterUserObject->screen_name;
					$fbUserProfileArray ['name'] = $twitterUserObject->name;
					
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
				$this->app->enqueueMessage(JText::sprintf('COM_JCHAT_ERROR_TWITTER_LOGIN', $e->getMessage ()), 'warning');
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
	
		$this->appId = $this->cParams->get('twitterKey', null);
		$this->secret = $this->cParams->get('twitterSecret', null);
	
		// Load framework classes without autoloading
		require_once JPATH_ROOT . '/plugins/system/jchatlogin/social/twitter/login.php';
		require_once JPATH_ROOT . '/plugins/system/jchatlogin/social/twitter/OAuth.php';
	}
}