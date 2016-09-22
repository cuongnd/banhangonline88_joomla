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

jimport( 'joomla.application.component.view');
jimport( 'joomla.html.toolbar' );

class EasyBlogViewTeamBlog extends EasyBlogView
{
	function display( $tmpl = null )
	{
		$mainframe	= JFactory::getApplication();
		$document	= JFactory::getDocument();
		$config		= EasyBlogHelper::getConfig();
		$theme		= $config->get( 'layout_theme' );
		$my			= JFactory::getUser();
		$acl		= EasyBlogACLHelper::getRuleSet();

		//setting pathway
		$pathway	= $mainframe->getPathway();

		if( ! EasyBlogRouter::isCurrentActiveMenu( 'teamblog' ) )
			$this->setPathway( JText::_( 'COM_EASYBLOG_TEAMBLOG_BREADCRUMB' ) , '' );

		// set meta tags for teamblog view
		EasyBlogHelper::setMeta( META_ID_TEAMBLOGS, META_TYPE_VIEW );

		$sort		= JRequest::getCmd('sort', $config->get( 'layout_postorder' ) );
		$model		= $this->getModel( 'TeamBlogs' );
		if ( $config->get('main_listprivateteamblog') )
		{
			$teams		= $model->getTeamBlogs();
		}
		else
		{
			$teams		= $model->getPrivateTeamBlogs();
		}
		$pagination	= $model->getPagination();

		//now get the blogs for each category
		$blogModel	= $this->getModel( 'Blog' );

		if(! empty($teams))
		{
			$gid	= EasyBlogHelper::getUserGids();

			for($i = 0; $i < count($teams); $i++)
			{
				$row		=& $teams[$i];
				$team		= EasyBlogHelper::getTable( 'Teamblog', 'Table' );
				$team->load( $row->id );

				// @task: Check if current logged in user is the member of this group
				$row->isMember			= $team->isMember($my->id, $gid);
				$row->isActualMember    = $team->isMember($my->id, $gid, false);

				//now get the teams info
				$members    	= $model->getTeamMembers( $row->id );
				$row->members   = EasyBlogHelper::formatTeamMembers($members);

				$teamBlogs		= array();

				if( $team->access != EBLOG_TEAMBLOG_ACCESS_MEMBER || $row->isMember || EasyBlogHelper::isSiteAdmin() )
				{
					$teamBlogs		= $blogModel->getBlogsBy('teamblog', $row->id, $sort, 5, EBLOG_FILTER_PUBLISHED);

					if(! empty($teamBlogs))
					{
						$teamBlogs	= EasyBlogHelper::formatBlog( $teamBlogs, true );
					}
				}

				$row->tags			= $team->getTags();
				$row->blogs			= $teamBlogs;
				$row->totalEntries	= $team->getPostCount();
				$row->categories	= $team->getCategories();

				$row->isFeatured    = EasyBlogHelper::isFeatured('teamblog', $row->id);

				if($config->get('layout_teamavatar', true))
				{
					$row->avatar   = $team->getAvatar();
				}

				// check if team description is emtpy or not. if yes, show default message.
				if(empty($row->description))
				{
					$row->description   = JText::_('COM_EASYBLOG_TEAMBLOG_NO_DESCRIPTION');
				}

			}
		}

		$title					= EasyBlogHelper::getPageTitle( JText::_( 'COM_EASYBLOG_TEAMBLOG_PAGE_TITLE' ) );

		// @task: Set the page title
		parent::setPageTitle( $title , $pagination , $config->get( 'main_pagetitle_autoappend' ) );

		$tpl	= new CodeThemes();
		$tpl->set( 'teams', $teams );
		$tpl->set( 'pagination' , $pagination->getPagesLinks());
		$tpl->set('siteadmin', EasyBlogHelper::isSiteAdmin() );
		$tpl->set('config', $config);
		$tpl->set('my', $my );
		$tpl->set('acl', $acl );

		echo $tpl->fetch( 'blog.teams.php' );
	}


