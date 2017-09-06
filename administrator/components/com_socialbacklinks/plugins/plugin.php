<?php
/**
 * SocialBacklinks plugin container
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
 * Plugin conteiner
 * @static
 */
class SBPlugin
{
	/**
	 * List of adapters
	 * @var ArrayObject
	 */
	private static $_a;

	/**
	 * Instantiates adapters
	 * @return void
	 */
	public static function instantiate( )
	{
		self::$_a = new ArrayObject( );
		self::addAdapter( 'network' );
		self::addAdapter( 'content' );
	}

	/**
	 * Registers plugin's adapter
	 * @var    SBPluginsInterface
	 * @throws SBPluginsException If adapter doesn't exist
	 * @return void
	 */
	public static function addAdapter( $adapter )
	{
		$class = 'SBPluginsAdapters' . ucfirst( $adapter );
		if ( class_exists( $class ) ) {
			self::$_a->offsetSet( $adapter, new $class( ) );
		}
		else {
			throw new SBPluginsException( JText::_( "Adapter '$adapter' doesn't exist" ), 500 );
		}
	}

	/**
	 * Adds a plugin to registry
	 * @param  SBAdapterPluginInterface $plugin
	 * @throws SBPluginsExceptions
	 * @return void
	 */
	public static function register( SBPluginsInterface $plugin )
	{
		foreach (self::$_a as $adapter) {
			if ( $adapter->register( $plugin ) ) {
				return;
			}
		}
		throw new SBPluginsException( get_class( $plugin ) . ' ' . JText::_( 'SB_PLG_DOESNT_IMPLEMENT_VALID_INTERFACE' ), 500 );
	}

	/**
	 * Returns the plugin or a list of plugin with spesific category.
	 * @param  string $identifier Can be '{adapter}.{plugin}', '{$adapter}.' and '{$plugin}'. It will return different result depends on
	 * identifier
	 * @return array
	 */
	public static function get( $identifier )
	{
		list( $adapter, $plugin ) = self::_parseIdentifier( $identifier );
		if ( !is_null( $adapter ) ) {
			//'{adapter}.{plugin}' or '{$adapter}.'
			if ( self::$_a->offsetExists( $adapter ) ) {
				$adapter = self::$_a->offsetGet( $adapter );
				return $adapter->get( $plugin );
			}
			else {
				return null;
			}
		}
		else {
			//'{$plugin}'
			foreach (self::$_a as $adapter) {
				if ( $result = $adapter->get( $plugin ) ) {
					return $result;
				}
			}
		}
		return null;
	}

	/**
	 * Returns adapter and plugin's name from parsed identifier
	 * @return array
	 */
	private static function _parseIdentifier( $identifier )
	{
		$identifier = trim( $identifier );
		if ( strpos( $identifier, '.' ) === strlen( $identifier ) - 1 ) {
			$adapter = substr( $identifier, 0, strlen( $identifier ) - 1 );
			$plugin = null;
		}
		elseif ( strpos( $identifier, '.' ) !== false ) {
			list( $adapter, $plugin ) = explode( '.', $identifier );
		}
		else {
			$adapter = null;
			$plugin = $identifier;
		}
		return array( $adapter, $plugin );
	}

}
