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

require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'date.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'string.php' );
require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'adsense.php' );

class EasyBlogViewCategories extends EasyBlogView
{
	function display( $tmpl = null )
	{
		$config		= EasyBlogHelper::getConfig();
		$jConfig	= EasyBlogHelper::getJConfig();

		if( !$config->get( 'main_rss') )
		{
			return;
		}

        $id			= JRequest::getCmd('id','0');
	    $category 	= EasyBlogHelper::getTable( 'Category', 'Table' );
	    $category->load($id);

	    // private category shouldn't allow to access.
		$privacy	= $category->checkPrivacy();
	    if(! $privacy->allowed )
	    {
	        return;
	    }


	    if($category->id == 0)
	    {
	        $category->title    = JText::_('COM_EASYBLOG_UNCATEGORIZED');
	    }

		//get the nested categories
		$category->childs = null;

		EasyBlogHelper::buildNestedCategories($category->id, $category);

		$linkage   = '';
		EasyBlogHelper::accessNestedCategories($category, $linkage, '0', '', 'link', ', ');

		$catIds     = array();
		$catIds[]   = $category->id;
		EasyBlogHelper::accessNestedCategoriesId($category, $catIds);

		$category->nestedLink    = $linkage;


        $model		= $this->getModel( 'Blog' );
        $sort		= JRequest::getCmd('sort', $config->get( 'layout_postorder' ) );
		$data		= $model->getBlogsBy('category', $catIds, $sort );

		$document	= JFactory::getDocument();
		$document->link	= EasyBlogRouter::_('index.php?option=com_easyblog&view=categories&id=' . $id . '&layout=listings');
		$document->setTitle( $this->escape($category->title) );
		$document->setDescription( JText::sprintf( 'COM_EASYBLOG_FEEDS_CATEGORY_DESC' , $this->escape($category->title) ) );

		if( empty($data))
		{
			return;
		}
		for( $i = 0; $i < count( $data ); $i++ )
		{
			$row	=& $data[ $i ];

			$blog 	= EasyBlogHelper::getTable( 'Blog' );
			$blog->load( $row->id );

			$user   = JFactory::getUser($row->created_by);
			$profile = EasyBlogHelper::getTable( 'Profile', 'Table' );
			$profile->load( $user->id );

			$created 			= EasyBlogHelper::getDate( $row->created );
			$formatDate         = true;
			if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
			{
			    $langCode   = EasyBlogStringHelper::getLangCode();
			    if($langCode != 'en-GB' || $langCode != 'en-US')
					$formatDate = false;
			}
			//$row->created       = ( $formatDate ) ? $created->toFormat( $config->get('layout_dateformat', '%A, %d %B %Y') ) : $created->toFormat();
			$row->created       = $created->toMySQL();
			if( $config->get( 'main_rss_content' ) == 'introtext' )
			{
				$row->text			= ( !empty( $row->intro ) ) ? $row->intro : $row->content;
			}
			else
			{
			    $row->text			= $row->intro . $row->content;
			}
			$row->text          = EasyBlogHelper::getHelper( 'Videos' )->strip( $row->text );
			$row->text			= EasyBlogGoogleAdsense::stripAdsenseCode( $row->text );

			$image 				= '';
			if( $blog->getImage() )
			{
				$image 			= '<img src="' . $blog->getImage()->getSource( 'frontpage' ) . '" />';
			}

			// load individual item creator class
			$item				= new JFeedItem();
			$item->title 		= html_entity_decode( $this->escape( $row->title ) );
			$item->link 		= EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id=' . $row->id );
			$item->description 	= $image . $row->text;
			$item->date			= $row->created;
			$item->category   	= $category->title;
			$item->author		= $profile->getName();

			if( $jConfig->get( 'feed_email' ) == 'author' )
			{
				$item->authorEmail	= $profile->user->email;
			}
			else
			{
				$item->authorEmail	= $jConfig->get( 'mailfrom' );
			}

			$document->addItem( $item );
		}
	}
}
