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

class EasyBlogMediaManagerFolder extends EasyBlogMediaManagerItem
{
	public $type 		= 'folder';
	private $contents 	= array();
	private $includeVariation	= null;
	private $place 		= null;
	private $isDS		= false;

	public function __construct( $file , $baseURI , $relativePath = '' , $includeVariation = false , $flatList = false , $path , $isUpload = false , $foldersOnly = false , $place = '' , $paginated = false )
	{
		parent::__construct( $file , $baseURI , $relativePath );

		$this->place 	= $place;

		// If the relativePath is empty, we would assume that the user is currently on the root folder.
		if( empty( $this->relativePath ) )
		{
			$this->baseURI		= dirname( $baseURI );
			$this->relativePath	= DIRECTORY_SEPARATOR;
		}

		if( $relativePath == DIRECTORY_SEPARATOR )
		{
			$this->isDS			= true;
			$this->relativePath	= parent::getRelativePath();
		}

		$this->includeVariation	= $includeVariation;
		$this->contents			= $this->getContents( $foldersOnly , $paginated );
	}

	public function getRelativePath()
	{
		if( $this->relativePath == DIRECTORY_SEPARATOR || $this->isDS )
		{
			return $this->relativePath;
		}

		return rtrim( $this->relativePath , '/\\' ) . DIRECTORY_SEPARATOR . $this->getTitle();
	}

	public function getURI()
	{
		return $this->baseURI . '/' .$this->getTitle();
	}

	public function getContents( $foldersOnly = false , $paginated = false )
	{
		$folderName		= $this->getTitle();
		$uri 			= $this->baseURI . '/' . ltrim( $folderName , '/\\');

		$relativePath	= $this->getRelativePath();

		$media 			= new EasyBlogMediaManager();

		$contents		= $media->getItems( $this->file , $uri , $relativePath , $this->includeVariation , false , array( 'index.html' , '.svn', 'CVS', '.DS_Store', '__MACOSX') , $foldersOnly , $this->place , $paginated )->toArray();

		return $contents;
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
		return false;
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
		return false;
	}

	public function getMime()
	{
		return false;
	}

	/**
	 * Get a list of variations for the particular image item.
	 *
	 * @access	public
	 * @param	null
	 * @return 	Array	An array of variation objects.
	 */
	public function getVariations()
	{
		return false;
	}

	public function getSize()
	{
		$handle		= opendir( $this->file );
		$size		= 0;

		if( !$handle )
		{
			return 0;
		}

		while( $file = readdir( $handle ) )
		{
			if( $file != '.' && $file != '..' && !is_dir( $this->file . DIRECTORY_SEPARATOR . $file ) )
			{
				$size	+= filesize( $this->file . DIRECTORY_SEPARATOR . $file );
			}
		}
		closedir( $handle );

		return $this->formatSize( $size );
	}

	public function getDateModified() {

		return filemtime( $this->file );
	}

	public function getCreationDate()
	{
		$date	= EasyBlogHelper::getDate( filemtime( $this->file ) );

		return $date->toMySQL( true );
	}

	public function getTotalItems()
	{
		return count( $this->contents );
	}

	public function inject( &$obj )
	{
		// @task: Get the total items (Only applicable for folder type)
		$obj->totalitems 	= $this->getTotalItems();
		$obj->contents 		= $this->contents;
		$obj->icon 			= new stdClass();
		$obj->icon->url		= rtrim( JURI::root() , '/' ) . '/components/com_easyblog/assets/images/gallery.png';
	}

	/**
	 * Override parent's delete implementation since this is a folder.
	 *
	 * @access	public
	 * @param	string	$path	The path to the folder.
	 */
	public function delete( $path )
	{
		// Delete the folder.
		$state	= JFolder::delete( $path );

		return $state;
	}


	/**
	 * Rename a specific item.
	 */
	public function rename( $source , $destination )
	{
		jimport( 'joomla.filesystem.folder' );

		return JFolder::move( $source, $destination );
	}
}
