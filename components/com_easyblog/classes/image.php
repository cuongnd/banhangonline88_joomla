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

class EasyBlogImage
{

	private $json		= null;

	private $image 		= null;
	private $fileName	= null;
	private $original 	= null;

	// Storage absolute path
	private $storage	= null;

	// Storage URI
	private $uri 		= null;

	public $sizes		= array();

	// Custom sizes defined by themes or template overrides.
	private $customSizes	= array();

	// Determines the current active theme.
	private $activeTheme	= '';

	public function __construct( $fileName , $storagePath , $storageURI )
	{
		// @task: Retrieve the configs.
		$config			= EasyBlogHelper::getConfig();

		// @task: Initialize json lib
		$this->json		= new Services_JSON();

		// @task: Just in case $fileName is a subfolder, we need to get the appropriate file name.
		//
		// need to make sure the directory separator is in the correct format.
		$fileName = str_ireplace( array( '/' , '\\' ) , DIRECTORY_SEPARATOR, $fileName );
		$paths			= pathinfo( $fileName );

		// @task: Set the current image item
		$this->image 	= trim( $paths[ 'basename' ] , '/\\' );

		// @task: Set the storage path
		$this->storage	= $storagePath;

		// @task: Set the storage uri
		$this->uri 		= $storageURI;

		if( $paths[ 'dirname' ] != '.' )
		{
			$this->storage	.= str_ireplace( array( '/' , '\\' ) , DIRECTORY_SEPARATOR, $paths[ 'dirname' ] );
			$this->uri 		.= str_ireplace( array( '/' , '\\' ) , '/' , $paths[ 'dirname' ] );
		}

		// Ensure that the storage doesn't have a trailing /
		$this->storage 		= rtrim( $this->storage , '/,\\' );

		// Ensure that the URI doesn't have a trailing /
		$this->uri 			= rtrim( $this->uri , '/' );

		// @task: Set the original path
		$this->original	= $this->storage . DIRECTORY_SEPARATOR . $this->image;

		// Initialize the original width / height based on the configurations
		$this->sizes[ 'original' ]          = new stdClass();
		$this->sizes[ 'original' ]->width   = $config->get( 'main_original_image_width' );
		$this->sizes[ 'original' ]->height  = $config->get( 'main_original_image_height' );
		$this->sizes[ 'original' ]->quality = $config->get( 'main_original_image_quality' );

		// Initialize the thumb size with width / height.
		$this->sizes[ 'thumbnail' ]          = new stdClass();
		$this->sizes[ 'thumbnail' ]->width   = $config->get( 'main_thumbnail_width' );
		$this->sizes[ 'thumbnail' ]->height  = $config->get( 'main_thumbnail_height' );
		$this->sizes[ 'thumbnail' ]->quality = $config->get( 'main_thumbnail_quality' );

		// Initialize the thumb size with width / height.
		$this->sizes[ 'icon' ]          = new stdClass();
		$this->sizes[ 'icon' ]->width   = $config->get( 'media_icon_width' );
		$this->sizes[ 'icon' ]->height  = $config->get( 'media_icon_height' );
		$this->sizes[ 'icon' ]->quality = $config->get( 'media_icon_quality' );

		// @task: Initialize all the sizes.
		$this->getSizes();
	}

	/**
	 * This method would get a list of sizes and lookup for the item. If it doesn't exist, it will automatically create the default sizes specified by the theme.
	 *
	 */
	public function initDefaultSizes()
	{
		// @task: Let's test if the image size exists
		jimport( 'joomla.filesystem.file' );

		foreach( $this->sizes as $size => $props )
		{
			// We will not want to touch the "original size"
			if( $size != 'original' )
			{
				$prefix		= EBLOG_BLOG_IMAGE_PREFIX;

				if( $size == 'thumbnail' || $size == 'icon' )
				{
					$prefix = EBLOG_SYSTEM_VARIATION_PREFIX;
				}


				$fileName 	= $prefix . '_' . $size . '_' . $this->image;

				// Get the storage path.
				$storage	= rtrim( $this->storage , '/\\' ) . DIRECTORY_SEPARATOR . $fileName;

				// @task: Get the current image properties for this particular size.
				$params 	= $this->sizes[ $size ];

				if( !JFile::exists( $storage ) )
				{
					// @task: Test if the original image exists, otherwise we can't really resize this.
					if( !JFile::exists( $this->original ) )
					{
						return false;
					}

					// @task: Create the missing image
					if( !@$this->createImage( $params , $storage ) )
					{
						return false;
					}
				}
			}
		}
		return true;
	}

