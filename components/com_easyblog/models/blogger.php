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

class EasyBlogModelBlogger extends EasyBlogModel
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

	function getBloggers($sort = 'latest', $limit = 0, $filter='showallblogger' , $search = '' )
	{
		if($filter == 'showbloggerwithpost')
		{
			$result = $this->getBloggersWithPost( $sort, $limit, $filter, $search);
		}
		else
		{
			$result = $this->getAllBloggers( $sort, $limit, $filter, $search);
		}

		return $result;
	}

	function getBloggersWithPost($sort = 'latest', $limit = 0, $filter='showbloggerwithpost' , $search = '' )
	{
		$db	= EasyBlogHelper::db();

		require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );
		$config				= EasyBlogHelper::getConfig();
		$nameDisplayFormat	= $config->get('layout_nameformat');

		$limit		= ($limit == 0) ? $this->getState('limit') : $limit;
		$limitstart = $this->getState('limitstart');
		$limitSQL	= ' LIMIT ' . $limitstart . ',' . $limit;

		$excludedQuery	= '';
		$excluded		= EasyBlogHelper::getConfig()->get( 'layout_exclude_bloggers' );

		if( !empty( $excluded ) )
		{
			$tmp	= explode( ',' , $excluded );
			$values	= array();

			foreach( $tmp as $id )
			{
				$values[]	= $db->Quote( $id );
			}
			$excludedQuery	= ' AND p.`created_by` NOT IN (' . implode( ',' , $values ) . ')';
		}

		$searchQuery = '';
		if( !empty( $search ) )
		{
			$searchQuery	.= ' AND ';

			switch( $nameDisplayFormat )
			{
				case 'name':
					$searchQuery	.= '`name` LIKE ' . $db->Quote( '%' . $search . '%' );
				break;
				case 'username':
					$searchQuery	.= '`username` LIKE ' . $db->Quote( '%' . $search . '%');
				break;
				default:
					$searchQuery	.= '`nickname` LIKE ' . $db->Quote( '%' . $search . '%' );
				break;
			}
		}

		$query	= 'SELECT COUNT(1) FROM (';
		$query  .= ' select p.id';
		$query  .= ' from `#__easyblog_post` as p';
		$query  .= '  inner join #__users a on p.created_by = a.id';
		$query  .= '  inner JOIN `#__easyblog_users` AS `b` ON p.`created_by` = b.`id`';
		$query  .= '  LEFT JOIN `#__easyblog_featured` AS `g` ON a.`id`= g.`content_id` AND g.`type`= ' . $db->Quote( 'blogger' );
		$query  .= ' where `p`.`published`=1';
		$query	.= $excludedQuery;
		$query	.= $searchQuery;
		$query  .= ' GROUP BY p.`created_by`';
		$query  .= ' ) as x';

		$db->setQuery( $query );
		$this->_total	= $db->loadResult();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}

		if( empty($this->_pagination) )
		{
			jimport('joomla.html.pagination');
			$this->_pagination	= EasyBlogHelper::getPagination( $this->_total , $limitstart , $limit );
		}


		// actual data
		$query  = 'select count( p.id ) as `totalPost`, p.id, MAX(p.`created`) as `latestPostDate`, COUNT( DISTINCT(g.content_id) ) as `featured`,';
		$query  .= ' a.`id`, b.`nickname`, a.`name`, a.`username`, a.`registerDate`, a.`lastvisitDate`';
		$query  .= ' from `#__easyblog_post` as p';
		$query  .= '  inner join #__users a on p.created_by = a.id';
		$query  .= '  inner JOIN `#__easyblog_users` AS `b` ON p.`created_by` = b.`id`';
		$query  .= '  LEFT JOIN `#__easyblog_featured` AS `g` ON a.`id`= g.`content_id` AND g.`type`= ' . $db->Quote( 'blogger' );
		$query  .= ' where `p`.`published`=1';
		$query	.= $excludedQuery;
		$query	.= $searchQuery;
		$query  .= ' GROUP BY p.`created_by`';

		switch($sort)
		{
			case 'featured':
				$query	.= ' ORDER BY `featured` DESC';
				break;
			case 'latestpost' :
				$query .= '	ORDER BY p.`id` DESC';
				break;
			case 'latest' :
				$query .= '	ORDER BY `registerDate` DESC';
				break;
			case 'active' :
				$query	.= ' ORDER BY `lastvisitDate` DESC';
				break;
			case 'alphabet' :
				if($nameDisplayFormat == 'name')
					$query .= '	ORDER BY `name` ASC';
				else if($nameDisplayFormat == 'username')
					$query .= '	ORDER BY `username` ASC';
				else
					$query .= '	ORDER BY `nickname` ASC';
				break;
			default	:
				break;
		}
		$query	.= 	$limitSQL;

		//echo $query;

		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}

		return $result;

	}

	function getAllBloggers($sort = 'latest', $limit = 0, $filter='showallblogger' , $search = '' )
	{
		$db	= EasyBlogHelper::db();

		require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );
		$config				= EasyBlogHelper::getConfig();
		$nameDisplayFormat	= $config->get('layout_nameformat');

		$limit		= ($limit == 0) ? $this->getState('limit') : $limit;
		$limitstart = $this->getState('limitstart');
		$limitSQL	= ' LIMIT ' . $limitstart . ',' . $limit;


		//first let get the id for add_entry acl
		$query  = 'select `id` from `#__easyblog_acl` where `action` = ' . $db->Quote( 'add_entry ' );
		$db->setQuery( $query );
		$aclId  = $db->loadResult();


		$query	= 'SELECT COUNT(1) FROM (';
		$query	.= ' (SELECT a.`id`, b.`nickname`, b.`avatar`, b.`description`,';
		$query	.= ' a.`name`, a.`username`, a.`registerDate`, a.`lastvisitDate` ';
		$query	.= ' FROM `#__users` AS `a`';

		if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
		{
			$query	.= '  INNER JOIN `#__user_usergroup_map` AS `d` ON a.`id` = d.`user_id`';
		}
		else
		{
			$query	.= '  INNER JOIN `#__core_acl_aro` AS `c` ON a.`id` = c.`value`';
			$query	.= '    AND c.`section_value` = ' . $db->Quote('users');
			$query	.= '  INNER JOIN `#__core_acl_groups_aro_map` AS `d` ON c.`id` = d.`aro_id`';
		}

		$query	.= '  INNER JOIN `#__easyblog_acl_group` AS `e` ON d.`group_id`  = e.`content_id`';

		$query	.= '  LEFT JOIN `#__easyblog_users` AS `b` ON a.`id` = b.`id`';
		$query 	.= '  LEFT JOIN `#__easyblog_post` AS `p` ON a.`id` = p.`created_by`';
		$query	.= '	AND `p`.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote( POST_ID_PUBLISHED );

		$query  .= ' WHERE e.acl_id = ' . $db->Quote( $aclId );
		$query	.= ' AND e.`type` = ' . $db->Quote('group') . ' AND e.`status` = 1';


		$query 	.= '  GROUP BY a.`id`';

		if($filter == 'showbloggerwithpost')
		{
			$query 	.= '  HAVING (COUNT(p.`id`) > 0)';
		}

		$query 	.= ' )';

		$query	.= ' UNION ';
		$query	.= ' (SELECT a1.`id`, b1.`nickname`, b1.`avatar`, b1.`description`,';
		$query	.= ' a1.`name`, a1.`username`, a1.`registerDate`, a1.`lastvisitDate` ';
		$query	.= ' FROM `#__users` AS `a1`';
		$query	.= '  INNER JOIN `#__easyblog_acl_group` AS `c1` ON a1.`id`  = c1.`content_id`';

		$query	.= '  LEFT JOIN `#__easyblog_users` AS `b1` ON a1.`id` = b1.`id`';
		$query 	.= '  LEFT JOIN `#__easyblog_post` AS `p1` ON a1.`id` = p1.`created_by`';
		$query	.= '	AND `p1`.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote( POST_ID_PUBLISHED );

		$query  .= ' WHERE c1.acl_id = ' . $db->Quote( $aclId );
		$query	.= ' AND c1.`type` = ' . $db->Quote('assigned') . ' AND c1.`status` = 1';

		$query 	.= '  GROUP BY a1.`id`';

		if($filter == 'showbloggerwithpost')
		{
			$query 	.= '  HAVING (COUNT(p1.`id`) > 0)';
		}

		$query 	.= ' )';

		$query  .= ' ) as x';

		$excludedQuery	= '';
		$excluded		= EasyBlogHelper::getConfig()->get( 'layout_exclude_bloggers' );

		if( !empty( $excluded ) )
		{
			$tmp	= explode( ',' , $excluded );
			$values	= array();

			foreach( $tmp as $id )
			{
				$values[]	= $db->Quote( $id );
			}
			$excludedQuery	= ' WHERE x.`id` NOT IN(' . implode( ',' , $values ) . ')';
		}

		$query	.= $excludedQuery;


		// Ensure that the count also respects the search
		if( !empty( $search ) )
		{
			switch( $nameDisplayFormat )
			{
				case 'name':
					$query	.= 'AND x.`name` LIKE ' . $db->Quote( '%' . $search . '%');
				break;
				case 'username':
					$query	.= 'AND x.`username` LIKE ' . $db->Quote( '%' . $search . '%' );
				break;
				default:
					$query	.= 'AND x.`nickname` LIKE ' . $db->Quote( '%' . $search . '%' );
				break;
			}
		}

		$db->setQuery( $query );

		$this->_total	= $db->loadResult();

		if($db->getErrorNum())
		{
			var_dump( $db->stderr() );exit;
			JError::raiseError( 500, $db->stderr());
		}

		if( empty($this->_pagination) )
		{
			jimport('joomla.html.pagination');
			$this->_pagination	= EasyBlogHelper::getPagination( $this->_total , $limitstart , $limit );
		}


		$query	= 'SELECT x.* FROM (';
		$query	.= ' (SELECT a.`id`, b.`nickname`, b.`avatar`, b.`description`,';
		$query	.= ' a.`name`, a.`username`, a.`registerDate`, a.`lastvisitDate`,';
		$query  .= ' COUNT(p.`id`) as `totalPost`, MAX(p.`created`) as `latestPostDate`,';
		$query	.= ' COUNT( DISTINCT(g.content_id) ) as `featured`';
		$query	.= ' FROM `#__users` AS `a`';
		$query	.= '  LEFT JOIN `#__easyblog_users` AS `b` ON a.`id` = b.`id`';

		if(EasyBlogHelper::getJoomlaVersion() >= '1.6'){
			$query	.= '  INNER JOIN `#__user_usergroup_map` AS `d` ON a.`id` = d.`user_id`';
		} else {
			$query	.= '  INNER JOIN `#__core_acl_aro` AS `c` ON a.`id` = c.`value`';
			$query	.= '    AND c.`section_value` = ' . $db->Quote('users');
			$query	.= '  INNER JOIN `#__core_acl_groups_aro_map` AS `d` ON c.`id` = d.`aro_id`';
		}

		$query	.= '  INNER JOIN `#__easyblog_acl_group` AS `e` ON d.`group_id`  = e.`content_id`';

		$query	.= '  LEFT JOIN `#__easyblog_featured` AS `g` ON a.`id`= g.`content_id` AND g.`type`=' . $db->Quote( 'blogger' );
		$query 	.= '  LEFT JOIN `#__easyblog_post` AS `p` ON a.`id` = p.`created_by`';
		$query	.= '	AND `p`.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote( POST_ID_PUBLISHED );

		$query  .= ' WHERE e.acl_id = ' . $db->Quote( $aclId );
		$query	.= ' AND e.`type` = ' . $db->Quote('group') . ' AND e.`status` = 1';


		$query 	.= '  GROUP BY a.`id`';
		if($filter == 'showbloggerwithpost')
			$query 	.= '  HAVING (COUNT(p.`id`) > 0)';
		$query 	.= ' )';

		$query	.= ' UNION ';
		$query	.= ' (SELECT a1.`id`, b1.`nickname`, b1.`avatar`, b1.`description`,';
		$query	.= ' a1.`name`, a1.`username`, a1.`registerDate`, a1.`lastvisitDate`,';
		$query  .= ' COUNT(p1.`id`) as `totalPost`, MAX(p1.`created`) as `latestPostDate`,';
		$query	.= ' COUNT( DISTINCT(g1.`content_id`) ) as `featured`';
		$query	.= ' FROM `#__users` AS `a1`';
		$query	.= '  LEFT JOIN `#__easyblog_users` AS `b1` ON a1.`id` = b1.`id`';
		$query	.= '  INNER JOIN `#__easyblog_acl_group` AS `c1` ON a1.`id`  = c1.`content_id`';
		//$query	.= '    AND c1.`type` = ' . $db->Quote('assigned') . ' AND c1.`status` = 1';

		//$query	.= '  INNER JOIN `#__easyblog_acl` as `d1` ON c1.`acl_id` = d1.`id`';
		//$query	.= '    AND d1.`action` = ' . $db->Quote('add_entry');

		$query	.= '  LEFT JOIN `#__easyblog_featured` AS `g1` ON a1.`id`= g1.`content_id` AND g1.`type`=' . $db->Quote( 'blogger' );
		$query 	.= '  LEFT JOIN `#__easyblog_post` AS `p1` ON a1.`id` = p1.`created_by`';
		$query	.= '	AND `p1`.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote( POST_ID_PUBLISHED );

		$query  .= ' WHERE c1.acl_id = ' . $db->Quote( $aclId );
		$query	.= ' AND c1.`type` = ' . $db->Quote('assigned') . ' AND c1.`status` = 1';


		$query 	.= '  GROUP BY a1.`id`';
		if($filter == 'showbloggerwithpost')
			$query 	.= '  HAVING (COUNT(p1.`id`) > 0)';
		$query 	.= ' )';

		$query  .= ' ) as x';
		$query	.= $excludedQuery;

		if( !empty( $search ) )
		{
			$query	.=  !empty( $excludedQuery ) ? ' AND ' : ' WHERE ';

			switch( $nameDisplayFormat )
			{
				case 'name':
					$query	.= 'x.`name` LIKE ' . $db->Quote( '%' . $search . '%');
				break;
				case 'username':
					$query	.= 'x.`username` LIKE ' . $db->Quote( '%' . $search . '%' );
				break;
				default:
					$query	.= 'x.`nickname` LIKE ' . $db->Quote( '%' . $search . '%' );
				break;
			}
		}

		switch($sort)
		{
			case 'featured':
				$query	.= ' ORDER BY x.`featured` DESC';
				break;
			case 'latestpost' :
				$query .= '	ORDER BY x.`latestPostDate` DESC';
				break;
			case 'latest' :
				$query .= '	ORDER BY x.`registerDate` DESC';
				break;
			case 'active' :
				$query	.= ' ORDER BY x.`lastvisitDate` DESC';
				break;
			case 'alphabet' :
				if($nameDisplayFormat == 'name')
					$query .= '	ORDER BY x.`name` ASC';
				else if($nameDisplayFormat == 'username')
					$query .= '	ORDER BY x.`username` ASC';
				else
					$query .= '	ORDER BY x.`nickname` ASC';
				break;
			default	:
				break;
		}
		$query	.= 	$limitSQL;


		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}

		return $result;

	}

	function isBloggerSubscribedUser($bloggerId, $userId, $email)
	{
		$db	= EasyBlogHelper::db();

		$query  = 'SELECT `id` FROM `#__easyblog_blogger_subscription`';
		$query  .= ' WHERE `blogger_id` = ' . $db->Quote($bloggerId);
		$query  .= ' AND (`user_id` = ' . $db->Quote($userId);
		$query  .= ' OR `email` = ' . $db->Quote($email) .')';

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	function isBloggerSubscribedEmail($bloggerId, $email)
	{
		$db	= EasyBlogHelper::db();

		$query  = 'SELECT `id` FROM `#__easyblog_blogger_subscription`';
		$query  .= ' WHERE `blogger_id` = ' . $db->Quote($bloggerId);
		$query  .= ' AND `email` = ' . $db->Quote($email);

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	function addBloggerSubscription($bloggerId, $email, $userId = '0', $fullname = '')
	{
		$config = EasyBlogHelper::getConfig();
		$acl = EasyBlogACLHelper::getRuleSet();
		$my = JFactory::getUser();

		if($acl->rules->allow_subscription || (empty($my->id) && $config->get('main_allowguestsubscribe')))
		{
			$date       = EasyBlogHelper::getDate();
			$subscriber = EasyBlogHelper::getTable( 'BloggerSubscription', 'Table' );

			$subscriber->blogger_id = $bloggerId;
			$subscriber->email    	= $email;
			if($userId != '0')
				$subscriber->user_id    = $userId;

			$subscriber->fullname	= $fullname;
			$subscriber->created	= $date->toMySQL();
			$state = $subscriber->store();

			if( $state )
			{
				$profile = EasyBlogHelper::getTable( 'Profile', 'Table');
				$profile->load( $bloggerId );

				// lets send confirmation email to subscriber.
				$helper 	= EasyBlogHelper::getHelper( 'Subscription' );
				$template 	= $helper->getTemplate();

				$template->uid 			= $subscriber->id;
				$template->utype 		= 'bloggersubscription';
				$template->user_id 		= $subscriber->user_id;
				$template->uemail 		= $email;
				$template->ufullname 	= $fullname;
				$template->ucreated 	= $subscriber->created;
				$template->targetname 	= $profile->getName();
				$template->targetlink	= EasyBlogRouter::getRoutedURL('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $bloggerId, false, true );	

				$helper->addMailQueue( $template );
			}

			return $state;

		}


	}

	function updateBloggerSubscriptionEmail($sid, $userid, $email)
	{
		$config = EasyBlogHelper::getConfig();
		$acl = EasyBlogACLHelper::getRuleSet();
		$my = JFactory::getUser();

		if($acl->rules->allow_subscription || (empty($my->id) && $config->get('main_allowguestsubscribe')))
		{
			$subscriber = EasyBlogHelper::getTable( 'BloggerSubscription', 'Table' );
			$subscriber->load($sid);
			$subscriber->user_id  = $userid;
			$subscriber->email    = $email;
			$subscriber->store();
		}
	}

	function getBlogggerSubscribers($bloggerId)
	{
		$db = EasyBlogHelper::db();

		$query  = "SELECT *, 'bloggersubscription' as `type` FROM `#__easyblog_blogger_subscription`";
		$query  .= " WHERE `blogger_id` = " . $db->Quote($bloggerId);

		//echo $query . '<br/><br/>';

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	function getTagUsed($bloggerId)
	{
		$db = EasyBlogHelper::db();

		$query  = 'select distinct a.* from `#__easyblog_tag` as a';
		$query  .= ' inner join `#__easyblog_post_tag` as b on a.`id` = b.`tag_id`';
		$query  .= ' inner join `#__easyblog_post` as c on b.`post_id` = c.`id`';
		$query	.= ' where c.`created_by` = ' . $db->Quote($bloggerId);

		$db->setQuery($query);

		$result	= $db->loadObjectList();
		return $result;
	}

	function getCategoryUsed($bloggerId)
	{
		$db = EasyBlogHelper::db();

		$query  = 'select distinct a.*, count(b.`id`) as `post_count` from `#__easyblog_category` as a';
		$query  .= ' inner join `#__easyblog_post` as b ON a.id = b.category_id';
		$query  .= ' where b.`created_by` = ' . $db->Quote($bloggerId);
		$query  .= ' and b.published = ' . $db->Quote('1');
		$query  .= ' group by a.id';

		$db->setQuery($query);

		$result	= $db->loadObjectList();
		return $result;
	}

	function getTotalBlogCreated($bloggerId)
	{
		$db = EasyBlogHelper::db();

		$query  = 'select count(1) as `post_count`';
		$query	.= ' from `#__easyblog_post` as a';
		$query  .= ' where a.`created_by` = ' . $db->Quote($bloggerId);
		$query  .= ' and a.published = ' . $db->Quote('1');

		$db->setQuery($query);

		$result	= $db->loadResult();
		return $result;
	}

}
