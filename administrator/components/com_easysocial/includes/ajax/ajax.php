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
* This is an extremely lightweight ajax library.
*
* Example:
* <code>
* <?php
* $ajax = FD::get('ajax');
*
* $ajax->success();
* // Returning a success callback, e.g.
* $ajax->success(arg1, arg2, arg3);
*
* $ajax->fail();
* // Returning a failed callback, e.g.
* $ajax->fail(arg1, arg2, arg3);
*
* // This function replaces addScriptCall routines, e.g.
* $ajax->script('alert("foobar")');
* ?>
*
* @since	1.0
* @author	Mark Lee <mark@stackideas.com>
*/
class SocialAjax
{
	private $commands	= array();
	static $instance	= null;

	public function __construct() {}

	public function addCommand($type, &$data)
	{
		$this->commands[] = array(
			'type' => $type,
			'data' =>& $data
		);

		return $this;
	}

	/**
	 * Creates a copy of it self and return to the caller.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	SocialProfiler
	 *
	 */
	public static function getInstance()
	{
		if( is_null( self::$instance ) )
		{
			self::$instance		= new self();
		}

		return self::$instance;
	}

	/**
	 * Resolve a given POSIX path.
	 *
	 * <code>
	 * <?php
	 * // This would translate to administrator/components/com_easysocial/controllers/fields.php
	 * FD::resolve( 'ajax:/admin/controllers/fields/renderSample' );
	 *
	 * // This would translate to components/com_easysocial/controllers/dashboard.php
	 * FD::resolve( 'ajax:/site/controllers/dashboard/someMethod' );
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string		The posix path to lookup for.
	 * @return	string		The translated path
	 */
	public static function resolveNamespace( $namespace )
	{
		// Split the paths.
		$parts 		= explode( '/' , $namespace );

		$location	= $parts[ 0 ];
		$config 	= FD::config();

		// Remove the location from parts.
		array_shift( $parts );

		// Get the absolute path of the initial location
		$path		= $location == 'admin' ? SOCIAL_ADMIN : SOCIAL_SITE;

		// Get the method to be invoked.
		$method 	= array_pop( $parts );

		if( $location == 'site' || $location == 'admin' )
		{
			// Determine if this is a view or controller.
			if( $parts[0] == 'controllers' )
			{
				$path 	= $path . '/' . implode( '/' , $parts ) . '.php';
			}
			else
			{
				$path 	= $path . '/' . implode( '/' , $parts ) . '/view.ajax.php';
			}
		}

		if( $location == 'apps' )
		{
			$group 		= $parts[ 0 ];
			$element 	= $parts[ 1 ];
			$type 		= $parts[ 2 ];
			$typeFile	= $parts[ 3 ];

			// E.g: apps:/user/tasks/views/viewName/functionName
			if( $type == 'views' )
			{
				$path 	= SOCIAL_APPS . '/' . $group . '/' . $element . '/views/' . $typeFile . '/view.ajax.php';
			}

			// E.g: apps:/user/tasks/controllers/tasks/functionName
			if( $type == 'controllers' )
			{
				// Import dependencies.
				FD::import( 'admin:/includes/apps/dependencies' );

				$path 	= SOCIAL_APPS . '/' . $group . '/' . $element . '/controllers/' . $typeFile . '.php';
			}
		}

		if( $location == 'fields' )
		{
			// This is the field group. E.g: users , groups etc.
			$group		= $parts[ 0 ];

			// This is the field element.
			$element	= $parts[ 1 ];

			$path 	= SOCIAL_FIELDS . '/' . $group . '/' . $element . '/ajax.php';
		}

		// Get the arguments from the query string if there is any.
		$args		= JRequest::getVar( 'args' , '' );

		$ajax 		= FD::ajax();

		// Check that the file exists.
		jimport( 'joomla.filesystem.file' );
		if( !JFile::exists( $path ) )
		{
			$ajax->reject( JText::sprintf( 'The file %1s does not exist.' , $namespace ) );
			return $ajax->send();
		}

		// Include the path.
		include_once( $path );

		// We need to know the name of the class before we can instantiate it.
		switch( $location )
		{
			case 'fields':

				// This is the group
				$group 		= $parts[ 0 ];

				// We know the second segment is always the element.
				$element	= $parts[ 1 ];

				// Construct parameters
				$config 	= array( 'group' => $group , 'element' => $element, 'field' => null, 'inputName' => SOCIAL_FIELDS_PREFIX . '0' );

				// Detect if there is an id passed in.
				$id 		= JRequest::getInt( 'id' , 0 );

				// If there is an id, it should also create a copy of the field.
				if( $id )
				{
					$field 	= FD::table( 'Field' );
					$field->load( $id );

					$step = FD::table( 'fieldstep' );
					$step->load( $field->step_id );

					$uid = $step->uid;

					$params = FD::fields()->getFieldConfigValues( $field );

					$config[ 'params' ] = $params;
					$config[ 'uid' ] = $uid;
					$config[ 'field' ]	= $field;
					$config[ 'inputName' ] = SOCIAL_FIELDS_PREFIX . $field->id;
				}

				// Determine the class name
				$class 		= 'SocialFields' . ucfirst( $group ) . ucfirst( $element );

				// Let's instantiate the new object now.
				$obj 		= new $class( $config );

				// Call the ajax method
				$obj->$method();

				break;

			case 'apps':

				// We know the second segment is always the element.
				$group		= $parts[ 0 ];
				$element 	= $parts[ 1 ];
				$type 		= $parts[ 2 ];

				// If this is a view call, it should use the method.
				$classType 	= $parts[ 3 ];

				if( $type == 'controllers' )
				{
					// Construct the classname
					$class 	= ucfirst( $element ) . 'Controller' . ucfirst( $classType );

					// Let's instantiate the new object now.
					$obj 		= new $class( $group , $element );
				}

				if( $type == 'views' )
				{
					$app 	= FD::table( 'App' );
					$app->load( JRequest::getInt( 'id' ) );

					$class 	= ucfirst( $element ) . 'View' . ucfirst( $classType );

					$obj 	= new $class( $app , $classType );
				}

				// If the method doesn't exist in this object, we know something is wrong.
				if( !method_exists( $obj, $method ) )
				{
					$ajax->reject( JText::sprintf( 'Method %1s does not exist' , $method ) );
					return $ajax->send();
				}

				if( !empty( $args ) )
				{
					call_user_func_array( array( $obj ,$method ) , FD::json()->decode( $args ) );
				}
				else
				{
					$obj->$method();
				}

				break;

			case 'site':
			case 'admin':
			default:

				// Currently only supports access to view and controller.
				$type 	= $parts[ 0 ];
				$name 	= $parts[ 1 ];

				if( $type == 'views' )
				{
					$class 	= 'EasySocialView' . preg_replace( '/[^A-Z0-9_]/i', '', $name );

					// Create the new view object.
					$obj     = new $class();
				}

				if( $type == 'controllers' )
				{
					$class 	= 'EasySocialController' . preg_replace( '/[^A-Z0-9_]/i', '', $name );

					// Create the new view object.
					$obj     = new $class();
				}

				if( $config->get( 'general.site.lockdown.enabled' ) && !JFactory::getUser()->id )
				{
					if( method_exists( $obj , 'lockdown' ) && $obj->lockdown() )
					{
						$ajax->reject( JText::_( 'You are not allowed here.' ) );
						return $ajax->send();
					}
				}

				// For controllers we need to use the standard `execute` method.
				if( $type == 'controllers')
				{
					$obj->execute( $method );
				}
				else
				{
					// If the method doesn't exist in this object, we know something is wrong.
					if( !method_exists( $obj, $method ) )
					{
						$ajax->reject( JText::sprintf( 'Method %1s does not exist' , $method ) );
						return $ajax->send();
					}

					if( !empty( $args ) )
					{
						call_user_func_array( array( $obj ,$method ) , FD::json()->decode( $args ) );
					}
					else
					{
						$obj->$method();
					}
				}

				break;
		}

		// Terminate the output.
		$ajax->send();

		return $path;
	}