	/**
	 * Returns the list of sizes from the current theme or overrides.
	 *
	 * @access	public
	 * @param	null
	 * @return	Array	An array containing size information.
	 */
	public function getSizes()
	{
		jimport( 'joomla.filesystem.file' );

		$theme 		= new CodeThemes();

		// @task: Retrieve the list of defined sizes in the template overrides.
		$template 	= JFactory::getApplication()->getTemplate();
		$file 		= JPATH_ROOT . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'image.ini';
		$this->setSize( $this->readSize( $file ) , $template );

		// Set the active theme to the template's name first.
		$this->activeTheme	= $template;

		// If override exists, we just stick to theirs
		if (!JFile::exists($file)) {
			// @task: Retrieve the list of defined sizes in the current theme.
			if ($theme->getName() != 'default') {
				$file = $theme->getPath() . '/image.ini';

				if (JFile::exists($file)) {
					$this->setSize( $this->readSize( $file ) , $theme->getName() );
					$this->activeTheme	= $theme->getName();
				} else {
					// If the current theme doesn't have any image.ini, we assume to use the default one.
					$this->activeTheme	= 'default';
				}
			}
		}

		// Read the default items
		$file = dirname($theme->getPath()) . '/default/image.ini';
		$this->setSize($this->readSize($file), 'default');

		return $this->customSizes;
	}

	/**
	 * Sets sizes for the images.
	 *
	 * @access	public
	 * @param	array 	$sizes		An array of sizes
	 * @param 	string	$themeName	Size from specific themes. False if not from themes.
	 *
	 */
	private function setSize( $sizes , $themeName = false )
	{
		if( !$sizes )
		{
			return false;
		}

		foreach( $sizes as $size )
		{
			// @rule: Do not allow to override the core thumb and original sizes otherwise everything would mess up.
			if( $size->name != 'thumb' && $size->name != 'original' )
			{
				$this->customSizes[ $themeName ][ $size->name ]		= new stdClass();
				$this->customSizes[ $themeName ][ $size->name ]->width	= $size->width;
				$this->customSizes[ $themeName ][ $size->name ]->height	= $size->height;
				$this->customSizes[ $themeName ][ $size->name ]->resize = isset( $size->resize ) ? $size->resize : EBLOG_IMAGE_DEFAULT_RESIZE;
			}
		}
		return true;
	}

	/**
	 * Reads an ini file defining the image sizes.
	 *
	 * @access	public
	 * @param	string	$file	The path to the .ini file.
	 *
	 */
	private function readSize( $file )
	{
		jimport( 'joomla.filesystem.file' );

		if( !JFile::exists( $file ) )
		{
			return false;
		}

		$contents	= JFile::read( $file );

		$sizes 		= $this->json->decode( $contents );

		if( is_null( $sizes ) )
		{
			return false;
		}

		if( !is_array( $sizes ) )
		{
			$sizes 	= array( $sizes );
		}

		return $sizes;
	}

