<?php
/**
 * SocialBacklinks plugin for Linkedin Social Network
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
 * SocialBacklinks plugin for Linkedin Social Network
 */
class PlgSBLinkedinAdapter extends  SBPluginsNetwork
{

	/**
	 * @see SBPluginsNetwork::__construct()
	 */
	public function __construct( $caller, $options = array() )
	{
		parent::__construct( $caller, array_merge( $options, array( 'window_size' => 'x:700,y:210' ) ) );
	}

	/**
	 * @see SBPluginsInterface::getAlias();
	 */
	public function getAlias( )
	{
		return 'linkedin';
	}

	/**
	 * Initializes and returns the Linkedin object
	 * @return object
	 */
	protected function _getAdapter( )
	{
		if ( empty( $this->_adapter ) ) {

			$app = $this->_getApp( );
			$oauth_state = $this->_getState( );
			if ( empty( $oauth_state ) ) {
				$this->_adapter = new LinkedInOAuth( $app['api_key_public'], $app['api_key_private'] );
			}
			elseif ( !empty( $oauth_state['request_token'] ) ) {
				$this->_adapter = new LinkedInOAuth( $app['api_key_public'], $app['api_key_private'], $oauth_state['request_token'], $oauth_state['request_token_secret'] );
			}
			else {
				$this->_adapter = new LinkedInOAuth( $app['api_key_public'], $app['api_key_private'], $oauth_state['access_token'], $oauth_state['access_token_secret'] );
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

			if ( !($key = $this->api_key_public) || !($secret = $this->api_key_private) ) {
				throw new SBPluginsException( JText::_( 'SB_APP_DATA_ERROR' ), 500 );
			}
			$this->_app = array(
				'api_key_public' => $key,
				'api_key_private' => $secret
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
		if ( empty( $_SESSION['linkedin_oauth_state'] ) && empty( $this->_state ) ) {

			if ( !($token = $this->oauth_token) || !($secret = $this->oauth_token_secret) ) {
				return null;
			}

			$this->_state = array(
				'request_token' => '',
				'request_token_secret' => '',
				'access_token' => $token,
				'access_token_secret' => $secret
			);

			$_SESSION['linkedin_oauth_state'] = $this->_state;
		}

		if ( empty( $this->_state ) ) {
			$this->_state = $_SESSION['linkedin_oauth_state'];
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
		$_SESSION['linkedin_oauth_state'] = $state;
	}

	/**
	 * Destroys custom data from session and in state
	 * @return void
	 */
	protected function _destroyState( )
	{
		unset( $_SESSION['linkedin_oauth_state'] );
		$this->_state = array( );
	}

	/**
	 * Connects to Linkedin network
	 * return result of the connection, or redirect otherwise
	 *
	 * @throws SBPluginsException if adapter was not found
	 * @param  array The list of connection parameters
	 * @param  boolean $callback Linkedin server return some data
	 * @return boolean
	 */
	public function connect( $params, $callback = false )
	{
		if ( !$callback ) {
			$this->disconnect( );
		}
		
		if ( !$linkedin = $this->_getAdapter( ) ) {
			throw new SBPluginsException( JText::sprintf( 'SB_NETWORK_CONNECT_ERROR', JText::_( 'SB_LINKEDIN' ) ), 500 );
		}

		if ( !$callback ) {
			// Get temporary credentials.
			$request_token = $linkedin->getRequestToken( $this->_getCallback( ) );

			if ( $linkedin->http_code == 200 ) {
				// Create a new set of information,
				// initially just containing the keys we need to make the request.
				$oauth_state = array(
					'request_token' => $request_token['oauth_token'],
					'request_token_secret' => $request_token['oauth_token_secret'],
					'access_token' => '',
					'access_token_secret' => ''
				);

				$this->_setState( $oauth_state );

				// Build authorize URL and redirect user to Linkedin.
				$url = $linkedin->getAuthorizeURL( $request_token['oauth_token'] );
				
				$app = JFactory::getApplication( );
				$app->redirect( $url );
			}
			else {
				$this->_destroyState( );

				if ( empty( $request_token['oauth_problem'] ) ) {
					throw new SBPluginsException( JText::sprintf( 'SB_NETWORK_CONNECT_ERROR', JText::_( 'SB_LINKEDIN' ) ), 500 );
				}
				else {
					throw new SBPluginsException( $request_token['oauth_problem'], 500 );
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
				throw new SBPluginsException( JText::sprintf( 'SB_NETWORK_CONNECT_ERROR', JText::_( 'SB_LINKEDIN' ) ), 500 );
			}

			// Request access tokens from Linkedin
			$access_token = $linkedin->getAccessToken( $oauth_verifier );

			$this->_destroyState( );

			if ( $linkedin->http_code == 200 ) {
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
				if ( empty( $request_token['oauth_problem'] ) ) {
					throw new SBPluginsException( JText::sprintf( 'SB_NETWORK_CONNECT_ERROR', JText::_( 'SB_LINKEDIN' ) ), 500 );
				}
				else {
					throw new SBPluginsException( $request_token['oauth_problem'], 500 );
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
		echo '<iframe width="50" height="50" style="display: none" frameborder="0"' . ' src="https://www.linkedin.com/secure/login?session_full_logout=&amp;trk=hb_signout"></iframe>';
		return true;
	}

	/**
	 * Checks if user is logged in Linkedin network
	 * @return boolean
	 */
	public function isLoggedIn( )
	{
		$user = $this->_getUser( );
		return !empty( $user );
	}

	/**
	 * Returns name of the user in Linkedin network
	 * @return string
	 */
	public function getUserName( )
	{
		return $user = $this->_getUser( ) ? $user->{'first-name'} . ' ' . $user->{'last-name'} : '';
	}

	/**
	 * Returns the Linkedin user object if user data correct, or null otherwise
	 * @return mixed
	 */
	protected function _getUser( )
	{
		if ( empty( $this->_user ) ) {
			$linkedin = $this->_getAdapter( );
			try {
				$this->_user = $linkedin->get( 'people/~', array( 'first-name', 'last-name' ) );
			}
			catch (Exception $e) {
				$this->_user = null;
			}

			if ( isset( $this->_user->{'error-code'} ) ) {
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

		$linkedin = $this->_getAdapter( );
		
		/*$xml = '<share>'
				//.'<comment>83% of employers will use social media to hire: 78% LinkedIn, 55% Facebook, 45% Twitter [SF Biz Times] http://bit.ly/cCpeOD</comment>'
				. '<content>' . '<title>' . htmlentities( $title, ENT_NOQUOTES, "UTF-8" ) . '</title>' . '<submitted-url>' . $link . '</submitted-url>'
				// .'<submitted-image-url>http://images.bizjournals.com/travel/cityscapes/thumbs/sm_sanfrancisco.jpg</submitted-image-url>'
					. '<description>' . htmlentities( $desc, ENT_NOQUOTES, "UTF-8" ) . '</description>' 
				. '</content>' 
				. '<visibility><code>anyone</code></visibility>' 
			. '</share>';*/
			
		//$e_title = htmlentities( $title, ENT_NOQUOTES, "UTF-8" );
		//$e_link = $link;
		//$e_desc = htmlentities( $desc, ENT_NOQUOTES, "UTF-8" );
		
		$writer = new XMLWriter();
		$writer->openMemory();

		$writer->startElement( 'share' );
			$writer->startElement( 'content' );
				$writer->writeElement( 'title', $title );
				$writer->writeElement( 'submitted-url', $link );
				$writer->writeElement( 'description', $desc );
			$writer->endElement( );
			$writer->startElement( 'visibility' );
				$writer->writeElement( 'code', 'anyone' );
			$writer->endElement( );
		$writer->endElement( );

		$xml = $writer->outputMemory();

		// Update status
		try {
			$result = $linkedin->post2( 'people/~/shares', $xml );
		}
		catch ( exception $e ) {
			$result = new stdClass;
			$result = false;
		}

		if (  $result === true) {
			return true;
		}
		else {
			if (!$result || empty( $result->message )) {
				throw new SBPluginsException( JText::sprintf( 'SB_NETWORK_POST_ERROR', JText::_( 'SB_LINKEDIN' ) ), 500 );
			}
			else {
				throw new SBPluginsException( ( string ) $result->message, 500 );
			}
		}
	}

}
