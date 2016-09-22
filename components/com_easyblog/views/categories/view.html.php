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

require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'date.php' );

class EasyBlogViewCategories extends EasyBlogView
{
	/**
	 * This method is responsible the output on the all categories layout.
	 */
	function display( $tmpl = null )
	{
		$app 		= JFactory::getApplication();
		$doc 		= JFactory::getDocument();
		$config 	= EasyBlogHelper::getConfig();

		// @task: Set meta tags for bloggers
		EasyBlogHelper::setMeta( META_ID_GATEGORIES , META_TYPE_VIEW );

		$limitstart	= JRequest::getVar('limitstart', 0);

		if ($limitstart) {
			$doc->setMetadata('robots', 'noindex,follow');
		}

		// @task: Set the pathway
		$pathway	= $app->getPathway();

		if( ! EasyBlogRouter::isCurrentActiveMenu( 'categories' ) )
		{
			$this->setPathway( JText::_('COM_EASYBLOG_CATEGORIES_BREADCRUMB') , '' );
		}

		// @task: Get the sorting options.
		$sortConfig = $config->get('layout_sorting_category','latest');
		$sort		= JRequest::getCmd('sort',$sortConfig);

		// @task: Retrieve models
		$blogModel		= $this->getModel( 'Blog' );
		$categoryModel	= $this->getModel( 'Category' );

		// @task: Test if there's any explicit inclusion of categories
		$menu 		= $app->getMenu()->getActive();
		$inclusion	= '';

		if( is_object( $menu ) && stristr($menu->link , 'view=categories') !== false )
		{
			$params 	= EasyBlogHelper::getRegistry( $menu->params );
			$inclusion	= EasyBlogHelper::getCategoryInclusion( $params->get( 'inclusion' ) );
		}

		// @task: Get limit
		$limit = $config->get( 'layout_pagination_categories_per_page' );

		$data				= $categoryModel->getCategories($sort, $config->get( 'main_categories_hideempty' ) , $limit , $inclusion );

		$pagination			= $categoryModel->getPagination();

		if(!empty($data))
		{
			for($i = 0; $i < count($data); $i++)
			{
				$row		=& $data[$i];
				$category 	= EasyBlogHelper::getTable( 'Category' );
				$category->load($row->id);

				$row->childs = null;

				EasyBlogHelper::buildNestedCategories($row->id, $row, false , true );

				// TODO: Parameterize initial subcategories to display. Ability to configure from backend.
				$nestedLinks = '';
				$initialLimit = ( $app->getCfg('list_limit') == 0) ? 5 : $app->getCfg('list_limit');

				if (count($row->childs) > $initialLimit)
				{
					$initialNestedLinks = '';
					$initialRow			= new stdClass();
					$initialRow->childs = array_slice($row->childs, 0, $initialLimit);

					EasyBlogHelper::accessNestedCategories($initialRow, $initialNestedLinks, '0', '', 'link', ', ');

					$moreNestedLinks 	= '';
					$moreRow			= new stdClass();
					$moreRow->childs	= array_slice($row->childs, $initialLimit);

					EasyBlogHelper::accessNestedCategories($moreRow, $moreNestedLinks, '0', '', 'link', ', ');

					// Hide more nested links until triggered
					$nestedLinks .= $initialNestedLinks;
					$nestedLinks .= '<span class="more-subcategories-toggle"> ' . JText::_('COM_EASYBLOG_AND') . ' <a href="javascript:void(0);" onclick="eblog.categories.loadMore( this );">' . JText::sprintf('COM_EASYBLOG_OTHER_SUBCATEGORIES', count($row->childs) - $initialLimit) . '</a></span>';
					$nestedLinks .= '<span class="more-subcategories" style="display: none;">, ' . $moreNestedLinks . '</span>';

				}
				else
				{
					EasyBlogHelper::accessNestedCategories($row, $nestedLinks, '0', '', 'link', ', ');
				}

				$catIds     = array();
				$catIds[]   = $row->id;
				EasyBlogHelper::accessNestedCategoriesId( $row , $catIds );

				$blogs		= $blogModel->getBlogsBy( 'category' , $catIds , 'latest' , EasyBlogHelper::getHelper( 'Pagination' )->getLimit( EBLOG_PAGINATION_CATEGORIES ) );

				for($j=0; $j < count($blogs); $j++)
				{
					$blogItem   	= $blogs[$j];
					$itemLinkageArr	= EasyBlogHelper::populateCategoryLinkage($blogItem->category_id);
					$itemLinkage	= '';

					foreach($itemLinkageArr as $linkItem)
					{
						$str    = '<a href="' . EasyBlogRouter::_('index.php?option=com_easyblog&view=categories&layout=listings&id='.$linkItem->id) . '">' . $linkItem->title . '</a>';
						$itemLinkage   .= (empty($itemLinkage)) ? $str : '&nbsp;' . '&gt;' . '&nbsp;' . $str;
					}
					$blogItem->nestedLink	= $itemLinkage;
					$author					= EasyBlogHelper::getTable( 'Profile' , 'Table' );

					$author->load( $blogItem->created_by );
					$blogItem->author		= $author;
				}

				$row->cnt   = $categoryModel->getTotalPostCount( $catIds );

				if(!empty($blogs))
				{
					$blogs	= EasyBlogHelper::formatBlog( $blogs , false , true , true , true );
				}

				$row->description	= $category->get( 'description' );
				$row->blogs			= $blogs;
				$row->rssLink   	= $category->getRSS();
				$row->avatar    	= $category->getAvatar();
				$row->nestedLink    = $nestedLinks;
				$row->bloggers		= $category->getActiveBloggers();
			}
		}

		// @task: Update the title of the page if navigating on different pages to avoid Google marking these title's as duplicates.
		$title 		= EasyBlogHelper::getPageTitle( JText::_( 'COM_EASYBLOG_CATEGORIES_PAGE_TITLE' ) );

		// @task: Set the page title
		parent::setPageTitle( $title , $pagination , $config->get( 'main_pagetitle_autoappend' ) );

		$theme	= new CodeThemes();
		$theme->set( 'data', $data );
		$theme->set( 'sort', $sort );
		$theme->set( 'pagination', $pagination->getPagesLinks());

		echo $theme->fetch( 'blog.categories.php' );
	}

