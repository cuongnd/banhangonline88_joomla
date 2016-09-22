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

class EasyBlogTableTwitterMicroblog extends EasyBlogTable
{
	var $id_str		= null;
	var $oauth_id	= null;
	var $post_id	= null;
	var $created	= null;
	var $tweet_author	= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db ){
		parent::__construct( '#__easyblog_twitter_microblog' , 'id_str' , $db );
	}

	function load($id = null, $reset = false )
	{
	    $db		= $this->getDBO();

	    $query  = 'select * FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( $this->_tbl );
	    $query  .= ' where `id_str`= ' . $db->Quote( $id );

	    $db->setQuery( $query );

	    $result = $db->loadAssoc();
		return parent::bind( $result );
	}

	public function loadByPostId( $id )
	{
	    $db		= $this->getDBO();

	    $query  = 'select * FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( $this->_tbl );
	    $query  .= ' where `post_id`= ' . $db->Quote( $id );

	    $db->setQuery( $query );

	    $result = $db->loadAssoc();

		return parent::bind( $result );
	}

	public function store( $updateNulls = false )
	{
		$db		= $this->getDBO();
		$query	= 'SELECT COUNT(1) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( $this->_tbl ) . ' '
				. 'WHERE `id_str`=' . $db->Quote( $this->id_str );
		$db->setQuery( $query );

		if( $db->loadResult() )
		{
			return $db->updateObject( $this->_tbl, $this, $this->_tbl_key );
		}
		return $db->insertObject( $this->_tbl, $this, $this->_tbl_key );
	}
}
