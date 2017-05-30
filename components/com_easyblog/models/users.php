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

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'model.php' );

class EasyBlogModelUsers extends EasyBlogModel
{
	/**
	 * Category total
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
	 * Category data array
	 *
	 * @var array
	 */
	var $_data = null;
	
	function __construct()
	{
		parent::__construct();
		
		$mainframe	= JFactory::getApplication();
		
		$limit		= ($mainframe->getCfg('list_limit') == 0) ? 5 : $mainframe->getCfg('list_limit');				
	    $limitstart = JRequest::getInt('limitstart', 0, 'REQUEST');
	    
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);		

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to get the total nr of the categories
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal( $bloggerOnly = false )
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery( $bloggerOnly );
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}
	
	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination( $bloggerOnly = false )
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination	= EasyBlogHelper::getPagination( $this->getTotal( $bloggerOnly ), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	function _buildQuery( $bloggerOnly = false )
	{
		// Get the WHERE and ORDER BY clauses for the query
 		$where		= $this->_buildQueryWhere();
 		$orderby	= $this->_buildQueryOrderBy();
 		
		$db			= EasyBlogHelper::db();

		if( $bloggerOnly )
		{
			$query	= 'SELECT x.* FROM (';
			$query	.= ' ( select a.* FROM `#__users` AS `a`';
			if(EasyBlogHelper::getJoomlaVersion() >= '1.6'){
				$query	.= '  INNER JOIN `#__user_usergroup_map` AS `d` ON a.`id` = d.`user_id`';
			} else {
				$query	.= '  INNER JOIN `#__core_acl_aro` AS `c` ON a.`id` = c.`value`';
				$query	.= '    AND c.`section_value` = ' . $db->Quote('users');
				$query	.= '  INNER JOIN `#__core_acl_groups_aro_map` AS `d` ON c.`id` = d.`aro_id`';
			}
			$query	.= '  INNER JOIN `#__easyblog_acl_group` AS `e` ON d.`group_id`  = e.`content_id`';
			$query	.= '    AND e.`type` = ' . $db->Quote('group') . ' AND e.`status` = 1';
			$query	.= '  INNER JOIN `#__easyblog_acl` as `f` ON e.`acl_id` = f.`id`';
			$query	.= '    AND f.`action` = ' . $db->Quote('add_entry');
			$query .= $where;

			$query 	.= ' )';

			$query	.= ' UNION ';
			$query	.= ' (SELECT a1.* FROM `#__users` AS `a1`';
			$query	.= '  INNER JOIN `#__easyblog_acl_group` AS `c1` ON a1.`id`  = c1.`content_id`';
			$query	.= '    AND c1.`type` = ' . $db->Quote('assigned') . ' AND c1.`status` = 1';
			$query	.= '  INNER JOIN `#__easyblog_acl` as `d1` ON c1.`acl_id` = d1.`id`';
			$query	.= '    AND d1.`action` = ' . $db->Quote('add_entry');
			$query .= $where;
			$query 	.= ' )';

			$query  .= ' ) as x';
		}
		else
		{
			$query	= 'SELECT * FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__users' );
			$query .= $where;
		}

		$query .= $orderby;

		return $query;
	}

	function _buildQueryWhere( $tblNS = '')
	{
		$mainframe			= JFactory::getApplication();
		$db					= EasyBlogHelper::db();
		
		//$filter_state		= $mainframe->getUserStateFromRequest( 'com_easyblog.users.filter_state', 'filter_state', '', 'word' );
		$filter_state 		= JRequest::getVar('filter_state', 'P', 'REQUEST');
		
		//$search 			= $mainframe->getUserStateFromRequest( 'com_easyblog.users.search', 'search', '', 'string' );
		$search 			= JRequest::getVar('search', '', 'REQUEST');		
		$search 			= $db->getEscaped( trim(JString::strtolower( $search ) ) );

		$where = array();

		if ( $filter_state )
		{
			if ( $filter_state == 'P' )
			{
				$where[] = EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'block' ) . '=' . $db->Quote( '0' );
			}
			else if ($filter_state == 'U' )
			{
				$where[] = EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'block' ) . '=' . $db->Quote( '1' );
			}
		}

		if ($search)
		{
			$where[] = ' LOWER( name ) LIKE \'%' . $search . '%\' ';
		}

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		//$filter_order		= $mainframe->getUserStateFromRequest( 'com_easyblog.users.filter_order', 		'filter_order', 	'a.id', 'cmd' );
		//$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easyblog.users.filter_order_Dir',	'filter_order_Dir',	'asc', 'word' );
		$filter_order		= JRequest::getVar('filter_order', 'id', 'REQUEST');
		$filter_order_Dir	= JRequest::getVar('filter_order_Dir', 'asc', 'REQUEST');
		
		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}
	
	function _buildQueryLimit()
	{
		$limit		= $this->getState('limit');
		$limitstart = $this->getState('limitstart');
		$limitSQL	= ' LIMIT ' . $limitstart . ',' . $limit;
		
		return $limitSQL;
	}
	
	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	function getUsers( $bloggerOnly = false )
	{
		$db	= EasyBlogHelper::db();
		
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query	= $this->_buildQuery( $bloggerOnly );
			$query .= $this->_buildQueryLimit();
			
			$db->setQuery($query);
			$rows = $db->loadObjectList();		
		}

		return $rows;
	}

	/**
	 * Determines if the permalink exists.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	Permalink
	 * @param	int 	The current user id.
	 * @return	bool	True if exists , false otherwise
	 */
	function permalinkExists( $permalink , $id )
	{
		$db 		= EasyBlogHelper::db();
		$query 		= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__easyblog_users' );
		$query 		.= ' WHERE ' . $db->nameQuote( 'permalink' ) . '=' . $db->Quote( $permalink );
		$query 		.= ' AND ' . $db->nameQuote( 'id' ) . '!=' . $db->Quote( $id );

		$db->setQuery( $query );

		$exists 	= $db->loadResult() >= 1;

		return $exists;
	}

	/**
	 * Returns the number of blog entries created within this category.
	 * 
	 * @return int	$result	The total count of entries.
	 * @param boolean	$published	Whether to filter by published.
	 */
	function getUsedCount( $categoryId , $published = false )
	{
		$db			= EasyBlogHelper::db();
		
		$query	= 'SELECT COUNT(1) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_post' ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'category_id' ) . '=' . $db->Quote( $categoryId );
		
		if( $published )
		{
			$query	.= ' AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote( 1 );
		}

		$db->setQuery( $query );
		
		$result	= $db->loadResult();
		
		return $result;
	}
}