<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

class EasyBlogRegistry
{
	public $registry 	= null;

	public function __construct( $contents = '' )
	{
		$this->registry 	= new JRegistry();

		if( $contents )
		{
			$this->load( $contents );
		}
	}

	public function bind( $data )
	{
		if( method_exists( $this->registry , 'bind' ) )
		{
			return $this->bind( $data );
		}

		return $this->bindData( $data );
	}

	public function load( $strData )
	{
		$version 	= EasyBlogHelper::getJoomlaVersion();
		
		if( $version >= '1.6' )
		{
			$this->registry->loadString( $strData );
		}
		else
		{
			$this->registry->loadINI( $strData , '' );
		}
	}

	public function get( $key , $default = null )
	{
		if( EasyBlogHelper::getJoomlaVersion() >= '2.5' )
		{
			return $this->registry->get( $key , $default );
		}

		return $this->registry->getValue( $key , $default );
	}

	public function set( $key , $value )
	{
		if( EasyBlogHelper::getJoomlaVersion() >= '2.5' )
		{
			return $this->registry->set( $key , $value );
		}

		return $this->registry->setValue( $key , $value );
	}

	public function __call( $method , $args )
	{
		$refArray	= array();

		if( $args )
		{
			foreach( $args as &$arg )
			{
				$refArray[]	=& $arg;
			}
		}
		return call_user_func_array( array( $this->registry , $method ) , $refArray );
	}

}