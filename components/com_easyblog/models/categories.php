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

class EasyBlogModelCategories extends EasyBlogModel
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

		$limit			= $mainframe->getUserStateFromRequest( 'com_easyblog.categories.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart		= (int) JRequest::getVar('limitstart', 0, '', 'int');

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
			$query = $this->_buildQuery();
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
			$this->_pagination	= EasyBlogHelper::getPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildQueryWhere();
		$orderby	= $this->_buildQueryOrderBy();
		$db			= EasyBlogHelper::db();

		$query	= 'SELECT * FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_category' )
				. $where . ' '
				. $orderby;

		return $query;
	}

	function _buildQueryWhere()
	{
		$mainframe			= JFactory::getApplication();
		$db					= EasyBlogHelper::db();

		$filter_state 		= $mainframe->getUserStateFromRequest( 'com_easyblog.categories.filter_state', 'filter_state', '', 'word' );
		$search 			= $mainframe->getUserStateFromRequest( 'com_easyblog.categories.search', 'search', '', 'string' );
		$search 			= $db->getEscaped( trim(JString::strtolower( $search ) ) );

		$where = array();

		if ( $filter_state )
		{
			if ( $filter_state == 'P' )
			{
				$where[] = EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote( '1' );
			}
			else if ($filter_state == 'U' )
			{
				$where[] = EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote( '0' );
			}
		}

		if ($search)
		{
			$where[] = ' LOWER( title ) LIKE \'%' . $search . '%\' ';
		}

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easyblog.categories.filter_order', 		'filter_order', 	'lft', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easyblog.categories.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.', ordering';

		return $orderby;
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	function getData( $usePagination = true )
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			if($usePagination)
				$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			else
			    $this->_data = $this->_getList($query);
		}

		return $this->_data;
	}


	/**
	 * Method to publish or unpublish categories
	 *
	 * @access public
	 * @return array
	 */
	function publish( $categories = array(), $publish = 1 )
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

		//blog privacy setting
		$my = JFactory::getUser();
		if($my->id == 0)
		    $query .= ' AND `private` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);

		$db->setQuery( $query );

		$result	= $db->loadResult();

		return $result;
	}


	/**
	 * *********************************************************************
	 * These part of codes will used in dashboard categories.
	 * *********************************************************************
	 */


	function _buildQueryByBlogger( $bloggerId , $ordering = '' )
	{
		$db			= EasyBlogHelper::db();
	    $config 	= EasyBlogHelper::getConfig();
		$sortConfig = $config->get('layout_sorting_category','latest');

	    $query	= 	'select a.`id`, a.`title`, a.`alias`, a.`created` , a.`avatar` , count(b.`id`) as `post_count`';
		$query	.=  ' from `#__easyblog_category` as a';
		$query	.=  '    left join `#__easyblog_post` as b';
		$query	.=  '    on a.`id` = b.`category_id`';
		$query	.=  ' where a.`created_by` = ' . $db->Quote($bloggerId);
		$query	.=  ' group by (a.`id`)';

		if( !empty( $ordering ) )
		{
			$sortConfig	= $ordering;
		}

		switch( $sortConfig )
		{
			case 'count':
				$orderBy = ' ORDER BY `post_count` DESC';
				break;
			case 'alphabet' :
				$orderBy = ' ORDER BY a.`title` ASC';
				break;
			case 'ordering' :
				$orderBy = ' ORDER BY a.`ordering` ASC';
				break;
			case 'latest' :
			default	:
				$orderBy = ' ORDER BY a.`created` DESC';
				break;
		}
		$query  .= $orderBy;

		return $query;
	}

	function getCategoriesByBlogger( $bloggerId , $ordering = '' )
	{
	    $db = EasyBlogHelper::db();

	    $query  = $this->_buildQueryByBlogger( $bloggerId , $ordering );
	    $db->setQuery($query);

	    $result = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));

	    return $result;
	}

	function getPaginationByBlogger($bloggerId)
	{
		jimport('joomla.html.pagination');
		$this->_pagination	= EasyBlogHelper::getPagination( $this->getTotalByBlogger($bloggerId), $this->getState('limitstart'), $this->getState('limit') );		
		return $this->_pagination;
	}

	function getTotalByBlogger($bloggerId)
	{
		// Lets load the content if it doesn't already exist
		$query = $this->_buildQueryByBlogger($bloggerId);
		$total = $this->_getListCount($query);

		return $total;
	}

	function getParentCategories( $contentId , $type = 'all', $isPublishedOnly = false, $isFrontendWrite = false , $exclusion = array() )
	{
	    $db 	= EasyBlogHelper::db();
	    $my     = JFactory::getUser();
	    $config = EasyBlogHelper::getConfig();

		$sortConfig = $config->get('layout_sorting_category','latest');

	    $query	= 	'select a.`id`, a.`title`, a.`alias`, a.`private`';
		$query	.=  ' from `#__easyblog_category` as a';
		$query	.=  ' where a.parent_id = ' . $db->Quote('0');

		if($type == 'blogger')
		{
			$query	.=  ' and a.created_by = ' . $db->Quote($contentId);
		}
		else if($type == 'category')
		{
		    $query	.=  ' and a.`id` = ' . $db->Quote($contentId);
		}

		if( $isPublishedOnly )
		{
		    $query	.=  ' and a.`published` = ' . $db->Quote('1');
		}

		if( $isFrontendWrite )
		{
			$gid	= EasyBlogHelper::getUserGids();
			$gids   = '';
			if( count( $gid ) > 0 )
			{
			    foreach( $gid as $id)
			    {
			        $gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
			    }
			}

			$query .= ' and a.id not in (';
			$query .= ' select id from `#__easyblog_category` as c';
			$query .= ' where not exists (';
			$query .= '		select b.category_id from `#__easyblog_category_acl` as b';
			$query .= '			where b.category_id = c.id and b.`acl_id` = '. $db->Quote( CATEGORY_ACL_ACTION_SELECT );
			$query .= '			and b.type = ' . $db->Quote('group');
			$query .= '			and b.content_id IN (' . $gids . ')';
			$query .= '      )';
			$query .= ' and c.`private` = ' . $db->Quote( CATEGORY_PRIVACY_ACL );
			$query .= ' and c.`parent_id` = ' . $db->Quote( '0' );
			$query .= ')';
		}

		// @task: Process exclusion list.
		if( !empty( $exclusion ) )
		{
			$excludeQuery	= 'AND a.`id` NOT IN (';
			for( $i = 0 ; $i < count( $exclusion ); $i++ )
			{
				$id		= $exclusion[ $i ];

				$excludeQuery	.= $db->Quote( $id );

				if( next( $exclusion ) !== false )
				{
					$excludeQuery	.= ',';
				}
			}

			$excludeQuery	.= ')';

			$query			.= $excludeQuery;
		}

		switch($sortConfig)
		{
			case 'alphabet' :
				$orderBy = ' ORDER BY a.`title` ASC';
				break;
			case 'ordering' :
				$orderBy = ' ORDER BY a.`lft` ASC';
				break;
			case 'latest' :
			default	:
				$orderBy = ' ORDER BY a.`created` DESC';
				break;
		}

		$query  .= $orderBy;

	    $db->setQuery($query);
	    $result = $db->loadObjectList();

		return $result;
	}

	function getChildCategories($parentId , $isPublishedOnly = false, $isFrontendWrite = false , $exclusion = array() )
	{
	    $db 	= EasyBlogHelper::db();
	    $my     = JFactory::getUser();
	    $config = EasyBlogHelper::getConfig();

		$sortConfig = $config->get('layout_sorting_category','latest');

	    $query	= 	'select a.`id`, a.`title`, a.`alias`, a.`private`';
		$query	.=  ' from `#__easyblog_category` as a';
		$query	.=  ' where a.parent_id = ' . $db->Quote($parentId);

		if( $isPublishedOnly )
		{
		    $query	.=  ' and a.`published` = ' . $db->Quote('1');
		}

		if( $isFrontendWrite )
		{
			$gid	= EasyBlogHelper::getUserGids();
			$gids   = '';
			if( count( $gid ) > 0 )
			{
			    foreach( $gid as $id)
			    {
			        $gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
			    }
			}

			$query .= ' and a.id not in (';
			$query .= ' select id from `#__easyblog_category` as c';
			$query .= ' where not exists (';
			$query .= '		select b.category_id from `#__easyblog_category_acl` as b';
			$query .= '			where b.category_id = c.id and b.`acl_id` = '. $db->Quote( CATEGORY_ACL_ACTION_SELECT );
			$query .= '			and b.type = ' . $db->Quote('group');
			$query .= '			and b.content_id IN (' . $gids . ')';
			$query .= '      )';
			$query .= ' and c.`private` = ' . $db->Quote( CATEGORY_PRIVACY_ACL );
			$query .= ' and c.`parent_id` = ' . $db->Quote( $parentId );
			$query .= ')';
		}

		// @task: Process exclusion list.
		if( !empty( $exclusion ) )
		{
			$excludeQuery	= 'AND a.`id` NOT IN (';
			for( $i = 0 ; $i < count( $exclusion ); $i++ )
			{
				$id		= $exclusion[ $i ];

				$excludeQuery	.= $db->Quote( $id );

				if( next( $exclusion ) !== false )
				{
					$excludeQuery	.= ',';
				}
			}

			$excludeQuery	.= ')';

			$query			.= $excludeQuery;
		}
		
		switch($sortConfig)
		{
			case 'alphabet' :
				$orderBy = ' ORDER BY a.`title` ASC';
				break;
			case 'ordering' :
				$orderBy = ' ORDER BY a.`lft` ASC';
				break;
			case 'latest' :
			default	:
				$orderBy = ' ORDER BY a.`created` DESC';
				break;
		}

		$query  .= $orderBy;

	    $db->setQuery($query);
	    $result = $db->loadObjectList();

		return $result;
	}

	function getPrivateCategories()
	{
	    $db 	= EasyBlogHelper::db();

	    $query	= 	'select a.`id`';
		$query	.=  ' from `#__easyblog_category` as a';
		$query	.=  ' where a.`private` = ' . $db->Quote('1');

	    $db->setQuery($query);
	    $result = $db->loadObjectList();

		return $result;
	}

	function getChildCount( $categoryId , $published = false )
	{
		$db			= EasyBlogHelper::db();

		$query	= 'SELECT COUNT(1) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_category' ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'parent_id' ) . '=' . $db->Quote( $categoryId );

		if( $published )
		{
			$query	.= ' AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote( 1 );
		}

		$db->setQuery( $query );

		$result	= $db->loadResult();

		return $result;
	}

	function getCategoriesHierarchy()
	{
	    $db = EasyBlogHelper::db();

		$limit		= '10';
		$limitstart = $this->getState('limitstart');

		$search = JRequest::getVar( 'search', '' );

		$gid	= EasyBlogHelper::getUserGids();
		$gids   = '';
		if( count( $gid ) > 0 )
		{
		    foreach( $gid as $id)
		    {
		        $gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
		    }
		}


	    $query  = 'SELECT a.*, ( SELECT COUNT(id) FROM `#__easyblog_category` WHERE `lft` < a.`lft` AND `rgt` > a.`rgt`) AS depth';
		$query  .= ' FROM `#__easyblog_category` AS a';
		$query  .= ' WHERE a.`published` = ' . $db->Quote( '1' );
		if( !empty($search) )
		    $query  .= ' AND a.`title` LIKE ' . $db->Quote( '%' . $search . '%' );

		$query .= ' and a.id not in (';
		$query .= ' select id from `#__easyblog_category` as c';
		$query .= ' where not exists (';
		$query .= '		select b.category_id from `#__easyblog_category_acl` as b';
		$query .= '			where b.category_id = c.id and b.`acl_id` = '. $db->Quote( CATEGORY_ACL_ACTION_SELECT );
		$query .= '			and b.type = ' . $db->Quote('group');
		$query .= '			and b.content_id IN (' . $gids . ')';
		$query .= '      )';
		$query .= ' and c.`private` = ' . $db->Quote( CATEGORY_PRIVACY_ACL );
		$query .= ')';

		$query  .= ' AND a.`parent_id` NOT IN (SELECT `id` FROM `#__easyblog_category` AS e WHERE e.`published` = ' . $db->Quote( '0' ) . ' AND e.`parent_id` = ' . $db->Quote( '0' ) . ' )';


		$query  .= ' ORDER BY a.`lft`';

		$this->_total = $this->_getListCount($query);

		jimport('joomla.html.pagination');
		$this->_pagination	= EasyBlogHelper::getPagination( $this->_total , $limitstart , $limit );		
		$query  .= ' LIMIT ' . $limitstart . ', ' . $limit;

		//echo $query;

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}


	public function getCategoryTree( $sort = 'latest' )
	{
		$db     = EasyBlogHelper::db();
		$my 	= JFactory::getUser();

        $config     = EasyBlogHelper::getConfig();

		$queryExclude	= '';
		$excludeCats	= array();

		// get all private categories id
		$excludeCats	= EasyBlogHelper::getPrivateCategories();

		if(! empty($excludeCats))
		{
			$queryExclude .= ' AND a.`id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		$query	= 'SELECT a.*, ';
		$query	.= ' ( SELECT COUNT(id) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_category' );
		$query	.= ' WHERE lft < a.lft AND rgt > a.rgt AND a.lft != ' . $db->Quote( 0 ) . ' ) AS depth ';
		$query	.= ' FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_category' ) . ' AS a ';
		$query	.= ' WHERE a.`published`=' . $db->Quote( '1' );
		$query	.= $queryExclude;

		switch( $sort )
		{
		    case 'ordering':
		 		$query	.= ' ORDER BY `lft`, `ordering`';
		 		break;
			case 'alphabet':
		 		$query	.= ' ORDER BY `title`, `lft`';
		 		break;
		    case 'latest':
		    default:
		        $query	.= ' ORDER BY `rgt` DESC';
				break;
		}

		// echo $query;

		$db->setQuery( $query );

		$rows		= $db->loadObjectList();
		$total		= count( $rows );
		$categories = array();

		for( $i = 0; $i < $total; $i++ )
		{
			$category 	= EasyBlogHelper::getTable( 'Category' );
			$category->bind( $rows[ $i ] );
			$category->depth	= $rows[ $i ]->depth;
			$categories[]		= $category;
		}
		return $categories;
	}



}
