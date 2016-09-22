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

class EasyBlogViewSearch extends EasyBlogView
{
	function display( $tmpl = null )
	{		
		// set meta tags for latest post
		EasyBlogHelper::setMeta( META_ID_SEARCH , META_TYPE_SEARCH );
		$document	= JFactory::getDocument();
		$document->setTitle( EasyBlogHelper::getPageTitle( JText::_( 'COM_EASYBLOG_SEARCH_PAGE_TITLE' ) ) );
		
		if( ! EasyBlogRouter::isCurrentActiveMenu( 'search' ) )
		{
			$this->setPathway( JText::_( 'COM_EASYBLOG_SEARCH_BREADCRUMB' ) );
		}
		
		$query		= JRequest::getVar( 'query' );
		$Itemid     = JRequest::getInt( 'Itemid' );
		
		if( empty( $query ) )
		{
			$posts		= array();
			$pagination	= '';
		}
		else
		{
			$model		= $this->getModel( 'Search' );
			$result		= $model->getData();

			if( count($result) > 0 )
			{
				// strip out all the media code
				for($i = 0; $i < count($result); $i++ )
				{
					$row	=& $result[$i];

					// strip videos
					$row->intro			= EasyBlogHelper::getHelper( 'Videos' )->strip( $row->intro);
					$row->content		= EasyBlogHelper::getHelper( 'Videos' )->strip( $row->content);

					// strip gallery
					$row->intro			= EasyBlogHelper::getHelper( 'Gallery' )->strip( $row->intro);
					$row->content		= EasyBlogHelper::getHelper( 'Gallery' )->strip( $row->content);

					// strip jomsocial album
					$row->intro			= EasyBlogHelper::getHelper( 'Album' )->strip( $row->intro );
					$row->content		= EasyBlogHelper::getHelper( 'Album' )->strip( $row->content );

					// strip audio
					$row->intro			= EasyBlogHelper::getHelper( 'Audio' )->strip( $row->intro );
					$row->content		= EasyBlogHelper::getHelper( 'Audio' )->strip( $row->content );
				}
			}

			$posts		= EasyBlogHelper::formatBlog( $result );
			$pagination	= $model->getPagination();
		}
		

		if( count($posts) > 0 )
		{
			$searchworda	= preg_replace('#\xE3\x80\x80#s', ' ', $query);
			$searchwords	= preg_split("/\s+/u", $searchworda);
			$needle			= $searchwords[0];
			$searchwords	= array_unique($searchwords);
		
			for($i = 0; $i < count($posts); $i++ )
			{
				$row		= $posts[$i];

				$content 	= preg_replace( '/\s+/' , ' ' , strip_tags( $row->content ) );

				$pattern	= '#(';
				$x 			= 0;

				foreach ($searchwords as $k => $hlword)
				{
					$pattern 	.= $x == 0 ? '' : '|';
					$pattern	.= preg_quote( $hlword , '#' );
					$x++;
				}
				$pattern 		.= ')#iu';

				$row->title 	= preg_replace( $pattern , '<span class="search-highlight">\0</span>' , $row->title );
				$row->content 	= preg_replace( $pattern , '<span class="search-highlight">\0</span>' , JString::substr( strip_tags( $row->content ) , 0 , 250 ) );
			}
		}

		$jConfig		= EasyBlogHelper::getJConfig();
		$theme			= new CodeThemes();
		$theme->set( 'jConfig'		, $jConfig );
		$theme->set( 'query'		, $query );
		$theme->set( 'posts'		, $posts );
		$theme->set( 'pagination'	, $pagination );
		$theme->set( 'Itemid'	, $Itemid );
		
		echo $theme->fetch( 'search.php' );
	}
	
	function parseQuery()
	{
		$mainframe	= JFactory::getApplication();
		$query		= JRequest::getVar('query', '');
		$query		= rtrim( $query , '.' );
		$mainframe->redirect(EasyBlogRouter::_( 'index.php?option=com_easyblog&view=search&query='.$query, false ));
		$mainframe->close();
	}
}