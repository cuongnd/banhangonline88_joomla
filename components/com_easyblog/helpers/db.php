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

class EasyBlogDBHelper
{
	public $helper		= null;

	public function __construct()
	{
		$version 	= EasyBlogHelper::getJoomlaVersion();
		$className	= 'EasyBlogDBJoomla15';

		if( $version >= '2.5' )
		{
			$className 	= 'EasyBlogDBJoomla30';
		}
		
		$this->helper 	= new $className();
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

		return call_user_func_array( array( $this->helper , $method ) , $refArray );
	}
}

class EasyBlogDbJoomla15
{
	public $db 		= null;

	public function __construct()
	{
		$this->db	= JFactory::getDBO();
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

		return call_user_func_array( array( $this->db , $method ) , $refArray );
	}
}


class EasyBlogDbJoomla30
{
	public $db 		= null;

	public function __construct()
	{
		$this->db	= JFactory::getDBO();
	}

	public function loadResultArray()
	{
		return $this->db->loadColumn();
	}
	
	public function getEscaped( $str , $extra = false )
	{
		return $this->db->escape( $str , $extra );
	}

	public function nameQuote( $str )
	{
		return $this->db->quoteName( $str );
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

		return call_user_func_array( array( $this->db , $method ) , $refArray );
	}
}