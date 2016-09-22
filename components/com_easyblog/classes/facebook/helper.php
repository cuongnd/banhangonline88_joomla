<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'constants.php' );
require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'facebook' . DIRECTORY_SEPARATOR . 'consumer.php' );

class EasyBlogFacebook extends EasyBlogFacebookConsumer
{
	var $callback		= '';
	var $_access_token	= '';

	public function __construct( $key , $secret , $callback )
	{
		$this->callback	= $callback;

		parent::__construct( array( 'appId' 	=> $key ,
									'secret'	=> $secret,
									'cookie'	=> true
							)
		);
	}

	/**
	 * Facebook does not need the request tokens
	 *
	 **/
	public function getRequestToken()
	{
		$obj		= new stdClass();
		$obj->token		= 'facebook';
		$obj->secret	= 'facebook';

		return $obj;
	}

	/**
	 * Returns the verifier option. Since Facebook does not have oauth_verifier,
	 * The only way to validate this is through the 'code' query
	 *
	 * @return string	$verifier	Any string representation that we can verify it isn't empty.
	 **/
	public function getVerifier()
	{
		$verifier	= JRequest::getVar( 'code' , '' );
		return $verifier;
	}

	/**
	 * Returns the authorization url.
	 *
	 * @return string	$url	A link to Facebook's login URL.
	 **/
	public function getAuthorizationURL( $token , $autoSignIn = false , $display = 'page' )
	{
		$scope	= array(
							'publish_actions',	// Allows publishing content to stream
							'manage_pages',	// Allows managing user's pages.
							'user_groups',
							'user_status',	// Provides access to user status and checkins.
							'publish_pages'	// Grant permission the ability to post, comment and like as any of the Pages managed by page owner.
						);

		$displayType = $display != 'page' ? '&display=' . $display : '';

		$url	= 'http://facebook.com/dialog/oauth?scope=' . implode( ',' , $scope ) . '&client_id=' . parent::getAppId() . '&redirect_uri=' . urlencode( $this->callback ) . '&response_type=code' . $displayType;

		return $url;
	}

	/**
	 * Javascript to close dialog when call=doneLogin is specified in the URI.
	 *
	 * @access	public
	 */
	public function doneLogin()
	{
		ob_start();
	?>
		<script type="text/javascript">
		window.opener.doneLogin();
		window.close();
		</script>
	<?php
		$contents 	= ob_get_contents();
		ob_end_clean();

		echo $contents;

		exit;
	}

	public function getAccess( $token , $secret , $verifier )
	{
		$code		= JRequest::getVar( 'code' );
		$params		= array( 'client_id' 	=> parent::getAppId() ,
							 'redirect_uri'	=> $this->callback,
							 'client_secret'=> parent::getApiSecret(),
							 'code'			=> $code
							);

		$token		= parent::_oauthRequest( parent::getUrl('graph', '/oauth/access_token' ) , $params );
		$token		= str_ireplace( 'access_token=' , '' , $token );

		// Get the expiry time and remove it from the token
		$parts 		= explode( '&expires=' , $token );
		$expires	= '';

		if( count( $parts ) > 1 )
		{
			$token 		= $parts[ 0 ];
			$expires	= $parts[ 1 ];
		}

		$obj			= new stdClass();
		$obj->token		= $token;
		$obj->secret	= 'facebook';
		$obj->params	= '';
		$obj->expires	= $expires;

		return $obj;
	}

	/**
	 * Shares a new content on Facebook
	 **/
	public function share( $blog , $message = '' , $oauth , $useSystem = false )
	{
		$config		= EasyBlogHelper::getConfig();
		$source		= $config->get( 'integrations_facebook_source' );

		$content	= isset( $blog->$source ) && !empty( $blog->$source ) ? $blog->$source : $blog->intro . $blog->content;

		$content	= EasyBlogHelper::getHelper( 'Videos' )->strip( $content );

		$image 		= '';

		// @rule: Ensure that only public posts are allowed
		// if( $blog->private != 0 )
		// {
		// 	return false;
		// }

		// @rule: Try to get the blog image.
		if( $blog->getImage() )
		{
			$image = $blog->getImage()->getSource('frontpage');
			$image = str_ireplace(' ', '%20', $image);
		}

		if( empty( $image ) )
		{
			// @rule: Match images from blog post
			$pattern	= '/<\s*img [^\>]*src\s*=\s*[\""\']?([^\""\'\s>]*)/i';
			preg_match( $pattern , $content , $matches );

			$image		= '';

			if( $matches )
			{
				$image		= isset( $matches[1] ) ? $matches[1] : '';

				if( JString::stristr( $matches[1], 'https://' ) === false && JString::stristr( $matches[1], 'http://' ) === false && !empty( $image ) )
				{
					$image	= rtrim(JURI::root(), '/') . '/' . ltrim( $image, '/');
				}
			}
		}

		$maxContentLen  = $config->get( 'integrations_facebook_blogs_length' );

		$text		= strip_tags( $content );

		if( !empty( $maxContentLen ) )
		{
		    $text       = ( JString::strlen( $text ) > $maxContentLen ) ? JString::substr( $text, 0, $maxContentLen) . '...' : $text;
		}

		$url		= EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry&id=' . $blog->id , false , true );

		// If blog post is being posted from the back end and SH404 is installed, we should just use the raw urls.
		$sh404exists	= EasyBlogRouter::isSh404Enabled();
		$mainframe 		= JFactory::getApplication();

		if( $mainframe->isAdmin() && $sh404exists )
		{
			$url		= rtrim( JURI::root() , '/' ) . '/index.php?option=com_easyblog&view=entry&id='. $blog->id;
		}

		preg_match( '/expires=(.*)/i', $this->_access_token , $expires );

		if( isset( $expires[1]) )
		{
			$this->_access_token	= str_ireplace( '&expires=' . $expires[1] , '' , $this->_access_token );
		}

		// Remove adsense codes
		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'adsense.php' );
		$text 		= EasyBlogGoogleAdsense::stripAdsenseCode( $text );

