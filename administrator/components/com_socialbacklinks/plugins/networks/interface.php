<?php
/**
 * SocialBacklinks Interface for network plugins
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
 * Interface for networks plugin
 */
interface SBPluginsNetworksInterface
{
	/**
	 * Connects to social network.
	 * Returns the result of the connection, or redirects otherwise
	 * @param  array The list of parameters of the connection
	 * @param  bool $callback Whether social server returns some data or not
	 * @return bool
	 */
	public function connect( $params, $callback = false );

	/**
	 * Disconnects from social network.
	 * @param  bool $callback Whether social server returns some data or not
	 * @return bool
	 */
	public function disconnect( );

	/**
	 * Generates the url to login user into social network
	 * @return string
	 */
	public function getLoginUrl( );

	/**
	 * Generates the url to logout user from social network
	 * @return string
	 */
	public function getLogoutUrl( );

	/**
	 * Returns a name of the user in social network
	 * @return string
	 */
	public function getUserName( );

	/**
	 * Checks if the user is logged in social network
	 * @return boolean
	 */
	public function isLoggedIn( );

	/**
	 * Adds post to user wall
	 * @param  string Title of the post
	 * @param  string Link to the items on User's site
	 * @param  string Description of the item
	 * @return boolean
	 */
	public function addPost( $title, $link, $desc = '', $image = '' );
}
