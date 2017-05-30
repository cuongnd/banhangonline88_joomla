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
class EasyBlogModelOauth extends EasyBlogModel
{
	var $_data	= null;
	var $_total = null;
	var $_pagination = null;

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
	function _buildQuery( $userId )
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildQueryWhere( $userId );
		$orderby	= $this->_buildQueryOrderBy();
		$db			= EasyBlogHelper::db();
		
		$query		= 'SELECT a.* FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_oauth' ) . ' AS a '
					. $where . ' '
					. $orderby;

		return $query;
	}
	
	function _buildQueryWhere( $userId )
	{
		$mainframe			= JFactory::getApplication();
		$db					= EasyBlogHelper::db();

		$where[]	= 'a.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId );

		$where		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}
	
	
	function _buildQueryOrderBy()
	{

		$orderby 	= ' ORDER BY a.`id`';

		return $orderby;
	}

	/**
	 * Method to get teamblog item data
	 *
	 * @access public
	 * @return array
	 */
	function getConsumers( $userId )
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery( $userId );

			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}
	
	/**
	 * Method to get the total nr of the team
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
}
