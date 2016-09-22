<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );

require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'mediamanager.php' );
require_once( EBLOG_ROOT . DIRECTORY_SEPARATOR . 'views.php' );

class EasyBlogViewMedia extends EasyBlogView
{
	public function getIconImage()
	{
		// This let's us know the type of folder we should lookup to
		$place		= JRequest::getString( 'place' );

		// Flat list can only be searched from the root.
		$source		= JRequest::getVar( 'path' );

		// @task: Create the media object.
		$media 		= new EasyBlogMediaManager();

		// @task: Let's find the exact path first as there could be 3 possibilities here.
		// 1. Shared folder
		// 2. User folder
		$absolutePath 	= EasyBlogMediaManager::getAbsolutePath( $source , $place );
		$absoluteURI	= EasyBlogMediaManager::getAbsoluteURI( $source , $place );

		// @task: Test if the thumbnail exists in the system.
		$basePath 		= dirname( $absolutePath );
		$fileName 		= basename( $absolutePath );
		$iconFileName	= EBLOG_SYSTEM_VARIATION_PREFIX . '_icon_' . $fileName;
		$iconFilePath	= $basePath . DIRECTORY_SEPARATOR . $iconFileName;


		// @task: Create the thumbnail
		if( !JFile::exists( $iconFilePath ) )
		{
			$media->createThumbnail( $fileName , $absolutePath , $iconFilePath );
		}

		$info			= getimagesize( $iconFilePath );

		$this->output( $info[ 'mime' ] , $iconFilePath );
	}

	public function output( $mime , $path )
	{
		$jCfg = JFactory::getConfig();

		if( $jCfg->get( 'debug') )
		{
				if( ob_get_length() !== false )
			{
				while (@ ob_end_clean());
				if( function_exists( 'ob_clean' ) )
				{
					@ob_clean();
				}
			}
		}

		header('Content-Type: ' . $mime );
		header('Content-Length: ' . filesize($path));

		flush();
		readfile( $path );
		exit;
	}
}
