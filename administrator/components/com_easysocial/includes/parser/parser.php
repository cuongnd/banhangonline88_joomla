<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

/**
 * Parser class to fix compatibility issues
 * between Joomla 1.5, 1.6 and 2.5
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialParser
{
	/**
	 * This is the Joomla helper object.
	 * @var SocialParserJoomla15|SocialParserJoomla25
	 */
	private $helper		= null;

	// private $xmlElement = null;
	// private $data       = null;

	/**
	 * Class constructor
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 */
	public function __construct()
	{
		// Determine the code name
		$name		= FD::getInstance( 'Version' )->getCodename();

		$file 		= dirname( __FILE__ ) . '/helpers/' . strtolower( $name ) . '.php';

		// If helper is not exist, we need to prevent any fatal errors.
		if( !JFile::exists( $file ) )
		{
			return;
		}

		require_once( $file );

		$className 		= 'SocialParser' . ucfirst( $name );

		$this->helper	= new $className();
	}

	/**
	 * This class can only be created every time.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	SocialParser
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function factory()
	{
		return new self();
	}

	/**
	 * Loads the content from a file or a string.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The file path or a string.
	 * @return	bool	The loading state.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function load( $item )
	{
		$contents		= $item;

		if( is_file( $item ) )
		{
			jimport( 'joomla.filesystem.file' );

			$contents 	= JFile::read( $item );
		}

		// Call the helper to load the items.
		$state	= $this->helper->load( $contents );

		if( !$state )
		{
			return false;
		}

		return $this;
	}

	/**
	 * Proxy function to call the xml helper object.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The key.
	 *
	 */
	public function __get( $key )
	{
		return ( isset( $this->helper->$key ) ) ? $this->helper->$key : false;
	}

	/**
	 * Proxy function to call the xml helper object methods.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The key.
	 *
	 */
	public function __call( $method , $args = array() )
	{
	    return call_user_func_array( array( $this->helper , $method ) , $args );
	}
}
