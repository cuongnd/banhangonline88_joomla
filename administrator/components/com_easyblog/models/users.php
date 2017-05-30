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

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'parent.php' );

class EasyBlogModelUsers extends EasyBlogModelParent
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

	var $_isBrowse   = null;

	/**
	 * Category data array
	 *
	 * @var array
	 */
	var $_data = null;

	function __construct()
	{
		parent::__construct();

		$this->_isBrowse = false;


		$mainframe	= JFactory::getApplication();

		$limit			= $mainframe->getUserStateFromRequest( 'com_easyblog.users.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart		= JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to get the total nr of the categories
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{

			$query = $this->_buildQuery( $this->_isBrowse );
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
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	function _buildQuery( $isBrowse = false )
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildQueryWhere( $isBrowse );
		$orderby	= $this->_buildQueryOrderBy();
		$db			= EasyBlogHelper::db();

		if( $isBrowse )
		{
	 		$query	= 'SELECT a.*, b.`content_id` AS `featured` FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__users' ) . ' AS a '
	 				. 'LEFT JOIN ' . $db->nameQuote( '#__easyblog_featured' ) . ' AS b '
					. 'ON a.`id` = b.`content_id` AND b.`type`=' . $db->Quote( 'blogger' ) . ' '
	 				. $where . ' '
	 				. $orderby;
 		}
 		else
 		{

			$query	= 'SELECT a.*, b.`content_id` AS `featured` FROM (';
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

			$query  .= ' ) as a';
			$query	.= ' LEFT JOIN ' . $db->nameQuote( '#__easyblog_featured' ) . ' AS b ';
			$query	.= ' ON a.`id` = b.`content_id` AND b.`type`=' . $db->Quote( 'blogger' );
			$query	.= $where;
			$query	.= $orderby;
		}

		return $query;
	}



	function _buildQueryWhere( $isBrowse = false)
	{
		$mainframe			= JFactory::getApplication();
		$db					= EasyBlogHelper::db();

		$filter_state 		= $mainframe->getUserStateFromRequest( 'com_easyblog.users.filter_state', 'filter_state', '', 'word' );
		$search 			= $mainframe->getUserStateFromRequest( 'com_easyblog.users.search', 'search', '', 'string' );
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
			$where[] = ' (LOWER( name ) LIKE \'%' . $search . '%\') OR (LOWER( username ) LIKE \'%' . $search . '%\') ';
		}

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	function _buildQueryGroupBy()
	{

		$groupby 	= ' GROUP BY a.id';

		return $groupby;
	}

	function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easyblog.users.filter_order', 		'filter_order', 	'a.id', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easyblog.users.filter_order_Dir',	'filter_order_Dir',	'asc', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	function getUsers( $isBrowse = false )
	{
		$this->_isBrowse = $isBrowse;

		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query	= $this->_buildQuery( $isBrowse );

			$pg		= $this->getPagination();
			$this->_data = $this->_getList($query, $pg->limitstart, $pg->limit);
		}

		return $this->_data;
	}


	/**
	 * Method to publish or unpublish categories
	 *
	 * @access public
	 * @return array
	 */
	function publish( &$categories = array(), $publish = 1 )
	{
		if( count( $categories ) > 0 )
		{
			$db		= EasyBlogHelper::db();

			$tags	= implode( ',' , $categories );

			$query	= 'UPDATE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_category' ) . ' '
					. 'SET ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote( $publish ) . ' '
					. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'id' ) . ' IN (' . $tags . ')';
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
