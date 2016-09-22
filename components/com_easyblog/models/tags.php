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

jimport('joomla.application.component.model');

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'model.php' );

class EasyBlogModelTags extends EasyBlogModel
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

		$limit			= $mainframe->getUserStateFromRequest( 'com_easyblog.tags.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart		= JRequest::getInt('limitstart', 0, '' );

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

		$query	= 'SELECT * FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_tag' )
				. $where . ' '
				. $orderby;

		return $query;
	}

	function _buildQueryWhere()
	{
		$mainframe			= JFactory::getApplication();
		$db					= EasyBlogHelper::db();

		$filter_state 		= $mainframe->getUserStateFromRequest( 'com_easyblog.tags.filter_state', 'filter_state', '', 'word' );
		$search 			= $mainframe->getUserStateFromRequest( 'com_easyblog.tags.search', 'search', '', 'string' );
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

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easyblog.tags.filter_order', 		'filter_order', 	'ordering', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easyblog.tags.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.', ordering';

		return $orderby;
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	function getData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}

	/**
	 * Method to publish or unpublish tags
	 *
	 * @access public
	 * @return array
	 */
	function publish( $tags = array(), $publish = 1 )
	{
		if( count( $tags ) > 0 )
		{
			$db		= EasyBlogHelper::db();

			$tags	= implode( ',' , $tags );

			$query	= 'UPDATE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_tag' ) . ' '
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

	function searchTag($title)
	{
		$db	= EasyBlogHelper::db();

		$query	= 'SELECT ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote('id') . ' '
				. 'FROM ' 	. EasyBlogHelper::getHelper( 'SQL' )->nameQuote('#__easyblog_tag') . ' '
				. 'WHERE ' 	. EasyBlogHelper::getHelper( 'SQL' )->nameQuote('title') . ' = ' . $db->quote($title) . ' '
				. 'LIMIT 1';
		$db->setQuery($query);

		$result	= $db->loadObject();

		return $result;
	}


	/**
	 * Method to get total tags created so far iregardless the status.
	 *
	 * @access public
	 * @return integer
	 */
	function getTotalTags( $userId = 0)
	{
		$db		= EasyBlogHelper::db();
		$where  = array();

		$query	= 'SELECT COUNT(1) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_tag' );

		if(! empty($userId))
			$where[]  = '`created_by` = ' . $db->Quote($userId);

		$extra 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		$query      = $query . $extra;



		$db->setQuery( $query );

		$result	= $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	/**
	 * *********************************************************************
	 * These part of codes will used in dashboard tags.
	 * *********************************************************************
	 */


	function _buildQueryByBlogger($bloggerId, $sort = 'title')
	{
		$db			= EasyBlogHelper::db();

		$query	= 	'select a.`id`, a.`title`, a.`alias`, a.`created`, count(b.`id`) as `post_count`, a.`published`';
		$query	.=  ' from #__easyblog_tag as a';
		$query	.=  '    left join #__easyblog_post_tag as b';
		$query	.=  '    on a.`id` = b.`tag_id`';
		$query	.=  ' where a.created_by = ' . $db->Quote($bloggerId);
		$query	.=  ' group by (a.`id`)';

		if( $sort == 'post')
			$query	.=  ' order by count(b.`id`) desc';
		else
			$query	.=  ' order by a.`title`';

		return $query;
	}


	function getTagsByBlogger($bloggerId, $usePagination = true, $sort = 'title')
	{
		$db = EasyBlogHelper::db();

		$query  = $this->_buildQueryByBlogger($bloggerId, $sort);
		//$db->setQuery($query);

		$result = null;
		if( $usePagination )
		{
			$pg		= $this->getPaginationByBlogger($bloggerId, $sort);
			$result = $this->_getList($query, $pg->limitstart, $pg->limit);
		}
		else
		{
			$result = $this->_getList( $query );
		}

		return $result;
	}

	function getPaginationByBlogger($bloggerId, $sort = 'title')
	{
		jimport('joomla.html.pagination');
		$this->_pagination	= EasyBlogHelper::getPagination( $this->getTotalByBlogger( $bloggerId , $sort ), $this->getState('limitstart'), $this->getState('limit') );
		return $this->_pagination;
	}

	function getTotalByBlogger($bloggerId, $sort = 'title')
	{
		// Lets load the content if it doesn't already exist
		$query = $this->_buildQueryByBlogger($bloggerId, $sort);
		$total = $this->_getListCount($query);

		return $total;
	}

	/**
	 * *********************************************************************
	 * END: These part of codes will used in dashboard tags.
	 * *********************************************************************
	 */

	function isExist($tagName, $excludeTagIds='0')
	{
		$db = EasyBlogHelper::db();

		$query  = 'SELECT COUNT(1) FROM #__easyblog_tag';
		$query  .= ' WHERE `title` = ' . $db->Quote($tagName);
		if($excludeTagIds != '0')
			$query  .= ' AND `id` != ' . $db->Quote($excludeTagIds);

		$db->setQuery($query);
		$result = $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	function getTagCloud($limit='', $order='title', $sort='asc', $checkAccess = false)
	{
		$db = EasyBlogHelper::db();
		$my = JFactory::getUser();

		$isBloggerMode  = EasyBlogRouter::isBloggerMode();
		$queryExclude   = '';
		$excludeCats	= array();

		if($checkAccess)
		{
			// get all private categories id
			$excludeCats	= EasyBlogHelper::getPrivateCategories();
		}

		if(! empty($excludeCats))
		{
			$queryExclude .= ' AND c.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}


		$query  =   'select a.`id`, a.`title`, a.`alias`, a.`created`, count(c.`id`) as `post_count`';
		$query	.=  ' from #__easyblog_tag as a';
		$query	.=  '    left join #__easyblog_post_tag as b';
		$query	.=  '    on a.`id` = b.`tag_id`';
		$query  .=  '    left join #__easyblog_post as c';
		$query  .=  '    on b.post_id = c.id';
		$query  .=  '    and c.`published` = ' . $db->Quote('1');

		if($isBloggerMode !== false)
			$query  .=  '    and c.`created_by` = ' . $db->Quote($isBloggerMode);

		if($checkAccess)
		{
			if($my->id == 0)
				$query  .=  '    and c.`private` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);

			$query  .= $queryExclude;
		}

		$query  .= 	' where a.`published` = ' . $db->Quote('1');
		$query	.=  ' group by (a.`id`)';

		//order
		switch($order)
		{
			case 'postcount':
				$query	.=  ' order by `post_count`';
				break;
			case 'title':
			default:
				$query	.=  ' order by a.`title`';
		}

		//sort
		switch($sort)
		{
			case 'asc':
				$query	.=  ' asc ';
				break;
			case 'desc':
			default:
				$query	.=  ' desc ';
		}

		//limit
		if(!empty($limit))
		{
			$query	.=  ' LIMIT ' . (INT)$limit;
		}

		$db->setQuery($query);
		$result = $db->loadObjectList();
		return $result;
	}

	function getTags($count="")
	{
		$db = EasyBlogHelper::db();

		$query  =   ' SELECT `id`, `title`, `alias` ';
		$query  .=  ' FROM #__easyblog_tag ';
		$query	.=  ' WHERE `published` = 1 ';
		$query	.=  ' ORDER BY `title`';

		if(!empty($count))
		{
			$query	.=  ' LIMIT ' . $count;
		}


		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}


	/**
	 * *********************************************************************
	 * These part of codes will used in tag clod tags.
	 * *********************************************************************
	 */


	function _buildQueryByTagBlogs()
	{
		$db			= EasyBlogHelper::db();

		$query	=  'select count(a.`tag_id`) as `cnt`, b.*';
		$query	.= ' from `#__easyblog_post_tag` as a';
		$query	.= '   inner join `#__easyblog_post` as b on a.`post_id` = b.`id`';
		$query	.= ' group by (a.`post_id`)';
		$query	.= ' order by `cnt` desc';

		return $query;
	}


	function getTagBlogs()
	{
		$db = EasyBlogHelper::db();

		$query  = $this->_buildQueryByTagBlogs();
		$pg		= $this->getPaginationByTagBlogs();
		//$db->setQuery($query);

		$result = $this->_getList($query, $pg->limitstart, $pg->limit);

		return $result;
	}

	function getPaginationByTagBlogs()
	{
		jimport('joomla.html.pagination');
		$this->_pagination	= EasyBlogHelper::getPagination( $this->getTotalByTagBlogs(), $this->getState('limitstart'), $this->getState('limit') );
		return $this->_pagination;
	}

	function getTotalByTagBlogs()
	{
		// Lets load the content if it doesn't already exist
		$query = $this->_buildQueryByTagBlogs();
		$total = $this->_getListCount($query);

		return $total;
	}

	function getTeamBlogCount( $tagId )
	{
		$db		= EasyBlogHelper::db();
		$my		= JFactory::getUser();
		$config = EasyBlogHelper::getConfig();


		$isBloggerMode  = EasyBlogRouter::isBloggerMode();
		$extraQuery     = '';

		$query	= 'select count(1) from `#__easyblog_post` as a';
		$query	.= '  inner join `#__easyblog_post_tag` as b';
		$query	.= '    on a.`id` = b.`post_id`';
		$query	.= '    and b.`tag_id` = ' . $db->Quote($tagId);
		$query	.= '  inner join `#__easyblog_team_post` as c';
		$query	.= '    on a.`id` = c.`post_id`';

		if( $config->get( 'main_includeteamblogpost' ) )
		{
			$teamBlogIds	= EasyBlogHelper::getViewableTeamIds();
			if( count( $teamBlogIds ) > 0 )
				$teamBlogIds    = implode( ',' , $teamBlogIds);
		}


		$query	.= '  where a.`issitewide` = ' . $db->Quote('0');

		if( !empty($extraQuery) )
			$query  .= $extraQuery;


		if($isBloggerMode !== false)
			$query	.= '  and a.`created_by` = ' . $db->Quote($isBloggerMode);


		$db->setQuery($query);
		$result = $db->loadResult();

		return (empty($result)) ? '0' : $result;
	}

	/**
	 * *********************************************************************
	 * These part of codes will used in tag clod tags.
	 * *********************************************************************
	 */

	function getTagPrivateBlogCount( $tagId )
	{
		$db = EasyBlogHelper::db();

		$queryExclude   = '';
		$excludeCats	= array();
		$isBloggerMode  = EasyBlogRouter::isBloggerMode();

		// get all private categories id
		$excludeCats	= EasyBlogHelper::getPrivateCategories();

		if(! empty($excludeCats))
		{
			$queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		$query	= 'select count(1) from `#__easyblog_post` as a';
		$query	.= '  inner join `#__easyblog_post_tag` as b';
		$query	.= '    on a.`id` = b.`post_id`';
		$query	.= '    and b.`tag_id` = ' . $db->Quote($tagId);
		$query	.= '  where a.`private` = ' . $db->Quote(BLOG_PRIVACY_PRIVATE);
		if($isBloggerMode !== false)
			$query	.= '  and a.`created_by` = ' . $db->Quote($isBloggerMode);

		$query  .= $queryExclude;

		$db->setQuery($query);
		$result = $db->loadResult();

		return (empty($result)) ? '0' : $result;
	}

	function getDefaultTags()
	{
		$db		= EasyBlogHelper::db();

		$query	= 'SELECT `id` FROM `#__easyblog_tag` '
				. 'WHERE `default` = 1';

		$db->setQuery($query);

		$tags	= $db->loadResultArray();

		return $tags;
	}
}
