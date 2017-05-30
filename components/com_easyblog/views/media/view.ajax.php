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

	public function getMeta()
	{
		$foldersOnly = JRequest::getBool( 'foldersOnly' );

		if ($foldersOnly)
		{
			return $this->listFolders();
		}
		else
		{
			return $this->listItems();
		}
	}

	/**
	 * Silently update the value in the database when a request is made
	 */
	public function listFolders()
	{
		$ajax		= EasyBlogHelper::getHelper( 'Ajax' );

		// This is the relative path to the items that needs to be deleted.
		$source		= JRequest::getVar( 'path' );

		if( $source == DIRECTORY_SEPARATOR )
		{
			$source = '';
		}

		// This let's us know the type of folder we should lookup to
		$place		= JRequest::getString( 'place' );

		// @task: Create the media object.
		$media 		= new EasyBlogMediaManager();

		$absolutePath 	= EasyBlogMediaManager::getAbsolutePath( $source , $place );
		$absoluteURI	= EasyBlogMediaManager::getAbsoluteURI( $source , $place );

		$items 			= $media->getItem( $absolutePath , $absoluteURI , $source , false, $place , true )->toArray();

		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'json.php' );
		$json 			= new Services_JSON();

		$table 			= EasyBlogHelper::getTable( 'MediaManager' );
		$table->load( $absolutePath , 'folders' );
		$table->path 	= $absolutePath;
		$table->type 	= 'folders';
		$table->params	= $json->encode( $items );
		$table->store();

		return $ajax->success( $items );
	}

	/**
	 * Method is responsible to create a folder in the site.
	 *
	 * @access	public
	 * @param	null
	 */
	public function createFolder()
	{
		$ajax		= EasyBlogHelper::getHelper( 'Ajax' );

		// This is the relative path to the items that needs to be deleted.
		$path		= JRequest::getVar( 'path' );

		// This let's us know the type of folder we should lookup to
		$place		= JRequest::getString( 'place' );

		// @task: Create the media object.
		$media 		= new EasyBlogMediaManager();

		$absolutePath 	= EasyBlogMediaManager::getAbsolutePath( $path , $place );

		if( JFolder::exists( $absolutePath ) )
		{
			return $ajax->fail( JText::_( 'COM_EASYBLOG_FOLDER_EXISTS' ) );
		}

		// @task: Let's create the folder
		JFolder::create( $absolutePath );

		// @task: Let's copy a standard index.html to prevent any directory browsing here.
		$source 		= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'index.html';
		$destination	= $absolutePath . DIRECTORY_SEPARATOR .'index.html';
        JFile::copy( $source , $destination );


		// @task: Get the absolute URI to the destination item.
		$uri 	= EasyBlogMediaManager::getAbsoluteURI( $path , $place );

		// @task: Try to get the relative path of the "path" . Since the last fragment is always the folder that we're trying to create.
		$relative	= dirname( $path );

		$obj 	= $media->getItem( $absolutePath , $uri , $relative , false , $place )->toArray();

		$ajax->success( $obj );
	}

	/**
	 * Method is responsible to rename an item from the media manager.
	 *
	 * @access	public
	 * @param	null
	 */
	public function move()
	{
		$ajax	= EasyBlogHelper::getHelper( 'Ajax' );

		// This is the original location
		$sourceRelative			= JRequest::getVar( 'fromPath' );

		// This is where it should be renamed to
		$destinationRelative	= JRequest::getVar( 'toPath' );

		// This let's us know the type of folder we should lookup to
		$place					= JRequest::getString( 'place' );

		// Rename types
		// 1. Renaming Image
		//	  - Renaming image should also rename image variations
		//	  - Renaming image should also return variations.
		// 2. Moving from user folder to shared folder
		// 3. Really just rename folder.

		// @task: Let's find the exact path first as there could be 3 possibilities here.
		// 1. Shared folder
		// 2. Article folder
		// 3. User folder
		$source 		= EasyBlogMediaManager::getAbsolutePath( $sourceRelative , $place );
		$destination 	= EasyBlogMediaManager::getAbsolutePath( $destinationRelative , $place );

		// @task: Create the media object.
		$media 			= new EasyBlogMediaManager();

		// @task: Let's test if the source folder really exist.
		if( !$media->exists( $source ) )
		{
			return $ajax->fail( JText::_( 'COM_EASYBLOG_FILE_OR_FOLDER_DOES_NOT_EXIST' ) );
		}

		// @task: Try to rename the source to destination
		$state	= $media->rename( $source , $destination );

		// @task: If failed, let's just return a failed status
		if( $state !== true )
		{
			return $ajax->fail( $state );
		}

		// @task: Get the absolute URI to the destination item.
		$uri 	= EasyBlogMediaManager::getAbsoluteURI( $destinationRelative , $place );

		$obj 	= $media->getItem( $destination , $uri )->toArray();

		$ajax->success( $obj );
	}


	/**
	 * Deletes a file or folder from the media manager.
	 *
	 * @access	public
	 * @param	null
	 *
	 */
	public function delete()
	{
		$ajax	= EasyBlogHelper::getHelper( 'Ajax' );

		// This is the relative path to the items that needs to be deleted.
		$relativePath	= JRequest::getVar( 'path' );

		// This let's us know the type of folder we should lookup to
		$place			= JRequest::getString( 'place' );

		// @task: Create the media object.
		$media 			= new EasyBlogMediaManager();

		// @task: Let's find the exact path first as there could be 3 possibilities here.
		// 1. Shared folder
		// 2. User folder
		$absolutePath 		= EasyBlogMediaManager::getAbsolutePath( $relativePath , $place );

		// @task: Let's test if the source folder really exist.
		if( !$media->exists( $absolutePath ) )
		{
			return $ajax->fail( JText::_( 'COM_EASYBLOG_FILE_OR_FOLDER_DOES_NOT_EXIST' ) );
		}

		// @task: Try to rename the source to destination
		$state	= $media->delete( $absolutePath );

		// @task: If failed, let's just return a failed status
		if( $state !== true )
		{
			return $ajax->fail( $state );
		}

		// @task: Return the deleted path's relative path
		return $ajax->success( $relativePath );
	}

	/**
	 * Deletes an image variation based on the given title.
	 *
	 */
	public function deleteVariation()
	{
		$ajax	= EasyBlogHelper::getHelper( 'Ajax' );

		// This is the relative path to the items that needs to be deleted.
		$relativePath	= JRequest::getVar( 'fromPath' );

		// This let's us know the type of folder we should lookup to
		$place			= JRequest::getString( 'place' );

		// @task: Create the media object.
		$media 			= new EasyBlogMediaManager();

		// The variation name.
		$variationName 	= JRequest::getVar( 'name' );

		// @task: Let's find the exact path first as there could be 3 possibilities here.
		// 1. Shared folder
		// 2. User folder
		$absolutePath 		= EasyBlogMediaManager::getAbsolutePath( $relativePath , $place );

		// @task: Let's test if the source folder really exist.
		if( !$media->exists( $absolutePath ) )
		{
			return $ajax->fail( JText::_( 'COM_EASYBLOG_FILE_OR_FOLDER_DOES_NOT_EXIST' ) );
		}

		// @task: Try to rename the source to destination
		$state	= $media->deleteVariation( $absolutePath , $variationName );

		// @task: If failed, let's just return a failed status
		if( $state !== true )
		{
			return $ajax->fail( $state );
		}

		// @task: Return the deleted path's relative path
		return $ajax->success();
	}

	/**
	 * Creates a new image variation based on an existing image.
	 *
	 * @access	public
	 * @param	null
	 */
	public function createVariation()
	{
		$ajax	= EasyBlogHelper::getHelper( 'Ajax' );

		// The source of the original image
		$source = JRequest::getVar( 'path' );

		// The variation name.
		$variationName 	= JRequest::getVar( 'name' );

		// The variation's width.
		$width	= JRequest::getVar( 'width' );

		// The variation's height.
		$height	= JRequest::getVar( 'height' );

		// This let's us know the type of folder we should lookup to
		$place	= JRequest::getString( 'place' );


		// @task: Let's test if the source item really exist.
		if( empty( $variationName ) )
		{
			return $ajax->fail( JText::_( 'COM_EASYBLOG_MM_PLEASE_ENTER_VARIATION_NAME' ) );
		}

		// @task: Let's test if the source item really exist.
		if( empty( $width ) )
		{
			return $ajax->fail( JText::_( 'COM_EASYBLOG_MM_PLEASE_ENTER_VARIATION_WIDTH' ) );
		}

		// @task: Let's test if the source item really exist.
		if( empty( $height ) )
		{
			return $ajax->fail( JText::_( 'COM_EASYBLOG_MM_PLEASE_ENTER_VARIATION_HEIGHT' ) );
		}

		// @task: Let's find the exact path first as there could be 3 possibilities here.
		// 1. Shared folder
		// 2. User folder
		$absolutePath 		= EasyBlogMediaManager::getAbsolutePath( $source , $place );
		$absoluteURI		= EasyBlogMediaManager::getAbsoluteURI( $source , $place );

		// @task: Create the media object.
		$media 				= new EasyBlogMediaManager();

		// @task: Let's test if the source item really exist.
		if( !$media->exists( $absolutePath ) )
		{
			return $ajax->fail( JText::_( 'COM_EASYBLOG_FILE_OR_FOLDER_DOES_NOT_EXIST' ) );
		}

		// @task: Let's try to create the variation here.
		$variationObj	= $media->createVariation( $absolutePath , $absoluteURI , $variationName , $width , $height , EBLOG_VARIATION_USER_TYPE );

		if( is_string( $variationObj ) )
		{
			return $ajax->fail( $variationObj );
		}

		return $ajax->success( $variationObj );
	}

	/**
	 * List down files and folders from a given path.
	 *
	 * @access	public
	 * @param	null
	 *
	 */
	public function listItems()
	{
		$ajax		= EasyBlogHelper::getHelper( 'Ajax' );

		// force loading frontend language file.
		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );

		// This let's us know the type of folder we should lookup to
		$place		= JRequest::getString( 'place' );

		if( $place == 'jomsocial' )
		{
			return $this->listJomSocialItems();
		}

		if( $place == 'easysocial' )
		{
			return $this->listEasySocialItems();
		}

		if( $place == 'flickr' )
		{
			return $this->listFlickrItems();
		}

		// The source of the original image
		$source		= JRequest::getVar( 'path' );

		if( $source == DIRECTORY_SEPARATOR )
		{
			$source = '';
		}

		// Let's us know if we need to get the image variation.
		$variation	= JRequest::getVar( 'variation' ) == '1' ? true : false;

		// Detect if there's any filter
		$filters	= JRequest::getVar( 'filters' , '' );

		// @task: Create the media object.
		$media 		= new EasyBlogMediaManager();

		// @task: Let's find the exact path first as there could be 2 possibilities here.
		// 1. Shared folder
		// 2. User folder
		$absolutePath 		= EasyBlogMediaManager::getAbsolutePath( $source , $place );
		$absoluteURI		= EasyBlogMediaManager::getAbsoluteURI( $source , $place );

		// @task: Let's test if the source item really exist.
		if( !$media->exists( $absolutePath ) || !$absolutePath )
		{
			return $ajax->fail( JText::_( 'COM_EASYBLOG_FILE_OR_FOLDER_DOES_NOT_EXIST' ) );
		}

		$withFileSize	= $variation ? true : false;

		$items 		= $media->getItem( $absolutePath , $absoluteURI , $source , $variation, $place , false , false , $withFileSize )->toArray();

		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'json.php' );
		$json 			= new Services_JSON();

		// Get a list of paginated items here.
		$paginatedItems = $media->getItem( $absolutePath , $absoluteURI , $source , $variation, $place , false , true , $withFileSize )->toArray();


		$table 			= EasyBlogHelper::getTable( 'MediaManager' );
		$table->load( $absolutePath , 'files' );

		$table->set( 'path'		, $absolutePath );
		$table->set( 'type'		, 'files' );
		$table->set( 'params' 	, $json->encode( $paginatedItems ) );
		$table->store();

		return $ajax->success( $items );
	}

	/**
	 * Returns a list of JomSocial albums and photos
	 *
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function listJomSocialItems()
	{
		$ajax		= EasyBlogHelper::getHelper( 'Ajax' );
		$cfg		= EasyBlogHelper::getConfig();
		$my			= JFactory::getUser();
		$profile	= EasyBlogHelper::getTable( 'Profile' );
		$profile->load( $my->id );

		// Show an error message if someone tries to force their way in.
		if( !$cfg->get( 'integrations_jomsocial_album') )
		{
			return $ajax->fail( JText::_( 'COM_EASYBLOG_THIS_FEATURED_IS_DISABLED' ) );
		}

		// @rule: Test if the user is really logged in or not.
		if( $my->id <= 0 )
		{
			return $ajax->fail( JText::_( 'COM_EASYBLOG_THIS_FEATURED_IS_DISABLED' ) );
		}

		$path	= JRequest::getVar( 'path' );


		// If fromPath is passed in the query, we know that the user is querying an item instead.
		if( !empty( $path ) && $path != DIRECTORY_SEPARATOR )
		{
			$path 		= explode( DIRECTORY_SEPARATOR , $path );

			if( count( $path ) == 3 )
			{
				$path 		= str_ireplace( array( '/' , '\\' ) , '' , $path[ 2 ] );
			}

			$media		= new EasyBlogMediaManager( EBLOG_MEDIA_SOURCE_JOMSOCIAL );
			$item		= $media->getItem( $path , '' )->toArray();

			return $ajax->success( $item );
		}

		$media		= new EasyBlogMediaManager( EBLOG_MEDIA_SOURCE_JOMSOCIAL );
		$items		= $media->getItems( '' , '' )->toArray();

		return $ajax->success( $items );
	}

	/**
	 * Returns a list of JomSocial albums and photos
	 *
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function listEasySocialItems()
	{
		$ajax		= EasyBlogHelper::getHelper( 'Ajax' );
		$cfg		= EasyBlogHelper::getConfig();
		$my			= JFactory::getUser();
		$profile	= EasyBlogHelper::getTable( 'Profile' );
		$profile->load( $my->id );

		// Show an error message if someone tries to force their way in.
		if( !$cfg->get( 'integrations_easysocial_album') )
		{
			return $ajax->fail( JText::_( 'COM_EASYBLOG_THIS_FEATURED_IS_DISABLED' ) );
		}

		// @rule: Test if the user is really logged in or not.
		if( $my->id <= 0 )
		{
			return $ajax->fail( JText::_( 'COM_EASYBLOG_THIS_FEATURED_IS_DISABLED' ) );
		}

		$path	= JRequest::getVar( 'path' );

		// If fromPath is passed in the query, we know that the user is querying an item instead.
		if( !empty( $path ) && $path != DIRECTORY_SEPARATOR )
		{
			$path 		= explode( DIRECTORY_SEPARATOR , $path );

			if( count( $path ) == 3 )
			{
				$path 		= str_ireplace( array( '/' , '\\' ) , '' , $path[ 2 ] );
			}

			$media		= new EasyBlogMediaManager( EBLOG_MEDIA_SOURCE_EASYSOCIAL );
			$item		= $media->getItem( $path , '' )->toArray();

			return $ajax->success( $item );
		}

		$media		= new EasyBlogMediaManager( EBLOG_MEDIA_SOURCE_EASYSOCIAL );
		$items		= $media->getItems( '' , '' )->toArray();

		return $ajax->success( $items );
	}

	/**
	 * Returns a list of JomSocial albums and photos
	 *
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function listFlickrItems()
	{
		$ajax		= EasyBlogHelper::getHelper( 'Ajax' );
		$cfg		= EasyBlogHelper::getConfig();
		$my			= JFactory::getUser();
		$profile	= EasyBlogHelper::getTable( 'Profile' );
		$profile->load( $my->id );

		// Show an error message if someone tries to force their way in.
		if( !$cfg->get( 'layout_media_flickr') )
		{
			return $ajax->fail( JText::_( 'COM_EASYBLOG_THIS_FEATURED_IS_DISABLED' ) );
		}

		// @rule: Test if the user is really logged in or not.
		if( $my->id <= 0 )
		{
			return $ajax->fail( JText::_( 'COM_EASYBLOG_THIS_FEATURED_IS_DISABLED' ) );
		}

		// @rule: Test if the user is already associated with Flickr
		$oauth		= EasyBlogHelper::getTable( 'Oauth' );
		$associated	= $oauth->loadByUser( $my->id , EBLOG_OAUTH_FLICKR );

		if( !$associated )
		{
			return $ajax->fail( JText::_( 'Please associate your account with Flickr first.' ) );
		}

		$path	= str_ireplace( array( '/' , '\\' ) , '' , JRequest::getVar( 'path' ) );

		// If fromPath is passed in the query, we know that the user is querying an item instead.
		if( !empty( $path ) )
		{
			$media		= new EasyBlogMediaManager( EBLOG_MEDIA_SOURCE_FLICKR );
			$item		= $media->getItem( $path , '' )->toArray();

			return $ajax->success( $item );
		}

		$media	= new EasyBlogMediaManager( EBLOG_MEDIA_SOURCE_FLICKR );
		$items	= $media->getItems( '' , '' )->toArray();


		return $ajax->success( $items );
	}


}
