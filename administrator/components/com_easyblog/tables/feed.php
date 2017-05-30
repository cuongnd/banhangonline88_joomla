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

class EasyBlogTableFeed extends EasyBlogTable
{
	var $id				= null;
	var $title			= null;
	var $url			= null;
	var $interval		= 5;
	var $cron			= true;
	var $item_creator	= null;
	var $item_category	= null;
	var $item_frontpage	= true;
	var $item_published	= true;
	var $item_get_fulltext	= false;

	// Specify whether to set as introtext or main body
	var $item_content	= null;

	// Determines if we should show the original author
	var $author			= null;

	var $params			= null;
	var $published		= true;
	var $created		= null;
	var $last_import	= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db )
	{
		parent::__construct( '#__easyblog_feeds' , 'id' , $db );
	}

	public function store( $updateNulls = false )
	{
		if( !$this->created )
		{
			$this->created	= EasyBlogHelper::getDate()->toMySQL();
		}

		return parent::store( $updateNulls );
	}

	function getCategoryName()
	{
	    $db = EasyBlogHelper::db();

	    if( ! empty($this->item_category) )
	    {
	        $query  = 'SELECT `title` FROM `#__easyblog_category` WHERE `id` = ' . $db->Quote( $this->item_category );
	        $db->setQuery( $query );
	        return $db->loadResult();
		}

		return '';
	}
}
