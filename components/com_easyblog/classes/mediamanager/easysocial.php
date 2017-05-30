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

class EasyBlogMediaManagerEasySocialSource
{
	private $relative	= null;
	private $path 		= null;
	private $fileName	= null;
	private $baseURI 	= null;
	private $exists		= null;

	public function __construct()
	{
		$file 	= JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php';

		jimport( 'joomla.filesystem.file' );

		if( !JFile::exists( $file ) )
		{
			$this->exists	= false;
		}

		require_once( $file );
		$this->exists	= true;
	}

	/**
	 * Returns an array of folders / albums in a given folder since jomsocial only stores user images here.
	 *
	 * @access	public
	 * @param	string	$path	The path that contains the items.
	 * @param	int 	$depth	The depth level to search for child items.
	 */
	public function getItems()
	{
		if( !$this->exists )
		{
			return false;
		}

		$my			= JFactory::getUser();

		// @rule: Get user's albums
		require_once( JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php' );


		$model 		= Foundry::model( 'Albums' );
		$albums 	= $model->getAlbums( $my->id , SOCIAL_TYPE_USER );

		if( !$albums )
		{
			$obj	= new stdClass();

			$obj->title		= JText::_( 'COM_EASYBLOG_MM_MY_ALBUMS' );
			$obj->type		= 'folder';
			$obj->place		= 'easysocial';
			$obj->path		= DIRECTORY_SEPARATOR;
			$obj->contents	= array();

			return $obj;
		}

		// Load EasySocial's language file
		JFactory::getLanguage()->load('com_easysocial', JPATH_ROOT);

		$data	= array();
		$photosModel 	= Foundry::model( 'Photos' );

		foreach( $albums as $album )
		{
			$container	= array();

			// Get a list of photos from this album
			$photos 	= $photosModel->getPhotos( array( 'album_id' => $album->id , 'pagination' => false ) );

			// Get the album object.
			$albumObj	= $this->getObject( $album , 'album' );

			$photosData	= array();

			foreach( $photos as $photo )
			{
				// Get the photo object.
				$photoObj		= $this->getObject( $photo , 'photo' );
				if ($photoObj) {
					$photosData[]	= $photoObj;
				}
			}

			$albumObj->contents	= $photosData;

			$data[]			= $albumObj;
		}

		$obj 	= new stdClass();

		$obj->type 		= 'folder';
		$obj->contents	= $data;
		$obj->place 	= 'easysocial';
		$obj->path 		= DIRECTORY_SEPARATOR;

		return $obj;
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
		if( !$this->exists )
		{
			return false;
		}

		$photo 	= Foundry::table( 'Photo' );
		$photo->load( $photoId );

		$obj				= $this->getObject( $photo , 'photo' );
		if (! $obj) {
			return false;
		}

		$obj->variations	= $this->getVariations( $photo );
		return $obj;
	}

	public function getVariations( &$photo )
	{
		$sizes		= array( 'original' , 'thumbnail' );

		// If remote storage is used, try to not include thumbnail.
		if( $photo->storage != 'joomla' )
		{
			$sizes	= array( 'original' );
		}

		$variations	= array();

		foreach( $sizes as $size )
		{
			$variation	= new stdClass();

			$path 		= str_ireplace( JURI::root() , JPATH_ROOT . '/' , $photo->getSource( $size ) );

			$info						= @getimagesize( $path );

			$variation->type 			= 'image';
			$variation->name			= JText::_( 'COM_EASYBLOG_MM_VARIATION_' . strtoupper( $size ) );
			$variation->title			= $photo->caption;

			// @task: Get the absolute URI to the item.
			$variation->url				= $photo->getSource( $size );

			// @task: Get the creation date of the item.
			$variation->creationDate 			= EasyBlogHelper::getDate( $photo->created )->toMySQL();

			// @task: Set the thumbnail
			$variation->thumbnail			= new stdClass();
			$variation->thumbnail->url 		= $photo->getSource( 'thumbnail' );

			$variation->relativePath	= '';

			$variation->mime			= 'n/a';

			$variation->width			= false;
			$variation->height			= false;

			// If this is a file type, we'll try to stat the image
			if( $photo->storage == 'joomla' )
			{
				$info						= @getimagesize( $path );
				$variation->mime			= $info[ 'mime' ];

				$variation->width			= $info[0];
				$variation->height			= $info[1];
			}

			$variation->default			= false;

			if( $size == 'thumbnail' )
			{
				$variation->default			= true;
			}

			$variation->canDelete		= false;
			$variation->size			= filesize( $path );
			$variations[]				= $variation;
		}

		return $variations;
	}

	private function getObject( $item , $type )
	{
		$obj 			= new stdClass();

		if( $type == 'album' )
		{
			$obj->type 		= 'folder';

			// @task: Get the media item's title.
			$obj->title		= JText::_($item->title);

			// @task: Get the mime type
			$obj->mime 		= '';

			// @task: Determine the filesize of this item (Bytes).
			$obj->filesize 	= '';

			// Set the current place.
			$obj->place 	= 'easysocial';

			// @task: Get the absolute URI to the item.
			$obj->uri 		= rtrim( JURI::root() , '/' ) . str_ireplace( JPATH_ROOT , '' , $item->getStoragePath() );

			// @task: Get the creation date of the item.
			$obj->creationDate 	= EasyBlogHelper::getDate( $item->created )->toMySQL();

			// @task: Get the contents
			// @todo
			$obj->path			= DIRECTORY_SEPARATOR . $item->id;

			$obj->icon 			= new stdClass();
			$obj->icon->url		= rtrim( JURI::root() , '/' ) . '/components/com_easyblog/assets/images/gallery.png';

			$obj->contents		= '';

		}
		else if( $type == 'photo' )
		{
			$obj->type 		= 'image';

			// @task: Get the media item's title.
			$obj->title		= JText::_($item->caption);

			$album 			= Foundry::table( 'Album' );
			$album->load( $item->album_id );

			$path 			= $item->getSource( 'original' );
			$path 			= str_ireplace( JURI::root() , JPATH_ROOT . '/' , $path );

			if (! JFile::exists($path)) {
				return false;
			}

			$info			= @getimagesize( $path );

			// @task: Get the media item's width.
			$obj->width 	= $info[ 0 ];

			// @task: Get the media item's height.
			$obj->height 	= $info[ 1 ];

			// @task: Get the mime type
			$obj->mime 		= $info[ 'mime' ];

			// @task: Determine the filesize of this item (Bytes).
			$obj->filesize 	= filesize( $path );

			// Set the current place.
			$obj->place 	= 'easysocial';

			// @task: Get the absolute URI to the item.
			$obj->url 			= $item->getSource( 'original' );

			// @task: Get the creation date of the item.
			$obj->creationDate 	= EasyBlogHelper::getDate( $item->created )->toMySQL();

			// @task: Set the thumbnail
			$obj->thumbnail			= new stdClass();
			$obj->thumbnail->url 	= $item->getSource( 'thumbnail' );

			// @task: Set the thumbnail
			$obj->icon				= new stdClass();
			$obj->icon->url			= $item->getSource( 'thumbnail' );

			// @task: Get the contents
			$obj->path 		= '/' . $item->album_id . '/' . $item->id;
		}

		return $obj;
	}
}
