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

class EasyBlogTableTrackbackSent extends EasyBlogTable
{
	var $id			= null;
	var $post_id	= null;
	var $url		= null;
	var $sent		= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db )
	{
		parent::__construct( '#__easyblog_trackback_sent' , 'id' , $db );
	}

	function load( $id = null, $isUrl = false , $postId = '' )
	{
		if( !$isUrl )
		{
			return parent::load( $id );
		}
		else
		{
			$url = $id;
		}

		$db		= EasyBlogHelper::db();

		$query	= 'SELECT * FROM #__easyblog_trackback_sent WHERE `url`=' . $db->Quote( $url ) . ' AND `post_id`=' . $db->Quote( $postId );
		$db->setQuery( $query );
		$result	= $db->loadObject();

		if( $result )
		{
			$this->id		= $result->id;
			$this->post_id	= $result->post_id;
			$this->url		= $result->url;
			$this->sent		= $result->sent;

			return true;
		}
		return false;
	}

	function sent( $postId, $url )
	{
		$db		= EasyBlogHelper::db();

		$query	= 'SELECT * FROM #__easyblog_trackback_sent WHERE `url`=' . $db->Quote( $url ) . ' AND `post_id` = ' . $db->Quote( $postId ) . ' AND `sent` = `1`';
		$db->setQuery( $query );
		$result	= $db->loadObject();

		if( $result )
		{
			$this->id		= $result->id;
			$this->post_id	= $result->post_id;
			$this->url		= $result->url;
			$this->sent		= $result->sent;

			return true;
		}
		return false;
	}

	public function markSent()
	{
		$this->sent			= 1;
		$this->store();
	}
}
