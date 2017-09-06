<?php
/**
 * SocialBacklinks Back-End
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and change.
 * Otherwise, please feel free to contact us at contact@joomunited.com
 *
 * @package 	Social Backlinks
 * @copyright 	Copyright (C) 2012 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @license 	GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */
 
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * SocialBacklinks dispatcher
 */
class SBDispatcher
{
	/**
	 * The list of loaded controllers
	 * @var array
	 */

	protected $_controllers = array( );

	/**
	 * Constructor
	 * @final
	 */
	private final function __construct( )
	{

	}

	/**
	 * Clone
	 * @final
	 */
	private final function __clone( )
	{

	}

	/**
	 * Returns Dispatcher instance
	 * @return SBDispatcher
	 */
	public static function getInstance( )
	{
		static $instance;
		if ( is_null( $instance ) ) {
			$instance = new self( );
		}
		return $instance;
	}

	/**
	 * Dispatches user's request
	 * @return void
	 */
	public function dispatch( )
	{
		// Define controller:
		if ( !$controller = $this->getController( ) ) {
			JError::raiseError( 404, JText::_( 'SB_PATH_NO_FOUND_ERROR' ) );
			return false;
		}

		$controller->execute( JRequest::getCmd( 'task' ) );
		$controller->redirect( );
	}

	/**
	 * Returns controller instance
	 * @param string Controller name. Optional. If empty it will got from Request
	 * @param array  An optional associative array of configuration settings.
	 * @return SBControllersBase
	 */
	public function getController( $controller = null, $config = array() )
	{
		if ( is_null( $controller ) ) {
			if ( !($controller = JRequest::getCmd( 'controller', JRequest::getCmd( 'view', '' ) )) ) {
				$controller = 'dashboard';
				JRequest::setVar( 'view', $controller );
			}
		}
		if ( isset( $this->_controllers[$controller] ) ) {
			return $this->_controllers[$controller];
		}
		else {
			$class = 'SBControllers' . ucfirst( $controller );
			$result = !class_exists( $class ) ? false : new $class( $config );
			$this->_controllers[$controller] = $result;
			return $result;
		}
	}

	/**
	 * Runs controller action
	 * @param  string Controller's name
	 * @param  array  Controller's request
	 * @return mixed
	 */
	public function runController( $controller_name, $request = array() )
	{
		if ( $controller = $this->getController( $controller_name ) ) {
			if ( empty( $request['view'] ) ) {
				$request['view'] = $controller_name;
			}
			$controller->setRequest( $request );
			$controller->execute( $controller->getRequest( )->get( 'task', '' ) );
		}
		else {
			throw new SBException( JText::_( 'SB_CONTROLLER_DOESNT_EXIST' ) );
		}
	}

}
