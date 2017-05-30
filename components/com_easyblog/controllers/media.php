<?php
/**
 * @version		$Id: file.php 14401 2010-01-26 14:10:00Z louis $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

require_once( EBLOG_ROOT . DIRECTORY_SEPARATOR . 'controller.php' );

require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'mediamanager.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'image.php' );
require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'easysimpleimage.php' );

class EasyBlogControllerMedia extends EasyBlogParentController
{
	public function upload()
	{
		$app 		= JFactory::getApplication();
		$my			= JFactory::getUser();
		$cfg		= EasyBlogHelper::getConfig();
		$acl 		= EasyBlogACLHelper::getRuleSet();

		// @rule: Only allowed users are allowed to upload images.
		if( $my->id == 0 || empty( $acl->rules->upload_image ) )
		{
			$sessionid	= JRequest::getVar('sessionid');
			if ($sessionid)
			{
				$session	= JTable::getInstance( 'Session' );
				$session->load($sessionid);

				if (!$session->userid)
				{
					$this->output( $this->getMessageObj( EBLOG_MEDIA_SECURITY_ERROR , JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ) ) );
				}
				$my	= JFactory::getUser($session->userid);
			}
			else
			{
				$this->output( $this->getMessageObj( EBLOG_MEDIA_SECURITY_ERROR , JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ) ) );
			}
		}

		// Let's get the path for the current request.
		$file	= JRequest::getVar( 'file' , '' , 'FILES' , 'array' );
		$place 	= JRequest::getVar( 'place' );

		// The user might be from a subfolder?
		$source	= urldecode(JRequest::getVar( 'path' , '/' ));

		// @task: Let's find the exact path first as there could be 3 possibilities here.
		// 1. Shared folder
		// 2. User folder
		$absolutePath 		= EasyBlogMediaManager::getAbsolutePath( $source , $place );
		$absoluteURI		= EasyBlogMediaManager::getAbsoluteURI( $source , $place );

		// @TODO: Test if user is allowed to upload this image
		$message 		= $this->getMessageObj();
		$allowed		= EasyImageHelper::canUploadFile( $file , $message );

		if( $allowed !== true )
		{
			return $this->output( $message );
		}

		$media 				= new EasyBlogMediaManager();
		$result 			= $media->upload( $absolutePath , $absoluteURI , $file , $source , $place );

		// This should be an error if the $result is not an MMIM object.
		if( !is_object( $result ) )
		{
			$message	= $this->getMessageObj( '404' , $result );
		}
		else
		{
			$message 	= $this->getMessageObj( EBLOG_MEDIA_UPLOAD_SUCCESS , JText::_( 'COM_EASYBLOG_IMAGE_MANAGER_UPLOAD_SUCCESS' ) , $result );
		}

		return $this->output( $message );
	}

	private function getMessageObj( $code = '' , $message = '', $item = false )
	{
		$obj			= new stdClass();
		$obj->code		= $code;
		$obj->message	= $message;

		if( $item )
		{
			$obj->item	= $item;
		}

		return $obj;
	}

	private function output( $response )
	{
		include_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'json.php' );
		$json	= new Services_JSON();
		echo $json->encode( $response );
		exit;
	}

}
