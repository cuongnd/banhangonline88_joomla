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

require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'router.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'image.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'date.php' );

class EasyBlogViewFeatured extends EasyBlogView
{
	function display( $tmpl = null )
	{
		JPluginHelper::importPlugin( 'easyblog' );
		$dispatcher = JDispatcher::getInstance();
		$mainframe	= JFactory::getApplication();
		$document	= JFactory::getDocument();
		$config		= EasyBlogHelper::getConfig();
		$acl 		= EasyBlogACLHelper::getRuleSet();

		if( ! EasyBlogRouter::isCurrentActiveMenu( 'featured' ) )
	    	$this->setPathway( JText::_('COM_EASYBLOG_FEATURED_BREADCRUMB') );

		// set meta tags for featured view
		EasyBlogHelper::setMeta( META_ID_FEATURED, META_TYPE_VIEW );

		EasyBlogHelper::getHelper( 'Feeds' )->addHeaders( 'index.php?option=com_easyblog&view=featured' );

        $model		= $this->getModel( 'Featured' );
        $data   	= $model->getFeaturedBlog();
        $pagination	= $model->getPagination();
		$params		= $mainframe->getParams('com_easyblog');
		$data		= EasyBlogHelper::formatBlog( $data );
		$blogModel	= $this->getModel( 'Blog' );

		$pageNumber	= $pagination->get( 'pages.current' );
		$pageText	= ($pageNumber == 1) ? '' : ' - ' . JText::sprintf( 'COM_EASYBLOG_PAGE_NUMBER', $pageNumber );
		$document->setTitle( EasyBlogHelper::getPageTitle( JText::_( 'COM_EASYBLOG_FEATURED_PAGE_TITLE' ) . $pageText ) );

		if($config->get('layout_showcomment', false))
		{
		    for($i = 0; $i < count($data); $i++)
		    {
		        $row  			=& $data[$i];
				$maxComment 	= $config->get('layout_showcommentcount', 3);
    			$comments		= EasyBlogHelper::getHelper( 'Comment' )->getBlogComment( $row->id, $maxComment , 'desc' );
                $comments   	= EasyBlogHelper::formatBlogCommentsLite($comments);
	    		$row->comments	= $comments;
		    }
		}

		$theme	= new CodeThemes();
		$theme->set('data', $data );
		$theme->set('pagination', $pagination->getPagesLinks());
		$theme->set( 'currentURL' , 'index.php?option=com_easyblog&view=featured' );
		$theme->set('siteadmin', EasyBlogHelper::isSiteAdmin() );
		$theme->set('config', $config );
		$theme->set( 'acl', $acl );

		echo $theme->fetch( 'blog.featured.php' );
	}
}
