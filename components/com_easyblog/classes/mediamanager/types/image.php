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

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'item.php' );

class EasyBlogMediaManagerImage extends EasyBlogMediaManagerItem
{
	public $type 		= 'image';
	private $info 		= null;
	private $includeVariation = null;
	private $place 				= null;

	private $includeDimension	= false;
	private $flatList			= false;

	public function __construct( $file , $baseURI , $relativePath = '' , $includeVariation = false , $flatList = false , $absolutePath = false , $isUpload = false , $foldersOnly = false , $place = '' )
	{
		$this->file 				= $file;
		$this->baseURI				= $baseURI;
		$this->relativePath			= $relativePath;

		if( $includeVariation && !$isUpload )
		{
			$this->baseURI			= dirname( $baseURI );
			$this->relativePath		= dirname( $relativePath );
		}

		$this->flatList		= $flatList;
		$this->place 		= $place;

		// If the current layout is in a flat list, the URL should contain the appropriate URL.
		if( $flatList )
		{
			// We ned to remove the absolute path from the file.
			$tmp		= str_ireplace( rtrim( $absolutePath , DIRECTORY_SEPARATOR ) , '' , dirname( $file ) );

			$this->baseURI	.= str_ireplace( '\\' , '/' , $tmp );;

			$this->relativePath	= str_ireplace( array( '\\' , '/') , DIRECTORY_SEPARATOR , $tmp );
		}

		$this->includeVariation	= $includeVariation;

		$this->includeDimension	= !$flatList;


		// @task: Initialize the image info first.
		if( $this->includeDimension )
		{
			$this->info 	= @getimagesize( $this->file );
		}
	}

	public function getURI()
	{
		return $this->baseURI . '/' .$this->getTitle();
	}

	/**
	 * Gets the filename from a given path.
	 *
	 * @access	public
	 * @param	string	$file	The absolute path to the file.
	 */
	public function getTitle()
	{
		$title	= basename( $this->file );

		return $title;
	}

	/**
	 * Gets the filename from a given path.
	 *
	 * @access	public
	 * @param	string	$file	The absolute path to the file.
	 * @return	Array if successful, false if failed.
	 */
	public function getWidth()
	{
		if( !$this->info )
		{
			return false;
		}

		return $this->info[ 0 ];
	}


	/**
	 * Gets the filename from a given path.
	 *
	 * @access	public
	 * @param	string	$file	The absolute path to the file.
	 * @return	Array if successful, false if failed.
	 */
	public function getHeight()
	{
		if( !$this->info )
		{
			return false;
		}

		return $this->info[ 1 ];
	}

	public function getMime()
	{
		if( !$this->info )
		{
			return false;
		}

		return $this->info[ 'mime' ];
	}

	public function getSize()
	{
		return $this->formatSize( filesize( $this->file ) );
	}

