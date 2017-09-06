<?php
/**
 * SocialBacklinks adapter for networks plugins
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
 * Adapter for networks plugins
 */
class SBPluginsAdaptersNetwork extends SBPluginsAdaptersAbstract
{
	/**
	 * Registers plugin in the adapter
	 * @param  SBPluginsInterface $plugin
	 * @return bool Whether plugin has been successfully registered
	 */
	public function register( $plugin )
	{
		if ( $plugin instanceof SBPluginsNetworksInterface ) {
			$this->_r->offsetSet( $plugin->getAlias( ), $plugin );
			return true;
		}
		return false;
	}

}
