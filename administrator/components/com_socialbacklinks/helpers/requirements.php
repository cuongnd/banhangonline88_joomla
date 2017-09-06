<?php
/**    
 * SocialBacklinks Requirements helper
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
 * SocialBacklinks Requirements helper class
 */
class SBHelpersRequirements extends JObject
{
	/**
	 * Version of the component
	 * 
	 * @var string
	 */
	private static $_version = '1.8.0';
	
	/**
	 * Returns the current version of component
	 * 
	 * @return string
	 */
	public static function getVersion( )
	{
		return self::$_version;
	}
	
	/**
	 * Checks required options to install component
	 * 
	 * @return boolean
	 */
	public function check( )
	{
		$success = true;
		
		if ( !$this->checkCURL( ) ) {
			$this->setError( JText::_( 'SB_CURL_ERROR' ) );
			$success = false;
		}
		
		return $success;
	}
	
	/**
	 * Checks CURL extension
	 * 
	 * @return boolean
	 */
	private function checkCURL( )
	{
		return extension_loaded( 'curl' );
	}
	
	/**
	 * Change status of selected Plugin
	 * 
	 * @return boolean
	 */
	public static function changePluginStatus( $status, $name, $type = 'system' )
	{
		$db = JFactory::getDBO( );
		
		$query = 'UPDATE `#__extensions` SET `enabled` = ' . (int) $status 
				.' WHERE `folder` = ' . $db->quote( $type ) 
				.' AND `element` = ' . $db->quote( $name )
				.' AND `type` = ' . $db->quote( 'plugin' );
		
		$db->setQuery( $query );
		return $db->execute( );
	}
	
	/**
	 * Returns list of plugins status
	 * 
	 * @return array
	 */
	public static function getPluginsStatusList( )
	{
		$query = 'SELECT * FROM `#__extensions`' 
				." WHERE `element` = 'sbsynchronizer'"
				." AND `type` = 'plugin'";
		
		$db = JFactory::getDBO( );
		$db->setQuery( $query );
		
		return $db->loadObjectList( 'element' );
	}
	
}
