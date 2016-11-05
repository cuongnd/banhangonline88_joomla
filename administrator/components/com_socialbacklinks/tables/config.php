<?php
/**
 * SocialBacklinks config table
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
 * SocialBacklinks config table class
 */
class SBTablesConfig extends SBTablesBase
{
	/**
	 * Object constructor
	 */
	public function SBTablesConfig( &$db )
	{
		$tbl_key = 'socialbacklinks_config_id';
		$tbl_suffix = 'configs';
		$name = 'socialbacklinks';
		
		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );
	}
	
}
