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

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'model.php' );

class EasyBlogModelRatings extends EasyBlogModel
{
	/**
	 * Tag total
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Tag data array
	 *
	 * @var array
	 */
	var $_data = null;

	function __construct()
	{
		parent::__construct();


		$mainframe	= JFactory::getApplication();

		$limit			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.tags.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart		= JRequest::getInt('limitstart', 0, '' );

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	public function hasVoted( $uid , $type , $userId )
	{
		$db		= EasyBlogHelper::db();
		$query	= 'SELECT COUNT(1) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_ratings' ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'uid' ) . '=' . $db->Quote( $uid ) . ' '
				. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'type' ) . '=' . $db->Quote( $type ) . ' '
				. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'created_by' ) . '=' . $db->Quote( $userId );
		$db->setQuery( $query );

		$rating	= $db->loadResult();
		$rating	= round( $rating );

		return $rating;
	}

	public function getRatingValues( $uid , $type )
	{
		$db		= EasyBlogHelper::db();
		$query	= 'SELECT AVG(value) AS ratings, COUNT(1) AS total FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_ratings' ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'uid' ) . '=' . $db->Quote( $uid ) . ' '
				. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'type' ) . '=' . $db->Quote( $type );

		$db->setQuery( $query );

		$rating				= $db->loadObject();
		$rating->ratings	= round( $rating->ratings );

		return $rating;
	}

	public function getRatingUsers( $uid , $type , $limit = 5)
	{
		$db		= EasyBlogHelper::db();

		$query	= 'SELECT COUNT(' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'created_by' ) . ') AS times, ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'created_by' ) . ' , ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'created' ) . ' '
				. 'FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_ratings' ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'uid' ) . ' = ' . $db->Quote( $uid ) . ' '
				. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'type' ) . ' = ' . $db->Quote( $type ) . ' '
				. 'GROUP BY ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'created_by' ) . ' '
				. 'LIMIT 0,' . $limit;

		$db->setQuery( $query );

		$result	= $db->loadObjectList();
		return $result;
	}
}
