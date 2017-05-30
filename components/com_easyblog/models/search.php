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

class EasyBlogModelSearch extends EasyBlogModel
{
	var $_data = null;
	var $_pagination = null;
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
		$limitstart		= JRequest::getInt('limitstart', 0, '' );
			
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if ( empty( $this->_pagination ) )
		{
			jimport('joomla.html.pagination');
			$this->_pagination	= EasyBlogHelper::getPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}
	
	function getTotal()
	{
		// Load total number of rows
		if( empty($this->_total) )
		{
			$this->_total	= $this->_getListCount( $this->_buildQuery() );
		}

		return $this->_total;
	}

	function _buildQuery()
	{
		$db			= EasyBlogHelper::db();
		$my			= JFactory::getUser();
		$config     = EasyBlogHelper::getConfig();
		
		// used for privacy
		$queryWhere             = '';
		$queryExclude			= '';
		$queryExcludePending    = '';
		$excludeCats			= array();
		$isBloggerMode  		= EasyBlogRouter::isBloggerMode();
		
		$where		= array();
		$where2		= array();
		$text		= JRequest::getVar( 'query' );

		$words		= explode( ' ', $text );
		$wheres		= array();
		
		foreach ($words as $word)
		{
			$word		= $db->Quote( '%'.$db->getEscaped( $word, true ).'%', false );
			
			$where[]	= 'a.`title` LIKE ' . $word;
			$where[]	= 'a.`content` LIKE ' . $word;
			$where[]	= 'a.`intro` LIKE ' . $word;
			
			$where2[]	= 't.title LIKE ' . $word;
			$wheres2[]	= implode( ' OR ' , $where2	);
			
			$wheres[] 	= implode( ' OR ', $where );					
		}
		$where	= '(' . implode( ') OR (' , $wheres ) . ')';
		$where2	= '(' . implode( ') OR (' , $wheres2 ) . ')';
		
		
	    //get teamblogs id.
	    $teamBlogIds    = '';
	    $query  		= '';
	    if( $config->get( 'main_includeteamblogpost' ) )
	    {
			$teamBlogIds	= EasyBlogHelper::getViewableTeamIds();
			if( count( $teamBlogIds ) > 0 )
            	$teamBlogIds    = implode( ',' , $teamBlogIds);
	    }
	    
		// get all private categories id
		$excludeCats	= EasyBlogHelper::getPrivateCategories();
		
		if(! empty($excludeCats))
		{
		    $queryWhere .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}
		
		$jsPostIds  = self::getJomSocialPosts();
		
	    if( $config->get( 'main_includeteamblogpost' ) && !empty($teamBlogIds))
	    {
			if( !empty( $jsPostIds ) )
			{
				$tmpIds = implode( ',', $jsPostIds);
				$queryWhere	.= ' AND (u.team_id IN ('.$teamBlogIds.') OR a.id IN (' . $tmpIds . ') OR a.`issitewide` = ' . $db->Quote('1') . ')';
			}
			else
			{
				$queryWhere	.= ' AND (u.team_id IN ('.$teamBlogIds.') OR a.`issitewide` = ' . $db->Quote('1') . ')';
			}
		}
		else
		{
			if( !empty( $jsPostIds ) )
			{
				$tmpIds = implode( ',', $jsPostIds);
				$queryWhere	.= ' AND (a.id IN (' . $tmpIds . ') OR a.`issitewide` = ' . $db->Quote('1') . ')';
			}
			else
			{
		    	$queryWhere	.= ' AND a.`issitewide` = ' . $db->Quote('1');
			}
		}
		
		if( $isBloggerMode )
		{
			$queryWhere .= ' AND a.`created_by`=' . $db->Quote( $isBloggerMode );
		}

		$query	= 'SELECT a.*, b.`title` AS `category` , CONCAT(a.`content` , a.`intro`) AS text';
		$query	.= ' FROM `#__easyblog_post` as a USE INDEX (`easyblog_post_search`)';
		
		if( $config->get( 'main_includeteamblogpost' ) )
		{
		    $query  .= ' LEFT JOIN `#__easyblog_team_post` AS u ON a.id = u.post_id';
		}

		$query	.= ' LEFT JOIN `#__easyblog_category` AS b';
		$query	.= ' 	ON a.category_id = b.id';

		// Always inner join with jos_users and a.created_by so that only valid blogs are loaded
		$query .= ' INNER JOIN ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__users' ) . ' AS c ON a.`created_by`=c.`id`';
		
