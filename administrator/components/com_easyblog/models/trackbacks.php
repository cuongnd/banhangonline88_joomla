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

class EasyBlogModelTrackbacks extends EasyBlogModelParent
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

		$limit		= $mainframe->getUserStateFromRequest( 'com_easyblog.trackbacks.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');

		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');
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
	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildQueryWhere();
		$orderby	= $this->_buildQueryOrderBy();
		$db			= EasyBlogHelper::db();
		
		$query	= 'SELECT a.*'
				. ' FROM `#__easyblog_trackback` AS a '
				. $where . ' '
				. $orderby;

		return $query;
	}

	function _buildQueryWhere()
	{
		$mainframe			= JFactory::getApplication();
		$db					= EasyBlogHelper::db();
		
		$filter_state 		= $mainframe->getUserStateFromRequest( 'com_easyblog.trackbacks.filter_state', 'filter_state', '', 'word' );
		$search 			= $mainframe->getUserStateFromRequest( 'com_easyblog.trackbacks.search', 'search', '', 'string' );
		$search 			= $db->getEscaped( trim(JString::strtolower( $search ) ) );

		$where = array();

		if ( $filter_state )
		{
			if ( $filter_state == 'P' )
			{
				$where[] = 'a.`published` =' . $db->Quote( '1' );
			}
			else if ($filter_state == 'U' )
			{
				$where[] = 'a.`published` =' . $db->Quote( '0' );
			}
			else if ($filter_state == 'M' )
			{
				$where[] = 'a.`published` =' . $db->Quote( '2' );
			}
		}


		if ($search)
		{
			$where[] = ' LOWER( a.title ) LIKE \'%' . $search . '%\' ';
		}

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easyblog.trackbacks.filter_order', 		'filter_order', 	'created DESC', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easyblog.trackbacks.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;
		
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
	 * Method to publish or unpublish categories
	 *
	 * @access public
	 * @return array
	 */
	function publish( &$categories, $publish = 1 )
	{
		if( count( $categories ) > 0 )
		{
			$db		= EasyBlogHelper::db();
			
			$tags	= implode( ',' , $categories );
			
			$query	= 'UPDATE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_trackback' ) . ' '
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
}