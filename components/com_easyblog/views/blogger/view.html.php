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

class EasyBlogViewBlogger extends EasyBlogView
{
	/**
	 * This method is responsible the output on the all bloggers layout.
	 */
	public function display( $tmpl = null )
	{
		$app 		= JFactory::getApplication();
		$doc 		= JFactory::getDocument();
		$config 	= EasyBlogHelper::getConfig();
		$my         = JFactory::getUser();
		$acl 		= EasyBlogACLHelper::getRuleSet();

		// @task: Set meta tags for bloggers
		EasyBlogHelper::setMeta( META_ID_BLOGGERS, META_TYPE_VIEW );

		// @task: Set the pathway
		$pathway	= $app->getPathway();

		if( ! EasyBlogRouter::isCurrentActiveMenu( 'blogger' ) )
		{
			$this->setPathway( JText::_('COM_EASYBLOG_BLOGGERS_BREADCRUMB') , '' );
		}

		// @task: Retrieve the current sorting options
		$sort		= JRequest::getCmd( 'sort' , 'featured' );
		$sortHTML   = $this->_getSorting( $sort );

		// @task: Retrieve the current filtering options.
		if( $config->get( 'main_bloggerlistingoption' ) )
		{
			$filter		= JRequest::getCmd( 'filter' , 'showbloggerwithpost' );
		}
		else
		{
			$filter		= JRequest::getCmd( 'filter' , 'showallblogger' );
		}
		$filterHTML = $this->_getFilter( $filter );

		// @task: Retrieve search values
		$search		= JRequest::getString( 'search' , '' );

		// @task: Retrieve the models.
		$bloggerModel	= $this->getModel( 'Blogger' );
		$blogModel 		= $this->getModel( 'Blog' );
		$postTagModel	= $this->getModel( 'PostTag' );

		// @task: Retrieve the bloggers to show on the page.
		$result			= $bloggerModel->getBloggers($sort, '', $filter , $search );
		$pagination		= $bloggerModel->getPagination();

		// @task: Determine the current page if there's pagination
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		// @task: Set the title of the page
		$title 		= JText::_( 'COM_EASYBLOG_BLOGGERS_PAGE_TITLE' );
		parent::setPageTitle( $title , $pagination , true );


		// Filter to show users which have the ability to write post
		$acl = EasyBlogACLHelper::getRuleSet( );
		$bloggers = array();

		if( !empty($result) )
		{
			foreach( $result as $user )
			{
				$acl = EasyBlogACLHelper::getRuleSet( $user->id );

				if( !empty($acl->rules->add_entry) )
				{
					// We call them bloggers for user with the acl permission to write entry
					$bloggers[] = $user;
				}
			}
		}
		// Pass back lists of bloggers back to users
		$result = $bloggers;


		// @task: Format the blogger listing.
		if( !empty( $result ) )
		{
			foreach( $result as $row )
			{
				// @task: Load the specific user object to be re-used on the theme.
				$author 		= EasyBlogHelper::getTable( 'Profile' );
				$author->load( $row->id );
				$row->blogger	= $author;

				// @task: Fetch entries from this particular blogger
				$entries		= $blogModel->getBlogsBy( 'blogger' , $row->id, $config->get( 'layout_postorder' ) , EasyBlogHelper::getHelper( 'Pagination' )->getLimit( EBLOG_PAGINATION_BLOGGERS ) , EBLOG_FILTER_PUBLISHED);

				// @task: Format the blog posts
				$entries 		= EasyBlogHelper::formatBlog($entries , false , true , true , true );

				$row->blogs 	= $entries;

				// @task: Get tags that are used by this author
				$row->tags		= $bloggerModel->getTagUsed( $row->id );

				// @task: Get categories that are used by this author
				$row->categories	= $bloggerModel->getCategoryUsed( $row->id );

				// @task: Get the twitter link for this author.
				$row->twitterLink 	= EasyBlogSocialShareHelper::getLink('twitter', $row->id);

				// @task: Get the rss link.
				$row->rssLink		= $author->getRSS();

				// @task: Get comments count.
				$row->commentsCount	= ( EasyBlogHelper::getHelper( 'Comment' )->isBuiltin() ) ? $author->getCommentsCount() : 0;

				// @task: Get total posts created by the author.
				$row->blogCount		= $bloggerModel->getTotalBlogCreated( $row->id );
			}
		}

		$theme	= new CodeThemes();

		$theme->set( 'data'			, $result );
		$theme->set( 'search' 		, $search );
		$theme->set( 'sort'			, $sort );
		$theme->set( 'pagination'	, $pagination->getPagesLinks() );
		$theme->set( 'sortHTML'		, $sortHTML );
		$theme->set( 'filterHTML'	, $filterHTML );

		echo $theme->fetch( 'blog.bloggers.php' );
	}

