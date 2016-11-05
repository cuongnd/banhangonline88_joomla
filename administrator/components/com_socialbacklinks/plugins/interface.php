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
 * The main interface for SB plugins
 */
interface SBPluginsInterface
{
	/**
	 * Returns the option of the plugin
	 * @param  string Option name
	 * @return mixed
	 */
	public function getOption( $option );

	/**
	 * Sets the option of the plugin
	 * @param  string Option name
	 * @param  mixed  Option value
	 * @return void
	 */
	public function setOption( $option, $value );

	/**
	 * Returns the list of plugin options
	 * @return array
	 */
	public function getOptions( );

	/**
	 * Sets the list of plugin options
	 * @param  array List of options
	 * @return void
	 */
	public function setOptions( $options );

	/**
	 * Returns the unique alias of the plugin
	 * @return string
	 */
	public function getAlias( );

	/**
	 * Returns class of caller
	 * @return string
	 */
	public function getCaller( );
	
	/**
	 * Returns plugin's type
	 * @return string
	 */
	public function getType( );

	/**
	 * Saves options
	 * @return void
	 */
	public function save( );
	
}
