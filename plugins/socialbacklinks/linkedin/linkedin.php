<?php
/**
 * SocialBacklinks plugin for Linkedin Social Network
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
 * Plugin for Linkedin network
 */
class plgSocialbacklinksLinkedin extends JPlugin
{
	/**
	 * Registers plugin in the system
	 * @return void
	 */
	public function onSBPluginRegister( )
	{
		JLoader::register( 'LinkedInOAuth', JPATH_ROOT . '/plugins/socialbacklinks/linkedin/linkedin/linkedinoauth.php' );
		JLoader::register( 'PlgSBLinkedinAdapter', JPATH_ROOT . '/plugins/socialbacklinks/linkedin/linkedin/adapter.php' );
		SBPlugin::register( new PlgSBLinkedinAdapter( $this, array( 'title' => 'Linkedin' ) ) );
		$this->loadLanguage( );
	}

}
