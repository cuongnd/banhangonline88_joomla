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

require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_media' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'media.php' );

class EasyBlogViewMedia extends EasyBlogView
{
	public function loadScript($scripts)
	{
		$document	= JFactory::getDocument();
		$version = str_ireplace('.', '', EasyBlogHelper::getLocalVersion());

		foreach($scripts as $script)
		{
			$document->addScript(JURI::root() . $script . '?' . $version);
		}
	}

	public function flickrLogin()
	{
		$callback = JRequest::getCmd( 'callback' );

		echo '<script type="text/javascript">';

		ob_start();
		?>

			// Re-initialize the Flickr Controller
			try {
				window.opener["<?php echo $callback; ?>"]();
			} catch(e) {}

			// Close the oauth dialog window.
			window.close();

		<?php

		$contents = ob_get_contents();
		ob_end_clean();

		echo $contents;
		echo '</script>';
		exit;
	}

	/**
	 * Displays the files and folders that are in the media manager.
	 */
	public function display($tpl = null)
	{
		$config     = EasyBlogHelper::getConfig();
		$document	= JFactory::getDocument();
		$my         = JFactory::getUser();
		$app		= JFactory::getApplication();
		$profile	= EasyBlogHelper::getTable( 'Profile' );
		$profile->load( $my->id );

		if( $my->id <= 0 )
		{
			echo JText::_( 'COM_EASYBLOG_NOT_ALLOWED' );
			exit;
		}

		$user 		= JFactory::getUser();

		$document->setTitle( JText::_( 'COM_EASYBLOG_MEDIA_MANAGER' ) );
		// Only allow admin to impersonate anyone.
		if( EasyBlogHelper::isSiteAdmin() )
		{
			$user 	= JFactory::getUser( JRequest::getVar( 'blogger_id' , $my->id ) );
		}

		$debug		= ( $config->get( 'debug_javascript') || JRequest::getVar( 'ebjsdebug' ) == 1 ) ? 'true' : 'false';

		$theme		= new CodeThemes( true );
		$theme->set( 'debug'		, $debug );
		$theme->set( 'session'		, JFactory::getSession() );
		$theme->set( 'blogger_id'	, $user->id );

		// @rule: Test if the user is already associated with Flickr
		$oauth		= EasyBlogHelper::getTable( 'Oauth' );
		$associated	= $oauth->loadByUser( $my->id , EBLOG_OAUTH_FLICKR );

		$theme->set( 'flickrAssociated' , $associated );

		echo $theme->fetch( 'media.php' );
	}

}