	/**
	 * Get a list of variations for the particular image item.
	 *
	 * @access	public
	 * @param	null
	 * @return 	Array	An array of variation objects.
	 */
	public function inject( &$obj )
	{

		// Insert variations to the obj here
		if( ($this->includeVariation || !$this->includeDimension ) && !$this->flatList )
		{
			// Initialize the variation.
			$obj->variations	= array();

			// @task: Get the filename
			$fileName 		= $this->getTitle();

			// @task: Filter mask for variations.
			$filter 		= '(' . EBLOG_USER_VARIATION_PREFIX . ')_[a-zA-Z0-9]*_' . str_ireplace( '.' , '\.' , $fileName );

			// @task: Get the base folder for this image.
			$baseFolder		= dirname( $this->file );

			// @task: Lookup for variations
			$variations		= JFolder::files( $baseFolder , $filter );

			// Always append the system variations into the object.
			$obj->variations	= $this->getSystemVariations();

			if( $variations )
			{
				foreach( $variations as $variation )
				{
					// @task: Get more information about the image variation.
					if( !$this->includeDimension )
					{
						$width	= 0;
						$height	= 0;
					}
					else
					{
						$info	= @getimagesize( $baseFolder . DIRECTORY_SEPARATOR . $variation );
						$width	= $info[0];
						$height	= $info[1];
					}

					// @task: determine if the variation can be deleted.
					$canDelete	= stristr( EBLOG_SYSTEM_VARIATION_PREFIX , $variation ) === false;

					$media				= new EasyBlogMediaManager();
					$data				= $media->getItem( $baseFolder . DIRECTORY_SEPARATOR . $variation , dirname( $this->getURI() ) , $this->relativePath, false, $this->place , false, false, true )->toObject();

					$variationObj		= $this->getVariationObject( $data , $variation , $width , $height , $canDelete , false );
					$obj->variations[]	= $variationObj;
				}
			}
		}


		// Include a thumbnail for image items.
		$fileName	= $this->getTitle();
		$thumbFile	= EBLOG_SYSTEM_VARIATION_PREFIX . '_thumbnail_' . $fileName;
		$path 		= JPath::clean( dirname( $this->file ) . DIRECTORY_SEPARATOR . $thumbFile );

		// Check if the new MMIM format for thumbnail exists
		if( !JFile::exists( $path ) )
		{
			// @task: Check for legacy thumbnail images
			$thumbFile	= EBLOG_MEDIA_THUMBNAIL_PREFIX . $fileName;
			$path 		= JPath::clean( dirname( $this->file ) . DIRECTORY_SEPARATOR . $thumbFile );

			if( !JFile::exists( $path ) )
			{
				// If all the above is not found, we have no choice but to use the original file.
				$thumbFile 		= $this->getTitle();
			}
		}

		$obj->thumbnail				= new stdClass();
		$obj->thumbnail->url		= $this->baseURI . '/' . $thumbFile;

		// Include a icon for image items
		$iconFile	= EBLOG_SYSTEM_VARIATION_PREFIX . '_icon_' . $fileName;
		$path		= JPath::clean( dirname( $this->file ) . DIRECTORY_SEPARATOR . $iconFile );

		// Check if the new MMIM format for thumbnail exists
		if( !JFile::exists( $path ) )
		{
			// @task: Check for MMIM format for thumbnail
			$iconFile	= EBLOG_SYSTEM_VARIATION_PREFIX . '_thumbnail_' . $fileName;;
			$path 		= JPath::clean( dirname( $this->file ) . DIRECTORY_SEPARATOR . $iconFile );

			if( !JFile::exists( $path ) )
			{
				// @task: Check for legacy thumbnail images
				$iconFile	= EBLOG_MEDIA_THUMBNAIL_PREFIX . $fileName;
				$path 		= JPath::clean( dirname( $this->file ) . DIRECTORY_SEPARATOR . $iconFile );

				if( !JFile::exists( $path ) )
				{
					// If all the above is not found, we have no choice but to use the original file.
					$iconFile 		= $this->getTitle();
				}
			}
		}

		$obj->icon			= new stdClass();
		$obj->icon->url		= $this->baseURI . '/' . $iconFile;
	}

	public function createVariation( $absoluteURI , $variationName , $width , $height , $variationType = EBLOG_VARIATION_USER_TYPE )
	{
		$this->baseURI	= $absoluteURI;

		// @task: Determine the path to this new image variation.
		$title 		= $this->getTitle();
		$prefix 	= EBLOG_USER_VARIATION_PREFIX;

		if( $variationType == EBLOG_VARIATION_SYSTEM_TYPE )
		{
			$prefix 	= EBLOG_SYSTEM_VARIATION_PREFIX;
		}

		$variationFile	= $prefix . '_' . $variationName . '_' . basename( $this->file );
		$path			= dirname( $this->file ) . DIRECTORY_SEPARATOR . $variationFile;

		// @task: Return an error when a variation with the same title already exist.
		if( JFile::exists( $path ) )
		{
			return JText::_( 'COM_EASYBLOG_FAILED_TO_CREATE_VARIATION_AS_IT_EXISTS' );
		}

		// @task: Let's work on the smart resizing now.
		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'easysimpleimage.php' );
		$config 		= EasyBlogHelper::getConfig();
		$simpleImage	= new EasySimpleImage();
		$simpleImage->load( $this->file );
		$simpleImage->resize( $width , $height );

		$state			= $simpleImage->save( $path , $simpleImage->image_type , $config->get( 'main_image_quality') );

		if( !$state )
		{
			return JText::_( 'COM_EASYBLOG_FAILED_TO_CREATE_VARIATION_PERMISSIONS' );
		}

		// @rule: Determine if this variation can be deleted.
		$canDelete 		= $variationType != EBLOG_VARIATION_SYSTEM_TYPE;

