<?php
/**
 * SocialBacklinks Interface for plugins
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
 * Abstract class for plugin adapters
 * @abstract
 */
abstract class SBPluginsAdaptersAbstract
{
	/**
	 * The list of registered plugins
	 * @var ArrayObject
	 */
	protected $_r;

	/**
	 * Constructor
	 * @return void
	 */
	public function __construct( )
	{
		$this->_r = new ArrayObject( );
	}

	/**
	 * Registers plugin in the adapter
	 * @param  SBPluginsInterface $plugin
	 * @return bool Whether plugin has been successfully registered
	 */
	abstract public function register( $plugin );

	/**
	 * Returns the plugin by alias or the list of plugins if alias is empty
	 * @param  string $plugin Optional. The plugin's alias
	 * @return SBPluginsInterface
	 */
	public function get( $plugin = null )
	{
		if ( is_null( $plugin ) ) {
			return (array)$this->_r;
		}
		elseif ( $this->_r->offsetExists( $plugin ) ) {
			return $this->_r->offsetGet( $plugin );
		}
		return null;
	}
}
