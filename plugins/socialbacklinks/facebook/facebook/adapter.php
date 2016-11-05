<?php
/**
 * SocialBacklinks plugin for Facebook Social Network
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
 * SocialBacklinks plugin for Facebook Social Network
 */
class PlgSBFacebookAdapter extends  SBPluginsNetwork
{
	
	/**
	 * @see SBPluginsNetwork::__construct()
	 */
	public function __construct( $caller, $options = array() )
	{
		parent::__construct( $caller, array_merge( $options, array( 'window_size' => 'x:520,y:170' ) ) );
	}
	
	/**
	 * @see SBPluginsInterface::getAlias();
	 */
	public function getAlias( )
	{
		return 'facebook';
	}

	/**
	 * Initializes and returns the Facebook object
	 * @return object
	 */
	protected function _getAdapter( )
	{
		if ( empty( $this->_adapter ) ) {
			$app = $this->_getApp( );
			$state = $this->_getState( );
			$this->_adapter = new Facebook( array(
				'appId' => $app['app_id'],
				'secret' => $app['secret']
			) );
			
			// Sets saved access token, if it exists		
			$access_token = $this->access_token;
			if ( $access_token ) {
				$this->_adapter->setAccessToken( $access_token );
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
			if ( !($id = $this->app_id) || !($secret = $this->secret) ) {
				throw new SBPluginsException( JText::_( 'SB_APP_DATA_ERROR' ), 500 );
			}
			$this->_app = array(
				'app_id' => $id,
				'secret' => $secret
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
		$app = $this->_getApp( );

		if ( empty( $this->_state ) ) {
			$app_id = $app['app_id'];

			if ( empty( $_SESSION["fb_{$app_id}_access_token"] ) ) {
				
				//Bgi: removed following clause
				//if ( empty( $this->access_token ) ) {
				//	return null;
				//}

				//Bgi: added if clause before setting state
				if ( !empty( $this->access_token ) ) {
					$state = array( 'access_token' => $this->access_token );
					$this->_setState( $state );
				}
			}
			else {
				$this->_state['access_token'] = $_SESSION["fb_{$app_id}_access_token"];
			}

			foreach ($this->getOptions() as $name => $value) {
				$this->_state[$name] = $value;
			}
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
		$app = $this->_getApp( );
		$_SESSION["fb_{$app[app_id]}_access_token"] = $state["access_token"];
	}

	/**
	 * Destroys custom data from session and in state
	 * @return void
	 */
	protected function _destroyState( )
	{
		try {
			$app = $this->_getApp( );
			if ( isset( $app['app_id'] ) && $id = $app['app_id'] ) {
				unset( $_SESSION["fb_{$id}_code"] );
				unset( $_SESSION["fb_{$id}_access_token"] );
				unset( $_SESSION["fb_{$id}_user_id"] );
			}
		}
		catch(SBPluginsException $e) {

		}
		$this->_state = array( );
	}

	/**
	 * Connects to Facebook network
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
			$this->disconnect( true );
		}
		
		if ( !$facebook = $this->_getAdapter( ) ) {
			throw new SBPluginsException( JText::sprintf( 'SB_NETWORK_CONNECT_ERROR', JText::_( 'SB_FACEBOOK' ) ), 500 );
		}
		
		if ( !$callback ) {

			$url = $facebook->getLoginUrl( array(
				'scope' => 'manage_pages',
				'redirect_uri' => $this->_getCallback( ),
				'display' => 'popup',
				'canvas' => 0
			) );
			$app = JFactory::getApplication( );
			$app->redirect( $url );
		}
		else {
			// If user data exists continue otherwise send to connect page to retry
			if ( $facebook->getUser( ) ) {
				// The user has been verified and the access tokens can be saved for future use
				$this->access_token = $facebook->getAccessToken( );
				$this->save( );
				return true;
			}
			else {
				$this->_destroyState( );
				throw new SBPluginsException( JText::sprintf( 'SB_NETWORK_CONNECT_ERROR', JText::_( 'SB_FACEBOOK' ) ), 500 );
				return false;
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
		/*if ( !$callback ) {
			$facebook = $this->_getAdapter( );
			$url = $facebook->getLogoutUrl( array( 'next' => JURI::root() . html_entity_decode( $this->getLogoutUrl( ) ) . '&callback=1' ) );
			$app = JFactory::getApplication( );
			$app->redirect( $url );
		}*/
		//else {
			// The user has been logged out from network
			$this->setOption( 'access_token', null );
			$this->save( );
			$this->_destroyState( );
		//}

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
	 * Returns the Facebook user object if user data correct, or null otherwise
	 * @return mixed
	 */
	protected function _getUser( )
	{
		if ( empty( $this->_user ) ) {
			$facebook = $this->_getAdapter( );

			if ( $facebook->getUser( ) ) {
				$this->_user = ( object )$facebook->api( '/me?fields=id,name' );
			}
			else {
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

		$facebook = $this->_getAdapter( );

		$attachment = array(
			//'message' => JText::_('SB_CONTENT_POST_TITLE'),
			'name' => $title,
			'link' => $link,
			// 'caption' => $title,
			'description' => $desc,
			// 'picture' => 'http://mysite.com/pic.gif'
		);
		
		if (!empty($image)) {
			$attachment['picture'] = $image;
		}

		$state = $this->_getState( );

		// Send post
		try {
			// Possible variants of post target value
			// 0 - post to my profile
			// 1 - post to selected page
			// 2 - post to both
			if ( empty( $state['post_target'] ) || in_array( $state['post_target'], array(
				0,
				2
			) ) ) {
				// Add post on profile wall
				$result = $facebook->api( '/me/feed/', 'post', $attachment );
			}
			if ( !empty( $state['post_target'] ) && $state['post_target'] > 0 ) {
				if ( !empty( $state['page_id'] ) ) {
					// Add post on specified page
					$page_id = $state['page_id'];
					if ( isset( $state['post_as_admin'] ) && $state['post_as_admin'] ) {
						$page_info = $facebook->api( "/{$page_id}?fields=access_token" );
						if ( !empty( $page_info['access_token'] ) ) {
							$attachment['access_token'] = $page_info['access_token'];
						}
					}
					$result = $facebook->api( "/{$page_id}/feed", "post", $attachment );
				}
				else {
					throw new SBPluginsException( JText::_( 'SB_FACEBOOK_NO_PAGE_FOUND' ), 500 );
				}
			}
		}
		catch ( exception $e ) {
			if ( !$e instanceof SBPluginsException ) {
				$result = $e->getResult( );
				throw new SBPluginsException( $result['error']['message'], 500 );
			}
		}

		if ( $result ) {
			return true;
		}
		return false;
	}

}
