<?php
/**
 * SocialBacklinks Abstract class for networks plugins
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
 * Abstract class for networks plugins
 * @abstract
 */
abstract class SBPluginsNetwork extends SBPluginsAbstract implements SBPluginsNetworksInterface
{
	/**
	 * Information about social application
	 * @var array
	 */
	protected $_app = array( );

	/**
	 * Information about current connection to social network
	 * @var array
	 */
	protected $_state = array( );

	/**
	 * Adapter object to manage social network connection
	 * @var object
	 */
	protected $_adapter = null;

	/**
	 * Object with user account information from social network
	 * @var object
	 */
	protected $_user = null;

	/**
	 * Returns array of the states
	 * @return array
	 */
	abstract protected function _getState( );

	/**
	 * Sets the state
	 * @param  array $state
	 * @return void
	 */
	abstract protected function _setState( $state );

	/**
	 * Destroys custom data from session and in state
	 * @return void
	 */
	abstract protected function _destroyState( );

	/**
	 * Return the social network user object if user logged in,
	 * or null otherwise
	 *
	 * @return mixed
	 */
	abstract protected function _getUser( );

	/**
	 * Constructor
	 * @param  Jplugin Object that has registered current plugin
	 * @param  array  The list of plugin options
	 * @return void
	 */
	public function __construct( $caller, $options = array() )
	{
		parent::__construct( $caller, $options );
	}

	/**
	 * @see SBPluginsNetworksInterface::getLoginUrl()
	 */
	public function getLoginUrl( )
	{
		$this->_destroyState( );
		$alias = $this->getAlias( );
		return JRoute::_( "index.php?option=com_socialbacklinks&view=plugin&task=networkConnect&network={$alias}&tmpl=component" );
	}

	/**
	 * @see SBPluginsNetworksInterface::getLogoutUrl()
	 */
	public function getLogoutUrl( )
	{
		$alias = $this->getAlias( );
		return JRoute::_( "index.php?option=com_socialbacklinks&view=plugin&task=networkDisconnect&network={$alias}&tmpl=component" );
	}

	/**
	 * Returns callback uri during connection
	 * @return string
	 */
	protected function _getCallback( )
	{
		$alias = $this->getAlias( );
		return JURI::base( ) . "index.php?option=com_socialbacklinks&view=plugin&task=networkConnect&network={$alias}&callback=1&tmpl=component";
	}

}
