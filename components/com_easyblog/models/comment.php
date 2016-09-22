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
class EasyBlogModelComment extends EasyBlogModel
{

	/**
	 * Record total
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
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();

		$mainframe	= JFactory::getApplication();

		$limit		= ($mainframe->getCfg('list_limit') == 0) ? 5 : $mainframe->getCfg('list_limit');
		$limitstart = JRequest::getVar('limitstart', 0, 'REQUEST');

		// In case limit has been changed, adjust it
		$limitstart = (int) ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}


	/**
	 * Method to get total comment post currently iregardless the status and associated blogs.
	 *
	 * @access public
	 * @return integer
	 */
	function getTotalComment( $userId = 0 )
	{
		$db		= EasyBlogHelper::db();
		$config		= EasyBlogHelper::getConfig();

		if( $config->get( 'comment_compojoom' ) )
		{
			$file	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_comment' . DIRECTORY_SEPARATOR . 'plugin' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'josc_com_easyblog.php';

			if( JFile::exists( $file ) )
			{
				require_once( $file );

				$query	= 'SELECT COUNT(1) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__comment' ) . ' '
						. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'component' ) . ' = ' . $db->Quote( 'com_easyblog' ) . ' '
						. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'userid' ) . ' = ' . $db->Quote( $userId ) . ' '
						. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . ' = ' . $db->Quote( 1 );
				$db->setQuery( $query );
				return $db->loadResult();
			}
		}

		$where  = array();

		$query	= 'SELECT COUNT(1) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_comment' );

		if(! empty($userId))
			$where[]  = '`created_by` = ' . $db->Quote($userId);

		$extra 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		$query      = $query . $extra;

		$db->setQuery( $query );

		$result	= $db->loadResult();

		return (empty($result)) ? 0 : $result;
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
	 *
	 */
	function getComments($max = 0, $userId = 0, $sort = 'latest',  $base='comment', $search = '', $published = 'all')
	{
		$config		= EasyBlogHelper::getConfig();

		if( $config->get( 'comment_compojoom' ) )
		{
			$file	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_comment' . DIRECTORY_SEPARATOR . 'plugin' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'josc_com_easyblog.php';

			if( JFile::exists( $file ) )
			{
				require_once( $file );

				return $this->getCompojoomComment($max = 0, $userId = 0, $sort = 'latest',  $base='comment', $search = '', $published = 'all');
			}
		}

		$db	= EasyBlogHelper::db();

		$queryPagination	= false;
		$queryLimit			= '';
		$queryOrder			= ' ORDER BY a.`created` DESC';
		$queryWhere			= '';

		switch($sort)
		{
			case 'latest' :
			default :
				$queryOrder			= ' ORDER BY a.`created` DESC';
				break;
		}

		if(! empty($userId))
		{
			if($base == 'comment')
			{
				$queryWhere	.= ' WHERE a.`created_by` = '. $db->Quote($userId);
			}
			else
			{
				$queryWhere	.= ' WHERE b.`created_by` = '. $db->Quote($userId);
			}
		}

		switch ( $published )
		{
			case 'published':
				$queryWhere	.= ' AND a.`published` = '. $db->Quote('1');
				break;

			case 'unpublished':
				$queryWhere	.= ' AND a.`published` = '. $db->Quote('0');
				break;

			case 'moderate':
				$queryWhere	.= ' AND a.`published` = '. $db->Quote('2');
				break;

			case 'all':
			default:
				break;
		}


		if(! empty($search))
		{
			$queryWhere .= (! empty($queryWhere)) ? ' AND' : ' WHERE';
			$queryWhere	.= ' ( a.`title` LIKE '.$db->Quote('%' . $search . '%') . ' OR ';
			$queryWhere	.= ' a.`comment` LIKE '.$db->Quote('%' . $search . '%' ) . ' OR ';
			$queryWhere .= ' b.`title` LIKE ' . $db->Quote( '%' . $search . '%' ) . ' )';
		}

		if($max > 0)
		{
			$queryLimit	= ' LIMIT '.$max;
		}
		else
		{
			$limit		= $this->getState('limit');
			$limitstart = $this->getState('limitstart');
			$queryLimit	= ' LIMIT ' . $limitstart . ',' . $limit;

			$queryPagination = true;
		}

		if($queryPagination)
		{
			$query	= 'SELECT COUNT(1)';
			$query	.= ' FROM `#__easyblog_comment` AS a INNER JOIN `#__easyblog_post` AS b';
			$query	.= ' ON a.`post_id` = b.`id`';
			$query	.= $queryWhere;

			$db->setQuery( $query );
			$this->_total	= $db->loadResult();

			jimport('joomla.html.pagination');
			$this->_pagination	= EasyBlogHelper::getPagination( $this->_total , $limitstart , $limit );
		}

		$query	= 'SELECT a.*, b.`created_by` AS `blog_owner`, b.`title` AS `blog_title`'
				. ' FROM `#__easyblog_comment` AS a INNER JOIN `#__easyblog_post` AS b'
				. ' ON a.`post_id` = b.`id`'
				. $queryWhere
				. $queryOrder
				. $queryLimit;

		$db->setQuery($query);

		$result	= $db->loadObjectList();

		if($db->getErrorNum() > 0)
		{
			JError::raiseError( $db->getErrorNum() , $db->getErrorMsg() . $db->stderr());
		}

		return $result;

	}

	function getCompojoomComment($max = 0, $userId = 0, $sort = 'latest',  $base='comment', $search = '', $published = 'all')
	{
		$db	= EasyBlogHelper::db();

		$queryPagination	= false;
		$queryLimit			= '';
		$queryOrder			= ' ORDER BY a.`date` DESC';
		$queryWhere			= '';

		switch($sort)
		{
			case 'latest' :
			default :
				$queryOrder			= ' ORDER BY a.`date` DESC';
				break;
		}

		if(! empty($userId))
		{
			if($base == 'comment')
			{
				$queryWhere	.= ' WHERE a.`userid` = '. $db->Quote($userId);
			}
			else
			{
				$queryWhere	.= ' WHERE b.`created_by` = '. $db->Quote($userId);
			}
		}

		switch ( $published )
		{
			case 'published':
				$queryWhere	.= ' AND a.`published` = '. $db->Quote('1');
				break;

			case 'unpublished':
				$queryWhere	.= ' AND a.`published` = '. $db->Quote('0');
				break;

			case 'moderate':
				$queryWhere	.= ' AND a.`published` = '. $db->Quote('2');
				break;

			case 'all':
			default:
				break;
		}


		if(! empty($search))
		{
			$queryWhere .= (! empty($queryWhere)) ? ' AND' : ' WHERE';
			$queryWhere	.= ' a.`comment` LIKE '.$db->Quote('%' . $search . '%');

		}

		$queryWhere		.= ' AND a.`component` = ' . $db->quote('com_easyblog');

		if($max > 0)
		{
			$queryLimit	= ' LIMIT '.$max;
		}
		else
		{
			$limit		= $this->getState('limit');
			$limitstart = $this->getState('limitstart');
			$queryLimit	= ' LIMIT ' . $limitstart . ',' . $limit;

			$queryPagination = true;
		}

		if($queryPagination)
		{
			$query	= 'SELECT COUNT(1)';
			$query	.= ' FROM `#__comment` AS a INNER JOIN `#__easyblog_post` AS b';
			$query	.= ' ON a.`contentid` = b.`id`';
			$query	.= $queryWhere;

			$db->setQuery( $query );
			$this->_total	= $db->loadResult();

			jimport('joomla.html.pagination');
			$this->_pagination	= EasyBlogHelper::getPagination( $this->_total , $limitstart , $limit );
		}

		$query	= 'SELECT'
				. ' a.`id` AS `id`,'
				. ' a.`contentid` AS `post_id`,'
				. ' a.`comment` AS `comment`,'
				. ' a.`name` AS `name`,'
				. ' a.`title` AS `title`,'
				. ' a.`email` AS `email`,'
				. ' a.`website` AS `url`,'
				. ' a.`ip` AS `ip`,'
				. ' a.`userid` AS `created_by`,'
				. ' a.`date` AS `created`,'
				. ' a.`date` AS `modified`,'
				. ' a.`published` AS `published`,'
				. ' ' . $db->quote('0000-00-00 00:00:00') . ' AS `publish_up`,'
				. ' ' . $db->quote('0000-00-00 00:00:00') . ' AS `publish_down`,'
				. ' ' . $db->quote('0') . ' AS `ordering`,'
				. ' a.`voting_yes` AS `vote`,'
				. ' ' . $db->quote('0') . ' AS `hits`,'
				. ' ' . $db->quote('1') . ' AS `sent`,'
				. ' ' . $db->quote('0') . ' AS `parent_id`,'
				. ' ' . $db->quote('0') . ' AS `lft`,'
				. ' ' . $db->quote('0') . ' AS `rgt`,'
				. ' b.`created_by` AS `blog_owner`, b.`title` AS `blog_title`'
				. ' FROM `#__comment` AS a INNER JOIN `#__easyblog_post` AS b'
				. ' ON a.`contentid` = b.`id`'
				. $queryWhere
				. $queryOrder
				. $queryLimit;

		$db->setQuery($query);

		$result	= $db->loadObjectList();

		if($db->getErrorNum() > 0)
		{
			JError::raiseError( $db->getErrorNum() , $db->getErrorMsg() . $db->stderr());
		}

		return $result;
	}

	function getLatestComment($blogId, $parentId = 0)
	{
		$db	= EasyBlogHelper::db();

		$query	= 'SELECT `id`, `lft`, `rgt` FROM `#__easyblog_comment`';
		$query	.= ' WHERE `post_id` = ' . $db->Quote($blogId);
		if($parentId != 0)
			$query	.= ' AND `parent_id` = ' . $db->Quote($parentId);
		else
			$query	.= ' AND `parent_id` = ' . $db->Quote('0');
		$query	.= ' ORDER BY `lft` DESC LIMIT 1';

		$db->setQuery($query);
		$result	= $db->loadObject();

		return $result;
	}

	function updateCommentSibling($blogId, $nodeValue)
	{
		$db	= EasyBlogHelper::db();

		$query	= 'UPDATE `#__easyblog_comment` SET `rgt` = `rgt` + 2';
		$query	.= ' WHERE `rgt` > ' . $db->Quote($nodeValue);
		$query	.= ' AND `post_id` = ' . $db->Quote($blogId);
		$db->setQuery($query);
		$db->query();

		$query	= 'UPDATE `#__easyblog_comment` SET `lft` = `lft` + 2';
		$query	.= ' WHERE `lft` > ' . $db->Quote($nodeValue);
		$query	.= ' AND `post_id` = ' . $db->Quote($blogId);
		$db->setQuery($query);
		$db->query();
	}

	function isLikeComment( $commentId, $userId )
	{
		// @rule: Since guest cannot like a comment, no point running the following query
		if( $userId == 0 )
		{
			return 0;
		}

		$db = EasyBlogHelper::db();

		$query  = 'SELECT `id` FROM `#__easyblog_likes`';
		$query  .= ' WHERE `type` = ' . $db->Quote('comment');
		$query  .= ' AND `content_id` = ' . $db->Quote($commentId);
		$query  .= ' AND `created_by` = ' . $db->Quote($userId);

		$db->setQuery($query);

		$result = $db->loadResult();
		return $result;
	}

	function getCommentTotalLikes($commentId)
	{
		$db = EasyBlogHelper::db();

		$query = 'SELECT COUNT(1) FROM `#__easyblog_likes`';
		$query .= ' WHERE `type` = ' . $db->Quote('comment');
		$query .= ' AND `content_id` = ' . $db->Quote($commentId);

		$db->setQuery($query);

		$result = $db->loadResult();
		return $result;
	}

	function getUserModerateCommentCount($userId)
	{
		if( $userId == 0 )
		{
			return 0;
		}

		$db = EasyBlogHelper::db();

		$query	= 'select count(1) from `#__easyblog_comment` as a';
		$query	.= '  inner join `#__easyblog_post` as b on a.`post_id` = b.`id`';
		$query	.= '  and b.`created_by` = ' . $db->Quote($userId);
		$query	.= ' where a.`published` = ' . $db->Quote(EBLOG_COMMENT_STATUS_MODERATED);

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

}