	function simple( $tmpl = null )
	{
		$app 		= JFactory::getApplication();
		$doc 		= JFactory::getDocument();
		$config 	= EasyBlogHelper::getConfig();

		// @task: Set meta tags for bloggers
		EasyBlogHelper::setMeta( META_ID_GATEGORIES , META_TYPE_VIEW );

		// @task: Set the pathway
		$pathway	= $app->getPathway();

		if( ! EasyBlogRouter::isCurrentActiveMenu( 'categories' ) )
		{
			$this->setPathway( JText::_('COM_EASYBLOG_CATEGORIES_BREADCRUMB') , '' );
		}

		// @task: Get the sorting options.
		$sortConfig = $config->get('layout_sorting_category','latest');
		$sort		= JRequest::getCmd('sort',$sortConfig);

		// @task: Retrieve models
		$categoriesModel	= $this->getModel( 'Categories' );
		$categoryModel		= $this->getModel( 'Category' );

		// @task: Test if there's any explicit inclusion of categories
		$menu 		= $app->getMenu()->getActive();
		$inclusion	= '';

		if( is_object( $menu ) && stristr($menu->link , 'view=categories') !== false )
		{
			$params 	= EasyBlogHelper::getRegistry( $menu->params );
			$inclusion	= EasyBlogHelper::getCategoryInclusion( $params->get( 'inclusion' ) );
		}

		$data				= $categoriesModel->getCategoryTree( $sort );

		if(!empty($data))
		{
			for($i = 0; $i < count($data); $i++)
			{
				$row		=& $data[$i];

// 				$row->childs = null;
// 				EasyBlogHelper::buildNestedCategories($row->id, $row, false , true );

				$catIds     = array();
				$catIds[]   = $row->id;

				$row->cnt   		= $categoryModel->getTotalPostCount( $catIds );
				$row->description	= $row->get( 'description' );
// 				$row->rssLink   	= $row->getRSS();
// 				$row->avatar    	= $row->getAvatar();
			}
		}

// 		echo '<pre>';
// 		print_r($data);
// 		echo '</pre>';
// 		exit;

		// @task: Set page title
		parent::setPageTitle( JText::_('COM_EASYBLOG_CATEGORIES_PAGE_TITLE') , '0' , true );

		$theme	= new CodeThemes();
		$theme->set( 'data', $data );
		$theme->set( 'sort', $sort );

		echo $theme->fetch( 'blog.categories.simple.php' );
	}


