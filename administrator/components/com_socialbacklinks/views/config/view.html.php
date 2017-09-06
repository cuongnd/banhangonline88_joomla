<?php
/**    
 * SocialBacklinks config view
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
 * SocialBacklinks Config view class
 */
class SBViewsConfig extends SBViewsBase
{
	/**
	 * Assigns to layout the list of basic settings
	 * @return void
	 */
	protected function _default( )
	{
		$this->assign( SBHelpersConfig::getBasicConfig( ) );
	}
	
	/**
	 * Assigns to layout the status message of current configuration
	 * @return void
	 */
	protected function _block()
	{
		// Get message of configuration status
		$config_status = SBHelpersConfig::buildStatusMsg( SBHelpersConfig::getBasicConfig( ) );
		$this->assignRef( 'config_status', $config_status );
	}
	
}
