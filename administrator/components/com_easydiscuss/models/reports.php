<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelReports extends EasyDiscussAdminModel
{
	/**
	 * Blogs data array
	 *
	 * @var array
	 */
	protected $_data = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	protected $_pagination = null;

	/**
	 * Configuration data
	 *
	 * @var int	Total number of rows
	 **/
	protected $_total;

	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();

		$mainframe 	= JFactory::getApplication();

		//get the number of events from database
		$limit		= $mainframe->getUserStateFromRequest('com_easydiscuss.reports.limit', 'limit', $mainframe->getCfg('list_limit') , 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	function getReports()
	{
		if(empty($this->_data) )
		{
			$query			= $this->_buildQuery();

			$this->_data	= $this->_getList( $query, $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_data;
	}

	function _buildQuery()
	{
		$db			= DiscussHelper::getDBO();

		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildQueryWhere();
		$orderby	= $this->_buildQueryOrderBy();

		//$query	= 'SELECT a.*, COUNT(b.`post_id`) as `reportCnt`, b.`created` as `lastreport`, b.`reason`, b.`created_by` as `reporter`';
		$query	= 'SELECT a.*, b.`created` as `lastreport`, b.`reason`, b.`created_by` as `reporter`, ( select count( id ) from `#__discuss_reports` as x where x.`post_id` = b.`post_id` ) as `reportCnt`';
		$query	.= ' from `#__discuss_reports` as b';
		$query	.= '  inner join `#__discuss_posts` as a on a.`id` = b.`post_id`';

		$query	.= $where;
		$query  .= ' and b.`created` = ( select max( created ) from `#__discuss_reports` as x1 where x1.`post_id` = b.`post_id` )';

		$query	.= ' order by b.`id` desc';
		//$query  .= ' limit 1';

		return $query;
	}

	function _buildQueryWhere()
	{
		$mainframe		= JFactory::getApplication();
		$db				= DiscussHelper::getDBO();

		$filter_state	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.reports.filter_state', 'filter_state', '', 'word' );

		$search			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.reports.search', 'search', '', 'string' );
		$search			= $db->getEscaped( trim(JString::strtolower( $search ) ) );

		$where = array();

		$where[] = ' a.`isreport` = ' . $db->Quote('1');

		if ( $filter_state )
		{
			if ( $filter_state == 'P' )
			{
				$where[] = $db->nameQuote( 'a.published' ) . '=' . $db->Quote( '1' );
			}
			else if ($filter_state == 'U' )
			{
				$where[] = $db->nameQuote( 'a.published' ) . '=' . $db->Quote( '0' );
			}
		}

		if ($search)
		{
			$where[] = ' LOWER( a.`title` ) LIKE \'%' . $search . '%\' ';
		}

		$where 		= count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' ;

		return $where;
	}

	function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.reports.filter_order', 		'filter_order', 	'a.id', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.reports.filter_order_Dir',	'filter_order_Dir',	'DESC', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.', ordering';

		return $orderby;
	}

	/**
	 * Method to return the total number of rows
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		// Load total number of rows
		if( empty($this->_total) )
		{
			$this->_total	= $this->_getListCount( $this->_buildQuery() );
		}

		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the events
	 *
	 * @access public
	 * @return integer
	 */
	function &getPagination()
	{
		// Lets load the content if it doesn't already exist
		if ( empty( $this->_pagination ) )
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	function publish( $posts = array(), $publish = 1 )
	{
		if( count( $posts ) > 0 )
		{
			$db		= DiscussHelper::getDBO();

			$ids	= implode( ',' , $posts );

			$query	= 'UPDATE ' . $db->nameQuote( '#__discuss_posts' ) . ' '
					. 'SET ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( $publish ) . ' '
					. 'WHERE ' . $db->nameQuote( 'id' ) . ' IN (' . $ids . ')';
			$db->setQuery( $query );

			if( !$db->query() )
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			return true;
		}
		return false;
	}

	function getReasons( $postId )
	{
		$db		= DiscussHelper::getDBO();

		$query  = 'select * from `#__discuss_reports` where  `post_id` = ' . $db->Quote($postId);
		$query  .= ' order by `id` desc';

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	function removeReports($postId)
	{
		$db		= DiscussHelper::getDBO();

		$query  = 'DELETE FROM `#__discuss_reports` WHERE `post_id` = ' . $db->Quote($postId);
		$db->setQuery($query);

		if( $db->query() )
		{
			//now update post record
			$query	= 'UPDATE `#__discuss_posts` SET `isreport` = ' . $db->Quote('0');
			$query	.= ' , `published` = ' . $db->Quote('1');
			$query	.= ' WHERE `id` = ' . $db->Quote($postId);
			$db->setQuery($query);
			if(! $db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	function removePostReports($postId)
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'DELETE FROM `#__discuss_reports` WHERE `post_id` = ' . $db->Quote($postId);
		$db->setQuery($query);

		$db->Query();

		return true;
	}

	function deleteReplies($parent_id)
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'select `id` from `#__discuss_posts` where `parent_id` = ' . $db->Quote($parent_id);
		$db->setQuery($query);

		$result	= $db->loadObjectList();

		if(count($result) > 0)
		{
			for($i = 0; $i < count($result); $i++)
			{
				$obj = $result[$i];
				$this->removePostReports($obj->id);
			}
		}
		return true;
	 }

}
