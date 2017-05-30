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

FD::import( 'admin:/includes/controller' );
FD::import( 'admin:/includes/themes/themes' );

class EasySocialController extends EasySocialControllerMain
{
	protected $app	= null;
	protected $input = null;
	protected $my = null;

	// This will notify the parent class that this is for the back end.
	protected $location 	= 'frontend';

	public function __construct()
	{
		$this->app	 = JFactory::getApplication();
		$this->input = $this->app->input;
		$this->my = FD::user();
		$this->config = FD::config();

		parent::__construct();
	}

	/**
	 * Override's parent's execute behavior
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function execute( $task )
	{
		$config 	= FD::config();
		$current	= JRequest::getWord( 'controller' );

		// Check and see if this view should be displayed
		// If private mode is enabled and user isn't logged in.
		if( $config->get( 'general.site.lockdown.enabled' ) && !JFactory::getUser()->id )
		{
			if( $this->lockdown( $task ) && !empty( $current ) )
			{
				JFactory::getApplication()->redirect( FRoute::login( array() , false ) );
			}
		}

		parent::execute( $task );
	}

	/**
	 * Determines if the current view should be locked down.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	public function lockdown( $task = '' )
	{
		// Default, all views are locked down.
		$state 	= true;

		if( method_exists( $this , 'isLockDown' ) )
		{
			$state 	= $this->isLockDown( $task );
		}

		return $state;
	}

	/**
	 * Override parent controller's display behavior.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display( $params = array() , $urlparams = false)
	{
		$doc 	= JFactory::getDocument();

		// @task: Get the view from Joomla.
		$type	= $doc->getType();
		$name 	= JRequest::getCmd( 'view' , 'dashboard' );
		$view	= $this->getView( $name , $type , '' );

		// @task: Once we have the view, set the appropriate layout.
		$layout	= JRequest::getCmd( 'layout' , 'default' );
		$view->setLayout( $layout );

		$config	= FD::config();

		// Check and see if this view should be displayed
		// If private mode is enabled and user isn't logged in.
		if( $config->get( 'general.site.lockdown.enabled' ) && !JFactory::getUser()->id )
		{
			if( $view->lockdown() )
			{
				JFactory::getApplication()->redirect( FRoute::login( array() , false ) );
			}
		}


		// For ajax methods, we just load the view methods.
		if( $type == 'ajax' )
		{
			if( !method_exists( $view , $layout ) )
			{
				$view->display();
			}
			else
			{
				$json 	= FD::json();
				call_user_func_array( array( $view , $viewLayout ) , $json->decode( JRequest::getVar( 'params' ) ) );
			}
		}
		else
		{
			// Disable inline scripts in templates.
			SocialThemes::$_inlineScript = false;

			if( $layout != 'default' )
			{
				if( !method_exists( $view , $layout ) )
				{
					$view->display();
				}
				else
				{
					call_user_func_array( array( $view , $layout ) , $params );
				}
			}
			else
			{
				$view->display();
			}

			// Restore inline script in templates.
			SocialThemes::$_inlineScript = true;
		}
	}
}
