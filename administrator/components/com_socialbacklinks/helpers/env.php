<?php
/**    
 * SocialBacklinks Environment helper
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

jimport( 'joomla.filesystem.file' );

/**
 * SocialBacklinks Env helper class
 * 
 * @static
 */
class SBHelpersEnv extends JObject
{
	/**
	 * Stores current work mode of component
	 * 
	 * @var string
	 */
	protected static $_mode = null;
	
	/**
	 * Stores suffixes for files in different mode
	 * 
	 * @var array
	 */
	protected static $_mode_suffix = array( 'dev' => '-uncompressed', 'production' => '' );
	
	/**
	 * Sets work mode for component
	 *
	 * @param	sting	$mode
	 * 
	 * @return 	void
	 */
	public static function setMode( $mode = 'dev' )
	{
		if ( !array_key_exists( $mode, self::$_mode_suffix ) )
		{
			throw new Exception( 'Not correct param value' );
		}
		
		self::$_mode = $mode;
	}
	
	/**
	 * Adds suffix for media file if it need and adds it to document
	 *
	 * @param	$file	A file name
	 * 
	 * @return 	string
	 */
	public static function getMediaFile( $file )
	{
		$name = JFile::stripExt( $file );
		$ext = JFile::getExt( $file );
		
		$file = $name . self::$_mode_suffix[self::$_mode] . '.' . $ext;
		
		return $file;
	}
	
}