	/*
	 * Show all the blogs in this category
	 */
	function listings()
	{
		$app 		= JFactory::getApplication();
		$doc 		= JFactory::getDocument();
		$config 	= EasyBlogHelper::getConfig();
		$my         = JFactory::getUser();
		$acl 		= EasyBlogACLHelper::getRuleSet();
		$sort		= JRequest::getCmd('sort', $config->get( 'layout_postorder' ) );
		$catId		= JRequest::getCmd('id','0');

		$category = EasyBlogHelper::getTable( 'Category', 'Table' );
		$category->load($catId);

		if($category->id == 0)
		{
			$category->title    = JText::_('COM_EASYBLOG_UNCATEGORIZED');
		}

		// Set the meta description for the category
		EasyBlogHelper::setMeta( $category->id , META_TYPE_CATEGORY );

		// Set the meta description for the category
		// $doc->setMetadata( 'description' , strip_tags( $category->description ) );
		$canonicalUrl   = EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=categories&layout=listings&id=' . $category->id , false , true, true );
		$doc->addCustomTag( '<link rel="canonical" href="' . $canonicalUrl . '"/>' );

		//setting pathway
		$pathway	= $app->getPathway();

		$privacy	= $category->checkPrivacy();

		$addRSS = true;
		if(! $privacy->allowed )
		{
			if( $my->id == 0 && !$config->get('main_allowguestsubscribe'))
			{
				$addRSS = false;
			}
		}

		if( $addRSS )
		{
			// Add rss feed link
			$doc->addHeadLink( $category->getRSS() , 'alternate' , 'rel' , array('type' => 'application/rss+xml', 'title' => 'RSS 2.0') );
			$doc->addHeadLink( $category->getAtom() , 'alternate' , 'rel' , array('type' => 'application/atom+xml', 'title' => 'Atom 1.0') );
		}

		if( ! EasyBlogRouter::isCurrentActiveMenu( 'categories', $category->id ) )
		{
			if( ! EasyBlogRouter::isCurrentActiveMenu( 'categories' ) )
				$this->setPathway( JText::_('COM_EASYBLOG_CATEGORIES_BREADCRUMB') , EasyBlogRouter::_('index.php?option=com_easyblog&view=categories') );

			//add the pathway for category
			$this->setPathway( $category->title , '' );
		}

		//get the nested categories
		$category->childs = null;

		EasyBlogHelper::buildNestedCategories($category->id, $category , false , true );

		// TODO: Parameterize initial subcategories to display. Ability to configure from backend.
		$nestedLinks = '';
		$initialLimit = ($app->getCfg('list_limit') == 0) ? 5 : $app->getCfg('list_limit');

		if (count($category->childs) > $initialLimit)
		{
			$initialNestedLinks = '';
			$initialRow = new stdClass();
			$initialRow->childs = array_slice($category->childs, 0, $initialLimit);

			EasyBlogHelper::accessNestedCategories($initialRow, $initialNestedLinks, '0', '', 'link', ', ');

			$moreNestedLinks = '';
			$moreRow = new stdClass();
			$moreRow->childs = array_slice($category->childs, $initialLimit);

			EasyBlogHelper::accessNestedCategories($moreRow, $moreNestedLinks, '0', '', 'link', ', ');

			// Hide more nested links until triggered
			$nestedLinks .= $initialNestedLinks;
			$nestedLinks .= '<span class="more-subcategories-toggle"> ' . JText::_('COM_EASYBLOG_AND') . ' <a href="javascript: void(0);onclick="eblog.categories.loadMore( this );">' . JText::sprintf('COM_EASYBLOG_OTHER_SUBCATEGORIES', count($category->childs) - $initialLimit) . '</a></span>';
			$nestedLinks .= '<span class="more-subcategories" style="display: none;">, ' . $moreNestedLinks . '</span>';

		} else {
			EasyBlogHelper::accessNestedCategories($category, $nestedLinks, '0', '', 'link', ', ');
		}

		$catIds     = array();
		$catIds[]   = $category->id;
		EasyBlogHelper::accessNestedCategoriesId($category, $catIds);

		$category->nestedLink = $nestedLinks;

		$modelC			= $this->getModel( 'Category' );
		$category->cnt	= $modelC->getTotalPostCount( $category->id );

		$modelPT	= $this->getModel( 'PostTag' );
		$model		= $this->getModel( 'Blog' );
		$modelCat	= $this->getModel( 'Category' );

		$data		= $model->getBlogsBy('category', $catIds, $sort, null , null, null, null, array(), null, null, true, array(), array(), null, EBLOG_PAGINATION_CATEGORIES );
		$pagination	= $model->getPagination();


		$allowCat	= $modelCat->allowAclCategory( $category->id );

		//for trigger
		$params		= $app->getParams('com_easyblog');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		if ($limitstart) {
			$doc->setMetadata('robots', 'noindex,follow');
		}

		if(! empty($data))
		{
			$data	= EasyBlogHelper::formatBlog( $data , false , true , true , true);

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
		}
		$teamBlogCount          = $modelCat->getTeamBlogCount( $category->id );

		$title					= EasyBlogHelper::getPageTitle( JText::_( $category->title ) );

		// @task: Set the page title
		parent::setPageTitle( $title , $pagination , $config->get( 'main_pagetitle_autoappend' ) );

		$themes	= new CodeThemes();

		$themes->set('allowCat', $allowCat);
		$themes->set('category', $category );
		$themes->set('sort', $sort );
		$themes->set('blogs', $data );
		$themes->set( 'currentURL' , 'index.php?option=com_easyblog&view=categories&layout=listings&id=' . $category->id );
		$themes->set('pagination', $pagination->getPagesLinks());
		$themes->set('config', $config );
		$themes->set('teamBlogCount', $teamBlogCount );
		$themes->set('my', $my );
		$themes->set('acl', $acl );
		$themes->set('privacy', $privacy );

		echo $themes->fetch( 'blog.category.php' );
	}
}
