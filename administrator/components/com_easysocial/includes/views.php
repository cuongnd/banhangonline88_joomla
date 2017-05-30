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

$version	= FD::getInstance( 'Version' );

if( $version->getVersion() >= '3.0' )
{
	class EasySocialViewMain extends JViewLegacy
	{
	}
}
else
{
	jimport( 'joomla.application.component.view' );

	class EasySocialViewMain extends JView
	{

	}
}

class EasySocialView extends EasySocialViewMain
{
	/**
	 * Stores the view's message queue.
	 * @var	stdClass
	 */
	protected $message 		= null;

	/**
	 * Stores the theme object.
	 * @var	SocialThemes
	 */
	protected $theme		= null;

	/**
	 * Determines if there's any errors on this view.
	 * @var	boolean
	 */
	protected $errors 		= false;

	protected $breadcrumbs	= null;
	protected $app			= null;

	public function __construct( $config = array() )
	{
		// Load Joomla's app
	    $this->app    = JFactory::getApplication();
		$this->input  = JFactory::getApplication()->input;
		$this->doc    = JFactory::getDocument();
		$this->theme  = FD::themes();
		$this->config = FD::config();
		$this->my   = FD::user();
		$this->info = FD::info();

		if ($this->doc->getType() == 'ajax') {
			$this->ajax = FD::ajax();
		}

		// @task: Initialize the necessary javascript header's that will be used throughout the site
		parent::__construct($config);
	}

	/**
	 * Allows caller to set some message to the info queue
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return	boolean
	 */
	public function setMessage( $message , $messageType = SOCIAL_MSG_SUCCESS )
	{
		// Accepts SocialException instance
		if ($message instanceof SocialException) {
			$messageType = $message->type;
			$message = $message->message;
		}

		$obj 			= new stdClass();
		$obj->message 	= $message;
		$obj->type 		= $messageType;

		if( $messageType == SOCIAL_MSG_ERROR )
		{
			$this->errors 	= true;
		}

		$format	= JRequest::getWord( 'format' , 'html' );

		if( $format == 'ajax' )
		{
			$ajax = FD::ajax();
			$ajax->notify($message, $messageType);
		}

		$this->message 	= $obj;

		return true;
	}

	/**
	 * Returns the message queue.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return	stdClass
	 */
	public function getMessage()
	{
		if( !$this->message )
		{
			return false;
		}

		return $this->message;
	}

	/**
	 * Calls a specific method from the view.
	 *
	 * @since	1.0
	 * @access	public
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function call( $method )
	{
		if( !method_exists( $this , $method ) )
		{
			return false;
		}

		// Get a list of arguments since we do not know
		// how many arguments are passed in here.
		$args 	= func_get_args();

		// Remove the first argument since the first argument is the method.
		array_shift( $args );

		return call_user_func_array( array( $this , $method ) , $args );
	}

	/**
	 * Allows overriden objects to redirect the current request only when in html mode.
	 *
	 * @access	public
	 * @param	string	$uri 	The raw uri string.
	 * @param	boolean	$route	Whether or not the uri should be routed
	 */
	public function redirect( $uri , $route = true )
	{
	    $this->app->redirect( $uri );
	    $this->app->close();
	}

	/**
	 * Determines if there's any errors on this view.
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function hasErrors()
	{
		return $this->errors;
	}

	/**
	 * Main method to output the contents
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display( $tpl = null )
	{
		$format = $this->input->get('format', 'html', 'word');

		if ($format == 'json') {
			header('Content-type: text/x-json; UTF-8');
			echo $this->theme->toJSON();
			exit;
		}

		/**
		 * For 'raw' types of output, we need to exit it after that
		 * as we do not want to process anything apart from our codes only.
		 */
		if ($format == 'raw') {
			echo $this->theme->output($tpl);
			return;
		}

		if ($format == 'ajax') {
		    return $this->theme->output($tpl);
		}

		if ($format == 'html') {
			echo $this->theme->output($tpl);
			return;
		}

		return parent::display($tpl);
	}

	public function set( $key , $value = null )
	{
		return $this->theme->set( $key , $value );
	}
}
