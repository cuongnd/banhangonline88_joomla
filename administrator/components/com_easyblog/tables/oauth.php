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

class EasyBlogTableOauth extends EasyBlogTable
{
	var $id			= null;
	var $user_id	= null;
	var $type		= null;
	var $auto		= null;
	var $request_token	= null;
	var $access_token	= null;
	var $message	= null;
	var $created	= null;
	var $private	= null;
	var $params		= null;
	var $system		= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct( $db )
	{
		parent::__construct( '#__easyblog_oauth' , 'id' , $db );
	}

	function loadSystemByType( $type )
	{
	    $db		= $this->getDBO();

		$query	= 'SELECT ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'id' ) . ' FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( $this->_tbl ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'type' ) . '=' . $db->Quote( $type ) . ' '
				. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'system' ) . '=' . $db->Quote( 1 );

	    $db->setQuery( $query );

	    $result = $db->loadResult();

	    if(empty($result))
	    {
	        $this->id	= 0;
			$this->type	= $type;
	        return $this;
	    }

		return parent::load($result);
	}

	function loadByUser( $id , $type )
	{
	    $db		= $this->getDBO();

		$query	= 'SELECT * FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( $this->_tbl ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'user_id' ) . '=' . $db->Quote( $id ) . ' '
				. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'type' ) . '=' . $db->Quote( $type ) . ' '
				. 'LIMIT 1';
	    $db->setQuery( $query );

	    $result = $db->loadObject();

	    if( !$result )
	    {
	    	return false;
	    }

	    return parent::bind( $result );
	}

	function store($updateNulls = false)
	{
		$db		= $this->getDBO();
		$query	= 'SELECT COUNT(1) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( $this->_tbl ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'user_id' ) . '=' . $db->Quote( $this->user_id ) . ' '
				. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'type' ) . '=' . $db->Quote( $this->type );
		$db->setQuery( $query );

		if( $db->loadResult() )
		{
			return $db->updateObject( $this->_tbl, $this, $this->_tbl_key, $updateNulls );
		}

		return $db->insertObject( $this->_tbl, $this, $this->_tbl_key );
	}

	/**
	 * Retrieves a key value from the access token object.
	 */
	public function getAccessTokenValue( $key )
	{
		$param 	= EasyBlogHelper::getRegistry( $this->access_token );

		return $param->get( $key );
	}

	function getMessage()
	{
		$config		= EasyBlogHelper::getConfig();
		$message	= !empty( $this->message ) ? $this->message : $config->get('integrations_' . $this->type . '_default_messsage' );
		return $message;
	}

	/*
	 * Determines whether we've shared the respective blog entry
	 * to the consumer site or not.
	 *
	 * @param	int		$blogId	The respective blog id.
	 * @return	boolean	True if entry is shared previously.
	 */
	public function isShared( $blogId , $system = false )
	{
		$db		= EasyBlogHelper::db();
		$query	= 'SELECT COUNT(1) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_oauth_posts' ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'oauth_id' ) . '=' . $db->Quote( $this->id ) . ' '
				. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'post_id' ) . '=' . $db->Quote( $blogId );

	    $db->setQuery( $query );
		$result = $db->loadResult();

		return $result > 0;
	}

	/*
	 * Get's the last shared date
	 *
	 * @param	int		$blogId	The respective blog id.
	 * @return	boolean	True if entry is shared previously.
	 */
	public function getSharedDate( $blogId )
	{
		$db		= EasyBlogHelper::db();
		$query	= 'SELECT ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'sent' ) . ' FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_oauth_posts' ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'oauth_id' ) . '=' . $db->Quote( $this->id ) . ' '
				. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'post_id' ) . '=' . $db->Quote( $blogId );

	    $db->setQuery( $query );
		$result = $db->loadResult();

		return EasyBlogDateHelper::dateWithOffSet( $result )->toMySQL();
	}
}
