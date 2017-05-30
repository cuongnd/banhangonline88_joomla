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

class SocialRequest
{
	public function __construct()
	{
	}

	public function init()
	{
	    return $this;
	}

	public function debug()
	{
	}

	/**
	 * Retrieves key values from the $_GET object.
	 *
	 * @param	string	$key	Specify the key to retrieve from $_GET request (optional)
	 * @param	string	$default	Specify the default value if key isn't found.
	 *
	 * @return	mixed	$value	Returns the value from the $_GET object.
	 **/
	public function get( $key = '' , $value = '' )
	{
		if( empty( $key ) )
		{
			return JRequest::get( 'GET' );
		}

		return JRequest::getVar( $key , $value , 'GET' );
	}

	/**
	 * Retrieves key values from the $_POST object.
	 *
	 * @param	string	$key	Specify the key to retrieve from $_POST request (optional)
	 * @param	string	$default	Specify the default value if key isn't found.
	 *
	 * @return	mixed	$value	Returns the value from the $_POST object.
	 **/
	public function post( $key = '' , $value = '' )
	{
		if( empty( $key ) )
		{
			return JRequest::get( 'POST' );
		}

		return JRequest::getVar( $key , $value , 'POST' );
	}

	/**
	 * Retrieves key values from the $_REQUEST object.
	 *
	 * @param	string	$key	Specify the key to retrieve from $_REQUEST request (optional)
	 * @param	string	$default	Specify the default value if key isn't found.
	 *
	 * @return	mixed	$value	Returns the value from the $_REQUEST object.
	 **/
	public function request( $key = '' , $value = '' )
	{
		if( empty( $key ) )
		{
			return JRequest::get( 'REQUEST' );
		}

		return JRequest::getVar( $key , $value , 'REQUEST' );
	}

	/**
	 * Retrieves the current method being accessed.
	 *
	 * @return	string	$value	Returns the current access type.
	 **/
	public function method()
	{
		return strtolower( JRequest::getMethod() );
	}

	public function command( $key , $default = '' , $type = 'REQUEST' )
	{
		return JRequest::getCmd( $key , $default , $type );
	}

	public function word( $key , $default = '' , $type = 'REQUEST' )
	{
		return JRequest::getWord( $key , $default , $type );
	}
}
