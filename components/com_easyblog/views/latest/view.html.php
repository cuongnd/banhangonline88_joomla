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

class EasyBlogViewLatest extends EasyBlogView
{
	/**
	 * Responsible to display the front page of the blog listings
	 *
	 * @access	public
	 */
	function display( $tmpl = null )
	{
		// @task: Set meta tags for latest post
		EasyBlogHelper::setMeta( META_ID_LATEST , META_TYPE_VIEW );

		// @task: Set rss links into headers.
		EasyBlogHelper::getHelper( 'Feeds' )->addHeaders( 'index.php?option=com_easyblog&view=latest' );

		$app 		= JFactory::getApplication();
		$doc 		= JFactory::getDocument();
		$config		= EasyBlogHelper::getConfig();
		$my         = JFactory::getUser();
		$acl 		= EasyBlogACLHelper::getRuleSet();

		// @task: Add a breadcrumb if the current menu that's being accessed is not from the latest view.
		if( ! EasyBlogRouter::isCurrentActiveMenu( 'latest' ) )
		{
			$this->setPathway( JText::_('COM_EASYBLOG_LATEST_BREADCRUMB') , '' );
		}

		// @task: Get the current active menu's properties.
		$menu 		= $app->getMenu()->getActive();
		$menu 		= JFactory::getApplication()->getMenu()->getActive();
		$inclusion	= '';

		if( is_object( $menu ) )
		{
			$params 	= EasyBlogHelper::getRegistry();
			$params->load( $menu->params );

			$inclusion	= EasyBlogHelper::getCategoryInclusion( $params->get( 'inclusion' ) );

			if( $params->get('includesubcategories', 0) && !empty( $inclusion ) )
			{
				$tmpInclusion   = array();

				foreach( $inclusion as $includeCatId )
				{
					//get the nested categories
					$category   = new stdClass();
					$category->id   	= $includeCatId;
					$category->childs 	= null;

					EasyBlogHelper::buildNestedCategories($category->id, $category);

					$linkage   = '';
					EasyBlogHelper::accessNestedCategories($category, $linkage, '0', '', 'link', ', ');

					$catIds     = array();
					$catIds[]   = $category->id;
					EasyBlogHelper::accessNestedCategoriesId($category, $catIds);

					$tmpInclusion	= array_merge( $tmpInclusion, $catIds);
				}

				$inclusion  = $tmpInclusion;
			}

		}

		$limitstart = JRequest::getInt('limitstart', 0);

		if ($limitstart) {
			$doc->setMetadata('robots', 'noindex,follow');
		}

		// @task: Necessary filters
		$sort			= JRequest::getCmd('sort', $config->get('layout_postorder'));
		$model			= $this->getModel('Blog');

		// @task: Retrieve the list of featured blog posts.
		$featured   	= $model->getFeaturedBlog($inclusion);
		$excludeIds		= array();

		// @task: Add canonical URLs.
		if( $config->get( 'main_canonical_entry') )
		{
			$canonicalUrl   = EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=latest' , false , true, true );
			$doc->addCustomTag( '<link rel="canonical" href="' . $canonicalUrl . '"/>' );
		}

		// Test if user also wants the featured items to be appearing in the blog listings on the front page.
		// Otherwise, we'll need to exclude the featured id's from appearing on the front page.
		if( !$config->get( 'layout_featured_frontpage' ) )
		{
			foreach($featured as $item)
			{
				$excludeIds[]	= $item->id;
			}
		}

		// @task: Admin might want to display the featured blogs on all pages.
		if( !$config->get( 'layout_featured_allpages') && ( JRequest::getInt( 'start' , 0 ) != 0 || $limitstart != 0 ) )
		{
			$featured 	= array();
		}
		else
		{
			for( $i = 0; $i < count( $featured ); $i++ )
			{
				$row					= $featured[ $i ];
				$row->featuredImage		= EasyBlogHelper::getFeaturedImage( $row->intro . $row->content );
			}

			$featured	= EasyBlogHelper::formatBlog( $featured , true , false , false , false , false );
		}

		// @task: Try to retrieve any categories to be excluded.
		$excludedCategories	= $config->get( 'layout_exclude_categories' );
		$excludedCategories	= ( empty( $excludedCategories ) ) ? '' : explode( ',' , $excludedCategories );


		// @task: Fetch the blog entries.
		$data		= $model->getBlogsBy( '' , '' , $sort , 0 , EBLOG_FILTER_PUBLISHED, null, true, $excludeIds , false , false , true , $excludedCategories , $inclusion );
		$pagination	= $model->getPagination();

		$params 	= $app->getParams( 'com_easyblog' );

		// @task: Perform necessary formatting here.
		$data		= EasyBlogHelper::formatBlog( $data , true , true , true , true );

		// @task: Update the title of the page if navigating on different pages to avoid Google marking these title's as duplicates.
		$title 		= EasyBlogHelper::getPageTitle( JText::_( 'COM_EASYBLOG_LATEST_PAGE_TITLE' ) );

		// @task: Set the page title
		parent::setPageTitle( $title , $pagination , $config->get( 'main_pagetitle_autoappend' ) );

		// @task: Get pagination output here.
		$paginationHTML		= $pagination->getPagesLinks();

		$theme		= new CodeThemes();
		$theme->set( 'data'			, $data );
		$theme->set( 'featured'		, $featured );
		$theme->set( 'currentURL'	, EasyBlogRouter::_( 'index.php?option=com_easyblog&view=latest' , false ) );
		$theme->set( 'pagination'	, $paginationHTML );

		// @task: Send back response to the browser.
		echo $theme->fetch( 'blog.latest.php' );
	}
}
