<?php
// namespace administrator\components\com_jchat\framework\google;
/**
 * @package JCHAT::FRAMEWORK::administrator::components::com_jchat
 * @subpackage framework
 * @subpackage google
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

// Load bootstrap file with OAuth autoloader and needed namespaced classes
require_once JPATH_ROOT . '/administrator/components/com_jchat/framework/OAuth/bootstrap.php';
use OAuth\ServiceFactory;
use OAuth\OAuth2\Service\Google;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;

/**
 * Google login connector responsibilities
 *
 * @package JCHAT::FRAMEWORK::administrator::components::com_jchat
 * @subpackage framework
 * @subpackage google
 * @since 2.0
 */
interface JChatGoogleLoginInterface {
	
	/**
	 * Initializes our service
	 */
	public function init();
	
	/**
	 * Returns the login url for the social network
	 *
	 * @return string
	 */
	public function getLoginUrl();
	
	/**
	 * Handles the login callback from the social network
	 *
	 * @param string $accessToken        	
	 *
	 * @return SocialUserInterface
	 */
	public function loginCallback($accessToken);
}


/**
 * Google login connector concrete implementation
 *
 * @package JCHAT::FRAMEWORK::administrator::components::com_jchat
 * @subpackage framework
 * @subpackage google
 * @since 2.0
 */
class JChatGoogleLogin implements JChatGoogleLoginInterface {
	
	/**
	 * Google service
	 *
	 * @var string
	 */
	protected $service;
	
	/**
	 * OAuth client ID
	 *
	 * @var string
	 */
	protected $clientId;
	
	/**
	 * OAuth key
	 *
	 * @var string
	 */
	protected $key;
	
	/**
	 * Callback url
	 *
	 * @var string
	 */
	protected $callbackUrl;
	
	/**
	 * Constructor.
	 *
	 * @param $clientId string        	
	 * @param $key string        	
	 * @param $callbackUrl string        	
	 */
	public function __construct($clientId, $key, $callbackUrl) {
		$this->clientId = $clientId;
		$this->key = $key;
		$this->callbackUrl = $callbackUrl;
	}
	
	/**
	 * Initializes our service
	 */
	public function init() {
		$storage = new Session ();
		$serviceFactory = new ServiceFactory ();
		$credentials = new Credentials ( $this->clientId, $this->key, $this->callbackUrl );
		$this->service = $serviceFactory->createService ( 'google', $credentials, $storage, array (
				'userinfo_email',
				'userinfo_profile' 
		) );
		
		return $this;
	}
	
	/**
	 * Returns the login url for the social network
	 *
	 * @return string
	 */
	public function getLoginUrl() {
		return $this->service->getAuthorizationUri ();
	}
	
	/**
	 * Handles the login callback from the social network
	 *
	 * @param string $accessCode        	
	 *
	 * @return SocialUserInterface
	 */
	public function loginCallback($accessCode) {
		$this->service->requestAccessToken ( $accessCode );
		$userData = json_decode ( $this->service->request ( 'https://www.googleapis.com/oauth2/v1/userinfo' ), true );
		$googleUser = new JChatGoogleUser ( $userData );
		return $googleUser;
	}
}
