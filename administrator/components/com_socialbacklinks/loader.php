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
 * Loader for SocialBacklinks
 */
class SBLoader
{
	/**
	 * The file container
	 *
	 * @var ArrayObject
	 */
	protected static $_registry = null;

	/**
	 * Constructor.
	 *
	 * @desc	Prevent creating instances of this class by making the contructor private
	 * @final
	 * @return	void
	 */
	final private function __construct( )
	{
		self::$_registry = new ArrayObject( );
		spl_autoload_register( array( __CLASS__, 'load' ) );

		// Register the autoloader in a way to play well with as many configurations as possible.
		if ( function_exists( '__autoload' ) ) {
			spl_autoload_register( '__autoload' );
		}
	}

	/**
	 * Clone.
	 *
	 * @desc	Prevent creating clones of this class
	 * @final
	 * @return	void
	 */
	final private function __clone( )
	{
	}

	/**
	 * Method for loading classes.
	 *
	 * @desc	The classname should begin from Uppercase Symbol and haven't contain "."
	 *
	 * @return	bool	Returns TRUE on success throws exception on failure
	 */
	public static function load( $class )
	{
		if ( (ctype_upper( substr( $class, 0, 1 ) ) || (strpos( $class, '.' ) !== false)) ) {
			if ( class_exists( $class, false ) || interface_exists( $class, false ) ) {
				return true;
			}

			//Get the path
			$result = self::path( $class );

			//Don't re-include files and stat the file if it exists
			if ( $result !== false && !in_array( $result, get_included_files( ) ) && file_exists( $result ) ) {
				$included =
				include $result;

				if ( $included ) {
					return $result;
				}
				else {
					JError::raiseError( 500, "The class '$class' doesn't exist in '$result'" );
				}
			}
		}
		return false;
	}

	/**
	 * Returns singleton instance of loader
	 *
	 * @static
	 * @return	SBLoader
	 */
	public static function instantiate( )
	{
		static $instance;
		if ( $instance === NULL ) {
			$instance = new self( );
		}
		SBPluginsBootstrap::run( );
		return $instance;
	}

	/**
	 * Returns the path based on a class name
	 *
	 * @param	string	The class name
	 * @return	string	Returns canonicalized absolute pathname
	 */
	public static function path( $class )
	{
		if ( self::$_registry->offsetExists( ( string )$class ) ) {
			return self::$_registry->offsetGet( ( string )$class );
		}

		$result = false;
		$word = preg_replace( '/(?<=\\w)([A-Z])/', '_\\1', $class );
		$parts = explode( '_', $word );
		if ( array_shift( $parts ) . array_shift( $parts ) === 'SB' ) {

			$basepath = JPATH_ADMINISTRATOR . '/components/com_socialbacklinks';
			$name = array_pop( $parts );

			$result = strtolower( implode( '/', $parts ) ) . '/' . strtolower( $name );

			// For classes like 'SBLoader' in 'com_socialbacklinks/loaders/loader.php'
			if ( !is_file( $basepath . '/' . $result . '.php' ) ) {
				if ( strpos( $class, 'Views' ) !== false ) {
					$result = strtolower( implode( '/', $parts ) ) . '/' . strtolower( $name ) . '/view.html';
				}
				else {
					$result = $result . 's/' . strtolower( $name );
				}
			}
			$result = $basepath . '/' . $result . '.php';
		}

		if ( $result !== false ) {
			//Get the canonicalized absolute pathname
			$path = realpath( $result );
			$result = $path !== false ? $path : $result;

			if ( $result !== false ) {
				self::$_registry->offsetSet( ( string )$class, $result );
			}
		}
		return $result;
	}

}