	/*
	 * Show all the blogs in this blogger
	 */
	function listings()
	{
		JPluginHelper::importPlugin( 'easyblog' );
		$dispatcher	= JDispatcher::getInstance();
		$mainframe	= JFactory::getApplication();
		$document	= JFactory::getDocument();
		$config		= EasyBlogHelper::getConfig();
		$my			= JFactory::getUser();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$sort		= JRequest::getCmd('sort', $config->get( 'layout_postorder' ) );
		$bId		= JRequest::getCmd('id','0');
		$limitstart	= JRequest::getVar('limitstart', 0);

		if ($limitstart) {
			$document->setMetadata('robots', 'noindex,follow');
		}

		$blogger = EasyBlogHelper::getTable( 'Profile', 'Table' );
		$blogger->load($bId);

		if(! $config->get('main_nonblogger_profile') )
		{
			if( ! EasyBlogHelper::isBlogger( $blogger->id ) )
			{
				$redirect	= EasyBlogRouter::_( 'index.php?option=com_easyblog&view=latest' , false );
				$mainframe->redirect($redirect);
				$mainframe->close();
			}
		}

		// If blogger does not exist, redirect
		if( !$blogger->id )
		{
			$redirect	= EasyBlogRouter::_( 'index.php?option=com_easyblog&view=latest' , false );
			$mainframe->redirect($redirect);
			$mainframe->close();
		}

		if( $acl->rules->allow_seo )
		{
			EasyBlogHelper::setMeta( $blogger->id, META_TYPE_BLOGGER, true );
		}

		if( ! EasyBlogRouter::isCurrentActiveMenu( 'blogger', $blogger->id ) )
		{
			if( ! EasyBlogRouter::isCurrentActiveMenu( 'blogger' ) )
				$this->setPathway( JText::_('COM_EASYBLOG_BLOGGERS_BREADCRUMB') , EasyBlogRouter::_('index.php?option=com_easyblog&view=blogger') );

			$this->setPathway( $blogger->getName() );
		}

		$model		= $this->getModel( 'Blog' );
		$data		= $model->getBlogsBy('blogger', $blogger->id, $sort);
		$pagination	= $model->getPagination();
		$data      	= EasyBlogHelper::formatBlog($data , false , true , true , true );
		$rssURL		= EasyBlogRouter::_('index.php?option=com_easyblog&view=blogger&task=rss');

		// @task: Add canonical URLs.
		if( $config->get( 'main_canonical_entry') )
		{
			$canonicalUrl   = EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $blogger->id , false , true, true );
			$document->addCustomTag( '<link rel="canonical" href="' . $canonicalUrl . '"/>' );
		}

		if($config->get('layout_showcomment', false))
		{
			for($i = 0; $i < count($data); $i++)
			{
				$row   =& $data[$i];
				$maxComment = $config->get('layout_showcommentcount', 3);
				$comments	= EasyBlogHelper::getHelper( 'Comment' )->getBlogComment( $row->id, $maxComment , 'desc' );
				$comments   = EasyBlogHelper::formatBlogCommentsLite($comments);
				$row->comments = $comments;
			}
		}

