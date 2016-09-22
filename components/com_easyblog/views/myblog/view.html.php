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

require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'image.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'date.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'router.php' );

class EasyBlogViewMyblog extends EasyBlogView
{
	function display( $tmpl = null )
	{
		$my   		= JFactory::getUser();

		if( $my->id < 1 )
		{
			EasyBlogHelper::showLogin();
			return;
		}

		JPluginHelper::importPlugin( 'easyblog' );
		$dispatcher = JDispatcher::getInstance();
		$mainframe	= JFactory::getApplication();
        $document	= JFactory::getDocument();
		$acl 		= EasyBlogACLHelper::getRuleSet();
		$config 	= EasyBlogHelper::getConfig();
        $sort		= JRequest::getCmd('sort', $config->get( 'layout_postorder' ) );
		$blogger	= EasyBlogHelper::getTable( 'Profile', 'Table' );
		$blogger->load( $my->id );

		// set meta tags for blogger
		EasyBlogHelper::setMeta( $my->id, META_ID_BLOGGERS );

		if( ! EasyBlogRouter::isCurrentActiveMenu( 'myblog', $my->id ) )
		{
			$this->setPathway( JText::_('COM_EASYBLOG_BLOGGERS_BREADCRUMB') , EasyBlogRouter::_('index.php?option=com_easyblog&view=blogger') );
			$this->setPathway( $blogger->getName() );
		}

        $model		= $this->getModel( 'Blog' );
		$data		= $model->getBlogsBy('blogger', $blogger->id, $sort);
		$pagination	= $model->getPagination();

		$pageNumber	= $pagination->get( 'pages.current' );
		$pageText	= ($pageNumber == 1) ? '' : ' - ' . JText::sprintf( 'COM_EASYBLOG_PAGE_NUMBER', $pageNumber );
		$document->setTitle( $blogger->getName() . $pageText . EasyBlogHelper::getPageTitle( JText::_( 'COM_EASYBLOG_MY_BLOG_PAGE_TITLE' ) ) );

		$data		= EasyBlogHelper::formatBlog($data , false , true , true , true );

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

		$rssURL		= EasyBlogRouter::_('index.php?option=com_easyblog&view=blogger&task=rss');

		//twitter follow me link
		$twitterFollowMelink= EasyBlogSocialShareHelper::getLink('twitter', $blogger->id);

		$theme	= new CodeThemes();

		$theme->set('rssURL'	, $rssURL );
		$theme->set('blogger', $blogger );
		$theme->set('sort', $sort );
		$theme->set('blogs', $data );
		$theme->set( 'currentURL' , 'index.php?option=com_easyblog&view=latest' );
		$theme->set('pagination', $pagination->getPagesLinks());
        $theme->set('twitterFollowMelink', $twitterFollowMelink);
        $theme->set('my', $my );
		$theme->set('acl', $acl );

		echo $theme->fetch( 'blog.blogger.php' );
	}
}
