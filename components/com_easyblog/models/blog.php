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

require_once( JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_easyblog'.DIRECTORY_SEPARATOR.'constants.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'router.php' );
require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'model.php' );

class EasyBlogModelBlog extends EasyBlogModel
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

		if( $limit != 0 )
		{
			$limitstart		= (int) floor( ( $limitstart / $limit ) * $limit );
		}
		else
		{
			$limitstart 	= 0;
		}

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Computes the total number of hits for blog posts created by any specific user throughout the site.
	 *
	 * @access	public
	 * @param 	int 	$userId 	The user's id.
	 * @return 	int 	$total 		The total number of hits.
	 */
	public function getTotalHits( $userId )
	{
		$db		= EasyBlogHelper::db();

		$query 	= 'SELECT SUM( `hits` ) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_post' ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'created_by' ) . '=' . $db->Quote( $userId );
		$db->setQuery( $query );

		$total 	= $db->loadResult();

		return $total;
	}

	public function getBlogComment($blogId, $limistFrontEnd = 0, $sort = 'asc', $isLite = false )
	{
		$db	= EasyBlogHelper::db();
		$config = EasyBlogHelper::getConfig();

		// Get comment sorting option.
		$sort = $config->get('comment_sorting');

		if( $isLite )
		{
			$query	= 'SELECT a.* FROM `#__easyblog_comment` a';
			// $query	.= ' LEFT JOIN #__users AS c ON a.`created_by` = c.`id`';
			$query	.= ' WHERE a.`post_id` = '.$db->Quote($blogId);
			$query	.= ' AND a.`published` = 1';
		}
		else
		{
			$query	= 'SELECT a.*, (count(b.id) - 1) AS `depth` FROM `#__easyblog_comment` AS a';
			$query	.= ' INNER JOIN `#__easyblog_comment` AS b';
			// $query	.= ' LEFT JOIN `#__users` AS c ON a.`created_by` = c.`id`';
			$query	.= ' WHERE a.`post_id` = '.$db->Quote($blogId);
			$query	.= ' AND b.`post_id` = '.$db->Quote($blogId);
			$query	.= ' AND a.`published` = 1';
			$query	.= ' AND b.`published` = 1';
			$query	.= ' AND a.`lft` BETWEEN b.`lft` AND b.`rgt`';
			$query	.= ' GROUP BY a.`id`';
		}

		// prepare the query to get total comment
		$queryTotal	= 'SELECT COUNT(1) FROM (';
		$queryTotal	.= $query;
		$queryTotal	.= ') AS x';

		// continue the query.
		$limit = $config->get('limit_comment', 0);

		// If configured to use Joomla's pagination instead
		if ($limit == 0) {
			$limit = (int) EasyBlogHelper::getJConfig()->get('list_limit');
		}

		$limitstart = $this->getState('limitstart');

		switch( $sort )
		{
			case 'desc':
				$query	.= ' ORDER BY a.`rgt` desc';
			break;
			default:
				$query	.= ' ORDER BY a.`lft` asc';
			break;
		}

		if ($limistFrontEnd > 0) {
			$query  .= ' LIMIT ' . $limistFrontEnd;
		}
		else
		{
			$query  .= ' LIMIT ' . $limitstart . ',' . $limit;
		}

		if($limistFrontEnd <= 0)
		{
			$db->setQuery( $queryTotal );
			$this->_total	= $db->loadResult();

			jimport('joomla.html.pagination');

			$this->_pagination	= EasyBlogHelper::getPagination( $this->_total , $limitstart , $limit );
		}

		// the actual content sql
		$db->setQuery($query);
		$result	= $db->loadObjectList();

		if( !$result )
		{
			return $result;
		}

		// Format the comments
		$result	= EasyBlogHelper::getHelper( 'Comment' )->format( $result );

		return $result;
	}

	function getFeaturedBlog( $categories = array() , $limit = null )
	{
		$my		= JFactory::getUser();
		$db		= EasyBlogHelper::db();
		$max	= is_null( $limit ) ? EBLOG_MAX_FEATURED_POST : $limit;

		$isBloggerMode	= EasyBlogRouter::isBloggerMode();

		$queryExclude	= '';
		$excludeCats	= array();

		// get all private categories id
		$excludeCats	= EasyBlogHelper::getPrivateCategories();

		if(! empty($excludeCats))
		{
			$queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}


		$query	 = 'SELECT a.*, b.`title` AS `category` FROM `#__easyblog_post` AS a';
		$query	.= ' LEFT JOIN `#__easyblog_category` AS b';
		$query	.= ' 	ON a.category_id = b.id';
		$query	.= ' INNER JOIN `#__easyblog_featured` AS c';
		$query	.= ' 	ON a.`id` = c.`content_id` AND c.`type` = ' . $db->Quote('post');
		$query	.= ' WHERE a.`published` = 1';

		if($isBloggerMode !== false)
		{
			$query  .= ' AND a.`created_by` = ' . $db->Quote($isBloggerMode);
		}

		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			// @rule: When language filter is enabled, we need to detect the appropriate contents
			$filterLanguage 	= JFactory::getApplication()->getLanguageFilter();

			if( $filterLanguage )
			{
				$query	.= ' AND (';
				$query	.= ' a.`language`=' . $db->Quote( JFactory::getLanguage()->getTag() );
				$query	.= ' OR a.`language`=' . $db->Quote( '' );
				$query	.= ' )';
			}
		}

		// @rule: Explicitly include posts only from these categories
		if( !empty( $categories ) )
		{
			// To support both comma separated categories an array of categories
			if( !is_array( $categories ) )
			{
				$categories	= explode( ',' , $categories );
			}

			$total	= count( $categories );
			$query	.= ' AND a.`category_id` IN (';
			for( $i = 0; $i < $total; $i++ )
			{
				$query  .= $db->Quote( $categories[ $i ] );

				if( ( $i + 1 )!= $total )
				{
					$query  .= ',';
				}
			}
			$query  .= ')';
		}
		$query	.= $queryExclude;

		//blog privacy setting
		if($my->id == 0)
			$query .= ' AND a.`private` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);

		$query	.= ' ORDER BY a.`created` DESC';
		if($max > 0)
			$query  .= ' LIMIT ' . $max;

		$db->setQuery($query);

		$result = $db->loadObjectList();

		return $result;
	}

	public function getDrafts( $userId )
	{
		$db		= EasyBlogHelper::db();
		$query	= 'SELECT * FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_drafts' ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'created_by' ) . '=' . $db->Quote( $userId );
		$db->setQuery( $query );

		$result	= $db->loadObjectList();
	}

	/**
	 * Get array of blogs defined by parameters
	 *
	 * @param	$type			str
	 * @param	$typeId			int
	 * @param	$sort			str
	 * @param	$max			int
	 * @param	$published		str
	 * @param	$search			bool
	 * @param	$frontpage		bool
	 * @param	$excludeBlogs	array
	 * @param	$pending		bool
	 * @param	$dashboard		bool
	 * @param	$protected		bool
	 * @param	$excludeCats	array
	 * @param	$includeCats	array
	 *
	*/
	public function getBlogsBy( $type, $typeId = 0, $sort = '', $max = 0 , $published = EBLOG_FILTER_PUBLISHED , $search = false, $frontpage = false, $excludeBlogs	= array(), $pending = false, $dashboard = false, $protected = true , $excludeCats = array() , $includeCats = array() , $postType = null , $limitType = 'listlength' , $pinFeatured = true )
	{
		$db		= EasyBlogHelper::db();
		$my		= JFactory::getUser();
		$config	= EasyBlogHelper::getConfig();

		$queryPagination			= false;
		$queryWhere					= '';
		$queryOrder					= '';
		$queryLimit					= '';
		$queryWhere					= '';
		$queryExclude				= '';
		$queryExcludePending		= '';
		$queryExcludePrivateJSGrp	= '';


		$excludeCats	= !empty( $excludeCats ) ? $excludeCats : array();

		$isBloggerMode	= EasyBlogRouter::isBloggerMode();
		$teamBlogIds	= '';


		$customOrdering = '';
		if( !empty( $sort ) && is_array( $sort ) )
		{
			$customOrdering = isset( $sort[1] ) ? $sort[1] : '';
			$sort 			= isset( $sort[0] ) ? $sort[0] : '';
		}

		$sort			= ( empty( $sort ) ) ? $config->get( 'layout_postorder', 'latest' ) : $sort;

		$isJSGrpPluginInstalled	= false;
		$isJSGrpPluginInstalled	= JPluginHelper::isEnabled( 'system', 'groupeasyblog');
		$isEventPluginInstalled	= JPluginHelper::isEnabled( 'system' , 'eventeasyblog' );
		$isJSInstalled			= false; // need to check if the site installed jomsocial.

		if(JFile::exists(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR. 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR .'core.php'))
		{
			$isJSInstalled = true;
		}


		$includeJSGrp	= ($type != 'teamblog' && !$dashboard && $isJSGrpPluginInstalled && $isJSInstalled) ? true : false;
		$includeJSEvent	= ($type != 'teamblog' && !$dashboard && $isEventPluginInstalled && $isJSInstalled ) ? true : false;

		$jsEventPostIds	= '';
		$jsGrpPostIds	= '';

		if( $includeJSEvent )
		{
			$queryEvent	= 'SELECT ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'post_id' ) . ' FROM';
			$queryEvent	.= ' ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_external' ) . ' AS ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'a' );
			$queryEvent	.= ' INNER JOIN' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__community_events' ) . ' AS ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'b' );
			$queryEvent	.= ' ON ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'a' ) . '.uid = ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'b' ) . '.id';
			$queryEvent	.= ' AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'a' ) . '.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'source' ) . '=' . $db->Quote( 'jomsocial.event' );
			$queryEvent	.= ' WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'b' ) . '.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'permission' ) . '=' . $db->Quote( 0 );

			$db->setQuery($queryEvent);
			$jsEventPostIds		= $db->loadResultArray();

			if( !empty( $excludeBlogs ) && !empty( $jsEventPostIds ) )
			{
				$jsEventPostIds	= array_diff($jsEventPostIds, $excludeBlogs);
			}
		}

		if( $includeJSGrp )
		{
			$queryJSGrp = 'select `post_id` from `#__easyblog_external_groups` as exg inner join `#__community_groups` as jsg';
			$queryJSGrp .= '      on exg.group_id = jsg.id ';
			$queryJSGrp .= '      where jsg.`approvals` = 0';

			$db->setQuery($queryJSGrp);
			$jsGrpPostIds   = $db->loadResultArray();

			if( !empty( $excludeBlogs ) && !empty( $jsGrpPostIds ) )
			{
				$jsGrpPostIds	= array_diff($jsGrpPostIds, $excludeBlogs);
			}
		}


		//get teamblogs id.
		$query  = '';
		if( $config->get( 'main_includeteamblogpost' ) || $dashboard )
		{
			$teamBlogIds	= EasyBlogHelper::getViewableTeamIds();
			if( count( $teamBlogIds ) > 0 )
				$teamBlogIds    = implode( ',' , $teamBlogIds);
		}

		// get all private categories id
		$excludeCats	= array_merge( $excludeCats , EasyBlogHelper::getPrivateCategories() );

		//check if the request come with statastic or not.
		$statType	= JRequest::getString('stat','');
		$statId		= '';
		if($statType != '')
		{
			$statId = ($statType == 'tag') ? JRequest::getString('tagid','') : JRequest::getString('catid','');
		}

		if(! empty($excludeBlogs))
		{
			$queryExclude .= ' AND a.`id` NOT IN (';
			for( $i = 0; $i < count( $excludeBlogs ); $i++ )
			{
				$queryExclude	.= $db->Quote( $excludeBlogs[ $i ] );

				if( next( $excludeBlogs ) !== false )
				{
					$queryExclude .= ',';
				}
			}
			$queryExclude	.= ')';
		}

		if(! empty($excludeCats))
		{
			$queryExclude .= ' AND a.`category_id` NOT IN (';

			for( $i = 0; $i < count( $excludeCats ); $i++ )
			{
				$queryExclude	.= $db->Quote( $excludeCats[ $i ] );

				if( next( $excludeCats ) !== false )
				{
					$queryExclude .= ',';
				}
			}
			$queryExclude	.= ')';
		}

		$queryInclude	= '';

		// Respect inclusion categories
		if( !empty( $includeCats ) )
		{
			$queryInclude	= ' AND a.`category_id` IN(';

			if( !is_array( $includeCats ) )
			{
				$includeCats	= array( $includeCats );
			}

			for( $i = 0; $i < count( $includeCats ); $i++ )
			{
				$queryInclude	.= $db->Quote( $includeCats[ $i ] );

				if( next( $includeCats ) !== false )
				{
					$queryInclude	.= ',';
				}
			}
			$queryInclude	.= ')';
		}

		switch( $published )
		{
			case EBLOG_FILTER_PENDING:
				$queryWhere	= ' WHERE a.`ispending` = 1 AND a.`published` != 3';
				break;
			case EBLOG_FILTER_ALL:
				$queryWhere	= ' WHERE (a.`published` = 1 OR a.`published`=0 OR a.`published`=2 OR a.`published`=3) ';
				break;
			case EBLOG_FILTER_SCHEDULE:
				$queryWhere	= ' WHERE a.`published` = 2 AND a.`ispending` = ' . $db->Quote('0');
				break;
			case EBLOG_FILTER_UNPUBLISHED:
				$queryWhere	= ' WHERE a.`published` = 0 AND a.`ispending` = ' . $db->Quote('0');
				break;
			case EBLOG_FILTER_DRAFT:
				$queryWhere	= ' WHERE a.`published` = 3 ';
				break;
			case EBLOG_FILTER_PUBLISHED:
			default:
				$queryWhere	= ' WHERE a.`published` = 1 AND a.`ispending` = ' . $db->Quote('0');
				break;
		}

		//do not list out protected blog in rss
		if(JRequest::getCmd('format', '') == 'feed')
		{
			if($config->get('main_password_protect', true))
			{
				$queryWhere	.= ' AND a.`blogpassword`="" ';
			}
		}

		//blog privacy setting
		// @integrations: jomsocial privacy
		$file		= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';

		$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );
		if( $config->get( 'integrations_easysocial_privacy' ) && $easysocial->exists() && !EasyBlogHelper::isSiteAdmin() && $type != 'teamblog' && !$dashboard )
		{
			$esPrivacyQuery  = $easysocial->buildPrivacyQuery( 'a' );
			$queryWhere 	.= $esPrivacyQuery;

		}
		else if( $config->get( 'main_jomsocial_privacy' ) && JFile::exists( $file ) && !EasyBlogHelper::isSiteAdmin() && $type != 'teamblog' && !$dashboard)
		{
			require_once( $file );

			$my			= JFactory::getUser();
			$jsFriends	= CFactory::getModel( 'Friends' );
			$friends	= $jsFriends->getFriendIds( $my->id );
			array_push($friends, $my->id);

			// Insert query here.
			$queryWhere	.= ' AND (';
			$queryWhere	.= ' (a.`private`= 0 ) OR';
			$queryWhere	.= ' ( (a.`private` = 20) AND (' . $db->Quote( $my->id ) . ' > 0 ) ) OR';

			if( empty( $friends ) )
			{
				$queryWhere	.= ' ( (a.`private` = 30) AND ( 1 = 2 ) ) OR';
			}
			else
			{
				$queryWhere	.= ' ( (a.`private` = 30) AND ( a.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'created_by' ) . ' IN (' . implode( ',' , $friends ) . ') ) ) OR';
			}

			$queryWhere	.= ' ( (a.`private` = 40) AND ( a.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'created_by' ) .'=' . $my->id . ') )';
			$queryWhere	.= ' )';
		}
		else
		{
			if( $my->id == 0)
			{
				$queryWhere .= ' AND a.`private` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);
			}
		}

		if($isBloggerMode !== false)
		{
			$queryWhere .= ' AND a.`created_by` = ' . $db->Quote($isBloggerMode);
		}

		$contentId	= '';
		$isIdArray	= false;
		if(is_array($typeId))
		{
			if(count($typeId) > 1)
			{
				for( $i = 0; $i < count($typeId ); $i++ )
				{
					if( $typeId[ $i ] )
					{
						$contentId	.= $typeId[ $i ];

						if( $i + 1 < count($typeId) )
						{
							$contentId .= ',';
						}
					}
				}
				$isIdArray  = true;
			}
			else
			{
				if(!empty($typeId))
				{
					$contentId	= $typeId[0];
				}
			}
		}
		else
		{
			$contentId  = $typeId;
		}

		if($contentId)
		{
			switch( $type )
			{
				case 'category':
					$queryWhere	.= ($isIdArray) ? ' AND a.`category_id` IN ('. $contentId .')' : ' AND a.`category_id` = ' . $db->Quote($contentId);

					if($isBloggerMode === false)
					{
						$catBloggerId   = EasyBlogHelper::getCategoryMenuBloggerId();
						if( !empty($catBloggerId) )
						{
							$queryWhere	.= ' AND a.`created_by` = ' . $db->Quote($catBloggerId);
						}
					}

					break;
				case 'blogger':
					$queryWhere	.= ($isIdArray) ? ' AND a.`created_by` IN ('. $contentId .')' : ' AND a.`created_by` = ' . $db->Quote($contentId);
					break;
				case 'teamblog':
					$queryWhere	.= ($isIdArray) ? ' AND u.`team_id` IN ('. $contentId .')' : ' AND u.`team_id` = ' . $db->Quote($contentId);
					break;
				default :
					break;
			}
		}

		// @rule: Filter for `source` column type.
		if( !is_null( $postType ) )
		{
			switch( $postType )
			{
				case 'microblog':
					$queryWhere .= ' AND a.`source` != ' . $db->Quote( '' );
				break;
				case 'posts':
					$queryWhere .= ' AND a.`source` = ' . $db->Quote( '' );
				break;
			}
		}

		if($type == 'blogger' || $type == 'teamblog')
		{
			if(! empty($statType))
			{
				if($statType == 'category')
					$queryWhere	.= ' AND a.`category_id` = ' . $db->Quote($statId);
				else
					$queryWhere	.= ' AND t.`tag_id` = ' . $db->Quote($statId);
			}
		}

		if( $search )
		{
			$queryWhere	.= ' AND a.`title` LIKE ' . $db->Quote( '%' . $search . '%' );
		}

		if( $frontpage )
		{
			$queryWhere	.= ' AND a.`frontpage` = ' . $db->Quote('1');
		}


		if( $type != 'teamblog' && !$dashboard )
		{
			$tmpIds = array();
			if( $jsGrpPostIds )
			{
				$tmpIds     = array_merge($jsGrpPostIds, $tmpIds);
			}

			if( $jsEventPostIds )
			{
				$tmpIds     = array_merge($jsEventPostIds, $tmpIds);
			}



			if( $config->get( 'main_includeteamblogpost' ) && !empty($teamBlogIds) )
			{
				if( empty( $jsGrpPostIds ) && empty( $jsEventPostIds) )
				{
					$queryWhere	.= ' AND (u.team_id IN ('.$teamBlogIds.') OR a.`issitewide` = ' . $db->Quote('1') . ')';
				}
				else
				{
					$tmpIds     = array_unique($tmpIds);
					$tmpIds 	= implode( ',', $tmpIds);
					$queryWhere	.= ' AND (u.team_id IN ('.$teamBlogIds.') OR a.id IN (' . $tmpIds . ') OR a.`issitewide` = ' . $db->Quote('1') . ')';
				}
			}
			else
			{
				if( $tmpIds )
				{
					$tmpIds     = array_unique($tmpIds);
					$tmpIds 	= implode( ',', $tmpIds);
					$queryWhere	.= ' AND ( a.`issitewide` = 1 OR a.id IN (' . $tmpIds . ') )';
				}
				else
				{
					$queryWhere	.= ' AND a.`issitewide` = ' . $db->Quote('1');
				}
			}
		}

		// show team postings in the entries if they are the admin
		if( $dashboard && $teamBlogIds && $postType != 'microblog')
		{
			$teamIds		= explode( ',' , $teamBlogIds );
			$adminTeamIds	= array();

			foreach( $teamIds as $teamId )
			{
				// We need to test if the user has admin access.
				$team		= EasyBlogHelper::getTable( 'TeamBlog' );
				$team->load( $teamId );

				if( $team->isTeamAdmin( $my->id ) )
				{
					$adminTeamIds[]	= $teamId;
				}
			}

			if( !empty( $adminTeamIds ) )
			{
				$queryWhere	.= ' OR a.`id` IN(';
				$queryWhere .= ' SELECT `post_id` FROM `#__easyblog_team_post` WHERE `team_id` IN(';

				for( $i = 0; $i < count( $adminTeamIds ); $i++ )
				{
					$queryWhere		.= $db->Quote( $adminTeamIds[ $i ] );

					if( next( $adminTeamIds ) !== false )
					{
						$queryWhere .= ',';
					}
				}

				$queryWhere		.= ') )';
			}
		}

		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			// @rule: When language filter is enabled, we need to detect the appropriate contents
			$filterLanguage 	= JFactory::getApplication()->getLanguageFilter();

			if( $filterLanguage )
			{
				$queryWhere	.= ' AND (';
				$queryWhere	.= ' a.`language`=' . $db->Quote( JFactory::getLanguage()->getTag() );
				$queryWhere	.= ' OR a.`language`=' . $db->Quote( '' );
				$queryWhere	.= ' OR a.`language`=' . $db->Quote( '*' );
				$queryWhere	.= ' )';
			}
		}

		if($protected == false)
		{
			$queryWhere	.= ' AND a.`blogpassword` = ""';
		}

		// get the default sorting.
		$defaultSorting = ( $customOrdering ) ? $customOrdering : $config->get( 'layout_postsort', 'desc' );

		$queryOrder 	= ' ORDER BY ';

		$sortableItems 	= array('latest', 'published', 'popular', 'active', 'alphabet', 'modified', 'random');

		if( $frontpage && $config->get( 'layout_featured_pin' ) && $pinFeatured )
		{
			$queryOrder 	.= ' f.`created` DESC ';

			if (in_array($sort, $sortableItems)) {
				$queryOrder 	.= ' ,';
			}
		}

		switch( $sort )
		{
			case 'latest':
				$queryOrder	.= ' a.`created` ' . $defaultSorting;
				break;
			case 'published':
				$queryOrder	.= ' a.`publish_up` ' . $defaultSorting;
				break;
			case 'popular':
				$queryOrder	.= ' a.`hits` ' . $defaultSorting;
				break;
			case 'active':
				$queryOrder	.= ' a.`publish_down` ' . $defaultSorting;
				break;
			case 'alphabet':
				$queryOrder	.= ' a.`title` ' . $defaultSorting;
				break;
			case 'modified':
				$queryOrder	.= ' a.`modified` ' . $defaultSorting;
				break;
			case 'random':
				$queryOrder	.= ' `random_id` ';
				break;
			default :
				break;
		}

		if($max > 0)
		{
			$queryLimit	= ' LIMIT '.$max;
		}
		else
		{
			//set frontpage list length if it is detected to be the frontpage
			$view		= JRequest::getCmd('view', '');

			$limit		= EasyBlogHelper::getHelper( 'Pagination' )->getLimit( $limitType );
			$limitstart = JRequest::getVar('limitstart', 0, 'REQUEST');

			// In case limit has been changed, adjust it
			$limitstart = (int) ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

			$queryLimit	= ' LIMIT ' . $limitstart . ',' . $limit;

			$queryPagination = true;
		}

		$excludePendingPost = '';
		if( $dashboard )
			$excludePendingPost = ' and not exists ( select `entry_id` from `#__easyblog_drafts` as ed where ed.`entry_id` = a.`id` and pending_approval = 1 )';

		if($queryPagination)
		{
			$query	= 'SELECT COUNT(1) FROM `#__easyblog_post` AS a';

			if($type == 'teamblog' || ( ( $config->get( 'main_includeteamblogpost' ) || $dashboard ) && !empty($teamBlogIds) ))
			{
				$query  .= ' LEFT JOIN `#__easyblog_team_post` AS u ON a.id = u.post_id';
			}

			if( ($type == 'blogger' || $type == 'teamblog') && $statType == 'tag')
			{
				$query  .= ' LEFT JOIN `#__easyblog_post_tag` AS t ON a.id = t.post_id';
				//$query  .= ' AND t.`tag_id` = ' . $db->Quote($statId);
			}
			$query	.= $queryWhere;
			$query	.= $queryExclude;
			$query	.= $queryInclude;
			$query	.= $excludePendingPost;

			$db->setQuery( $query );
			$this->_total	= $db->loadResult();

			$this->_pagination	= EasyBlogHelper::getPagination( $this->_total , $limitstart , $limit );
		}


		$query	= 'SELECT a.`id` AS key1, a.*, b.`id` as key2, b.`title` as `category`';
		if( ($type == 'teamblog' || $config->get( 'main_includeteamblogpost' ) || $dashboard ) && !empty($teamBlogIds) )
			$query  .= ' ,u.`team_id` ';

		if( $sort == 'random' )
			$query .= ', floor( 1 + rand() * rd.`rid` ) as `random_id`';

		$query .= ' FROM `#__easyblog_post` AS a';
		$query .= ' LEFT JOIN `#__easyblog_category` AS b';
		$query .= ' ON a.category_id = b.id';

		if( $frontpage && $config->get( 'layout_featured_pin' ) && $pinFeatured )
		{
			$query	.= ' LEFT JOIN `#__easyblog_featured` AS f';
			$query	.= ' 	ON a.`id` = f.`content_id` AND f.`type` = ' . $db->Quote('post');
		}

		if( $type == 'teamblog' || ( ( $config->get( 'main_includeteamblogpost' ) || $dashboard ) && !empty($teamBlogIds) ) )
		{
			$query  .= ' LEFT JOIN `#__easyblog_team_post` AS u ON a.id = u.post_id';
		}

		if( ($type == 'blogger' || $type == 'teamblog') && $statType == 'tag')
		{
			$query  .= ' LEFT JOIN `#__easyblog_post_tag` AS t ON a.`id` = t.`post_id`';
			$query  .= ' AND t.`tag_id` = ' . $db->Quote($statId);
		}

		if( $sort == 'random' )
		{
			$query .= ', (select max(tmp.`id`) - 1 as `rid` from `#__easyblog_post` as tmp ) as rd';
		}


		$query	.= $queryWhere;
		$query	.= $queryExclude;
		$query	.= $queryInclude;
		$query	.= $excludePendingPost;
		$query	.= $queryOrder;
		$query	.= $queryLimit;


		// echo str_ireplace( '#__' , 'jos_' , $query );
		// exit;

		$db->setQuery($query);

		if($db->getErrorNum() > 0)
		{
			JError::raiseError( $db->getErrorNum() , $db->getErrorMsg() . $db->stderr());
		}

		$result	= $db->loadObjectList();
		return $result;
	}


	function getPending( $typeId = 0, $sort = 'latest', $max = 0 , $search = false, $frontpage = false )
	{
		$db	= EasyBlogHelper::db();

		$queryPagination	= false;
		$queryWhere		= '';
		$queryOrder		= '';
		$queryLimit		= '';
		$queryWhere		= '';
		$queryExclude	= '';

		$queryWhere	.= ' WHERE a.`pending_approval` = ' . $db->Quote('1');

		if( $search )
		{
			$queryWhere	.= ' AND a.`title` LIKE ' . $db->Quote( '%' . $search . '%' );
		}

		if( ! empty( $typeId ) )
		{
			$queryWhere	.= ' AND a.`created_by` = ' . $db->Quote( $typeId );
		}

		switch( $sort )
		{
			case 'latest':
				$queryOrder	= ' ORDER BY a.`created` DESC';
				break;
			case 'popular':
				$queryOrder	= ' ORDER BY a.`hits` DESC';
				break;
			case 'active':
				$queryOrder	= ' ORDER BY a.`publish_down` DESC';
				break;
			case 'alphabet':
				$queryOrder	= ' ORDER BY a.`title` ASC';
				break;
			default :
				break;
		}

		if($max > 0)
		{
			$queryLimit	= ' LIMIT '.$max;
		}
		else
		{
			$limit		= $this->getState('limit');
			$limitstart = $this->getState('limitstart');

			//set frontpage list length if it is detected to be the frontpage
			$view		= JRequest::getCmd('view', '');

			if($view=='latest')
			{
				$config		= EasyBlogHelper::getConfig();
				$listlength = $config->get('layout_listlength', '0');

				if($listlength)
				{
					$limit = $listlength;
					$limitstart = JRequest::getVar('limitstart', 0, 'REQUEST');

					// In case limit has been changed, adjust it
					$limitstart = (int) ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
				}
			}

			$queryLimit	= ' LIMIT ' . $limitstart . ',' . $limit;

			$queryPagination = true;
		}

		if($queryPagination)
		{
			$query	= 'SELECT COUNT(1) FROM `#__easyblog_drafts` AS a';

			$query	.= $queryWhere;
			$query	.= $queryExclude;

			$db->setQuery( $query );
			$this->_total	= $db->loadResult();

			jimport('joomla.html.pagination');
			$this->_pagination	= EasyBlogHelper::getPagination( $this->_total , $limitstart , $limit );
		}


		$query	= 'SELECT a.*, b.`title` AS `category` FROM `#__easyblog_drafts` AS a';
		$query	.= ' LEFT JOIN `#__easyblog_category` AS b';
		$query	.= ' ON a.category_id = b.id';

		$query	.= $queryWhere;
		$query	.= $queryExclude;
		$query	.= $queryOrder;
		$query	.= $queryLimit;

		// echo $query;

		$db->setQuery($query);
		if($db->getErrorNum() > 0)
		{
			JError::raiseError( $db->getErrorNum() , $db->getErrorMsg() . $db->stderr());
		}

		$result	= $db->loadObjectList();
		return $result;
	}


	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		return $this->_pagination;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		return $this->_total;
	}

	/**
	 * Method to get total blogs post currently iregardless the status.
	 *
	 * @access public
	 * @return integer
	 */
	function getTotalBlogs( $userId	= 0 )
	{
		$db		= EasyBlogHelper::db();
		$where	= array();

		$query	= 'SELECT COUNT(1) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_post' );

		//blog privacy setting
		$my = JFactory::getUser();
		if($my->id == 0)
			$where[] = '`private` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);

		if(! empty($userId) )
			$where[] = '`created_by` = ' . $db->Quote($userId);

		$extra	= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		$query	= $query . $extra;

		$db->setQuery( $query );

		$result	= $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	function getTotalBlogSubscribers( $userId = 0 )
	{
		$db		= EasyBlogHelper::db();
		$where	= array();

		$query	= 'select count(1) from `#__easyblog_post_subscription` as a';
		$query	.= '  inner join `#__easyblog_post` as b';
		$query	.= '    on a.post_id = b.id';
		if(! empty($userId))
		$query	.= '    and b.created_by = ' . $db->Quote($userId);

		$db->setQuery( $query );
		$result	= $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	/**
	 * Method to retrieve blog posts based on the given tag id.
	 *
	 * @access public
	 * @param	int		$tagId	The tag id.
	 * @return	array	$rows	An array of blog objects.
	 */
	function getTaggedBlogs( $tagId = 0 , $limit = false, $includeCatIds = '' )
	{
		if( $tagId ==  0 )
			return false;

		$my		= JFactory::getUser();
		$db		= EasyBlogHelper::db();
		$config	= EasyBlogHelper::getConfig();

		if( $limit === false )
		{
			if( $config->get( 'layout_listlength' ) == 0 )
			{
				$limit	= $this->getState( 'limit' );
			}
			else
			{
				$limit	= $config->get( 'layout_listlength' );
			}
		}
		$limitstart = $this->getState('limitstart');

		$isBloggerMode	= EasyBlogRouter::isBloggerMode();
		$queryExclude	= '';
		$excludeCats	= array();


		$isJSGrpPluginInstalled	= false;
		$isJSGrpPluginInstalled	= JPluginHelper::isEnabled('system', 'groupeasyblog');
		$isEventPluginInstalled	= JPluginHelper::isEnabled( 'system' , 'eventeasyblog' );
		$isJSInstalled			= false; // need to check if the site installed jomsocial.

		if(JFile::exists(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR. 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR .'core.php'))
		{
			$isJSInstalled = true;
		}


		$includeJSGrp	= ( $isJSGrpPluginInstalled && $isJSInstalled) ? true : false;
		$includeJSEvent	= ( $isEventPluginInstalled && $isJSInstalled ) ? true : false;
		$jsGrpPostIds	= '';
		$jsEventPostIds	= '';

		if( $includeJSEvent )
		{
			$queryEvent	= 'SELECT ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'post_id' ) . ' FROM';
			$queryEvent	.= ' ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_external' ) . ' AS ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'a' );
			$queryEvent	.= ' INNER JOIN' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__community_events' ) . ' AS ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'b' );
			$queryEvent	.= ' ON ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'a' ) . '.uid = ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'b' ) . '.id';
			$queryEvent	.= ' AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'a' ) . '.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'source' ) . '=' . $db->Quote( 'jomsocial.event' );
			$queryEvent	.= ' WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'b' ) . '.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'permission' ) . '=' . $db->Quote( 0 );

			$db->setQuery($queryEvent);
			$jsEventPostIds		= $db->loadResultArray();

			if( !empty( $excludeBlogs ) && !empty( $jsEventPostIds ) )
			{
				$jsEventPostIds	= array_diff($jsEventPostIds, $excludeBlogs);
			}
		}

		if( $includeJSGrp )
		{
			$queryJSGrp = 'select `post_id` from `#__easyblog_external_groups` as exg inner join `#__community_groups` as jsg';
			$queryJSGrp .= '      on exg.group_id = jsg.id ';
			$queryJSGrp .= '      where jsg.`approvals` = 0';

			$db->setQuery($queryJSGrp);
			$jsGrpPostIds   = $db->loadResultArray();

			if( !empty( $excludeBlogs ) && !empty( $jsGrpPostIds ) )
			{
				$jsGrpPostIds	= array_diff($jsGrpPostIds, $excludeBlogs);
			}
		}

		//get teamblogs id.
		if( $config->get( 'main_includeteamblogpost' ) )
		{
			$teamBlogIds	= EasyBlogHelper::getViewableTeamIds();
			if( count( $teamBlogIds ) > 0 )
				$teamBlogIds	= implode( ',' , $teamBlogIds);
		}

		// get all private categories id
		$excludeCats	= EasyBlogHelper::getPrivateCategories();

		if(! empty($excludeCats))
		{
			$queryExclude .= ' AND b.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		$query	= 'SELECT b.*, c.`title` as `category`';

		if($config->get( 'main_includeteamblogpost' ) && !empty($teamBlogIds) )
			$query  .= ' ,u.`team_id`';

		$query	.= ' FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_post_tag' ) . ' AS a ';
		$query	.= ' INNER JOIN ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_post' ) . ' AS b ';
		$query	.= ' ON a.post_id=b.id ';
		$query	.= ' LEFT JOIN `#__easyblog_category` AS c';
		$query	.= ' ON b.category_id = c.id';

		if($config->get( 'main_includeteamblogpost' ) && !empty($teamBlogIds) )
		{
			$query  .= ' LEFT JOIN `#__easyblog_team_post` AS u ON b.id = u.post_id';
		}


		$query	.= ' WHERE a.tag_id = ' . $db->Quote( $tagId );
		$query	.= ' AND b.`published` = ' . $db->Quote('1');

		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			// @rule: When language filter is enabled, we need to detect the appropriate contents
			$filterLanguage 	= JFactory::getApplication()->getLanguageFilter();

			if( $filterLanguage )
			{
				$query	.= ' AND (';
				$query	.= ' b.`language`=' . $db->Quote( JFactory::getLanguage()->getTag() );
				$query	.= ' OR b.`language`=' . $db->Quote( '' );
				$query	.= ' OR b.`language`=' . $db->Quote( '*' );
				$query	.= ' )';
			}
		}

		$file	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';
		//blog privacy setting
		if( $config->get( 'main_jomsocial_privacy' ) && JFile::exists( $file ) && !EasyBlogHelper::isSiteAdmin())
		{
			require_once( $file );

			$jsFriends	= CFactory::getModel( 'Friends' );
			$friends	= $jsFriends->getFriendIds( $my->id );
			array_push($friends, $my->id);

			// Insert query here.
			$query	.= ' AND (';
			$query	.= ' (b.`private`= 0 ) OR';
			$query	.= ' ( (b.`private` = 20) AND (' . $db->Quote( $my->id ) . ' > 0 ) ) OR';

			if( empty( $friends ) )
			{
				$query	.= ' ( (b.`private` = 30) AND ( 1 = 2 ) ) OR';
			}
			else
			{
				$query	.= ' ( (b.`private` = 30) AND ( b.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'created_by' ) . ' IN (' . implode( ',' , $friends ) . ') ) ) OR';
			}

			$query	.= ' ( (b.`private` = 40) AND ( b.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'created_by' ) .'=' . $my->id . ') )';
			$query	.= ' )';
		}
		else
		{
			if( $my->id == 0)
			{
				$query .= ' AND b.`private` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);
			}
		}


		if($isBloggerMode !== false)
			$query .= ' AND b.`created_by` = ' . $db->Quote($isBloggerMode);