		if( $config->get('main_rss') )
		{
			if( $config->get('main_feedburner') && $config->get('main_feedburnerblogger') )
			{
				$document->addHeadLink( EasyBlogHelper::getHelper( 'String' )->escape( $blogger->getRSS() ), 'alternate' , 'rel' , array('type' => 'application/rss+xml', 'title' => 'RSS 2.0') );
			}
			else
			{
				// Add rss feed link
				$document->addHeadLink( $blogger->getRSS() , 'alternate' , 'rel' , array('type' => 'application/rss+xml', 'title' => 'RSS 2.0') );
				$document->addHeadLink( $blogger->getAtom() , 'alternate' , 'rel' , array('type' => 'application/atom+xml', 'title' => 'Atom 1.0') );
			}
		}

		$title					= EasyBlogHelper::getPageTitle( $blogger->getName() );

		// @task: Set the page title
		parent::setPageTitle( $title , $pagination , $config->get( 'main_pagetitle_autoappend' ) );

		$theme	= new CodeThemes();
		$theme->set( 'twitterLink'	, $blogger->getTwitterLink() );
		$theme->set( 'blogger'		, $blogger );
		$theme->set( 'sort'			, $sort );
		$theme->set( 'blogs'		, $data );
		$theme->set( 'pagination'	, $pagination->getPagesLinks());
		$theme->set( 'my'			, $my );
		$theme->set( 'acl'			, $acl );
		$theme->set( 'currentURL'	, $blogger->getProfileLink() );
		$theme->set( 'showAvatar'	, false );
		echo $theme->fetch( 'blog.blogger.php' );
		echo EasyBlogHelper::getFBInitScript();
	}

	function statistic()
	{
		JPluginHelper::importPlugin( 'easyblog' );
		$dispatcher	= JDispatcher::getInstance();
		$mainframe	= JFactory::getApplication();
		$document	= JFactory::getDocument();
		$config		= EasyBlogHelper::getConfig();
		$my			= JFactory::getUser();
		$acl		= EasyBlogACLHelper::getRuleSet();

		// Add noindex for tags view by default.
		$document->setMetadata( 'robots' , 'noindex,follow' );

		$sort	= JRequest::getCmd('sort', $config->get( 'layout_postorder' ) );
		$bId	= JRequest::getCmd('id','0');

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
			JTable::addIncludePath( EBLOG_TABLES );
			$statObject = EasyBlogHelper::getTable( 'Tag', 'Table' );
			$statObject->load($statId);
		}

		$blogger = EasyBlogHelper::getTable( 'Profile', 'Table' );
		$blogger->load( $bId );

		// set meta tags for blogger
		if( $acl->rules->allow_seo )
		{
			EasyBlogHelper::setMeta( $blogger->id, META_TYPE_BLOGGER, true );
		}

		if( ! EasyBlogRouter::isCurrentActiveMenu( 'blogger' ) )
			$this->setPathway( JText::_('COM_EASYBLOG_BLOGGERS') , EasyBlogRouter::_('index.php?option=com_easyblog&view=blogger') );

		if( ! EasyBlogRouter::isCurrentActiveMenu( 'blogger', $blogger->id ) )
			$this->setPathway( $blogger->getName() );

		$model		= $this->getModel( 'Blog' );
		$data		= $model->getBlogsBy('blogger', $blogger->id, $sort);
		$pagination	= $model->getPagination();

		$data		= EasyBlogHelper::formatBlog($data);
		$rssURL		= EasyBlogRouter::_('index.php?option=com_easyblog&view=blogger&task=rss');

		if($config->get('layout_showcomment', false))
		{
			for($i = 0; $i < count($data); $i++)
			{
				$row   =& $data[$i];

				$maxComment = $config->get('layout_showcommentcount', 3);
				$comments	= EasyBlogHelper::getHelper( 'Comment' )->getBlogComment( $row->id, $maxComment , 'desc' );
				$comments   = EasyBlogHelper::formatBlogCommentsLite($comments);
				$row->comments = $comments;
			}
		}

		$twitterFollowMelink= EasyBlogSocialShareHelper::getLink('twitter', $blogger->id);

		if( $config->get('main_rss') )
		{
			if( $config->get('main_feedburner') && $config->get('main_feedburnerblogger') )
			{
			$document->addHeadLink( $blogger->getRSS() , 'alternate' , 'rel' , array('type' => 'application/rss+xml', 'title' => 'RSS 2.0') );
			}
			else
			{
				// Add rss feed link
				$document->addHeadLink( $blogger->getRSS() , 'alternate' , 'rel' , array('type' => 'application/rss+xml', 'title' => 'RSS 2.0') );
				$document->addHeadLink( $blogger->getAtom() , 'alternate' , 'rel' , array('type' => 'application/atom+xml', 'title' => 'Atom 1.0') );
			}
		}

		$pageTitle	= EasyBlogHelper::getPageTitle($config->get('main_title'));
		$pageNumber	= $pagination->get( 'pages.current' );
		$pageText	= ($pageNumber == 1) ? '' : ' - ' . JText::sprintf( 'COM_EASYBLOG_PAGE_NUMBER', $pageNumber );

		$statTitle	= '';
		if ( isset($statType) )
		{
			if ( $statType == 'tag' )
			{
				$statTitle	= ' - ' . JText::sprintf( 'COM_EASYBLOG_BLOGGER_STAT_TAG' , $statObject->title);
			}
			else
			{
				$statTitle = ' - ' . JText::sprintf('COM_EASYBLOG_BLOGGER_STAT_CATEGORY', $statObject->title);
			}
		}

		$document->setTitle($blogger->getName() . $statTitle . $pageText . $pageTitle);

		$tpl	= new CodeThemes();
		$tpl->set('blogger', $blogger );
		$tpl->set('sort', $sort );
		$tpl->set('blogs', $data );
		$tpl->set('config', $config );
		$tpl->set('siteadmin', EasyBlogHelper::isSiteAdmin() );
		$tpl->set('pagination', $pagination->getPagesLinks());
		$tpl->set('twitterFollowMelink', $twitterFollowMelink);
		$tpl->set('my', $my );
		$tpl->set('acl', $acl );
		$tpl->set( 'currentURL'	, ltrim( '/' , JRequest::getURI() ) );

		$tpl->set('statType', $statType );
		$tpl->set('statObject', $statObject );

		echo $tpl->fetch( 'blog.blogger.php' );
	}

	function _getSorting($sorting_type = 'featured')
	{
		$filter[] = JHTML::_('select.option', 'featured', JText::_('COM_EASYBLOG_SORT_BY_FEATURED_BLOGGERS') );
		$filter[] = JHTML::_('select.option', 'latestpost', JText::_('COM_EASYBLOG_SORT_BY_LATEST_POST') );
		$filter[] = JHTML::_('select.option', 'latest', JText::_('COM_EASYBLOG_SORT_BY_LATEST_BLOGGER') );
		$filter[] = JHTML::_('select.option', 'alphabet', JText::_('COM_EASYBLOG_SORT_BY_ALPHABETICAL') );

		return JHTML::_('select.genericlist', $filter, 'sort', 'size="1" class="form-control"', 'value', 'text', $sorting_type );
	}

	function _getFilter($filter_type = 'showbloggerwithpost')
	{
		$filter[] = JHTML::_('select.option', 'showbloggerwithpost', JText::_('COM_EASYBLOG_FILTERS_BLOGGER_WITH_POST') );
		$filter[] = JHTML::_('select.option', 'showallblogger', JText::_('COM_EASYBLOG_FILTERS_ALL_BLOGGERS') );

		return JHTML::_('select.genericlist', $filter, 'filter', 'size="1"', 'value', 'text', $filter_type );
	}
}
