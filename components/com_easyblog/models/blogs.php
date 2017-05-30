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

/**
 * Content Component Article Model
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class EasyBlogModelBlogs extends EasyBlogModel
{
	/**
	 * Blogs data array
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Configuration data
	 * 
	 * @var int	Total number of rows
	 **/
	var $_total;
	
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
		$limit       	= $mainframe->getUserStateFromRequest('com_easyblog.blogs.limit', 'limit', $mainframe->getCfg('list_limit') , 'int');
		$limitstart		= (int) JRequest::getVar('limitstart', 0, '', 'int');
			
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		// Get the filter request variables
		//$this->setState('filter_order', JRequest::getCmd('filter_order', 'a.dates'));
		//$this->setState('filter_order_dir', JRequest::getCmd('filter_order_Dir', 'ASC'));
	}
	
	function getBlogs( $userId = null )
	{
		if(empty($this->_data) )
		{
			$query = $this->_buildQuery( $userId );

			// Apply limit
			$query	.= ' LIMIT ' . $this->getState( 'limitstart' ) . ',' . $this->getState( 'limit' );

			$db		= JFactory::getDBO();
			$db->setQuery( $query );

			$this->_data 	= $db->loadObjectList();
		}
		return $this->_data;
	}

	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildQueryWhere();
		$orderby	= $this->_buildQueryOrderBy();
		$db			= EasyBlogHelper::db();
		
		$query	= 'SELECT * FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_post' )
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
		
		//blog privacy setting
		$my = JFactory::getUser();
		if($my->id == 0)
		{
			$where[] = EasyBlogHelper::getHelper( 'SQL' )->nameQuote('private') . '=' . $db->Quote(BLOG_PRIVACY_PUBLIC);
		}
		
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
			$db 			= JFactory::getDBO();
			$query			= 'SELECT COUNT(1) FROM `#__easyblog_post`';
			$db->setQuery( $query );

			$this->_total 	= $db->loadResult();
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
			$this->_pagination	= EasyBlogHelper::getPagination(  $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	function publish( &$blogs = array(), $publish = 1 )
	{
		if( count( $blogs ) > 0 )
		{
			$db		= EasyBlogHelper::db();
			
			$blogs	= implode( ',' , $blogs );
			
			$query	= 'UPDATE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_post' ) . ' '
					. 'SET ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote( $publish ) . ' '
					. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'id' ) . ' IN (' . $blogs . ')';
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
	
	function getTotalPublished( $uid )
	{
		$db		= EasyBlogHelper::db();
		$query	= 'SELECT COUNT(1) AS `total`' .
				  ' FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_post' ) .
				  ' WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'created_by' ) . '=' . $db->Quote( $uid ) .
				  ' AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote('1');
				  
		//blog privacy setting
		$my = JFactory::getUser();
		if($my->id == 0)
		    $query .= ' AND `private` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);
		
		$db->setQuery( $query );
		
		$result	= $db->loadResult();
		return (empty($result)) ? 0 : $result;
	}
	
	function getTotalPending()
	{
		$db		= EasyBlogHelper::db();
		$query	= 'SELECT COUNT(1) AS `total`' .
				  ' FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_drafts' ) .
				  ' WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'pending_approval' ) . ' = ' . $db->Quote( '1' );
		
		$db->setQuery( $query );
		
		$result	= $db->loadResult();
		return (empty($result)) ? 0 : $result;
	}
	
	function getTotalUnpublished( $uid )
	{
		$db		= EasyBlogHelper::db();
		$query	= 'SELECT COUNT(1) AS `total`' .
				  ' FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_post' ) .
				  ' WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'created_by' ) . '=' . $db->Quote( $uid ) .
				  ' AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote('0');
				  
		//blog privacy setting
		$my = JFactory::getUser();
		if($my->id == 0)
		    $query .= ' AND `private` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);
		
		$db->setQuery( $query );
		
		$result	= $db->loadResult();
		
		return (empty($result)) ? 0 : $result;
	}
		
}
