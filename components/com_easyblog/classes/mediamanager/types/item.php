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

class EasyBlogMediaManagerItem
{
	public $file			= null;
	public $baseURI			= null;
	public $relativePath	= null;

	public function __construct( $file , $baseURI , $relativePath = '' )
	{
		$this->file 		= $file;
		$this->baseURI		= $baseURI;
		$this->relativePath	= $relativePath;
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
		$mimeType	= false;

		if( is_file( $this->file ) )
		{
			if( function_exists('finfo_open') )
			{
				$finfo = finfo_open( FILEINFO_MIME_TYPE );
				$mimeType = finfo_file( $finfo, $this->file );
				finfo_close($finfo);
			}
			if( !$mimeType && function_exists('mime_content_type') )
			{
				$mimeType = mime_content_type( $this->file );
			}
// 			if( !$mimeType && function_exists('exec') && function_exists('escapeshellarg') )
// 			{
// 				if( $cmdOut = trim(exec('file --mime-type -b ' . escapeshellarg( $this->file ))) )
// 				{
// 					$mimeType = $cmdOut;
// 				}
// 			}
			if( !$mimeType && function_exists('pathinfo') && $pathinfo = pathinfo($this->file) )
			{
				$imagetypes = array('gif', 'jpeg', 'jpg', 'png', 'swf', 'psd', 'bmp', 'tiff', 'tif', 'jpc', 'jp2', 'jpx', 'jb2', 'swc', 'iff', 'wbmp', 'xbm', 'ico' );

				if( in_array($pathinfo['extension'], $imagetypes) && getimagesize( $this->file ) )
				{
					$imageinfo = getimagesize( $this->file );
					$mimeType = $imageinfo['mime'];
				}
			}
			if( !$mimeType )
			{
				$mimeType = 'application/octet-stream';
			}
		}

		return $mimeType;
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
		// @TODO: Calculate children items.
		return filesize( $this->file );
	}

	public function getDateModified() {

		return filemtime( $this->file );
	}

	public function getDateCreated()
	{
		$date	= EasyBlogHelper::getDate( filemtime( $this->file ) );
		$format	= JText::_( 'DATE_FORMAT_LC1' );

		if( method_exists( $date , 'format' ) )
		{
			return $date->format( $format );
		}

		return $date->toFormat( $format );
	}

	public function getTotalItems()
	{
		return count( $this->contents );
	}

	public function inject( &$obj )
	{
		// Implemented on client
	}

	public function getRelativePath()
	{
		return rtrim( $this->relativePath , '/\\' ) . DIRECTORY_SEPARATOR . $this->getTitle();
	}

	/**
	 * Given a size, try to format it according to the appropriate abbrev
	 *
	 * @access	protected
	 */
	protected function formatSize($size)
	{
		$units = array(' B', ' KB', ' MB', ' GB', ' TB');
		for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
		return round($size, 2).$units[$i];
	}

	/**
	 * Given the absolute path to an item, delete it.
	 *
	 * @access	public
	 * @param	string	$path	The full path to the item.
	 * @return	boolean			True on success, false otherwise.
	 */
	public function delete( $path )
	{
		return JFile::delete( $path );
	}

	public function getType()
	{
		return $this->type;
	}

	public function includeInMedia()
	{
		return true;
	}

	/**
	 * Default way of upload implementation here. If child items needs it's own method of implementing upload,
	 * this method should then be overriden.
	 *
	 *
	 */
	public static function upload( $storagePath , $fileName , $fileItem )
	{
		$source			= $fileItem[ 'tmp_name' ];
		$destination	= $storagePath . DIRECTORY_SEPARATOR . $fileName;

		// Let's try to copy the item now
		jimport( 'joomla.filesystem.file' );

		if( !JFile::copy( $source , $destination ) )
		{
			//This was the old error code - EBLOG_MEDIA_PERMISSION_ERROR
			return JText::_( 'COM_EASYBLOG_IMAGE_MANAGER_UPLOAD_ERROR' );
		}

		return true;
	}

	/**
	 * Rename a specific item.
	 */
	public function rename( $source , $destination )
	{
		jimport( 'joomla.filesystem.file' );

		return JFile::move( $source, $destination );
	}

	/**
	 * Returns an icon map based on a given extension.
	 *
	 * @access	protected
	 * @param	string	$extension	The extension type.
	 * @return	string				A html class representation
	 */
	protected function getIconMap( $extension )
	{
		$mimeTypes = array(
			'txt'	=> 'text',
			'htm'	=> 'text',
			'html'	=> 'text',
			'php'	=> 'text',
			'css'	=> 'text',
			'js'	=> 'text',
			'json'	=> 'text',
			'xml'	=> 'text',
			'rtf'	=> 'text',

			// archives
			'zip' => 'archive',
			'rar' => 'archive',
			'7z' => 'archive',
			'gz' => 'archive',
			'tar' => 'archive',

			// adobe
			'pdf' => 'pdf',

			// ms office
			'doc' => 'document',
			'docx'=> 'document',
			'odt'=> 'document',

			'xls' => 'spreadsheet',
			'xlsx'=> 'spreadsheet',
			'ods' => 'spreadsheet',

			'ppt' => 'presentation',
			'pptx' => 'presentation',
			'odp' => 'presentation'
		);

		//
		if( array_key_exists( $extension , $mimeTypes ) )
		{
			return $mimeTypes[ $extension ];
		}


		// If this is an unknown type, let's just return the generic types.
		return 'file';
	}
}
