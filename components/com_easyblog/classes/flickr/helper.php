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
require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'flickr' . DIRECTORY_SEPARATOR . 'consumer.php' );

class EasyBlogFlickr extends FlickrOauth
{
	public $callback		= '';
	public $_access_token	= '';

	private $param		= '';

	public function __construct( $key , $secret , $callback )
	{
		$this->callback	= $callback;

		parent::__construct( $key , $secret );
	}

	/**
	 * Facebook does not need the request tokens
	 *
	 **/
	public function getRequestToken()
	{
		$token		= parent::getRequestToken( $this->callback );

		$obj			= new stdClass();
		$obj->token		= $token[ 'oauth_token' ];
		$obj->secret	= $token[ 'oauth_token_secret' ];

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
		$verifier	= JRequest::getVar( 'oauth_verifier' , '' );
		return $verifier;
	}

	/**
	 * Returns the authorization url.
	 *
	 * @return string	$url	A link to Facebook's login URL.
	 **/
	public function getAuthorizationURL( $token )
	{
		return parent::getAuthorizeURL( $token );
	}

	public function getAccess( $token , $secret , $verifier )
	{
		$this->token = new OAuthConsumer($token, $secret);

		$access		= parent::getAccessToken($verifier);

		if(empty($access['oauth_token']) && empty($access['oauth_token_secret']))
		{
			return false;
		}

		$obj			= new stdClass();

		$obj->token		= $access['oauth_token'];
		$obj->secret	= $access['oauth_token_secret'];

		$param			= EasyBlogHelper::getRegistry('');
		$param->set( 'user_id' 		, $access['user_nsid'] );
		$param->set( 'username' 	, $access['username'] );

		$obj->params	= $param->toString();
		//@todo: expiry

		return $obj;
	}

	/**
	 * Returns a list of photos for a specific user.
	 */
	public function getPhotos()
	{
		// Initialize the token for the current request.
		$this->token 	= new OAuthConsumer( $this->_access_token , $this->_secret_token );

		$result			= parent::get( array(	'method'			=> 'flickr.people.getPhotos',
											 	'format'			=> 'json',
											 	'nojsoncallback'	=> 1,
												'user_id'			=> 'me',
												'privacy_filter'	=> 1,
												'per_page'			=> 500,
												'extras'			=> 'date_upload,original_format,media,description,license'
										)
									);

		if( empty( $result->photos->photo ) )
		{
			return false;
		}

		// Let's build the photos URL now.
		$photos		= array();

		foreach( $result->photos->photo as $photoItem )
		{
			$obj		= $this->buildPhotoObject( $photoItem );

			$obj->id			= $photoItem->id;
			$obj->dateupload 	= $photoItem->dateupload;

			$photos[]	= $obj;
		}

		return $photos;
	}

	public function getPhoto( $photoId )
	{
		// Initialize the token for the current request.
		$this->token 	= new OAuthConsumer( $this->_access_token , $this->_secret_token );

		$result			= parent::get( array(	'method'			=> 'flickr.photos.getInfo',
											 	'format'			=> 'json',
											 	'nojsoncallback'	=> 1,
												'photo_id'			=> $photoId,
												'privacy_filter'	=> 1
										)
									);


		if( empty( $result->photo ) )
		{
			return false;
		}

		$photo				= $result->photo;

		$photo->title		= $photo->title->_content;
		$obj				= $this->buildPhotoObject( $photo );
		$obj->id			= $photo->id;
		$obj->dateupload 	= $photo->dateuploaded;

		return $obj;
	}

	/**
	 * Formats the raw data provided by Flickr and get the correct URL to the Flickr image.
	 *
	 * @access	public
	 * @param	object	$photo	The metadata of the photo
	 * @return	object			A stdClass object with it's own properties.
	 */
	public function buildPhotoObject( $photoItem )
	{
		// Initialize the token for the current request.
		$this->token 	= new OAuthConsumer( $this->_access_token , $this->_secret_token );

		$result			= parent::get( array(	'method'			=> 'flickr.photos.getSizes',
											 	'format'			=> 'json',
											 	'nojsoncallback'	=> 1,
												'photo_id'			=> $photoItem->id,
												'privacy_filter'	=> 1
										)
									);

		$sizes			= $result->sizes->size;

		$photo 			= new stdClass();
		$photo->title	= $photoItem->title;
		// $photo->public	= $photoItem->ispublic;

		$photo->sizes	= array();

		foreach( $sizes as $size )
		{
			$obj		= new stdClass();
			$obj->title		= $size->label;
			$obj->width		= $size->width;
			$obj->height 	= $size->height;
			$obj->source 	= $size->source;
			$photo->sizes[ $size->label ]	= $obj;
		}

		return $photo;
	}

	public function setParams( $params )
	{
		$param	= EasyBlogHelper::getRegistry( $params );

		$this->_param	= $param;

		return $this->_param;
	}

	public function setAccess( $access )
	{
		$access	= EasyBlogHelper::getRegistry( $access );

		$this->_access_token	= $access->get( 'token' );
		$this->_secret_token 	= $access->get( 'secret' );
		return true;
	}

	public function revokeApp()
	{
		return true;
	}
}
