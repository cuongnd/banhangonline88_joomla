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
class EasyBlogModelArchive extends EasyBlogModel
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

	function getArchive( $archiveYear, $archiveMonth, $archiveDay='')
	{
		$db	= EasyBlogHelper::db();
		$my = JFactory::getUser();

		$config         = EasyBlogHelper::getConfig();
		$isBloggerMode  = EasyBlogRouter::isBloggerMode();
		$excludeCats	= array();
		$teamBlogIds    = '';
		$queryExclude   = '';
		$queryInclude   = '';

		$modCid			= JRequest::getVar( 'modCid', array() );

		//where
		$queryWhere	= ' WHERE a.`published` = 1';
		$queryWhere	.= ' AND a.`ispending` = 0';


	    //get teamblogs id.
	    $query  = '';
	    if( $config->get( 'main_includeteamblogpost' ) )
	    {
			$teamBlogIds	= EasyBlogHelper::getViewableTeamIds();
			if( count( $teamBlogIds ) > 0 )
            	$teamBlogIds    = implode( ',' , $teamBlogIds);
	    }

	    //var_dump($teamBlogIds);
	    $excludeCats	= EasyBlogHelper::getPrivateCategories();

		if(! empty($excludeCats))
		{
		    $queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		if(! empty($modCid))
		{
		    $queryInclude .= ' AND a.`category_id` IN (' . implode(',', $modCid) . ')';
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
		if( $config->get( 'integrations_easysocial_privacy' ) && $easysocial->exists() && !EasyBlogHelper::isSiteAdmin() )
		{
			$esPrivacyQuery  = $easysocial->buildPrivacyQuery( 'a' );
			$queryWhere 	.= $esPrivacyQuery;
		}
		else if( $config->get( 'main_jomsocial_privacy' ) && JFile::exists( $file ) && !EasyBlogHelper::isSiteAdmin() )
		{
			require_once( $file );

			$my			= JFactory::getUser();
			$jsFriends	= CFactory::getModel( 'Friends' );
			$friends	= $jsFriends->getFriendIds( $my->id );

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

	    if( $config->get( 'main_includeteamblogpost' ) && !empty($teamBlogIds))
	    {
			$queryWhere	.= ' AND (u.team_id IN ('.$teamBlogIds.') OR a.`issitewide` = ' . $db->Quote('1') . ')';
		}
		else
		{
		    $queryWhere	.= ' AND a.`issitewide` = ' . $db->Quote('1');
		}


		if(empty($archiveDay))
		{
			$fromDate	= $archiveYear.'-'.$archiveMonth.'-01 00:00:00';
			$toDate		= $archiveYear.'-'.$archiveMonth.'-31 23:59:59';
		}
		else
		{
			$fromDate	= $archiveYear.'-'.$archiveMonth.'-'.$archiveDay.' 00:00:00';
			$toDate		= $archiveYear.'-'.$archiveMonth.'-'.$archiveDay.' 23:59:59';
		}

		// When language filter is enabled, we need to detect the appropriate contents
		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
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

		$tzoffset   = EasyBlogDateHelper::getOffSet( true );
		$queryWhere	.= ' AND ( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) >= '. $db->Quote($fromDate) .' AND DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) <= '. $db->Quote($toDate) . ' ) ';

		if($isBloggerMode !== false)
		    $queryWhere .= ' AND a.`created_by` = ' . $db->Quote($isBloggerMode);

		//ordering
		$queryOrder	= ' ORDER BY a.`created` DESC';

		//limit
		$limit		= $this->getState('limit');
		$limitstart = $this->getState('limitstart');
		$queryLimit	= ' LIMIT ' . $limitstart . ',' . $limit;

		//set pagination
		$query	= 'SELECT COUNT(1) FROM `#__easyblog_post` AS a';
		$query .= ' LEFT JOIN `#__easyblog_category` AS b';
		$query .= ' ON a.category_id = b.id';

		if( $config->get( 'main_includeteamblogpost' ) && !empty($teamBlogIds) )
		{
		    $query  .= ' LEFT JOIN `#__easyblog_team_post` AS u ON a.id = u.post_id';
		}

		$query	.= $queryWhere;
		$db->setQuery( $query );
		$this->_total	= $db->loadResult();
		jimport('joomla.html.pagination');
		$this->_pagination	= EasyBlogHelper::getPagination( $this->_total , $limitstart , $limit );

		//get archive
		$query	= 'SELECT a.*, b.`title` AS `category`';
		if( $config->get( 'main_includeteamblogpost' ) && !empty($teamBlogIds) )
		    $query  .= ' ,u.team_id';


		$query .= ' FROM `#__easyblog_post` AS a';
		$query .= ' LEFT JOIN `#__easyblog_category` AS b';
		$query .= ' ON a.category_id = b.id';

		if( $config->get( 'main_includeteamblogpost' ) && !empty($teamBlogIds) )
		{
		    $query  .= ' LEFT JOIN `#__easyblog_team_post` AS u ON a.id = u.post_id';
		}


		$query .= $queryWhere;
		$query .= $queryExclude;
		$query .= $queryInclude;
		$query .= $queryOrder;
		$query .= $queryLimit;

		// echo $query . '<br><br>';

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

    function getArchiveMinMaxYear()
	{
		$db 	= EasyBlogHelper::db();
		$user	= JFactory::getUser();

		$query	= 'SELECT YEAR(MIN( '.EasyBlogHelper::getHelper( 'SQL' )->nameQuote('created').' )) AS minyear, '
				. 'YEAR(MAX( '.EasyBlogHelper::getHelper( 'SQL' )->nameQuote('created').' )) AS maxyear '
				. 'FROM '.EasyBlogHelper::getHelper( 'SQL' )->nameQuote('#__easyblog_post').' '
				. 'WHERE '.EasyBlogHelper::getHelper( 'SQL' )->nameQuote('published').' = '.$db->Quote(true).' ';

		if(empty($user->id))
		{
			$query .= 'AND '.EasyBlogHelper::getHelper( 'SQL' )->nameQuote('private').' = '.$db->Quote(false).' ';
		}

		$db->setQuery($query);
		$row = $db->loadAssoc();

		if(empty($row['minyear']) || empty($row['maxyear']))
		{
			$year = array();
		}
		else
		{
			$year = $row;
		}

		return $year;
	}

	function getArchivePostCount($yearStart='', $yearStop='0', $excludeCats = '')
	{
		$result = self::getArchivePostCounts($yearStart, $yearStop, $excludeCats, '');
		return $result;
	}

	function getArchivePostCounts($yearStart='', $yearStop='0', $excludeCats = '', $includeCats = '')
	{
		$db 	= EasyBlogHelper::db();
		$user	= JFactory::getUser();
		$config = EasyBlogHelper::getConfig();

		if(empty($yearStart))
		{
			$year		= $this->getArchiveMinMaxYear();
			$yearStart	= $year['maxyear'];
		}

		if(!empty($yearStop))
		{
			$fr = $yearStart - 1;
			$to	= $yearStop + 1;
		}
		else
		{
			$fr = $yearStart - 1;
			$to	= $yearStart + 1;
		}

		if( !is_array( $excludeCats ) && !empty( $excludeCats ) )
		{
			$excludeCats	= explode( ',' , $excludeCats );
		}
		else if( !is_array( $excludeCats ) && empty( $excludeCats ) )
		{
			$excludeCats    = array();
		}


		if( !is_array( $includeCats ) && !empty( $includeCats ) )
		{
			$includeCats	= explode( ',' , $includeCats );
		}
		else if( !is_array( $includeCats ) && empty( $includeCats ) )
		{
			$includeCats    = array();
		}

		$includeCats    = array_diff( $includeCats, $excludeCats );

		$excludeCatIds = '';
		if( !empty( $excludeCats ) && count( $excludeCats ) >= 1 )
		{
			foreach($excludeCats as $cat)
			{
				if( trim($cat) != '')
				{
					$excludeCatIds = empty( $excludeCatIds ) ? $db->Quote($cat) : $excludeCatIds . ',' . $db->Quote($cat);
				}
			}
		}

		$includeCatIds = '';
		if( !empty( $includeCats ) && count( $includeCats ) >= 1 )
		{
			foreach($includeCats as $icat)
			{
				if( trim($icat) != '')
				{
					$includeCatIds = empty( $includeCatIds ) ? $db->Quote($icat) : $includeCatIds . ',' . $db->Quote($icat);
				}
			}
		}

		//blog privacy setting
		// @integrations: jomsocial privacy
		$privateBlog = '';

		$file		= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';

		$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );

		if( $config->get( 'integrations_easysocial_privacy' ) && $easysocial->exists() && !EasyBlogHelper::isSiteAdmin() )
		{
			$esPrivacyQuery  = $easysocial->buildPrivacyQuery( 'a' );
			$privateBlog 	.= $esPrivacyQuery;
		}
		else if( $config->get( 'main_jomsocial_privacy' ) && JFile::exists( $file ) && !EasyBlogHelper::isSiteAdmin() )
		{
			require_once( $file );

			$jsFriends	= CFactory::getModel( 'Friends' );
			$friends	= $jsFriends->getFriendIds( $user->id );

			// Insert query here.
			$privateBlog	.= ' AND (';
			$privateBlog	.= ' (a.`private`= 0 ) OR';
			$privateBlog	.= ' ( (a.`private` = 20) AND (' . $db->Quote( $user->id ) . ' > 0 ) ) OR';

			if( empty( $friends ) )
			{
				$privateBlog	.= ' ( (a.`private` = 30) AND ( 1 = 2 ) ) OR';
			}
			else
			{
				$privateBlog	.= ' ( (a.`private` = 30) AND ( a.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'created_by' ) . ' IN (' . implode( ',' , $friends ) . ') ) ) OR';
			}

			$privateBlog	.= ' ( (a.`private` = 40) AND ( a.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'created_by' ) .'=' . $user->id . ') )';
			$privateBlog	.= ' )';
		}
		else
		{
			if( $user->id == 0)
			{
				$privateBlog .= ' AND a.`private` = ' . $db->Quote(0);
			}
		}

		$catExcludeSQL = (! empty($excludeCatIds)) ? 'AND `category_id` NOT IN ('.$excludeCatIds.')' : '';

		$catIncludeSQL = (! empty($includeCatIds)) ? 'AND `category_id` IN ('.$includeCatIds.')' : '';

		$languageFilterSQL = '';
		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			// @rule: When language filter is enabled, we need to detect the appropriate contents
			$filterLanguage 	= JFactory::getApplication()->getLanguageFilter();

			if( $filterLanguage )
			{
				$languageFilterSQL	.= ' AND (';
				$languageFilterSQL	.= ' a.`language`=' . $db->Quote( JFactory::getLanguage()->getTag() );
				$languageFilterSQL	.= ' OR a.`language`=' . $db->Quote( '' );
				$languageFilterSQL	.= ' OR a.`language`=' . $db->Quote( '*' );
				$languageFilterSQL	.= ' )';
			}
		}

		$query	= 'SELECT COUNT(1) as count, MONTH( a.'.EasyBlogHelper::getHelper( 'SQL' )->nameQuote('created').' ) AS month, YEAR( a.'.EasyBlogHelper::getHelper( 'SQL' )->nameQuote('created').' ) AS year '
				. 'FROM '.EasyBlogHelper::getHelper( 'SQL' )->nameQuote('#__easyblog_post').' AS a '
				. 'INNER JOIN ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_category') . ' AS b '
				. 'ON a.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'category_id' ) . '=b.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'id' ) . ' '
				. 'WHERE a.'.EasyBlogHelper::getHelper( 'SQL' )->nameQuote('published').' = '.$db->Quote(POST_ID_PUBLISHED).' '
				. $privateBlog.' '
				. $catExcludeSQL.' '
				. $catIncludeSQL.' '
				. $languageFilterSQL. ' '
				. 'AND ( a.'.EasyBlogHelper::getHelper( 'SQL' )->nameQuote('created').' > '.$db->Quote($fr.'-12-31 23:59:59').' AND a.'.EasyBlogHelper::getHelper( 'SQL' )->nameQuote('created').' < '.$db->Quote($to.'-01-01 00:00:00').') '
				. 'GROUP BY year, month DESC '
				. 'ORDER BY a.'.EasyBlogHelper::getHelper( 'SQL' )->nameQuote('created').' DESC ';

		$db->setQuery($query);
		$row = $db->loadAssocList();


		if(empty($row))
		{
			return false;
		}

		$postCount = new stdClass();
		foreach($row as $data)
		{
			if(!isset($postCount->{$data['year']}))
				$postCount->{$data['year']} = new stdClass();
			$postCount->{$data['year']}->{$data['month']} = $data['count'];
		}

		return $postCount;
	}


	function getArchivePostCountByMonth($month='', $year='', $showPrivate=true)
	{
		$db 	= EasyBlogHelper::db();
		$user	= JFactory::getUser();

		$privateBlog = $showPrivate? '' : 'AND '.EasyBlogHelper::getHelper( 'SQL' )->nameQuote('private').' = '.$db->Quote(false);

		$tzoffset   = EasyBlogDateHelper::getOffSet( true );
		$query	= 'SELECT COUNT(1) as count, DAY( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS day,';
		$query	.= ' MONTH( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS month,';
		$query	.= ' YEAR( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS year ';
		$query	.= ' FROM '.EasyBlogHelper::getHelper( 'SQL' )->nameQuote('#__easyblog_post');
		$query	.= ' WHERE '.EasyBlogHelper::getHelper( 'SQL' )->nameQuote('published').' = '.$db->Quote(POST_ID_PUBLISHED);
		$query	.= ' ' . $privateBlog;
		$query	.= ' AND ('.EasyBlogHelper::getHelper( 'SQL' )->nameQuote('created').' > '.$db->Quote($year.'-'.$month.'-01 00:00:00').' AND '.EasyBlogHelper::getHelper( 'SQL' )->nameQuote('created').' < '.$db->Quote($year.'-'.$month.'-31 23:59:59').')';
		$query	.= ' GROUP BY day, year, month ';
		$query	.= ' ORDER BY '.EasyBlogHelper::getHelper( 'SQL' )->nameQuote('created').' ASC ';

		$db->setQuery($query);
		$row = $db->loadAssocList();

		$postCount = new stdClass();

		for($i=1; $i<=31; $i++)
		{
			$postCount->{$year}->{$month}->{$i} = 0;
		}

		if(!empty($row))
		{
			foreach($row as $data)
			{
				$postCount->{$year}->{$month}->{$data['day']} = $data['count'];
			}
		}

		return $postCount;
	}

	function getArchivePostByMonth( $month='', $year='', $showPrivate=false )
	{
		$db 	= EasyBlogHelper::db();
		$user	= JFactory::getUser();
		$config = EasyBlogHelper::getConfig();

		// used for privacy
		$queryWhere             = '';
		$queryExclude			= '';
		$queryExcludePending    = '';
		$excludeCats			= array();

		if( $user->id == 0) $showPrivate = false;

		if( !$showPrivate )
		{
			$excludeCats	= EasyBlogHelper::getPrivateCategories();
		}

		//blog privacy setting
		// @integrations: jomsocial privacy
		$privateBlog = '';

		$file		= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';

		$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );

		if( $config->get( 'integrations_easysocial_privacy' ) && $easysocial->exists() && !EasyBlogHelper::isSiteAdmin() )
		{
			$esPrivacyQuery  = $easysocial->buildPrivacyQuery( 'a' );
			$privateBlog 	.= $esPrivacyQuery;
		}
		else if( $config->get( 'main_jomsocial_privacy' ) && JFile::exists( $file ) && !EasyBlogHelper::isSiteAdmin() )
		{
			require_once( $file );

			$jsFriends	= CFactory::getModel( 'Friends' );
			$friends	= $jsFriends->getFriendIds( $user->id );

			// Insert query here.
			$privateBlog	.= ' AND (';
			$privateBlog	.= ' (a.`private`= 0 ) OR';
			$privateBlog	.= ' ( (a.`private` = 20) AND (' . $db->Quote( $user->id ) . ' > 0 ) ) OR';

			if( empty( $friends ) )
			{
				$privateBlog	.= ' ( (a.`private` = 30) AND ( 1 = 2 ) ) OR';
			}
			else
			{
				$privateBlog	.= ' ( (a.`private` = 30) AND ( a.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'created_by' ) . ' IN (' . implode( ',' , $friends ) . ') ) ) OR';
			}

			$privateBlog	.= ' ( (a.`private` = 40) AND ( a.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'created_by' ) .'=' . $user->id . ') )';
			$privateBlog	.= ' )';
		}
		else
		{
			if( $user->id == 0)
			{
				$privateBlog .= ' AND a.`private` = ' . $db->Quote(0);
			}
		}

		$privateBlog = $showPrivate? '' : $privateBlog;

	    //get teamblogs id.
	    $teamBlogIds    = '';
	    $query  		= '';
	    if( $config->get( 'main_includeteamblogpost' ) )
	    {
			$teamBlogIds	= EasyBlogHelper::getViewableTeamIds();
			if( count( $teamBlogIds ) > 0 )
            	$teamBlogIds    = implode( ',' , $teamBlogIds);
	    }

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


		$extraSQL   = '';
		$blogger	= EasyBlogRouter::isBloggerMode();
		if( $blogger !== false )
		{
		    $extraSQL   = ' AND a.`created_by` = ' . $db->Quote($blogger);
		}

		$tzoffset   = EasyBlogDateHelper::getOffSet( true );
		$query	= 'SELECT *, DAY( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS day,';
		$query	.= ' MONTH( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS month,';
		$query	.= ' YEAR( DATE_ADD(a.`created`, INTERVAL ' . $tzoffset . ' HOUR) ) AS year ';
		$query  .= ' FROM '.EasyBlogHelper::getHelper( 'SQL' )->nameQuote('#__easyblog_post') . ' as a';
		if( $config->get( 'main_includeteamblogpost' ) )
		{
		    $query  .= ' LEFT JOIN `#__easyblog_team_post` AS u ON a.id = u.post_id';
		}
		$query  .= ' WHERE a.`published` = '.$db->Quote(true).' ';
		$query  .= $privateBlog.' ';
		$query  .= ' AND (a.`created` > ' . $db->Quote($year.'-'.$month.'-01 00:00:00') . ' AND a.`created` < ' . $db->Quote($year.'-'.$month.'-31 23:59:59').') ';
		$query  .= $extraSQL . ' ';

		$query	.= $queryWhere;
		$query  .= ' ORDER BY a.`created` ASC ';

		$db->setQuery($query);
		$row = $db->loadObjectList();

		$postCount = new EasyblogCalendarObject($month, $year);


		if(!empty($row))
		{
			foreach($row as $data)
			{
				if( $postCount->{ $year }->{ $month }->{$data->day} == 0 )
				{
					$postCount->{$year}->{$month}->{$data->day}	= array( $data );
				}
				else
				{
					array_push( $postCount->{$year}->{$month}->{$data->day} , $data );
				}
			}
		}

		return $postCount;
	}

	function getJomSocialPosts()
	{
		$db = EasyBlogHelper::db();

		$isJSGrpPluginInstalled	= false;
		$isJSGrpPluginInstalled	= JPluginHelper::isEnabled( 'system', 'groupeasyblog');
		$isEventPluginInstalled	= JPluginHelper::isEnabled( 'system' , 'eventeasyblog' );
		$isJSInstalled			= false; // need to check if the site installed jomsocial.

		if(JFile::exists(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR. 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR .'core.php'))
		{
			$isJSInstalled = true;
		}

		$includeJSGrp	= ($isJSGrpPluginInstalled && $isJSInstalled) ? true : false;
		$includeJSEvent	= ($isEventPluginInstalled && $isJSInstalled ) ? true : false;

		$jsEventPostIds	= array();
		$jsGrpPostIds	= array();

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
}

class EasyblogCalendarObject
{
	public function __construct( $month, $year )
	{
		$this->{$year} = new stdClass();
		$this->{$year}->{$month} = new stdClass();

		for($i=1; $i<=31; $i++)
		{
			$this->{$year}->{$month}->{$i} = 0;
		}
	}
}
