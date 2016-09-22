<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

/**
 * System plugin that performs system wide tasks for EasySocial
 *
 * @package     Joomla.Plugin
 * @subpackage  Content.EasySOcial
 * @since       1.0.0
 */
class PlgSystemEasySocial extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param   object	$subject The object to observe
	 * @param   array  $config  An array that holds the plugin configuration
	 * @since   1.0
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		$this->app 	= JFactory::getApplication();
	}

	/**
	 * Determines if EasySocial exists on the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	public function exists()
	{
		$file 	= JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php';

		jimport('joomla.filesystem.file');

		if( !JFile::exists( $file ) )
		{
			return false;
		}

		include_once( $file );

		return true;
	}

	/**
	 * Executes before the router
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAfterRoute()
	{
		if( !$this->exists() )
		{
			return;
		}

		$this->processUsersRedirection();
	}

	/**
	 * Redirects users view to easysocial
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function processUsersRedirection()
	{
		$doc 	= JFactory::getDocument();

		if( $doc->getType() != 'html' )
		{
			return;
		}

		// Check if the admin wants to enable this
		if( !$this->params->get( 'redirection' , true ) )
		{
			return;
		}

		// If this is registration from com_users, redirect to the appropriate page.
		if( $this->isUserRegistration() )
		{
			// Redirect to EasySocial's registration
			$url 	= FRoute::registration( array() , false );

			return $this->app->redirect( $url );
		}

		// If this is username reminder, redirect to the appropriate page.
		if( $this->isUserRemind() )
		{
			// Redirect to EasySocial's registration
			$url 	= FRoute::account( array( 'layout' => 'forgetUsername' ) , false );

			return $this->app->redirect( $url );
		}

		// If this is password reset, redirect to the appropriate page.
		if( $this->isUserReset() )
		{
			// Redirect to EasySocial's registration
			$url 	= FRoute::account( array( 'layout' => 'forgetPassword' ) , false );

			return $this->app->redirect( $url );
		}

		// If this is password reset, redirect to the appropriate page.
		if( $this->isUserLogin() )
		{
			// Determine if there's any "return" url in the query string
			$return 	= JRequest::getVar('return');

			if ($return) {
				FD::setCallback(base64_decode($return));
			}

			// Redirect to EasySocial's registration
			$url 	= FRoute::login(array(), false);

			return $this->app->redirect($url);
		}

		// If this is password reset, redirect to the appropriate page.
		if( $this->isUserProfile() )
		{
			// Redirect to EasySocial's registration
			$url 	= FRoute::profile( array() , false );

			return $this->app->redirect( $url );
		}
	}

	/**
	 * Determines if the current access is for profile
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	public function isUserProfile()
	{
		$option 	= JRequest::getVar( 'option' );
		$view 		= JRequest::getVar( 'view' );

		if( $option == 'com_users' && $view == 'profile' )
		{
			return true;
		}

		return false;
	}

	/**
	 * Determines if the current access is for login
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	public function isUserLogin()
	{
		$option 	= JRequest::getVar( 'option' );
		$view 		= JRequest::getVar( 'view' );

		if( $option == 'com_users' && $view == 'login' )
		{
			return true;
		}

		return false;
	}

	/**
	 * Determines if the current access is for reset password
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	public function isUserReset()
	{
		$option 	= JRequest::getVar( 'option' );
		$view 		= JRequest::getVar( 'view' );

		if( $option == 'com_users' && $view == 'reset' )
		{
			return true;
		}

		return false;
	}

	/**
	 * Determines if the current access is for remind username
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	public function isUserRemind()
	{
		$option 	= JRequest::getVar( 'option' );
		$view 		= JRequest::getVar( 'view' );

		if( $option == 'com_users' && $view == 'remind' )
		{
			return true;
		}

		return false;
	}

	/**
	 * Determines if the current access is for registration
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	public function isUserRegistration()
	{
		$option 	= JRequest::getVar( 'option' );
		$view 		= JRequest::getVar( 'view' );

		if( $option == 'com_users' && $view == 'registration' )
		{
			return true;
		}

		return false;
	}
}
