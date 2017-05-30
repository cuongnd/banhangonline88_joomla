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
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'string.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'date.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'comment.php' );

class EasyBlogViewMicroBlog extends EasyBlogView
{
	public function save()
	{
		$ajax 	= EasyBlogHelper::getHelper( 'Ajax' );
		$acl 	= EasyBlogACLHelper::getRuleSet();
		$my 	= JFactory::getUser();
		$type 	= JRequest::getCmd( 'type' );
		$config	= EasyBlogHelper::getConfig();

		// @rule: Let's test if the user is a valid user.
		if( empty( $acl->rules->add_entry ) || $my->id == 0 )
		{
			return $ajax->fail( JText::_( 'COM_EASYBLOG_NO_PERMISSION_TO_CREATE_BLOG' ) );
		}

		// @rule: Test if microblogging is allowed
		if( !$config->get( 'main_microblog' ) )
		{
			return $ajax->fail( JText::_( 'COM_EASYBLOG_NO_PERMISSION_TO_CREATE_BLOG' ) );
		}

		if( !$type )
		{
			return $ajax->fail( JText::_( 'COM_EASYBLOG_SPECIFY_POST_TYPE' ) );
		}

		$microblog		= $this->getMicroBlogObject( $type );

		if( $microblog === false )
		{
			return $ajax->fail( JText::_( 'COM_EASYBLOG_INVALID_POST_TYPE' ) );
		}

		// @rule: Type validations are done here
		$state			= $microblog->validate();

		if( $state !== true )
		{
			return $ajax->fail( $state );
		}

		$blog				= EasyBlogHelper::getTable( 'Blog' );

		// @task: If user does not have privilege to store, we need to push this to the drafts
		if( empty( $acl->rules->publish_entry ) )
		{
			$blog 						= EasyBlogHelper::getTable( 'Draft' );
			$blog->pending_approval		= 1;
			$blog->ispending			= 1;
		}

		// @task: Check if category is set
		$category 			= JRequest::getVar( 'category' , 1 );
		$blog->category_id	= $category;

		// @task: Check if this user is allowed to publish blog
		$blog->created 		= EasyBlogHelper::getDate()->toMySQL();
		$blog->modified 	= $blog->created;
		$blog->publish_up	= $blog->created;
		$blog->published	= empty( $acl->rules->publish_entry ) ? false: true;
		$blog->created_by	= $my->id;
		$blog->frontpage 	= (empty($acl->rules->contribute_frontpage)) ? '0' : true;
		$blog->isnew		= $blog->published ? true : false;

		// @rule: Check if user has permissions to set the privacy
		$private 			= JRequest::getInt( 'privacy' , 0 );
		$blog->private		= empty( $acl->rules->enable_privacy ) ? 0 : $private;

		// @rule: We do not allow quick post to submit to team blogs
		$blog->issitewide	= true;

		// @rule: Allow microblog client to manipulate the blog object
		$microblog->bind( $blog );

		// Try to store the blog
		if( !$blog->store() )
		{
			return $ajax->fail( $blog->getError() );
		}

		if( $blog->published )
		{
			$tags 	= JRequest::getVar( 'tags' , '' );

			if( !empty( $tags ) )
			{
				$blog->processTags( $tags );
			}

			// @rule: If blog is published, perform the autopostings
			$sites 	= JRequest::getVar( 'autopost' );
			$blog->autopost( $sites );

			$blog->notify( $blog->ispending );
		}
		$message 	= $microblog->getSuccessMessage();

		if( !$blog->published )
		{
			$message 	= JText::_( 'COM_EASYBLOG_DASHBOARD_QUICKPOST_SAVED_REQUIRE_MODERATION' );
		}

		return $ajax->success( $message );
	}

	private function getMicroBlogObject( $type )
	{
		$path		= EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'microblog' . DIRECTORY_SEPARATOR . strtolower( $type ) . '.php';

		if( !JFile::exists( $path ) )
		{
			return false;
		}

		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'microblog' . DIRECTORY_SEPARATOR . strtolower( $type ) . '.php' );

		$className	= 'EasyBlogMicroBlog' . ucfirst( $type );

		if( !class_exists( $className ) )
		{
			return false;
		}

		$obj		= new $className;

