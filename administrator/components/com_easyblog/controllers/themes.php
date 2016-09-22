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

jimport('joomla.application.component.controller');

class EasyBlogControllerThemes extends EasyBlogController
{
	function __construct()
	{
		parent::__construct();

		$this->registerTask( 'apply' , 'save' );
		$this->registerTask( 'addTheme' , 'addTheme' );
		$this->registerTask( 'removeSetting' , 'removeSetting' );

	}

	public function removeSetting()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$config = EasyBlogHelper::getConfig();
		$element	= JRequest::getVar( 'element' );

		// Get the names to delete
		$names	= JRequest::getVar( 'cid' , '' , 'POST' );

		$message	= '';
		$type		= 'message';

		if( empty( $names ) )
		{
			$this->setRedirect( 'index.php?option=com_easyblog&view=theme&element=' . $element  , JText::_( 'COM_EASYBLOG_BLOGS_INVALID_ID' ) , 'error' );
		}

		$this->updateBlogImage( $element, $names );
	}

	private function deleteImages( $folders )
	{
		jimport( 'joomla.filesystem.folder' );
		jimport( 'joomla.filesystem.file' );

		$count = 0;

		foreach( $folders as $folder )
		{
			if( !JFolder::exists( $folder ) )
			{
				continue;
			}

			$filter 	= EBLOG_BLOG_IMAGE_PREFIX . '*';
			$images 	= JFolder::files( $folder , $filter , true , true );

			foreach( $images as $image )
			{
				JFile::delete( $image );
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Allow admin to remove blog images
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function cleanImages()
	{
		$app 			= JFactory::getApplication();
		$config 		= EasyBlogHelper::getConfig();
		$element 		= JRequest::getVar( 'element' );
		$mainFolder		= JPATH_ROOT . DIRECTORY_SEPARATOR . trim( $config->get( 'main_image_path' ) , '/\\');
		$shareFolder	= JPATH_ROOT . DIRECTORY_SEPARATOR . trim( $config->get( 'main_shared_path' ) , '/\\');

		$mainFolders	= JFolder::folders( $mainFolder , '' , false , true , array());

		// Delete all blog images from main folders.
		$count 	= $this->deleteImages( $mainFolders );

		// Delete all blog images from shared folders.
		$count 	= $count + $this->deleteImages( array($shareFolder) );


		$msg 	= JText::sprintf( 'COM_EASYBLOG_THEME_SUCCESSFULLY_CLEANED' , $count );

		if( $count == 0 )
		{
			$msg    = JText::_( 'COM_EASYBLOG_THEME_NO_BLOG_IMAGES_FOUND' );
		}

		$this->setRedirect( 'index.php?option=com_easyblog&view=theme&element=' . $element , $msg , 'message' );
	}

	/**
	 * Make the provided theme a default theme for EasyBlog
	 */
	public function makeDefault()
	{
		JRequest::checkToken( 'request' ) or die( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'theme' );

		$msg    	= '';

		$theme 	= JRequest::getWord( 'element' );


		if( !empty($theme) )
		{
			$config	= EasyBlogHelper::getConfig();

			$config->set( 'layout_theme' , $theme );

			$table	= EasyBlogHelper::getTable( 'Configs' , 'Table' );
			$table->load( 'config' );

			$table->params	= $config->toString( 'INI' );
			$table->store();

			// Clear the component's cache
			$cache = JFactory::getCache('com_easyblog');
			$cache->clean();

			$msg    = JText::sprintf( 'COM_EASYBLOG_THEME_SET_AS_DEFAULT' , $theme );
		}


		$this->setRedirect( 'index.php?option=com_easyblog&view=themes' , $msg , 'message' );
	}



	public function save()
	{
		JRequest::checkToken() or die( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'theme' );

		$element 	= JRequest::getVar( 'element' );
		$params 	= JRequest::getVar( 'params' );
		$obj 		= EasyBlogHelper::getRegistry( '' );

		foreach( $params as $key => $value )
		{
			$obj->set( $key , $value );
		}

		if( EasyBlogHelper::getJoomlaVersion() >= '3.0' )
		{
			// Update creation source.
			$creation 	= JRequest::getVar( 'creation_source' );
			$obj->set( 'creation_source' , $creation );

			// Update tags colour scheme.
			$tagColour 	= JRequest::getVar( 'tags_color_scheme' );
			$obj->set( 'tags_color_scheme' , $tagColour );
		}

		$this->updateBlogImage( $element );

		// Store this value in the configs table.
		$table 	= EasyBlogHelper::getTable( 'Configs' );
		$table->load( $element );
		$table->set( 'name'		, $element );
		$table->set( 'params'	, $obj->toString( 'INI' ) );
		$table->store( $element );

		$url 		= $this->getTask() == 'apply' ? 'index.php?option=com_easyblog&view=theme&element=' . $element : 'index.php?option=com_easyblog&view=themes';

		$this->setRedirect( $url , JText::_( 'COM_EASYBLOG_THEME_SAVED_SUCCESSFULLY' ) , 'message' );
	}

	public function updateBlogImage( $element, $exclude = null )
	{
		// Get the table values
		$themePosition 	= JRequest::getVar( 'themePosition' );
		$themeWidth 	= JRequest::getVar( 'themeWidth' );
		$themeHeight	= JRequest::getVar( 'themeHeight' );
		$themeMethod 	= JRequest::getVar( 'themeMethod' );

		$themes = array();

		for( $i=0; $i<count($themePosition); $i++ )
		{
			$obj = new stdClass();
			$obj->name = $themePosition[$i];
			$obj->width = $themeWidth[$i];
			$obj->height = $themeHeight[$i];
			$obj->resize = $themeMethod[$i];

			$themes[] = $obj;
		}

		// Let's update the blog image.
		$blogImageFile	=	EBLOG_THEMES . DIRECTORY_SEPARATOR . $element . DIRECTORY_SEPARATOR . 'image.ini';

		jimport( 'joomla.filesystem.file' );

		if( !JFile::exists( $blogImageFile ) )
		{
			return false;
		}

		$contents 	= JFile::read( $blogImageFile );

		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'json.php' );

		$json 		= new Services_JSON();
		$types		= $json->decode( $contents );
		$modified	= false;

		foreach( $types as $i => $type )
		{
			$type->name = $themes[$i]->name;
			$type->width = $themes[$i]->width;
			$type->height = $themes[$i]->height;
			$type->resize = $themes[$i]->resize;
			$type->visible = (empty($type->visible) ? '' : $type->visible);

			$modified	= true;
		}

		if( $modified )
		{
			// Empty name field will be removed
			$myFinalResults = array();

			if( !empty($exclude) )
			{
				foreach ($types as $type)
				{
					// if( !empty($type->name) )
					// {
					// 	// If the pending item have value, replace the existing record
					// 	$myFinalResults[] = $type;
					// }

					if( !in_array($type->name, $exclude) )
					{
						$myFinalResults[] = $type;
					}
				}

				$types = $myFinalResults;
			}

			// Now let's save this
			$contents	= $json->encode( $types );
			$state 		= JFile::write( $blogImageFile , $contents );

			if( !empty($exclude) )
			{
				// User perfrom deletion
				$total 		= count( $exclude );
				$message	= JText::sprintf( 'COM_EASYBLOG_THEME_REMOVED' , $total );
				$this->setRedirect( 'index.php?option=com_easyblog&view=theme&element=' . $element , $message );
				return;
			}

			if( $state )
			{
				$this->setRedirect( 'index.php?option=com_easyblog&view=theme&element=' . $element, JText::_('COM_EASYBLOG_THEME_SUCCESS_NEW_THEME_SETTING'), 'info' );
			}
			else
			{
				$this->setRedirect( 'index.php?option=com_easyblog&view=theme&element=' . $element, JText::_('COM_EASYBLOG_THEME_FAIL_NEW_THEME_SETTING'), 'error' );
			}
		}
	}

	public function addTheme()
	{
		// Get the new added values
		$newThemePosition 	= JRequest::getVar( 'newThemePosition' );
		$newThemeWidth 		= JRequest::getVar( 'newThemeWidth' );
		$newThemeHeight		= JRequest::getVar( 'newThemeHeight' );
		$newThemeMethod 	= JRequest::getVar( 'newThemeMethod' );
		$element 			= JRequest::getVar( 'element' );

		// Let's update the blog image.
		$blogImageFile	=	EBLOG_THEMES . DIRECTORY_SEPARATOR . $element . DIRECTORY_SEPARATOR . 'image.ini';

		jimport( 'joomla.filesystem.file' );

		if( !JFile::exists( $blogImageFile ) )
		{
			return false;
		}

		$contents 	= JFile::read( $blogImageFile );

		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'json.php' );

		$json 		= new Services_JSON();
		$types		= $json->decode( $contents );
		$modified	= false;

		$obj = new stdClass();
		$obj->name = $newThemePosition;
		$obj->width = $newThemeWidth;
		$obj->height = $newThemeHeight;
		$obj->resize = $newThemeMethod;

		// Append below the list
		$types[] = $obj;

		// Now let's save this
		$contents	= $json->encode( $types );
		$state 		= JFile::write( $blogImageFile , $contents );

		if( $state )
		{
			$this->setRedirect( 'index.php?option=com_easyblog&view=theme&element=' . $element, JText::_('COM_EASYBLOG_THEME_SUCCESS_NEW_THEME_SETTING'), 'info' );
		}
		else
		{
			$this->setRedirect( 'index.php?option=com_easyblog&view=theme&element=' . $element, JText::_('COM_EASYBLOG_THEME_FAIL_NEW_THEME_SETTING'), 'error' );
		}
	}


	public function getAjaxTemplate()
	{
		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' );
		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );

		$files	= JRequest::getVar( 'names' , '' );

		if( empty( $files ) )
		{
			return false;
		}

		// Ensure the integrity of each items submitted to be an array.
		if( !is_array( $files ) )
		{
			$files	= array( $files );
		}

		$result		= array();

		foreach( $files as $file )
		{
			$dashboard = explode( '/' , $file );

			if( $dashboard[0]=="dashboard" )
			{
				$template 	= new CodeThemes( true );
				$out		= $template->fetch( $dashboard[1] . '.ejs' );
			}
			elseif ( $dashboard[0]=="media" )
			{
				$template 	= new CodeThemes( true );
				$out		= $template->fetch( "media." . $dashboard[1] . '.ejs' );
			}
			else
			{
				$template 	= new CodeThemes();
				$out		= $template->fetch( $file . '.ejs' );
			}

			$obj			= new stdClass();
			$obj->name		= $file;
			$obj->content	= $out;

			$result[]		= $obj;
		}


		header('Content-type: text/x-json; UTF-8');
		$json	 		= new Services_JSON();
		echo $json->encode( $result );
		exit;
	}
}
