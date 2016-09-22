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

class EasyBlogViewTags extends EasyBlogView
{
	function display( $tmpl = null )
	{
		$document	= JFactory::getDocument();
		$config		= EasyBlogHelper::getConfig();

		// set meta tags for teamblog view
		EasyBlogHelper::setMeta( META_ID_TAGS, META_TYPE_VIEW );

		if( ! EasyBlogRouter::isCurrentActiveMenu( 'tags' ) )
			$this->setPathway( JText::_('COM_EASYBLOG_TAGS_BREADCRUMB') );

		// $document->setTitle( JText::_( 'COM_EASYBLOG_TAGS_PAGE_TITLE' ) );
		parent::setPageTitle( JText::_('COM_EASYBLOG_TAGS_PAGE_TITLE') , '' , true );

		// Add canonical URL to satify Googlebot. Incase they think it's duplicated content.
		EasyblogHelper::addCanonicalURL( array('ordering', 'sorting') );

		$model		= $this->getModel( 'Tags' );
		$ordering	= JString::strtolower( JRequest::getString( 'ordering'	, '' ) );
		$sorting	= JString::strtolower( JRequest::getString( 'sorting' , $config->get( 'main_tags_sorting' ) ) );
		$tags   	= $model->getTagCloud( '' , $ordering , $sorting ,true);

		$titleURL	= 'index.php?option=com_easyblog&view=tags&ordering=title';
		$titleURL	.= ( $sorting ) ? '&sorting=' . $sorting : '';
		$postURL	= 'index.php?option=com_easyblog&view=tags&ordering=postcount';
		$postURL	.= ( $sorting ) ? '&sorting=' . $sorting : '';

		$ascURL		= 'index.php?option=com_easyblog&view=tags&sorting=asc';
		$ascURL		.= ( $ordering ) ? '&ordering=' . $ordering : '';
		$descURL	= 'index.php?option=com_easyblog&view=tags&sorting=desc';
		$descURL	.= ( $ordering ) ? '&ordering=' . $ordering : '';

		$tpl		= new CodeThemes();
		$tpl->set( 'ascURL'		, $ascURL );
		$tpl->set( 'descURL'	, $descURL );
		$tpl->set( 'titleURL'	, $titleURL );
		$tpl->set( 'postURL'	, $postURL );
		$tpl->set( 'tags'		, $tags );
		$tpl->set( 'sorting'	, $sorting );
		$tpl->set( 'ordering'	, $ordering );

		echo $tpl->fetch( 'blog.tagcloud.php' );

	}

	/**
	 * Display specific tag from the site.
	 **/
	function tag()
	{
		$document	= JFactory::getDocument();
		$config 	= EasyBlogHelper::getConfig();
		$my         = JFactory::getUser();
		$acl 		= EasyBlogACLHelper::getRuleSet();
		$id			= JRequest::getVar( 'id' , '' , 'REQUEST' );

		JTable::addIncludePath( EBLOG_TABLES );

		// Add noindex for tags view by default.
		$document->setMetadata( 'robots' , 'noindex,follow' );

		$tag		= EasyBlogHelper::getTable( 'Tag' , 'Table' );
		$tag->load( $id );
		$title		= EasyBlogHelper::getPageTitle( $tag->title );

		// @task: Set the page title
		parent::setPageTitle( $title , false , $config->get( 'main_pagetitle_autoappend' ) );

		// set meta tags for tags view
		EasyBlogHelper::setMeta( META_ID_TAGS, META_TYPE_VIEW, JText::_( $tag->title ) . ' - ' . EasyBlogHelper::getPageTitle($config->get('main_title')) );

		if( ! EasyBlogRouter::isCurrentActiveMenu( 'tags' ) )
		{
			$this->setPathway( JText::_('COM_EASYBLOG_TAGS_BREADCRUMB') , EasyBlogRouter::_( 'index.php?option=com_easyblog&view=tags' ) );
		}

		$this->setPathway( JText::_( $tag->title ) );

		$blogModel	= $this->getModel( 'Blog' );
		$tagModel	= $this->getModel( 'Tags' );
		$rows		= $blogModel->getTaggedBlogs( $id );
		$pagination	= $blogModel->getPagination();

		$privateBlogCount   = 0;
		$teamBlogCount   	= 0;

		if($my->id == 0)
		{
			$privateBlogCount   = $tagModel->getTagPrivateBlogCount( $id );
		}

		if( !$config->get( 'main_includeteamblogpost' ) )
		{
			$teamBlogCount          = $tagModel->getTeamBlogCount( $id );
		}

		//for trigger only
		JPluginHelper::importPlugin( 'easyblog' );
		$dispatcher	= JDispatcher::getInstance();
		$mainframe	= JFactory::getApplication();
		$params		= $mainframe->getParams('com_easyblog');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		// @task: Add canonical URLs.
		if( $config->get( 'main_canonical_entry') )
		{
			$canonicalUrl   = EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=tags&layout=tag&id=' . $tag->id , false , true, true );
			$document->addCustomTag( '<link rel="canonical" href="' . $canonicalUrl . '"/>' );
		}

		if(!empty( $rows ))
		{
		    $rows	= EasyBlogHelper::formatBlog( $rows , true , true , true , true );

			for( $i = 0; $i < count( $rows ); $i++ )
			{
        		$row	=& $rows[ $i ];

        		$row->category  	= $blogModel->getCategoryName($row->category_id);
				// $row->readmore		= JText::_('COM_EASYBLOG_CONTINUE_READING');

				if($config->get('layout_showcomment', false))
				{
					$maxComment = $config->get('layout_showcommentcount', 3);
					$comments	= EasyBlogHelper::getHelper( 'Comment' )->getBlogComment( $row->id, $maxComment , 'desc' );
	                $comments   = EasyBlogHelper::formatBlogCommentsLite($comments);
		    		$row->comments = $comments;
				}
   			}
		}

		$theme		= new CodeThemes();
		$theme->set( 'tag' , $tag );
		$theme->set( 'rows' , $rows );
		$theme->set( 'pagination'	, $pagination );
		$theme->set( 'currentURL' , 'index.php?option=com_easyblog&view=tags&layout=tag&id=' . $tag->id );
		$theme->set( 'privateBlogCount', $privateBlogCount );
		$theme->set( 'teamBlogCount', $teamBlogCount );

		echo $theme->fetch( 'blog.tags.php' );
	}

	function _getSorting($sorting_type = 'latestpost')
	{
		$filter[] = JHTML::_('select.option', 'alphabetical', JText::_('Alphabetical') );
		$filter[] = JHTML::_('select.option', 'posts', JText::_('Tag weight') );

		return JHTML::_('select.genericlist', $filter, 'sort', 'class="inputbox" size="1"', 'value', 'text', $sorting_type );
	}
}
