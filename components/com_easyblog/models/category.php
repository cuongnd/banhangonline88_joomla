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
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'router.php' );

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'model.php' );

class EasyBlogModelCategory extends EasyBlogModel
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

		$limit		= EasyBlogHelper::getHelper( 'Pagination' )->getLimit();
		$limitstart = JRequest::getVar('limitstart', 0, 'REQUEST');

		// In case limit has been changed, adjust it
		$limitstart = (int) ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

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
		return $this->_pagination;
	}

	function _getParentIdsWithPost( $accessibleCatsIds = array() )
	{
		$db	= EasyBlogHelper::db();
		$my = JFactory::getUser();

		$query	= 'select * from `#__easyblog_category`';
		$query	.= ' where `published` = 1';
		$query	.= ' and `parent_id` = 0';

		if( ! empty( $accessibleCatsIds ) )
		{
			$catAccessQuery	= ' `id` IN(';

			if( !is_array( $accessibleCatsIds ) )
			{
				$accessibleCatsIds	= array( $accessibleCatsIds );
			}

			for( $i = 0; $i < count( $accessibleCatsIds ); $i++ )
			{
				$catAccessQuery	.= $db->Quote( $accessibleCatsIds[ $i ]->id );

				if( next( $accessibleCatsIds ) !== false )
				{
					$catAccessQuery	.= ',';
				}
			}
			$catAccessQuery .= ')';

			$query	.= ' and ' . $catAccessQuery;
		}

		$db->setQuery($query);
		$result = $db->loadObjectList();

		$validCat   = array();

		if(count($result) > 0)
		{
			for($i = 0; $i < count($result); $i++)
			{
				$item =& $result[$i];

				$item->childs = null;
				EasyBlogHelper::buildNestedCategories($item->id, $item);

				$catIds     = array();
				$catIds[]   = $item->id;
				EasyBlogHelper::accessNestedCategoriesId($item, $catIds);

				$item->cnt   = $this->getTotalPostCount($catIds);

				if($item->cnt > 0)
				{
					$validCat[] = $item->id;
				}

			}
		}

		return $validCat;
	}

	function getCategories($sort = 'latest', $hideEmptyPost = true, $limit = 0 , $inclusion = array() )
	{
		$db	= EasyBlogHelper::db();

		//blog privacy setting
		$my = JFactory::getUser();
		$isBloggerMode  = EasyBlogRouter::isBloggerMode();

		$orderBy	= '';
		$limit		= ($limit == 0) ? $this->getState('limit') : $limit;
		$limitstart = JRequest::getInt( 'limitstart', $this->getState('limitstart') );
		$limitSQL	= ' LIMIT ' . $limitstart . ',' . $limit;
		$extra		= '';

		$andWhere   = array();

		$andWhere[]	= ' a.`published` = 1';
		$andWhere[]	= ' a.`parent_id` = 0';

		$accessibleCatsIds = EasyBlogHelper::getAccessibleCategories();

		// Respect inclusion categories
		if( !empty( $inclusion ) )
		{
			if( ! empty( $accessibleCatsIds ) )
			{
				$accessibleCatsIdsArray	= array();
				foreach($accessibleCatsIds as $row)
				{
					$accessibleCatsIdsArray[] = $row->id;
				}
				$inclusion = array_intersect($inclusion, $accessibleCatsIdsArray);
			}

			$inclusionQuery	= ' a.`id` IN(';

			if( !is_array( $inclusion ) )
			{
				$inclusion	= array( $inclusion );
			}

			$inclusion	= array_values($inclusion);

			for( $i = 0; $i < count( $inclusion ); $i++ )
			{
				$inclusionQuery	.= $db->Quote( $inclusion[ $i ] );

				if( next( $inclusion ) !== false )
				{
					$inclusionQuery	.= ',';
				}
			}
			$inclusionQuery	.= ')';

			$andWhere[]	= $inclusionQuery;
			$extra		.= ' AND ' . $inclusionQuery;
		}
		else
		{

			if( ! empty( $accessibleCatsIds ) )
			{
				$catAccessQuery	= ' a.`id` IN(';

				if( !is_array( $accessibleCatsIds ) )
				{
					$accessibleCatsIds	= array( $accessibleCatsIds );
				}

				for( $i = 0; $i < count( $accessibleCatsIds ); $i++ )
				{
					$catAccessQuery	.= $db->Quote( $accessibleCatsIds[ $i ]->id );

					if( next( $accessibleCatsIds ) !== false )
					{
						$catAccessQuery	.= ',';
					}
				}
				$catAccessQuery .= ')';

				$andWhere[]	= $catAccessQuery;
			}
		}

		if($isBloggerMode !== false)
		{
			  $andWhere[]		= ' (b.`created_by` = ' . $db->Quote($isBloggerMode) . ' OR a.`created_by` = ' . $db->Quote( $isBloggerMode ) . ')';
		}

		if($hideEmptyPost)
		{
			$arrParentIds	= $this->_getParentIdsWithPost( $accessibleCatsIds );
			if(! empty($arrParentIds))
			{
				$tmpParentId	= implode(',', $arrParentIds);
				$andWhere[]		= ' a.`id` IN (' . $tmpParentId . ')';
			}
			else
			{
				// this mean no categories fond. just return the empty result.
				if( empty($this->_pagination) )
				{
					jimport('joomla.html.pagination');
					$this->_pagination	= EasyBlogHelper::getPagination( 0 , $limitstart , $limit );
				}
				// return array();
			}

			$this->_total   = count($arrParentIds);
		}
		else
		{
			$extra 		= ( count( $andWhere ) ? ' WHERE ' . implode( ' AND ', $andWhere ) : '' );

			$query	= 'SELECT a.`id` FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_category' ) . ' AS a';
			$query	.= ' LEFT JOIN '. EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_post' ) . ' AS b';
			$query	.= ' ON a.`id` = b.`category_id`';
			$query  .= ' AND b.`published` = ' . $db->Quote('1');
			if($my->id == 0)
				$query .= ' AND b.`private` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);

			$query	.= $extra;
			$query	.= ' GROUP BY a.`id`';

			$db->setQuery( $query );
			$result	= $db->loadResultArray();

			$this->_total	= count($result);

			if($db->getErrorNum())
			{
				JError::raiseError( 500, $db->stderr());
			}
		}



		if( empty($this->_pagination) )
		{
			jimport('joomla.html.pagination');
			$this->_pagination	= EasyBlogHelper::getPagination( $this->_total , $limitstart , $limit );
		}

		$extra 		= ( count( $andWhere ) ? ' WHERE ' . implode( ' AND ', $andWhere ) : '' );

		$query	= 'SELECT a.`id`, a.`title`, a.`alias`, a.`private`, COUNT(b.`id`) AS `cnt`';
		$query	.= ' FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_category' ) . ' AS `a`';
		$query	.= ' LEFT JOIN '. EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_post' ) . ' AS b';
		$query	.= ' ON a.`id` = b.`category_id`';
		$query  .= ' AND b.`published` = ' . $db->Quote('1');

		if($my->id == 0)
			$query .= ' AND b.`private` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);
		$query	.= $extra;

		$query	.= ' GROUP BY a.`id`';

		switch($sort)
		{
			case 'popular' :
				$orderBy	= ' ORDER BY `cnt` DESC';
				break;
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
		$query	.= $orderBy;
		$query	.= $limitSQL;

		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}

		return $result;

	}

	function getTotalPostCount($catIds)
	{
		$db	= EasyBlogHelper::db();

		//blog privacy setting
		$my = JFactory::getUser();

		$isBloggerMode  = EasyBlogRouter::isBloggerMode();

		$categoryId = '';
		$isIdArray  = false;

		if(is_array($catIds))
		{
			if(count($catIds) > 1)
			{
				$categoryId	= implode(',', $catIds);
				$isIdArray  = true;
			}
			else
			{
				$categoryId	= $catIds[0];
			}
		}
		else
		{
			$categoryId  = $catIds;
		}


		$query	= 'SELECT COUNT(b.`id`) AS `cnt`';
		$query	.= ' FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_category' ) . ' AS `a`';
		$query	.= ' LEFT JOIN '. EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_post' ) . ' AS b';
		$query	.= ' ON a.`id` = b.`category_id`';
		$query  .= ' AND b.`published` = ' . $db->Quote('1');
		if($my->id == 0)
			$query .= ' AND b.`private` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);

		if($isBloggerMode !== false)
		{
			$query	.= ' AND b.`created_by` = ' . $db->Quote($isBloggerMode);
		}
		else
		{
			$catBloggerId   = EasyBlogHelper::getCategoryMenuBloggerId();
			if( !empty($catBloggerId) )
			{
				$query	.= ' AND b.`created_by` = ' . $db->Quote($catBloggerId);
			}
		}

		$query	.= ' WHERE a.`published` = 1';
		$query	.= ($isIdArray) ? ' AND a.`id` IN (' . $categoryId. ')' :  ' AND a.`id` = ' . $db->Quote($categoryId);

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

		$query	.= ' GROUP BY a.`id` HAVING (COUNT(b.`id`) > 0)';

		$db->setQuery($query);
		$result = $db->loadResultArray();

		if(!empty($result))
		{
			return array_sum($result);
		}
		else
		{
			return '0';
		}
	}

	function devInsertBlog($catogories)
	{
		$db	= EasyBlogHelper::db();

		foreach ($catogories as $cat)
		{

			for($i = 1; $i <= 8; $i++)
			{
				$hits	= rand(10, 50);
				$vote	= rand(5, 30);
				$user	= rand(62, 66);

				$date	= EasyBlogHelper::getDate();

				$blog = EasyBlogHelper::getTable( 'Blog', 'Table' );

				$blog->created_by	= $user;
				$blog->modified		= $date->toMySql();
				$blog->created		= $date->toMySql();
				$blog->publish_up	= $date->toMySql();
				$blog->title		= 'Test '.$cat->title.' Blog Title' . $i;
				$blog->content		= 'Test '.$cat->title.' Blog Content'.$i.'<br />Dancho Danchev: A currently active malware campaign is taking advantage of bogus LinkedIn profiles impersonating celebrities in an attempt to trick users into clicking on links serving bogus media players.<br /><br />Dancho Danchev: A currently active malware campaign is taking advantage of bogus LinkedIn profiles impersonating celebrities in an attempt to trick users into clicking on links serving bogus media players.';
				$blog->excerpt		= 'Test '.$cat->title.' Excerpt '.$i.' Dancho Danchev: A currently active malware campaign is taking advantage of bogus LinkedIn profiles impersonating celebrities in an attempt to trick users into clicking on links serving bogus media players.';
				$blog->category_id	= $cat->id;
				$blog->published	= 1;
				$blog->vote			= $vote;
				$blog->hits			= $hits;

				$blog->store();
			}

		}


	}

	/**
	 * Method to get total category created so far iregardless the status.
	 *
	 * @access public
	 * @return integer
	 */
	function getTotalCategory( $userId = 0 )
	{
		$db		= EasyBlogHelper::db();
		$where	= array();

		$query	= 'SELECT COUNT(1) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_category' );

		if(! empty($userId))
			$where[]  = '`created_by` = ' . $db->Quote($userId);

		$extra 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		$query      = $query . $extra;

		$db->setQuery( $query );

		$result	= $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	function isExist($categoryName, $excludeCatIds='0')
	{
		$db = EasyBlogHelper::db();

		$query  = 'SELECT COUNT(1) FROM #__easyblog_category';
		$query  .= ' WHERE `title` = ' . $db->Quote($categoryName);
		if($excludeCatIds != '0')
			$query  .= ' AND `id` != ' . $db->Quote($excludeCatIds);

		$db->setQuery($query);
		$result = $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	function isCategorySubscribedUser($categoryId, $userId, $email)
	{
		$db	= EasyBlogHelper::db();

		$query  = 'SELECT `id` FROM `#__easyblog_category_subscription`';
		$query  .= ' WHERE `category_id` = ' . $db->Quote($categoryId);
		$query  .= ' AND (`user_id` = ' . $db->Quote($userId);
		$query  .= ' OR `email` = ' . $db->Quote($email) .')';

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	function isCategorySubscribedEmail($categoryId, $email)
	{
		$db	= EasyBlogHelper::db();

		$query  = 'SELECT `id` FROM `#__easyblog_category_subscription`';
		$query  .= ' WHERE `category_id` = ' . $db->Quote($categoryId);
		$query  .= ' AND `email` = ' . $db->Quote($email);

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	function addCategorySubscription($categoryId, $email, $userId = '0', $fullname = '')
	{
		$config = EasyBlogHelper::getConfig();
		$acl = EasyBlogACLHelper::getRuleSet();
		$my = JFactory::getUser();

		if($acl->rules->allow_subscription || (empty($my->id) && $config->get('main_allowguestsubscribe')))
		{
			$date       = EasyBlogHelper::getDate();
			$subscriber = EasyBlogHelper::getTable( 'CategorySubscription', 'Table' );

			$subscriber->category_id 	= $categoryId;
			$subscriber->email    		= $email;
			if($userId != '0')
				$subscriber->user_id    = $userId;

			$subscriber->fullname	= $fullname;
			$subscriber->created  	= $date->toMySQL();
			$state = $subscriber->store();

			if( $state )
			{
				$category = EasyBlogHelper::getTable( 'Category', 'Table');
				$category->load( $categoryId );

				// lets send confirmation email to subscriber.
				$helper 	= EasyBlogHelper::getHelper( 'Subscription' );
				$template 	= $helper->getTemplate();

				$template->uid 			= $subscriber->id;
				$template->utype 		= 'categorysubscription';
				$template->user_id 		= $subscriber->user_id;
				$template->uemail 		= $email;
				$template->ufullname 	= $fullname;
				$template->ucreated 	= $subscriber->created;
				$template->targetname 	= $category->title;
				$template->targetlink	= EasyBlogRouter::getRoutedURL('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $categoryId, false, true );	

				$helper->addMailQueue( $template );
			}

			return $state;			
		}
	}

	function updateCategorySubscriptionEmail($sid, $userid, $email)
	{
		$config = EasyBlogHelper::getConfig();
		$acl = EasyBlogACLHelper::getRuleSet();
		$my = JFactory::getUser();

		if($acl->rules->allow_subscription || (empty($my->id) && $config->get('main_allowguestsubscribe')))
		{
			$subscriber = EasyBlogHelper::getTable( 'CategorySubscription', 'Table' );
			$subscriber->load($sid);
			$subscriber->user_id  = $userid;
			$subscriber->email    = $email;
			$subscriber->store();
		}
	}

	function getCategorySubscribers($categoryId)
	{
		$db = EasyBlogHelper::db();

		$query  = "SELECT *, 'categorysubscription' as `type` FROM `#__easyblog_category_subscription`";
		$query  .= " WHERE `category_id` = " . $db->Quote($categoryId);

		//echo $query . '<br/><br/>';

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	function getTeamBlogCount( $catId )
	{
		$db = EasyBlogHelper::db();
		$isBloggerMode  = EasyBlogRouter::isBloggerMode();

		$query	= 'select count(1) from `#__easyblog_post` as a';
		$query	.= '  inner join `#__easyblog_category` as b';
		$query	.= '    on a.`category_id` = b.`id`';
		$query	.= '    and b.`id` = ' . $db->Quote($catId);
		$query	.= '  inner join `#__easyblog_team_post` as c';
		$query	.= '    on a.`id` = c.`post_id`';
		$query	.= '  where a.`issitewide` = ' . $db->Quote('0');
		if($isBloggerMode !== false)
			$query	.= '  and a.`created_by` = ' . $db->Quote($isBloggerMode);

		$db->setQuery($query);
		$result = $db->loadResult();

		return (empty($result)) ? '0' : $result;
	}

	function allowAclCategory( $catId = 0 )
	{
		$db = EasyBlogHelper::db();

		$gid	= EasyBlogHelper::getUserGids();
		$gids	= '';

		if( count( $gid ) > 0 )
		{
			$temp = array();
			foreach( $gid as $id)
			{
				$temp[] = $db->quote($id);
			}

			$gids = implode( ',', $temp );
		}

		$query  = 'SELECT COUNT(1) FROM `#__easyblog_category_acl`';
		$query .= ' WHERE `acl_id` = ' . $db->quote('1');
		$query .= ' AND `status` = ' . $db->quote('1');
		$query .= ' AND `category_id` = ' . $db->quote($catId);
		if( $gids )
		{
			$query .= ' AND `content_id` IN (' . $gids . ')';
		}

		$db->setQuery( $query );

		return $db->loadResult();
	}

}