	/* This will handle all ajax commands e.g. success/fail/script */
	public function __call($method, $args)
	{
		$this->addCommand($method, $args);

		return $this;
	}

	public function EasySocial($selector=null)
	{
		$chain = array();

		$this->addCommand('script', $chain);

		// Because we need to maintain the variable to be passed by reference,
		// we need to use an array instead as arguments.
		$js = FD::get( array( 'Javascript' , true ) , array( &$chain ) );

		if (isset($selector))
		{
			$js->EasySocial($selector);
		}
		else
		{
			$js->EasySocial;
		}

		return $js;
	}

	public function send()
	{
		header('Content-type: text/x-json; UTF-8');

		$json 		= FD::json();
		$callback 	= JRequest::getVar( 'callback' , '' );

		// Isolate PHP errors and send it as a notify command.
		$error_reporting = ob_get_contents();
		if (strlen(trim($error_reporting))) {
			$this->notify($error_reporting, 'debug');
		}

		ob_clean();

		// Process jsonp requests if necessary.
		if( $callback )
		{
			header('Content-type: application/javascript; UTF-8');
			echo $callback . '(' . $json->encode( $this->commands ) . ');';
			exit;
		}

		$transport = JRequest::getVar('transport');

		if ($transport=="iframe") {
			header('Content-type: text/html; UTF-8');
			echo '<textarea data-type="application/json" data-status="200" data-statusText="OK">' . $json->encode( $this->commands ) . '</textarea>';
			exit;
		}

		echo $json->encode( $this->commands );
		exit;
	}

	/**
	 * Processes an ajax call that is passed to the server. It is smart enough to decide which
	 * file would be responsible to keep these codes.
	 *
	 * @since	1.0
	 * @access	public
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function listen()
	{
		$doc	= JFactory::getDocument();
		$ajax	= FD::getInstance( 'Ajax' );

		// Do not proceed if the request is not in ajax format.
		if( $doc->getType() != 'ajax' )
		{
			return;
		}

		// Namespace format should be POSIX format.
		$namespace	= JRequest::getVar( 'namespace' );

		$parts 		= explode( ':/' , $namespace );

		// Detect if the user passed in a protocol.
		$hasProtocol	= count( $parts ) > 1;

		if( !$hasProtocol )
		{
			$namespace 	= 'ajax:/' . $namespace;
		}

		return FD::resolve( $namespace );
	}
}