	function listings()
	{
		$mainframe	= JFactory::getApplication();
		$document	= JFactory::getDocument();
		$config		= EasyBlogHelper::getConfig();
		$theme 		= $config->get( 'layout_theme' );
		$my			= JFactory::getUser();
		$acl		= EasyBlogACLHelper::getRuleSet();

		//setting pathway
		$pathway	= $mainframe->getPathway();

		$id		= JRequest::getInt( 'id' , 0 );

		if( $id == 0 )
		{
			echo JText::_('COM_EASYBLOG_TEAMBLOG_INVALID_ID');
			return;
		}

		// set meta tags for teamblog view
		EasyBlogHelper::setMeta( $id, META_TYPE_TEAM );

		$team	= EasyBlogHelper::getTable( 'TeamBlog' , 'Table' );
		$team->load( $id );
		$team->avatar   = $team->getAvatar();

		$gid		= EasyBlogHelper::getUserGids();
		$isMember   = $team->isMember($my->id, $gid);
		//check if the logged in user a teammember when the team set to member only.
		if($team->access == EBLOG_TEAMBLOG_ACCESS_MEMBER)
		{
			$isMember   = $team->isMember($my->id, $gid);
		}
		$team->isMember    		 = $isMember;
		$team->isActualMember    = $team->isMember($my->id, $gid, false);


		if($team->access == EBLOG_TEAMBLOG_ACCESS_EVERYONE || $team->isMember)
		{
			// Add rss feed link
			$document->addHeadLink( $team->getRSS() , 'alternate' , 'rel' , array('type' => 'application/rss+xml', 'title' => 'RSS 2.0') );
			$document->addHeadLink( $team->getAtom() , 'alternate' , 'rel' , array('type' => 'application/atom+xml', 'title' => 'Atom 1.0') );
		}

		// check if team description is emtpy or not. if yes, show default message.
		if(empty($team->description))
			$team->description   = JText::_('COM_EASYBLOG_TEAMBLOG_NO_DESCRIPTION');

		//add the pathway for teamblog
		if( ! EasyBlogRouter::isCurrentActiveMenu( 'teamblog', $team->id ) )
		{
			if( ! EasyBlogRouter::isCurrentActiveMenu( 'teamblog' ) )
				$this->setPathway(JText::_('COM_EASYBLOG_TEAMBLOG'), EasyBlogRouter::_('index.php?option=com_easyblog&view=teamblog'));

			$this->setPathway($team->title, '');
		}


		$tbModel	= $this->getModel( 'TeamBlogs' );
		$model		= $this->getModel( 'Blog' );
		$blogs		= $model->getBlogsBy( 'teamblog' , $team->id );
		$blogs		= EasyBlogHelper::formatBlog( $blogs, true );
		$pagination	= $model->getPagination();

		//now get the teams info
		$members    		= $tbModel->getTeamMembers( $team->id );
		$teamMembers		= EasyBlogHelper::formatTeamMembers($members);
		$isFeatured         = EasyBlogHelper::isFeatured('teamblog', $team->id);

		$title					= EasyBlogHelper::getPageTitle( $team->title );

		// @task: Set the page title
		parent::setPageTitle( $title , $pagination , $config->get( 'main_pagetitle_autoappend' ) );

		EasyBlogHelper::storeSession($team->id, 'EASYBLOG_TEAMBLOG_ID');

		$tpl	= new CodeThemes();
		$tpl->set( 'team', $team );
		$tpl->set( 'teamMembers', $teamMembers );
		$tpl->set( 'data' , $blogs );
		$tpl->set( 'isFeatured' , $isFeatured );
		$tpl->set( 'pagination', $pagination->getPagesLinks());
		$tpl->set( 'siteadmin', EasyBlogHelper::isSiteAdmin() );
		$tpl->set( 'currentURL' , 'index.php?option=com_easyblog&view=teamblog&layout=listings&id=' . $team->id );
		$tpl->set( 'config', $config);
		$tpl->set('my', $my );
		$tpl->set('acl', $acl );

		echo $tpl->fetch( 'blog.teamblogs.php' );
	}