		$media			= new EasyBlogMediaManager();
		$data			= $media->getItem( $path , dirname( dirname( $this->getURI() ) ) , $this->relativePath, false, $this->place , false, false, true)->toObject();
		return $this->getVariationObject( $data , $variationName , $width , $height , $canDelete , false );
	}

	/**
	 * Deletes a given variation type.
	 */
	public function deleteVariation( $variationName )
	{
		// Get the title of the current image.
		$title 	= $this->getTitle();

		// We only allow deletion of user prefixes.
		$prefix = EBLOG_USER_VARIATION_PREFIX;

		$variationFile 	= $prefix . '_' . $variationName . '_' . basename( $this->file );
		$path 			= dirname( $this->file ) . DIRECTORY_SEPARATOR . $variationFile;

		// @task: Check for the existance of such variation
		if( !JFile::exists( $path ) )
		{
			return JText::_( 'COM_EASYBLOG_FAILED_TO_DELETE_VARIATION_AS_IT_DOESNT_EXISTS' );
		}

		$state 	= JFile::delete( $path );

		if( !$state )
		{
			return JText::_( 'COM_EASYBLOG_FAILED_TO_DELETE_VARIATION_PERMISSIONS' );
		}

		return true;
	}

	/**
	 * Retrieves a list of system variations for the image type.
	 */
	public function getSystemVariations()
	{
		$variations		= array( 'thumbnail' , 'icon' );
		$fileName 		= $this->getTitle();
		$result			= array();
		$uri 			= dirname( $this->getURI() );

		// @task: Get all new 3.5 image items that has a prefix of EBLOG_SYSTEM_VARIATION_PREFIX
		foreach( $variations as $variation )
		{
			$systemVariationFile 	= dirname( $this->file ) . DIRECTORY_SEPARATOR . EBLOG_SYSTEM_VARIATION_PREFIX . '_' . $variation . '_' . $fileName;

			// @Legacy: Prior to 3.5 !IMPORTANT!
			// @task: Check if there are any missing items like thumbnail or icon. If it's missing, we need to fix this.
			if( !JFile::exists( $systemVariationFile ) )
			{
				$systemVariationFile 	= dirname( $this->file ) . DIRECTORY_SEPARATOR . 'thumb_' . $fileName;

				// If the legacy thumb item still doesn't exist, we just fall back to the original image :(
				// Otherwise we have nothing to show to the user.
				if( !JFile::exists( $systemVariationFile ) )
				{
					$systemVariationFile 	= $this->file;
				}
			}

			// @task: Get more information about the image variation.
			if(!$this->includeDimension )
			{
				$width	= 0;
				$height	= 0;
			}
			else
			{
				$info	= @getimagesize( $systemVariationFile );
				$width	= $info[0];
				$height	= $info[1];
			}

			$media			= new EasyBlogMediaManager();
			$data			= $media->getItem( $systemVariationFile , $uri , $this->relativePath, false, $this->place , false, false, true)->toObject();

			$variationObj	= $this->getVariationObject( $data , $variation , $width , $height , false , $variation == 'thumbnail' );

			$result[] 	= $variationObj;
		}

		// @task: The original file is also treated as a variation
		if(!$this->includeDimension )
		{
			$width	= 0;
			$height	= 0;
		}
		else
		{
			$info 		= @getimagesize( $this->file );
			$width 		= $info[0];
			$height 	= $info[1];
		}

		$media		= new EasyBlogMediaManager();

		$data		= $media->getItem( $this->file , dirname( $this->getURI() ) , $this->relativePath, false, $this->place , false, false, true)->toObject();

		$result[] 	= $this->getVariationObject( $data , 'original' , $width , $height , false , false );

		return $result;
	}

	public function getVariationObject( &$obj , $variationName , $width , $height , $canDelete , $default )
	{
		// Remove prefixes from the title
		$pattern	= '/' . EBLOG_USER_VARIATION_PREFIX . '_[0-9a-zA-Z]*/is';

		preg_match( $pattern , $variationName , $matches );

		if( isset( $matches[0] )  && !empty( $matches[ 0 ] ) )
		{
			// Remove the variation part
			$variationName	= str_ireplace( EBLOG_USER_VARIATION_PREFIX . '_' , '' , $matches[ 0 ] );
		}

		$obj->name 		= $variationName;

		$obj->width		= $width;
		$obj->height	= $height;
		$obj->canDelete	= $canDelete;
		$obj->default 	= $default;

		return $obj;
	}

	/**
	 * Override parent's delete implementation since this is a folder.
	 *
	 * @access	public
	 * @param	string	$path	The path to the folder.
	 */
	public function delete( $path )
	{
		// @TODO: Delete image variations here.
		jimport( 'joomla.filesystem.file' );

		$name		= basename( $path );
		$name 		= str_ireplace( '.' , '\.' , $name );
		$filter		= '(.*)_' . $name;
		$base		= dirname( $path );

		$files		= JFolder::files( $base , $filter , false , true );

		// @task: Add original image to path.
		$files[]	= $path;

		// Let's request the parent to delete the item first.
		foreach( $files as $file )
		{
			parent::delete( $file );
		}


		return true;
	}

	public static function upload( $storagePath , $fileName , $fileItem )
	{
		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'easysimpleimage.php' );

		$cfg 		= EasyBlogHelper::getConfig();
		$user 		= JFactory::getUser();

		// @task: Resize original image if necessary. Otherwise, just copy the file over.
		$originalStorage	= JPath::clean( $storagePath . DIRECTORY_SEPARATOR . $fileName );

		// check if folder not exists, then we need to create first or else the copy operation will fail.
		if(! JFolder::exists( $storagePath ) )
		{
			JFolder::create( $storagePath );
		}

		$image	= new EasySimpleImage();
		$image->load( $fileItem['tmp_name'] );

		if( $cfg->get('main_resize_original_image' ) )
		{
			// @task: Get the maximum width / height for the original images.
			$maxWidth	= $cfg->get('main_original_image_width');
			$maxHeight	= $cfg->get('main_original_image_height');

			if ($image->getWidth() > $maxWidth || $image->getHeight() > $maxHeight) {
				$image->resizeWithin( $maxWidth , $maxHeight );
				$state	= $image->save( $originalStorage , $image->image_type , $cfg->get( 'main_original_image_quality' ) );
			} else {
				$state	= JFile::copy( $fileItem[ 'tmp_name' ] , $originalStorage );
			}
		}
		else
		{
			// @task: Since no resizing is required, just upload the whole file.
			if ($image->orientationFixed) {
				$state	= $image->save( $originalStorage , $image->image_type , $cfg->get( 'main_original_image_quality' ) );
			} else {
				$state	= JFile::copy( $fileItem[ 'tmp_name' ] , $originalStorage );
			}
		}

		// @task: Once the original storage has been taken care off, we need to find the sizes that are required to be resized.
		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'image.php' );
		$imageObj	= new EasyBlogImage( $fileName , $storagePath , '' );

		// @TODO: Cater for error checking here.
		$imageObj->initDefaultSizes();

		if( $state === false )
		{
			return JText::_( 'COM_EASYBLOG_MM_UNABLE_TO_SAVE_FILE' );
		}

		return true;
	}

	public function getVariations()
	{
		// @task: Get the filename
		$fileName 		= $this->getTitle();

		// @task: Filter mask for variations.
		$filter 		= '(' . EBLOG_USER_VARIATION_PREFIX . ')_[a-zA-Z0-9]*_' . str_ireplace( '.' , '\.' , $fileName );

		// @task: Get the base folder for this image.
		$baseFolder		= dirname( $this->file );

		// @task: Lookup for variations
		$variations		= JFolder::files( $baseFolder , $filter );

		// @task: Get system variations
		$filter 		= '(' . EBLOG_SYSTEM_VARIATION_PREFIX . ')_[a-zA-Z0-9]*_' . str_ireplace( '.' , '\.' , $fileName );

		// @task: Lookup for variations
		$variations		= array_merge( $variations , JFolder::files( $baseFolder , $filter ) );

		return $variations;
	}

	/**
	 * Override parent's implementation of renaming since we also need to rename image variations.
	 *
	 *
	 */
	public function rename( $source , $destination )
	{
		$info		= @pathinfo( $source );
		$name		= $info[ 'filename' ];
		$fileName	= $info[ 'basename' ];

		// @task: Now we need to rename all the variations to use the correct title.
		$variations	= $this->getVariations();

		foreach( $variations as $variation )
		{
			$path 	= dirname( $this->file );
		}

		// @task: Move the original file.
		//JFile::move( $source , $destination );

		var_dump( $info );exit;
		var_dump( $source );exit;
	}
}
