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

require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'json.php' );

class EasyBlogMediaManagerFlickrSource
{
	private $oauth		= null;

	public function __construct()
	{
		$my 				= JFactory::getUser();

		// @rule: Test if the user is already associated with Flickr
		$this->oauth		= EasyBlogHelper::getTable( 'Oauth' );
		$this->oauth->loadByUser( $my->id , EBLOG_OAUTH_FLICKR );
	}

	/**
	 * Return a list of images that this user has.
	 */
	public function getItems()
	{
		$config		= EasyBlogHelper::getConfig();

		// @rule: If account is already associated, we just need to get the photos from their Flickr account.
		$consumer	= EasyBlogHelper::getHelper( 'Oauth' )->getConsumer( EBLOG_OAUTH_FLICKR , $config->get( 'integrations_flickr_api_key' ) , $config->get( 'integrations_flickr_secret_key' ) , JURI::root() );
		$consumer->setAccess( $this->oauth->access_token );
		$consumer->setParams( $this->oauth->params );

		$result		= $consumer->getPhotos();
		$photos		= array();

		foreach( $result as $photoItem )
		{
			$photos[]	= $this->getObject( $photoItem );
		}

		// @task: Need to mimic the behavior of the MMIM
		$folder				= new stdClass();
		$folder->title		= JText::_( 'COM_EASYBLOG_YOUR_FLICKR_PHOTOS' );
		$folder->type 		= 'folder';
		$folder->path 		= DIRECTORY_SEPARATOR;
		$folder->place 		= 'flickr';
		$folder->contents	= $photos;
		$folder->width 		= '';
		$folder->height		= '';


		return $folder;
	}

	/**
	 * Returns the information of a photo object.
	 *
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function getItem( $photoId )
	{
		$config		= EasyBlogHelper::getConfig();

		// @rule: If account is already associated, we just need to get the photos from their Flickr account.
		$consumer	= EasyBlogHelper::getHelper( 'Oauth' )->getConsumer( EBLOG_OAUTH_FLICKR , $config->get( 'integrations_flickr_api_key' ) , $config->get( 'integrations_flickr_secret_key' ) , JURI::root() );
		$consumer->setAccess( $this->oauth->access_token );
		$consumer->setParams( $this->oauth->params );

		$result		= $consumer->getPhoto( $photoId );
		$photo 		= $this->getObject( $result );

		$photo->variations	= $this->getVariations( $result->sizes );

		return $photo;
	}

	public function getVariations( $sizes )
	{
		if( !$sizes )
		{
			return array();
		}

		$variations	= array();

		foreach( $sizes as $size => $obj )
		{
			$variation	= new stdClass();

			$variation->type 	= 'image';
			$variation->title	= $size;
			$variation->url		= $obj->source;
			$variation->thumbnail	= new stdClass();
			$variation->thumbnail->url	=  $obj->source;
			$variation->relativePath	= '';
			$variation->mime			= '';
			$variation->name	= $size;
			$variation->width	= $obj->width;
			$variation->height	= $obj->height;
			$variation->default	= false;
			$variation->canDelete	= false;

			$variations[]	= $variation;
		}

		return $variations;
	}

	public function getObject( &$photoItem )
	{

		$obj 				= new stdClass();
		$obj->type 			= 'image';
		$obj->title			= $photoItem->title;

		// @task: Get the absolute URI to the item.
		$obj->url 			= $photoItem->sizes['Large']->source;

		// @task: Get the media item's width.
		$obj->width 		= $photoItem->sizes['Large']->width;

		// @task: Get the media item's height.
		$obj->height 		= $photoItem->sizes['Large']->height;

		// Set the current place.
		$obj->place 		= 'flickr';


		// @task: Set the thumbnail
		$obj->thumbnail 		= new stdClass();
		$obj->thumbnail->url	= $photoItem->sizes[ 'Medium' ]->source;

		// @task: Set the thumbnail
		$obj->icon 		= new stdClass();
		$obj->icon->url	= $photoItem->sizes[ 'Thumbnail' ]->source;


		$obj->relativePath		= '';

		// @task: Get the creation date of the item.
		$obj->dateModified      = $photoItem->dateupload;

		$obj->creationDate		= EasyBlogHelper::getDate( $photoItem->dateupload )->toMySQL();

		// @task: Get the mime type
		$obj->mime 				= '';

		// @task: Determine the filesize of this item (Bytes).
		$obj->filesize 			= '';

		// @task: Get the contents
		// @todo
		$obj->path				= DIRECTORY_SEPARATOR . $photoItem->id;

		return $obj;
	}
}