	function statistic()
	{
		JPluginHelper::importPlugin( 'easyblog' );
		$dispatcher = JDispatcher::getInstance();
		$mainframe	= JFactory::getApplication();
		$document	= JFactory::getDocument();
		$config		= EasyBlogHelper::getConfig();
		$my			= JFactory::getUser();
		$acl		= EasyBlogACLHelper::getRuleSet();

		$sort	= JRequest::getCmd('sort','latest');
		$id		= JRequest::getInt( 'id' , 0 );

		//setting pathway
		$pathway	= $mainframe->getPathway();
		$this->setPathway(JText::_('COM_EASYBLOG_TEAMBLOG'), EasyBlogRouter::_('index.php?option=com_easyblog&view=teamblog'));

		$id		= JRequest::getInt( 'id' , 0 );

		if( $id == 0 )
		{
			echo JText::_('COM_EASYBLOG_TEAMBLOG_INVALID_ID');
			return;
		}

		// set meta tags for teamblog view
		EasyBlogHelper::setMeta( $id, META_TYPE_TEAM );

		//stats type
		$statType	= JRequest::getString('stat','');
		$statId     = ($statType == 'tag') ? JRequest::getString('tagid','') : JRequest::getString('catid','');

		$statObject = null;
		if($statType == 'category')
		{
			$statObject = EasyBlogHelper::getTable( 'Category', 'Table' );
			$statObject->load($statId);
		}
		else
		{
			$statObject = EasyBlogHelper::getTable( 'Tag', 'Table' );
			$statObject->load($statId);
		}


		$team	= EasyBlogHelper::getTable( 'TeamBlog' , 'Table' );
		$team->load( $id );
		$team->avatar   = $team->getAvatar();

		$gid		= EasyBlogHelper::getUserGids();
		$isMember   = $team->isMember($my->id, $gid);
		//check if the logged in user a teammember when the team set to member only.
		if($team->access == EBLOG_TEAMBLOG_ACCESS_MEMBER)
		{
			$isMember   = $team->isMember($my->id, $gid);
		}
		$team->isMember    = $isMember;

		// check if team description is emtpy or not. if yes, show default message.
		if(empty($team->description))
			$team->description   = JText::_('COM_EASYBLOG_TEAMBLOG_NO_DESCRIPTION');

		//add the pathway for teamblog
		$this->setPathway($team->title, '');


		$tbModel	= $this->getModel( 'TeamBlogs' );
		$model		= $this->getModel( 'Blog' );
		$blogs		= $model->getBlogsBy( 'teamblog' , $team->id );
		$blogs		= EasyBlogHelper::formatBlog( $blogs );
		$pagination	= $model->getPagination();

		//now get the teams info
		$members    		= $tbModel->getTeamMembers( $team->id );
		$teamMembers		= EasyBlogHelper::formatTeamMembers($members);
		$isFeatured         = EasyBlogHelper::isFeatured('teamblog', $team->id);

		$pageTitle	= EasyBlogHelper::getPageTitle($config->get('main_title'));
		$pageNumber	= $pagination->get( 'pages.current' );
		$pageText	= ($pageNumber == 1) ? '' : ' - ' . JText::sprintf( 'COM_EASYBLOG_PAGE_NUMBER', $pageNumber );
		$document->setTitle( $team->title . $pageText . $pageTitle );

		EasyBlogHelper::storeSession($team->id, 'EASYBLOG_TEAMBLOG_ID');

		//var_dump($blogs);exit;
		$tpl	= new CodeThemes();
		$tpl->set( 'team', $team );
		$tpl->set( 'teamMembers', $teamMembers );
		$tpl->set( 'data' , $blogs );
		$tpl->set( 'isFeatured' , $isFeatured );
		$tpl->set( 'pagination', $pagination->getPagesLinks());
		$tpl->set( 'siteadmin', EasyBlogHelper::isSiteAdmin() );
		$tpl->set( 'config', $config);
		$tpl->set('my', $my );
		$tpl->set('acl', $acl );

		$tpl->set('statType', $statType );
		$tpl->set('statObject', $statObject );

		echo $tpl->fetch( 'blog.teamblogs.php' );
	}

}