		return $obj;
	}

	/**
	 * Responsible to retrieve video html codes when given a video URL
	 *
	 * @access	public
	 * @param	null
	 */
	public function getVideo()
	{
		$url 		= JRequest::getVar( 'url' );
		$ajax 		= EasyBlogHelper::getHelper( 'Ajax' );

		$embedCodes	= EasyBlogHelper::getHelper('Videos')->processVideoLink( $url , 450 , 400 );

		$ajax->success( $embedCodes );
	}

	/**
	 * Handles photo uploads via the microblogging page.
	 *
	 * @access	public
	 * @param	null
	 **/
	public function uploadPhoto()
	{
		$my 		= JFactory::getUser();
		$config 	= EasyBlogHelper::getConfig();

		if( !$my->id )
		{
			return $this->outputJSON(
				array(
						'type'		=> 'error',
						'message'	=> JText::_( 'You need to be logged in first' )
				)
			);
		}

		$file 				= JRequest::getVar( 'photo-source', '', 'files', 'array' );

		if( !isset( $file['tmp_name'] ) )
		{
			return $this->outputJSON(
				array(
						'type'		=> 'error',
						'message'	=> JText::_( 'There is an error when uploading the image to the server. Perhaps the temporary folder <strong>upload_tmp_path</strong> was not configured correctly.' )
				)
			);
		}

		require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'image.php' );

		// @rule: Photos should be stored in the user's home folder by default.
		$imagePath			= str_ireplace( array( "/" , "\\" ) , DIRECTORY_SEPARATOR , rtrim( $config->get('main_image_path') , '/') );
		$userUploadPath    	= JPATH_ROOT . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $imagePath . DIRECTORY_SEPARATOR . $my->id);
		$storageFolder		= JPath::clean( $userUploadPath );

		// @rule: Get the image URI
		$imageURI			= rtrim( str_ireplace( '\\' , '/' , $config->get( 'main_image_path') ) , '/' ) . '/' . $my->id;
		$imageURI			= rtrim( JURI::root() , '/' ) . '/' . $imageURI;

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name']	= JFile::makeSafe($file['name']);

		// After making the filename safe, and the first character begins with . , we need to rename this file. Perhaps it's a unicode character
		$file['name']	= trim( $file['name'] );
		$filename		= strtolower( $file['name'] );

		if(strpos( $filename , '.' ) === false )
		{
		    $filename	= EasyBlogHelper::getDate()->toFormat( "%Y%m%d-%H%M%S" ) . '.' . $filename;
		}
		else if( strpos( $filename , '.' ) == 0 )
		{
			$filename	= EasyBlogHelper::getDate()->toFormat( "%Y%m%d-%H%M%S" ) . $filename;
		}

		// remove the spacing in the filename.
		$filename 		= str_ireplace(' ', '-', $filename);
		$storagePath 	= JPath::clean( $storageFolder . DIRECTORY_SEPARATOR . $filename );

// 		// @task: try to rename the file if another image with the same name exists
// 		if( JFile::exists( $storagePath ) )
// 		{
// 			$i	= 1;
// 			while( JFile::exists( $storagePath ) )
// 			{
// 				$tmpName	= $i . '_' . EasyBlogHelper::getDate()->toFormat( "%Y%m%d-%H%M%S" ) . '_' . $filename;
// 				$storagePath	= JPath::clean( $storageFolder . DIRECTORY_SEPARATOR . $tmpName );
// 				$i++;
// 			}
// 			$filename	= $tmpName;
// 		}

		$allowed		= EasyImageHelper::canUploadFile( $file );

		if( $allowed !== true )
		{
			return $this->outputJSON(
				array(
						'type'		=> 'error',
						'message'	=> $allowed
				)
			);
		}

		// @rule: Pass to EasyBlogImageHelper to upload the image
		// $result		= EasyImageHelper::upload( $storageFolder , $filename , $file , $imageURI , $storagePath );

// 		// @task: Ensure that images goes through the same resizing format when uploading via media manager.
		$result = new stdClass();
		$result->message    = JText::_( 'COM_EASYBLOG_IMAGE_MANAGER_UPLOAD_ERROR' );
		$result->item       = '';

		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'mediamanager.php' );
		$media 				= new EasyBlogMediaManager();
		$uploaded 			= $media->upload( $storageFolder , $imageURI , $file , '/', 'user' );

		if( $uploaded !== false )
		{
			$result->message    = JText::_( 'COM_EASYBLOG_IMAGE_MANAGER_UPLOAD_SUCCESS' );
			$result->item       = $uploaded;
		}
		else
		{
			// failed.
			$result->item->url  = '';
		}


		return $this->outputJSON(
			array(
					'type'		=> 'success',
					'message'	=> $result->message,
					'uri'		=> $result->item->url
			)
		);
	}
}
