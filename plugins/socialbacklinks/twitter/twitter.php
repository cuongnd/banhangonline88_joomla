<?php
/**
 * SocialBacklinks plugin for Twitter Social Network
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and change.
 * Otherwise, please feel free to contact us at contact@joomunited.com
 *
 * @package 	Social Backlinks
 * @copyright 	Copyright (C) 2012 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @license 	GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Joomla SocialBacklinks Synchronizer plugin
 */
class plgSocialbacklinksTwitter extends JPlugin
{
	/**
	 * Registers plugin in the system
	 * @return void
	 */
	public function onSBPluginRegister( )
	{
		JLoader::register( 'TwitterOAuth', JPATH_ROOT . '/plugins/socialbacklinks/twitter/twitter/twitteroauth.php' );
		JLoader::register( 'PlgSBTwitterAdapter', JPATH_ROOT . '/plugins/socialbacklinks/twitter/twitter/adapter.php' );
		SBPlugin::register( new PlgSBTwitterAdapter( $this, array( 'title' => 'Twitter' ) ) );
		$this->loadLanguage( );
	}

}
