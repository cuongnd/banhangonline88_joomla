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

class EasyBlogModelSubscription extends EasyBlogModel
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
		return $this->_pagination;
	}
	
    function isSiteSubscribedUser($userId, $email)
    {
		$db	= EasyBlogHelper::db();

        $query  = 'SELECT `id` FROM `#__easyblog_site_subscription`';
        $query  .= ' WHERE (`user_id` = ' . $db->Quote($userId);
		$query  .= ' OR `email` = ' . $db->Quote($email) .')';

        $db->setQuery($query);
        $result = $db->loadResult();

        return $result;
    }

    function isSiteSubscribedEmail($email)
    {
		$db	= EasyBlogHelper::db();

        $query  = 'SELECT `id` FROM `#__easyblog_site_subscription`';
        $query  .= ' WHERE `email` = ' . $db->Quote($email);

        $db->setQuery($query);
        $result = $db->loadResult();

        return $result;
    }
    
    function addSiteSubscription($email, $userId = '0', $fullname = '')
    {
    	$config 	= EasyBlogHelper::getConfig();
    	$acl		= EasyBlogACLHelper::getRuleSet();
    	$my			= JFactory::getUser();
			
		if( $acl->rules->allow_subscription || (empty($my->id) && $config->get('main_allowguestsubscribe')))
		{
			$date       	= EasyBlogHelper::getDate();

			$subscription	= EasyBlogHelper::getTable( 'SiteSubscription' );

			$subscription->email 	= $email;

			if( $userId )
			{
				$subscription->user_id		= $userId;
			}

			$subscription->fullname		= $fullname;
			$subscription->created		= EasyBlogHelper::getDate()->toMySQL();

			$state = $subscription->store();

			if( $state )
			{
				// lets send confirmation email to subscriber.
				$helper 	= EasyBlogHelper::getHelper( 'Subscription' );
				$template 	= $helper->getTemplate();

				$template->uid 			= $subscription->id;
				$template->utype 		= 'sitesubscription';
				$template->user_id 		= $subscription->user_id;
				$template->uemail 		= $email;
				$template->ufullname 	= $fullname;
				$template->ucreated 	= $subscription->created;
				$template->targetname 	= $config->get('main_title');
				$template->targetlink	= EasyBlogRouter::getRoutedURL('index.php?option=com_easyblog&view=latest', false, true );	

				$helper->addMailQueue( $template );
			}

			return $state;


		}
    }

    function updateSiteSubscriptionEmail($sid, $userid, $email)
    {
    	$config = EasyBlogHelper::getConfig();
    	$acl = EasyBlogACLHelper::getRuleSet();
    	$my = JFactory::getUser();
    	
		if($acl->rules->allow_subscription || (empty($my->id) && $config->get('main_allowguestsubscribe')))
		{
			$subscriber = EasyBlogHelper::getTable( 'SiteSubscription', 'Table' );
			$subscriber->load($sid);
			$subscriber->user_id  = $userid;
			$subscriber->email    = $email;
			$subscriber->store();
		}
    }
    
    function getSiteSubscribers()
    {
        $db = EasyBlogHelper::db();
        
        $query  = "SELECT *, 'sitesubscription' as `type` FROM `#__easyblog_site_subscription`";

        $db->setQuery($query);
        $result = $db->loadObjectList();

        return $result;
    }
    
    function getMembersAndSubscribers()
    {
        $db = EasyBlogHelper::db();
        
        // do not get superadmin users.
        
		$query  = "(select `id`, `id` as `user_id`, `name` as `fullname`, `email`, now() as `created`, 'member' as `type` from `#__users`";
		$query  .= " where `block` = 0";
		if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
		{
		    $saUsersIds	= EasyBlogHelper::getSAUsersIds();
		    $query	.= " and `id` NOT IN (" . implode(',', $saUsersIds) . ")";
		}
		else
		{
			$query	.= " and LOWER( `usertype` ) != " . $db->Quote('super administrator');
		}
		$query  .= ")";
		$query  .= " union ";
		$query  .= "(select `id`, `user_id`, `fullname`, `email`, `created` , 'bloggersubscription' as `type` from `#__easyblog_blogger_subscription` where `user_id` = 0)";
		$query  .= " union ";
		$query  .= "(select `id`, `user_id`, `fullname`, `email`, `created` , 'categorysubscription' as `type` from `#__easyblog_category_subscription` where `user_id` = 0)";
		$query  .= " union ";
		$query  .= "(select `id`, `user_id`, `fullname`, `email`, `created` , 'teamsubscription' as `type` from `#__easyblog_team_subscription` where `user_id` = 0)";
		$query  .= " union ";
		$query  .= "(select `id`, `user_id`, `fullname`, `email`, `created` , 'sitesubscription' as `type` from `#__easyblog_site_subscription` where `user_id` = 0)";

        $db->setQuery($query);
        $result = $db->loadObjectList();

        return $result;
    }
		
}