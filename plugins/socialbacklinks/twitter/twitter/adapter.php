<?php
/**
 * SocialBacklinks plugin for Twitter Social Network
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and change.
 * Otherwise, please feel free to contact us at contact@joomunited.com
 *
 * @package 	Social Backlinks
 * @copyright 	Copyright (C) 2012 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @license 	GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */
 
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * SocialBacklinks plugin for Twitter Social Network
 */
class PlgSBTwitterAdapter extends SBPluginsNetwork
{

	/**
	 * @see SBPluginsNetwork::__construct()
	 */
	public function __construct( $caller, $options = array() )
	{
		parent::__construct( $caller, array_merge( $options, array( 'window_size' => 'x:550,y:160' ) ) );
	}

	/**
	 * @see SBPluginsInterface::getAlias();
	 */
	public function getAlias( )
	{
		return 'twitter';
	}

	/**
	 * Initializes and returns the Twitter object
	 * @return object
	 */
	protected function _getAdapter( )
	{
		if ( empty( $this->_adapter ) ) {

			$app = $this->_getApp( );
			$oauth_state = $this->_getState( );
			if ( empty( $oauth_state ) ) {
				$this->_adapter = new TwitterOAuth( $app['consumer_key'], $app['consumer_secret'] );
			}
			elseif ( !empty( $oauth_state['request_token'] ) ) {
				$this->_adapter = new TwitterOAuth( $app['consumer_key'], $app['consumer_secret'], $oauth_state['request_token'], $oauth_state['request_token_secret'] );
			}
			else {
				$this->_adapter = new TwitterOAuth( $app['consumer_key'], $app['consumer_secret'], $oauth_state['access_token'], $oauth_state['access_token_secret'] );
			}
		}
		return $this->_adapter;
	}

	/**
	 * Returns array of the application data
	 * @throws SBPluginsException
	 * @return array
	 */
	protected function _getApp( )
	{
		if ( empty( $this->_app ) ) {

			if ( !($key = $this->consumer_key) || !($secret = $this->consumer_secret) ) {
				throw new SBPluginsException( JText::_( 'SB_APP_DATA_ERROR' ), 500 );
			}
			$this->_app = array(
				'consumer_key' => $key,
				'consumer_secret' => $secret
			);
		}
		return $this->_app;
	}

	/**
	 * Returns the list of the oauth states
	 * @return array
	 */
	protected function _getState( )
	{
		if ( empty( $_SESSION['twitter_oauth_state'] ) && empty( $this->_state ) ) {

			if ( !($token = $this->oauth_token) || !($secret = $this->oauth_token_secret) ) {
				return null;
			}

			$this->_state = array(
				'request_token' => '',
				'request_token_secret' => '',
				'access_token' => $token,
				'access_token_secret' => $secret
			);

			$_SESSION['twitter_oauth_state'] = $this->_state;
		}

		if ( empty( $this->_state ) ) {
			$this->_state = $_SESSION['twitter_oauth_state'];
		}

		return $this->_state;
	}

	/**
	 * Sets the oauth state
	 * @param  array $state
	 * @return void
	 */
	protected function _setState( $state )
	{
		$this->_state = $state;
		$_SESSION['twitter_oauth_state'] = $state;
	}

	/**
	 * Destroys custom data from session and in state
	 * @return void
	 */
	protected function _destroyState( )
	{
		unset( $_SESSION['twitter_oauth_state'] );
		$this->_state = array( );
	}

