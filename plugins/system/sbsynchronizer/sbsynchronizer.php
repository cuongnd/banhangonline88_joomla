<?php
/**
 * SocialBacklinks Synchronizer System Plugin
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
jimport( 'joomla.utilities.date' );

/**
 * Joomla SocialBacklinks Synchronizer plugin
 */
class plgSystemSBSynchronizer extends JPlugin
{
	/**
	 * Do something onAfterInitialise
	 * @return void
	 */
	public function onAfterInitialise( )
	{
		JLoader::register( 'SBLoader', JPATH_ADMINISTRATOR . '/components/com_socialbacklinks/loader.php' );
		SBLoader::instantiate( );
	}

	/**
	 * Do something onAfterRoute
	 * @return void
	 */
	public function onAfterRoute( )
	{	
		// Check requirements for correct component work
		$helper = new SBHelpersRequirements( );
		if ( !$helper->check( ) ) {
			return true;
		}

		// Do not re-trigger a sync if we are trying to rich the sync controller
		if (
		(@$_SERVER['LOCAL_ADDR'] == @$_SERVER['REMOTE_ADDR'] || @$_SERVER['SERVER_ADDR'] == @$_SERVER['REMOTE_ADDR']) // local query
		|| 
		(JRequest::getString('option', '') == 'com_socialbacklinks' && JRequest::getString('task','') == 'sync') // sync query
		) {
			return true;
		}

		// Check periodicity of articles posts
		if ( !SBHelpersSync::isNeedSync() ) {
			return true;
		}

		// Trigger an asynchronous sync
		SBHelpersSync::asynchronousCall();
	}

}