		$query	.= ' WHERE (' . $where;

		$query	.= ' OR a.`id` IN( ';
		$query	.= '		SELECT tp.`post_id` FROM `#__easyblog_tag` AS t ';
		$query	.= '		INNER JOIN `#__easyblog_post_tag` AS tp ON tp.`tag_id` = t.`id` ';
		$query	.= '		WHERE ' . $where2;
		$query	.= ') )';
		
		if( $my->id < 1 )
		{
			//guest should only see public post.
			$query    .= ' AND a.`private` = ' . $db->Quote('0');
		}
		
		//do not show unpublished post
		$query	.= ' AND a.`published` = ' . $db->Quote('1');
		$query	.= $queryWhere;
		$query	.= ' ORDER BY a.`created` DESC';

		// echo $query;

		return $query;
	}
	
	function getData()
	{
		if(empty($this->_data) )
		{
			$query = $this->_buildQuery();

			$this->_data	= $this->_getList( $this->_buildQuery() , $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_data;
	}

	function getJomSocialPosts()
	{
		$db = EasyBlogHelper::db();

		$isJSGrpPluginInstalled	= false;
		$isJSGrpPluginInstalled	= JPluginHelper::isEnabled( 'system', 'groupeasyblog');
		$isEventPluginInstalled	= JPluginHelper::isEnabled( 'system' , 'eventeasyblog' );
		$isJSInstalled			= false; // need to check if the site installed jomsocial.

		if(JFile::exists(JPATH_ROOT . DS . 'components' . DS. 'com_community' . DS . 'libraries' . DS .'core.php'))
		{
			$isJSInstalled = true;
		}

		$includeJSGrp	= ($isJSGrpPluginInstalled && $isJSInstalled) ? true : false;
		$includeJSEvent	= ($isEventPluginInstalled && $isJSInstalled ) ? true : false;

		$jsEventPostIds	= array();
		$jsGrpPostIds	= array();

		if( $includeJSEvent )
		{
			$queryEvent	= 'SELECT ' . $db->nameQuote( 'post_id' ) . ' FROM';
			$queryEvent	.= ' ' . $db->nameQuote( '#__easyblog_external' ) . ' AS ' . $db->nameQuote( 'a' );
			$queryEvent	.= ' INNER JOIN' . $db->nameQuote( '#__community_events' ) . ' AS ' . $db->nameQuote( 'b' );
			$queryEvent	.= ' ON ' . $db->nameQuote( 'a' ) . '.uid = ' . $db->nameQuote( 'b' ) . '.id';
			$queryEvent	.= ' AND ' . $db->nameQuote( 'a' ) . '.' . $db->nameQuote( 'source' ) . '=' . $db->Quote( 'jomsocial.event' );
			$queryEvent	.= ' WHERE ' . $db->nameQuote( 'b' ) . '.' . $db->nameQuote( 'permission' ) . '=' . $db->Quote( 0 );

			$db->setQuery($queryEvent);
			$jsEventPostIds		= $db->loadResultArray();
		}

		if( $includeJSGrp )
		{
			$queryJSGrp = 'select `post_id` from `#__easyblog_external_groups` as exg inner join `#__community_groups` as jsg';
			$queryJSGrp .= '      on exg.group_id = jsg.id ';
			$queryJSGrp .= '      where jsg.`approvals` = 0';

			$db->setQuery($queryJSGrp);
			$jsGrpPostIds   = $db->loadResultArray();
		}

		$includePostIds = array();
		if( !empty($jsGrpPostIds) || !empty( $jsEventPostIds ) )
		{
			$includePostIds = array_merge($jsGrpPostIds, $jsEventPostIds);
		}

		return $includePostIds;

	}
	
	function searchtext($text)
	{
		if(empty($text))
		{
			return false;	
		}
		
		JRequest::setVar( 'query', $text );
		
		return $this->getData();
	}
}
