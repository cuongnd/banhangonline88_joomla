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

class EasyBlogTableFeedHistory extends EasyBlogTable
{
	// Just for JTable to work
	var $id				= null;

	// This is a foreign key to easyblog_feeds.id
	var $feed_id		= null;

	// This is a foreign key to easyblog_post.id
	var $post_id		= null;

	// uid of the feed - feed url.
	var $uid			= null;

	// DateTime value of creation date
	var $created		= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db )
	{
		parent::__construct( '#__easyblog_feeds_history' , 'id' , $db );
	}

	function isExists( $feedid, $uid )
	{
	    $db = EasyBlogHelper::db();

	    $query  = 'select count(1) from `#__easyblog_feeds_history`';
	    $query  .= ' where `feed_id` = ' . $db->Quote($feedid);
	    $query  .= ' and `uid` = ' . $db->Quote($uid);

	    $db->setQuery($query);
	    $result = $db->loadResult();

	    return ( empty($result) ) ? false : true ;
	}

}
