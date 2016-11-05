<?php
/**
 * SocialBacklinks Plugins Bootstrap
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

 jimport( 'joomla.plugin.helper' );
 
/**
 * Plugins Bootstrap
 * @static
 */
class SBPluginsBootstrap
{
	/**
	 * Flag whether bootsrap was run
	 * @var bool
	 */
	private static $_is_run = false;

	/**
	 * Runs plugins proccesses
	 * @static
	 * @return void
	 */
	public static function run( )
	{
		if ( self::$_is_run )
			return;
		
		self::$_is_run = true;
		SBPlugin::instantiate( );
		
		JPluginHelper::importPlugin('socialbacklinks');
		
		JDispatcher::getInstance( )->trigger( 'onSBPluginRegister' );
	}

}