	/**
	 * Returns the source to the image for a particular size.
	 */
	public function getSource( $size , $html = false )
	{
		static $items = array();

		$isHTML		= $html ? '-html' : '';
		$index 		= $this->image . $size . $isHTML;

		if( !isset( $items[ $index ] ) )
		{
			if( !isset( $this->customSizes[ $this->activeTheme ] ) || !isset( $this->customSizes[ $this->activeTheme ][ $size ] ) )
			{
				// Let's test if the default has this. Otherwise, we should just return false.
				if( !isset( $this->customSizes[ 'default' ][ $size ] ) && $size != 'thumbnail' && $size != 'original' && $size != 'icon')
				{
					return false;
				}
			}

			// @task: For the original size, we don't really need to do anything since it should be there.
			if( $size == 'original' )
			{
				$items[ $index ]		= $this->uri . '/' . $this->image;
				return $items[ $index ];
			}

			$prefix		= EBLOG_BLOG_IMAGE_PREFIX;
			$imageObj	= '';

			if( isset( $this->customSizes[ $this->activeTheme ][ $size ] ) )
			{
				$imageObj	= $this->customSizes[ $this->activeTheme ];
			}
			else
			{
				// Let's test if the default has this. Otherwise, we should just return false.
				if( !isset( $this->customSizes[ 'default' ][ $size ] ) && $size != 'thumbnail' && $size != 'original' && $size != 'icon' )
				{
					return false;
				}
				else
				{
					$imageObj	= $this->customSizes[ 'default' ];
				}
			}


			// @task: Let's test if the image size exists
			jimport( 'joomla.filesystem.file' );

			// @task: File name should also have a prefix of the theme if there's a value for it.
			$fileName 	= $prefix . '_' . $this->activeTheme . '_' . $size . '_' . $this->image;

			if( $size == 'thumbnail' )
			{
				$prefix 	= EBLOG_SYSTEM_VARIATION_PREFIX;
				$imageObj	= $this->sizes[ 'thumbnail' ];

				$fileName	= $prefix . '_' . $size . '_' . $this->image;
			}

			// Get the storage path.
			$storage	= $this->storage . DIRECTORY_SEPARATOR . $fileName;

			if( !JFile::exists( $storage ) )
			{
				// @task: Get the current image properties for this particular size.
				$params = '';
				if( $size == 'thumbnail' )
				{
					$params 	= $imageObj;
				}
				else
				{
					$params 	= $imageObj[ $size ];
				}

				// @task: Test if the original image exists, otherwise we can't really resize this.
				if( !JFile::exists( $this->original ) )
				{
					return false;
				}

				// @task: Create the missing image
				if( !$this->createImage( $params , $storage ) )
				{
					return false;
				}
			}

			if( $html )
			{
				$items[ $index ]	= '<img src="' . $this->uri . '/' . $fileName . '" />';
			}
			else
			{
				$items[ $index ]	= $this->uri . '/' . $fileName;
			}
		}

		return $items[ $index ];
	}

	public function createImage( $params , $storeTo )
	{
		// @task: Retrieve the configs.
		$config			= EasyBlogHelper::getConfig();

		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'easysimpleimage.php' );

		// @rule: Generate a thumbnail for each uploaded images
		$image = new EasySimpleImage();
		$image->load( $this->original );

		$resizeType     = isset( $params->resize ) ? $params->resize : EBLOG_IMAGE_DEFAULT_RESIZE;
		$originalWidth  = $image->getWidth();
		$originalHeight = $image->getHeight();

		// If quality is not given, use default quality given in configuration
		if ( !isset( $params->quality) ) {
			$params->quality = $config->get( 'main_image_quality' );
		}

		// TODO: This should be done in the crop function itself
		if( $resizeType == 'crop' )
		{
			if( $originalWidth < $params->width || $originalHeight < $params->height )
			{
				$resizeType = 'fill';
			}
		}

		switch( $resizeType )
		{
			case 'crop':
				$image->crop( $params->width , $params->height );
				break;
			case 'fit':
				$image->resizeToFit( $params->width , $params->height );
				break;
			case 'within':
				$image->resizeWithin( $params->width , $params->height );
				break;
			case 'fill':
			default:
				$image->resizeToFill( $params->width , $params->height );
				break;
		}

		$image->save( $storeTo, $image->image_type, $params->quality );

		return true;
	}
}
