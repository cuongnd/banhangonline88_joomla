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

class EasyBlogViewFeatured extends EasyBlogView
{
	function display( $tmpl = null )
	{
		$config 	= EasyBlogHelper::getConfig();
		$jConfig	= EasyBlogHelper::getJConfig();

		if( !$config->get( 'main_rss') )
		{
			return;
		}

		$model		= $this->getModel( 'Featured' );
		$data		= $model->getFeaturedBlog();

		$document	= JFactory::getDocument();
		$document->link	= EasyBlogRouter::_('index.php?option=com_easyblog&view=featured');

		$document->setTitle( JText::_( 'COM_EASYBLOG_FEEDS_FEATURED_TITLE' ) );
		$document->setDescription( JText::sprintf( 'COM_EASYBLOG_FEEDS_FEATURED_DESC' , JURI::root() ) );

		if(!empty($data))
		{
			for( $i = 0; $i < count( $data ); $i++ )
			{
				$row	=& $data[ $i ];

				$profile = EasyBlogHelper::getTable( 'Profile', 'Table' );
				$profile->load( $row->created_by );

				$created			= EasyBlogDateHelper::dateWithOffSet($row->created);
				$formatDate			= true;
				if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
				{
					$langCode		= EasyBlogStringHelper::getLangCode();
					if($langCode != 'en-GB' || $langCode != 'en-US')
						$formatDate = false;
				}
				// $row->created       = ( $formatDate ) ? $created->toFormat( $config->get('layout_dateformat', '%A, %d %B %Y') ) : $created->toFormat();
				$row->created		= $created->toMySQL();
				if( $config->get( 'main_rss_content' ) == 'introtext' )
				{
					$row->text		= ( !empty( $row->intro ) ) ? $row->intro : $row->content;
					//read more for feed
					$row->text 		.= '<br /><a href=' . EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id=' . $row->id ).'>Read more</a>';
				}
				else
				{
					$row->text		= $row->intro . $row->content;
				}
				$row->text			= EasyBlogHelper::getHelper( 'Videos' )->strip( $row->text );
				$row->text			= EasyBlogGoogleAdsense::stripAdsenseCode( $row->text );

				$category	= EasyBlogHelper::getTable( 'Category', 'Table' );
				$category->load( $row->category_id );

				// Assign to feed item
				$title	= $this->escape( $row->title );
				$title	= html_entity_decode( $title );

				// load individual item creator class
				$item				= new JFeedItem();
				$item->title		= $title;
				$item->link			= EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id=' . $row->id );
				$item->description	= $row->text;
				$item->date			= $row->created;
				$item->category		= $category->title;
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
}
