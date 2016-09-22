<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'table.php' );

class EasyBlogTableAclFilter extends EasyBlogTable
{
	var $content_id		= null;
	var $disallow_tags	= null;
	var $disallow_attributes	= null;
	var $type 			= null;

	function __construct(& $db )
	{
		parent::__construct( '#__easyblog_acl_filters' , 'content_id' , $db );
	}

	public function load( $id = null , $type = '' )
	{
		$db 	= EasyBlogHelper::db();

		$query	= 'SELECT * FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( $this->_tbl ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'content_id' ) . '=' . $db->Quote( $id ) . ' '
				. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'type' ) . '=' . $db->Quote( $type );

		$db->setQuery( $query );
		$result = $db->loadObject();

		if( !$result )
		{
			return false;
		}
		
		return parent::bind( $result );
	}

	public function store($updateNulls = false)
	{
		$db 	= EasyBlogHelper::db();

		// Test if record exists
		$query 	= 'SELECT COUNT(1) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( $this->_tbl ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'content_id' ) . '=' . $db->Quote( $this->content_id ) . ' '
				. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'type' ) . '=' . $db->Quote( $this->type );
		$db->setQuery( $query );
		$exists = $db->loadResult();

		if( !$exists )
		{
			$obj 	= new stdclass();
			$obj->content_id 	= $this->content_id;
			$obj->disallow_tags	= $this->disallow_tags;
			$obj->disallow_attributes	= $this->disallow_attributes;
			$obj->type	= $this->type;

			return $db->insertObject( $this->_tbl , $obj );
		}

		$query	= 'UPDATE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( $this->_tbl ) . ' SET '
				. EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'content_id' ) . '=' . $db->Quote( $this->content_id ) . ','
				. EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'disallow_tags' ) . '=' . $db->Quote( $this->disallow_tags ) . ','
				. EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'disallow_attributes' ) . '=' . $db->Quote( $this->disallow_attributes ) . ','
				. EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'type' ) . '=' . $db->Quote( $this->type ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'content_id' ) . '=' . $db->Quote( $this->content_id ) . ' '
				. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'type' ) . '=' . $db->Quote( $this->type );
		$db->setQuery( $query );
		return $db->Query();
	}
}