		$jConfig	= EasyBlogHelper::getJConfig();
		$params		= array(
							'link' 			=>  $url,
							'name'			=> $blog->title,
							// 'actions'		=> '{"name": "' . JText::sprintf( 'COM_EASYBLOG_INTEGRATIONS_FACEBOOK_VIEWON_BUTTON' , addslashes( $jConfig->get( 'sitename' ) ) ) . '", "link" : "' . $url . '"}',
							'description' 	=> $text,
							'message' 		=> $blog->title,
							'access_token' 	=> $this->_access_token
							);

		if( empty( $image ) )
		{
			$params['picture' ]		= rtrim( JURI::root() , '/' ) . '/components/com_easyblog/assets/images/default_facebook.png';
			$params['source' ]		= rtrim( JURI::root() , '/' ) . '/components/com_easyblog/assets/images/default_facebook.png';
		}
		else
		{
			$params[ 'picture' ]	= $image;
			$params[ 'source' ]		= $image;
		}

		// @rule: For system messages, we need to see if there's any pages associated.
		if( $oauth->system && $useSystem )
		{
			if( $config->get( 'integrations_facebook_impersonate_page' ) || $config->get( 'integrations_facebook_impersonate_group' ) )
			{
				if( $config->get( 'integrations_facebook_impersonate_page' ) )
				{
					$pages	= JString::trim( $config->get( 'integrations_facebook_page_id' ) );
					$pages	= explode( ',' , $pages );
					$total	= count( $pages );

					// @rule: Test if there are any pages at all the user can access
					$accounts	= parent::api( '/me/accounts' , array( 'access_token' => $this->_access_token ) );

					if( is_array( $accounts ) && isset( $accounts[ 'data' ] ) )
					{
						for( $i = 0; $i < $total; $i++ )
						{
							foreach( $accounts[ 'data' ] as $page )
							{
								if( $page[ 'id' ] == $pages[ $i ] )
								{
									$params['access_token']	= $page[ 'access_token' ];
									$query	= parent::api( '/' . $page[ 'id' ] . '/feed' , 'post' , $params );
								}
							}
						}
					}
				}

				if( $config->get( 'integrations_facebook_impersonate_group' ) )
				{
					$groupsId	= JString::trim( $config->get( 'integrations_facebook_group_id' ) );
					$groupsId	= explode( ',' , $groupsId );
					$total	= count( $groupsId );

					// @rule: Test if there are any groups at all the user can access
					$accounts	= parent::api( '/me/groups' , 'GET' , array( 'access_token' => $this->_access_token ) );

					$params[ 'access_token' ]	= $this->_access_token;

					if( is_array( $accounts ) && isset( $accounts[ 'data' ] ) )
					{
						for( $i = 0; $i < $total; $i++ )
						{
							foreach( $accounts[ 'data' ] as $group )
							{

								if( $group[ 'id' ] == $groupsId[ $i ] )
								{

									$query	= parent::api( '/' . $group[ 'id' ] . '/feed' , 'post' , $params );

								}
							}
						}
					}
				}
			}
			else
			{
				// @rule: If this is just a normal posting, just post it on their page.
				$query		= parent::api( '/me/feed' , 'post' , $params );
			}
		}
		else
		{
			// @rule: If this is just a normal posting, just post it on their page.
			$query		= parent::api( '/me/feed' , 'post' , $params );
		}

		$success	= isset( $query['id'] ) ? true : false;

		return $success;
	}

    function findItem($needle, $haystack, $partial_matches = false, $search_keys = false)
	{
        if(!is_array($haystack)) return false;
        foreach($haystack as $key=>$value) {
            $what = ($search_keys) ? $key : $value;
            if($needle===$what) return $key;
            else if($partial_matches && @strpos($what, $needle)!==false) return $key;
            else if(is_array($value) && self::findItem($needle, $value, $partial_matches, $search_keys)!==false) return $key;
        }
        return false;
    }

	public function setAccess( $access )
	{
		$access	= EasyBlogHelper::getRegistry( $access );

		$this->_access_token	= $access->get( 'token' );
		return true;
	}

	public function revokeApp()
	{
		return true;
	}
}