// 		if( !empty( $jsGrpPostIds ) )
// 		{
// 			$tmpIds = implode( ',', $jsGrpPostIds);
// 			$query	.= ' AND ( b.id IN (' . $tmpIds . ') OR b.`issitewide` = ' . $db->Quote('1') . ')';
// 		}

		$jsTmpIds = array();
		if( $jsGrpPostIds )
		{
			$jsTmpIds     = array_merge($jsGrpPostIds, $jsTmpIds);
		}

		if( $jsEventPostIds )
		{
			$jsTmpIds     = array_merge($jsEventPostIds, $jsTmpIds);
		}

		if( $jsTmpIds )
		{
			$jsTmpIds     = array_unique($jsTmpIds);
			$extmpIds 	= implode( ',', $jsTmpIds);
			$query	.= ' AND ( b.id IN (' . $extmpIds . ') OR b.`issitewide` = ' . $db->Quote('1') . ')';
		}

		if( $config->get( 'main_includeteamblogpost' ) && ( !empty($teamBlogIds) || !empty( $jsTmpIds ) ) )
		{
			$query 	.= ' AND (';

			if( !empty( $teamBlogIds ) )
			{
				$query	.= ' u.`team_id` IN (' . $teamBlogIds . ')';
			}

			if( $jsTmpIds )
			{
				$extmpIds 	= implode( ',', $jsTmpIds);
 				$query		.= ( $teamBlogIds ) ? ' OR' : '';
				$query		.= ' b.`id` IN(' . $extmpIds . ')';
			}

			$query	.= ' OR b.`issitewide`=' . $db->Quote( 1 );
			$query	.= ')';
		}
		else
		{
			$query	.= ' AND b.`issitewide` = ' . $db->Quote('1');
		}

		$includeCats	= array();
		$includeCatIds	= trim( $includeCatIds );
		if( !empty( $includeCatIds) )
		{
			$includeCats = explode( ',' , $includeCatIds );

			if( !empty($excludeCats) )
			{
				$includeCats	= array_diff($includeCats, $excludeCats);
			}

			if( !empty( $includeCats ) )
			{
				$query .= ' AND b.`category_id` IN (' . implode(',', $includeCats) . ')';
			}
		}


		$query .= $queryExclude;
		  //$query .= ' ORDER BY `created` DESC';


		  $sort = $config->get( 'layout_postorder', 'latest' );
		  $defaultSorting = $config->get( 'layout_postsort', 'desc' );

		  switch( $sort )
		  {
		   case 'latest':
		    $queryOrder = ' ORDER BY b.`created` ' . $defaultSorting;
		    break;
		   case 'published':
		    $queryOrder = ' ORDER BY b.`publish_up` ' . $defaultSorting;
		    break;
		   case 'popular':
		    $queryOrder = ' ORDER BY b.`hits` ' . $defaultSorting;
		    break;
		   case 'active':
		    $queryOrder = ' ORDER BY b.`publish_down` ' . $defaultSorting;
		    break;
		   case 'alphabet':
		    $queryOrder = ' ORDER BY b.`title` ' . $defaultSorting;
		    break;
		   case 'modified':
		    $queryOrder = ' ORDER BY b.`modified` ' . $defaultSorting;
		    break;
		   case 'random':
		    $queryOrder = ' ORDER BY RAND() ';
		    break;
		   default :
		    break;
		  }

 		 $query .= $queryOrder;

		//total tag's post sql
		$totalQuery	= 'SELECT COUNT(1) FROM (';
		$totalQuery	.= $query;
		$totalQuery	.= ') as x';



		$query	.= ' LIMIT ' . $limitstart . ',' . $limit;


		$db->setQuery( $query );
		$rows	= $db->loadObjectList();


		$db->setQuery( $totalQuery );

		$db->loadResult();
		$this->_total	= $db->loadResult();

		jimport('joomla.html.pagination');
		$this->_pagination	= EasyBlogHelper::getPagination( $this->_total , $limitstart , $limit );

		return $rows;
	}


	function isBlogSubscribedUser($blogId, $userId, $email)
	{
		$db	= EasyBlogHelper::db();

		$query	= 'SELECT `id` FROM `#__easyblog_post_subscription`';
		$query	.= ' WHERE `post_id` = ' . $db->Quote($blogId);
		$query	.= ' AND (`user_id` = ' . $db->Quote($userId);
		$query	.= ' OR `email` = ' . $db->Quote($email) .')';

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	function isBlogSubscribedEmail($blogId, $email)
	{
		$db	= EasyBlogHelper::db();

		$query	= 'SELECT `id` FROM `#__easyblog_post_subscription`';
		$query	.= ' WHERE `post_id` = ' . $db->Quote($blogId);
		$query	.= ' AND `email` = ' . $db->Quote($email);

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	function addBlogSubscription($blogId, $email, $userId = '0', $fullname = '')
	{
		$config = EasyBlogHelper::getConfig();
		$acl = EasyBlogACLHelper::getRuleSet();
		$my = JFactory::getUser();

		if($acl->rules->allow_subscription || (empty($my->id) && $config->get('main_allowguestsubscribe')))
		{
			$date		= EasyBlogHelper::getDate();
			$subscriber	= EasyBlogHelper::getTable( 'Subscription', 'Table' );

			$subscriber->post_id	= $blogId;
			$subscriber->email		= $email;
			if($userId != '0')
				$subscriber->user_id	= $userId;

			$subscriber->fullname	= $fullname;
			$subscriber->created	= $date->toMySQL();
			$state = $subscriber->store();

			if( $state )
			{
				$blog = EasyBlogHelper::getTable( 'Blog', 'Table');
				$blog->load( $blogId );

				// lets send confirmation email to subscriber.
				$helper 	= EasyBlogHelper::getHelper( 'Subscription' );
				$template 	= $helper->getTemplate();

				$template->uid 			= $subscriber->id;
				$template->utype 		= 'subscription';
				$template->user_id 		= $subscriber->user_id;
				$template->uemail 		= $email;
				$template->ufullname 	= $fullname;
				$template->ucreated 	= $subscriber->created;
				$template->targetname 	= $blog->title;
				$template->targetlink	= EasyBlogRouter::getRoutedURL('index.php?option=com_easyblog&view=entry&id=' . $blogId, false, true );

				if($blog->created_by != $subscriber->user_id)
				{
					$helper->addMailQueue( $template );
				}
			}

			return $state;

		}

		return false;
	}

	function updateBlogSubscriptionEmail($sid, $userid, $email)
	{
		$config = EasyBlogHelper::getConfig();
		$acl = EasyBlogACLHelper::getRuleSet();
		$my = JFactory::getUser();

		if($acl->rules->allow_subscription || (empty($my->id) && $config->get('main_allowguestsubscribe')))
		{
			$subscriber = EasyBlogHelper::getTable( 'Subscription', 'Table' );
			$subscriber->load($sid);
			$subscriber->user_id	= $userid;
			$subscriber->email		= $email;
			$subscriber->store();
		}
	}

	function getCategoryName( $category_id )
	{
		$db = EasyBlogHelper::db();

		if($category_id == 0)
			return JText::_('COM_EASYBLOG_UNCATEGORIZED');

		$query  = 'SELECT `title`, `id` FROM `#__easyblog_category` WHERE `id` = ' . $db->Quote($category_id);
		$db->setQuery($query);

		$result = $db->loadResult();
		return $result;
	}

	function getTrackback( $blogId )
	{
		$db = EasyBlogHelper::db();

		$query	= 'SELECT * FROM `#__easyblog_trackback`';
		$query	.= ' WHERE `post_id` = ' . $db->Quote($blogId);
		$query	.= ' AND `published`=' . $db->Quote( 1 );
		$query	.= ' ORDER BY `created` DESC';

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	function getRelatedBlog( $blogId, $max	= 0 )
	{
		$db		= EasyBlogHelper::db();
		$config	= EasyBlogHelper::getConfig();

		$result = array();
		$limit	= ($max == 0) ? $config->get('main_max_relatedpost', '5') : $max;

		$tagQuery   = 'select `tag_id` from `#__easyblog_post_tag` where `post_id` = ' . $db->Quote( $blogId );
		$db->setQuery($tagQuery);
		$tags = $db->loadResultArray();

		if( count( $tags ) > 0 )
		{
			//$query	= 'select count(a.`tag_id`) as `cnt`, c.*, k.`title` as `category`';
			$query	= 'select distinct c.*, k.`title` as `category`';
			$query	.= ' from `#__easyblog_post_tag` as a';

    		$query	.= '   inner join `#__easyblog_post_tag` as a1';
			$query	.= '   on a.tag_id = a1.tag_id and a1.post_id = ' . $db->Quote( $blogId );

			$query	.= '   inner join `#__easyblog_post` as c';
			$query	.= '   on a.`post_id` = c.`id`';

			$query	.= '   left join `#__easyblog_category` as k';
			$query	.= '   on k.`id` = c.`category_id`';


			$query	.= ' WHERE a.`post_id` != ' . $db->Quote($blogId);
			$query	.= ' and c.`published` = ' . $db->Quote('1');
			// $query	.= ' group by (a.`post_id`)';
			// $query	.= ' order by `cnt` desc';

			if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
			{
				// @rule: When language filter is enabled, we need to detect the appropriate contents
				$filterLanguage 	= JFactory::getApplication()->getLanguageFilter();

				if( $filterLanguage )
				{
					$query	.= ' AND (';
					$query	.= ' c.`language`=' . $db->Quote( JFactory::getLanguage()->getTag() );
					$query	.= ' OR c.`language`=' . $db->Quote( '' );
					$query	.= ' OR c.`language`=' . $db->Quote( '*' );
					$query	.= ' )';
				}
			}


			//
			$query	.= ' limit ' . $limit;

			//echo $query;

			$db->setQuery($query);
			$result = $db->loadObjectList();

		}

		return $result;
	}

	function approveBlog( $id )
	{
		$db		= EasyBlogHelper::db();

		$query	= 'UPDATE `#__easyblog_post` SET `ispending`= ' . $db->Quote('0') . ' WHERE `id` = ' . $db->Quote($id) . ';';
		$db->setQuery($query);

		if (!($db->query())) {
			JError::raiseError( 500, $db->stderr() );
			return false;
		}

		return true;
	}

	public function getBlogPostsCount( $userId , $dashboard = true )
	{
		$db		= EasyBlogHelper::db();

		$query	= 'SELECT COUNT(1) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_post' ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'source' ) . '=' . $db->Quote( '' ) . ' '
				. 'AND (' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'created_by' ) . '=' . $db->Quote( $userId ) . ' '
				. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '!=' . $db->Quote( POST_ID_TRASHED );

		$teamBlogIds	= EasyBlogHelper::getViewableTeamIds();

		// show team postings in the entries if they are the admin
		if( $dashboard && $teamBlogIds )
		{
			$adminTeamIds	= array();

			foreach( $teamBlogIds as $teamId )
			{
				// We need to test if the user has admin access.
				$team		= EasyBlogHelper::getTable( 'TeamBlog' );
				$team->load( $teamId );

				if( $team->isTeamAdmin( JFactory::getUser()->id ) )
				{
					$adminTeamIds[]	= $teamId;
				}
			}

			if( !empty( $adminTeamIds ) )
			{
				$query	.= ' OR `id` IN(';
				$query .= ' SELECT `post_id` FROM `#__easyblog_team_post` WHERE `team_id` IN(';

				for( $i = 0; $i < count( $adminTeamIds ); $i++ )
				{
					$query		.= $db->Quote( $adminTeamIds[ $i ] );

					if( next( $adminTeamIds ) !== false )
					{
						$query .= ',';
					}
				}

				$query		.= ') )';
			}
		}
		$query	.= ')';

		$db->setQuery( $query );
		$result = $db->loadResult();

		return $result;
	}

	public function getMicroPostsCount( $userId , $dashboard = true )
	{
		$db		= EasyBlogHelper::db();

		$query	= 'SELECT COUNT(1) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_post' ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'source' ) . '!=' . $db->Quote( '' ) . ' '
				. 'AND (' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'created_by' ) . '=' . $db->Quote( $userId ) . ' '
				. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '!=' . $db->Quote( POST_ID_TRASHED );
		$teamBlogIds	= EasyBlogHelper::getViewableTeamIds();

		// show team postings in the entries if they are the admin
		if( $dashboard && $teamBlogIds )
		{
			$adminTeamIds	= array();

			foreach( $teamBlogIds as $teamId )
			{
				// We need to test if the user has admin access.
				$team		= EasyBlogHelper::getTable( 'TeamBlog' );
				$team->load( $teamId );

				if( $team->isTeamAdmin( JFactory::getUser()->id ) )
				{
					$adminTeamIds[]	= $teamId;
				}
			}

			if( !empty( $adminTeamIds ) )
			{
				$query	.= ' OR `id` IN(';
				$query .= ' SELECT `post_id` FROM `#__easyblog_team_post` WHERE `team_id` IN(';

				for( $i = 0; $i < count( $adminTeamIds ); $i++ )
				{
					$query		.= $db->Quote( $adminTeamIds[ $i ] );

					if( next( $adminTeamIds ) !== false )
					{
						$query .= ',';
					}
				}

				$query		.= ') )';
			}
		}
		$query	.= ')';
		$db->setQuery( $query );
		$result = $db->loadResult();

		return $result;
	}
}