	/**
	 * Connects to Twitter network
	 * return result of the connection, or redirect otherwise
	 *
	 * @throws SBPluginsException if adapter was not found
	 * @param  array The list of connection parameters
	 * @param  boolean $callback Twitter server return some data
	 * @return boolean
	 */
	public function connect( $params, $callback = false )
	{
		if ( !$callback ) {
			$this->disconnect( );
		}

		if ( !$twitter = $this->_getAdapter( ) ) {
			throw new SBPluginsException( JText::sprintf( 'SB_NETWORK_CONNECT_ERROR', JText::_( 'SB_TWITTER' ) ), 500 );
		}

		if ( !$callback ) {
			// Get temporary credentials.
			$request_token = $twitter->getRequestToken( $this->_getCallback( ) );

			if ( $twitter->http_code == 200 ) {
				// Create a new set of information,
				// initially just containing the keys we need to make the request.
				$oauth_state = array(
					'request_token' => $request_token['oauth_token'],
					'request_token_secret' => $request_token['oauth_token_secret'],
					'access_token' => '',
					'access_token_secret' => ''
				);

				$this->_setState( $oauth_state );

				// Build authorize URL and redirect user to Twitter.
				$url = $twitter->getAuthorizeURL( $request_token['oauth_token'] ) . '&force_login=true';

				$app = JFactory::getApplication( );
				$app->redirect( $url );
			}
			else {
				$this->_destroyState( );

				if ( empty( $twitter->http_header['status'] ) ) {
					throw new SBPluginsException( JText::sprintf( 'SB_NETWORK_CONNECT_ERROR', JText::_( 'SB_TWITTER' ) ), 500 );
				}
				else {
					throw new SBPluginsException( $twitter->http_header['status'], 500 );
				}
				return false;
			}
		}
		else {
			$oauth_token = isset( $params['oauth_token'] ) ? $params['oauth_token'] : null;
			$oauth_verifier = isset( $params['oauth_verifier'] ) ? $params['oauth_verifier'] : null;

			$oauthstate = $this->_getState( );

			// If the oauth_token is old redirect to the connect page.
			if ( !empty( $oauth_token ) && $oauthstate['request_token'] !== $oauth_token ) {
				$this->_destroyState( );
				throw new SBPluginsException( JText::sprintf( 'SB_NETWORK_CONNECT_ERROR', JText::_( 'SB_TWITTER' ) ), 500 );
			}

			// Request access tokens from twitter
			$access_token = $twitter->getAccessToken( $oauth_verifier );

			$this->_destroyState( );

			if ( $twitter->http_code == 200 ) {
				// Save access token to use in future.
				$oauth_state = array(
					'request_token' => '',
					'request_token_secret' => '',
					'access_token' => $access_token['oauth_token'],
					'access_token_secret' => $access_token['oauth_token_secret']
				);

				$this->_setState( $oauth_state );

				// The user has been verified and the access tokens can be saved for future use
				$this->setOptions( array(
					'oauth_token' => $access_token['oauth_token'],
					'oauth_token_secret' => $access_token['oauth_token_secret']
				) );

				$this->save( );
				return true;
			}
			else {
				if ( empty( $twitter->http_header['status'] ) ) {
					throw new SBPluginsException( JText::sprintf( 'SB_NETWORK_CONNECT_ERROR', JText::_( 'SB_TWITTER' ) ), 500 );
				}
				else {
					throw new SBPluginsException( $twitter->http_header['status'], 500 );
				}
			}
		}
	}

	/**
	 * Disconnects from social network.
	 * @param  bool $callback Whether social server returns some data or not
	 * @return bool
	 */
	public function disconnect( $callback = false )
	{
		// The user has been logged out from network
		$this->setOptions( array(
			'oauth_token' => null,
			'oauth_token_secret' => null
		) );
		$this->save( );
		$this->_destroyState( );
		return true;
	}

	/**
	 * Checks if user is logged in Twitter network
	 * @return boolean
	 */
	public function isLoggedIn( )
	{
		$user = $this->_getUser( );
		return !empty( $user );
	}

	/**
	 * Returns name of the user in Twitter network
	 * @return string
	 */
	public function getUserName( )
	{
		return $user = $this->_getUser( ) ? $user->name : '';
	}

	/**
	 * Returns the Twitter user object if user data correct, or null otherwise
	 * @return mixed
	 */
	protected function _getUser( )
	{
		if ( empty( $this->_user ) ) {
			$twitter = $this->_getAdapter( );
			try {
				$this->_user = $twitter->get( 'account/verify_credentials' );
			}
			catch (Exception $e) {
				$this->_user = null;
			}
			
			if ( isset( $this->_user->error ) ) {
				$this->_user = null;
			}
		}

		return $this->_user;
	}

	/**
	 * Adds post to user wall
	 * @param  string Title of the post
	 * @param  string Link to the items on User's site
	 * @param  string Description of the item
	 * @return boolean
	 */
	public function addPost( $title, $link, $desc = '', $image = '' )
	{
		if ( !$this->isLoggedIn( ) ) {
			throw new SBPluginsException( JText::_( 'SB_LOGIN_ERROR' ), 500 );
		}

		$tweet = $this->_getAdapter( );
		$statusMessage = $title . ' ' . $link;

		// Update status
		try {
			$result = $tweet->post( 'statuses/update', array( 'status' => $statusMessage ) );
		}
		catch ( exception $e ) {
			$result = new stdClass;
			$result->error = true;
		}
		
		if (!empty($result->errors)) {
			$result->error = true;
		}
		
		if ( empty( $result->error ) ) {
			return true;
		}
		else {
			if ( $result->error === true ) {
				$error = null;
				if (!empty($result->errors)) {
					$error = $result->errors[0];
				}
				throw new SBPluginsException( JText::sprintf( 'SB_NETWORK_POST_ERROR', JText::_( 'SB_TWITTER' ) ) . ((!empty($error)) ? " {$error->code}: {$error->message}" : ""), 500 );
			}
			else {
				throw new SBPluginsException( $result->error, 500 );
			}
		}
	}

}
