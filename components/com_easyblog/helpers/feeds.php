<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file' );
jimport('joomla.filesystem.folder' );


require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'constants.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'router.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'socialshare.php' );

class EasyBlogFeedsHelper
{
	function addHeaders( $feedUrl )
	{
		$config		= EasyBlogHelper::getConfig();
		$document	= JFactory::getDocument();

		// If rss is disabled or the current view type is not of html, do not add the headers
		if( !$config->get('main_rss') || $document->getType() != 'html' )
		{
			return false;
		}

		$enabled	= $config->get( 'main_feedburner' );
		$url		= $config->get( 'main_feedburner_url' );

		require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'router.php' );
		$sef  = EasyBlogRouter::isSefEnabled();
		$concat		= $sef ? '?' : '&';

		if( $enabled && !empty( $url ) )
		{
			$document->addHeadLink( $url , 'alternate' , 'rel' , array('type' => 'application/rss+xml', 'title' => 'RSS 2.0') );
			return;
		}

	    // Add default rss feed link
	    $document->addHeadLink( EasyBlogRouter::_( $feedUrl ) . $concat . 'format=feed&type=rss' , 'alternate' , 'rel' , array('type' => 'application/rss+xml', 'title' => 'RSS 2.0') );
		$document->addHeadLink( EasyBlogRouter::_( $feedUrl ) . $concat . 'format=feed&type=atom' , 'alternate' , 'rel' , array('type' => 'application/atom+xml', 'title' => 'Atom 1.0') );
	}

	function getFeedURL( $url , $atom = false, $type = 'site')
	{
	    $config		= EasyBlogHelper::getConfig();
		$enabled	= $config->get( 'main_feedburner' );

		if( $enabled && $type == 'site' && $config->get('main_feedburner_url') != '' )
		{
		    $url		= $config->get( 'main_feedburner_url' );
		    if( !empty( $url ) )
		    {
				return EasyBlogHelper::getHelper( 'String' )->escape( $url );
			}
		}

		require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'router.php' );
		$sef  		= EasyBlogRouter::isSefEnabled();
		$join		= $sef ? '?' : '&';
		$url		= EasyBlogRouter::_( $url ) . $join . 'format=feed';
		$url		.= $atom ? '&type=atom' : '&type=rss';

		return $url;
	}

	function import( $feedObj, $maxItems = 0)
	{
		jimport('simplepie.simplepie');

	    $config     	= EasyBlogHelper::getConfig();
	    $itemMigrated   = 0;
	    $isDomSupported	= false;
	    $defaultAllowedHTML = '<img>,<a>,<br>,<table>,<tbody>,<th>,<tr>,<td>,<div>,<span>,<p>,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>';

	    if(class_exists('DomDocument'))
	    {
	    	$isDomSupported = true;
			require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'readability' . DIRECTORY_SEPARATOR . 'Readability.php' );
	    }

		$params		= EasyBlogHelper::getRegistry( $feedObj->params );
	    $maxItems   = ( $maxItems ) ? $maxItems : $params->get( 'feedamount' , 0 );

    	$feedURL	= $feedObj->url;

	    require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'connectors.php' );
	    $connector  = new EasyBlogConnectorsHelper();
		$connector->addUrl( $feedURL );
		$connector->execute();
	    $content	= $connector->getResult( $feedURL );

		// to ensure the leading no text before the <?xml> tag
		//$pattern	= '/(.*?)(?=<\?xml)/ims';
		$pattern    	= '/(.*?)<\?xml version/is';
		$replacement    = '<?xml version';
		$content		= preg_replace( $pattern , $replacement , $content, 1);

		if( strpos( $content, '<?xml version' ) === false )
		{
			// look like the content missing the xml header. lets manually add in.
			$content = '<?xml version="1.0" encoding="utf-8"?>' . $content;
		}

	    $parser	= new SimplePie();
		$parser->strip_htmltags(false);
		$parser->set_raw_data( $content );
		$parser->init();

		$items  = '';
		$items  = $parser->get_items();

		if( count($items) > 0)
		{
			//lets process the data insert

			$myCnt = 0;

			foreach( $items as $item )
			{
				@ini_set('max_execution_time', 180);

				if( !empty( $maxItems ) && ( $myCnt == $maxItems )  )
				{
					break;
				}

			    $timezoneSec 	= $item->get_date('Z');
			    $itemdate 		= $item->get_date('U');
			    $itemdate 		= $itemdate - $timezoneSec;

			    $mydate = date('Y-m-d H:i:s', $itemdate);

				$feedUid    	= $item->get_id();
				$feedPath       = $item->get_link();

				$feedHistory    = EasyBlogHelper::getTable( 'FeedHistory' );
				$newHistoryId	= '';

				if( $feedHistory->isExists( $feedObj->id, $feedUid ) )
				{
					continue;
				}
				else
				{
					//log the feed item so that in future it will not process again.
					$date					= EasyBlogHelper::getDate();
					$newHistory				= EasyBlogHelper::getTable( 'FeedHistory' );
					$newHistory->feed_id	= $feedObj->id;
					$newHistory->uid		= $feedUid;
					$newHistory->created	= $date->toMySQL();
					$newHistory->store();

					$newHistoryId = $newHistory->id;
				}

			    $blogObj   = new stdClass();
			    // set the default setting from the feed configuration via backend.
			    $blogObj->category_id   = $feedObj->item_category;
			    $blogObj->published   	= $feedObj->item_published;
			    $blogObj->frontpage   	= $feedObj->item_frontpage;
			    $blogObj->created_by   	= $feedObj->item_creator;
			    $blogObj->allowcomment	= $config->get('main_comment', 1);
				$blogObj->subscription	= $config->get('main_subscription', 1);
				$blogObj->issitewide	= '1';

				$text   = $item->get_content();

			    // @rule: Append copyright text
			    $blogObj->copyrights	= $params->get( 'copyrights' , '' );

			    if( $feedObj->item_get_fulltext && $isDomSupported )
			    {

					$feedItemUrl    = urldecode( $item->get_link() );

					$fiConnector	= new EasyBlogConnectorsHelper();
					$fiConnector->addUrl( $feedItemUrl );
					$fiConnector->execute();
		    		$fiContent	= $fiConnector->getResult( $feedItemUrl );

					// to ensure the leading no text before the <?xml> tag
					$pattern    	= '/(.*?)<html/is';
					$replacement    = '<html';
					$fiContent		= preg_replace( $pattern , $replacement , $fiContent, 1);

		    		if( !empty( $fiContent ) )
		    		{
						$fiContent	= EasyBlogHelper::getHelper( 'string' )->forceUTF8( $fiContent );
			    		$readability = new Readability($fiContent);

			    		$readability->debug = false;
			    		$readability->convertLinksToFootnotes = false;
			    		$result = $readability->init();

			    		if( $result )
			    		{
							$content 	= $readability->getContent()->innerHTML;
							//$content	= EasyBlogHelper::getHelper( 'string' )->fixUTF8( $content );
       						$content	= EasyBlogFeedsHelper::tidyContent($content);

							if( stristr( html_entity_decode(  $content ) , '<!DOCTYPE html' ) === false )
							{
							    $text		= $content;
							    $text		= $this->_processRelLinktoAbs($text, $feedPath);
							}

			    		}
		    		}
	    		}


	    		// strip un-allowed html tag.
	    		$text	= strip_tags( $text , $params->get( 'allowed' , $defaultAllowedHTML) );

				// Append original source link into article if necessary
				if( $params->get( 'sourceLinks') )
				{
					JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );
					$text	.= '<div><a href="' . $item->get_link() . '" target="_blank">' . JText::_( 'COM_EASYBLOG_FEEDS_ORIGINAL_LINK' ) . '</a></div>';
				}

			    if( $feedObj->author )
			    {
					$feedAuthor		= $item->get_author();
					if( !empty($feedAuthor) )
					{
						$authorName     = $feedAuthor->get_name();
						$authorEmail    = $feedAuthor->get_email();

						if( !empty($authorName) )
						{
							// Store it as copyright column instead
							$text	.= '<div>' . JText::sprintf( 'COM_EASYBLOG_FEEDS_ORIGINAL_AUTHOR' , $authorName ) . '</div>';
						}
						else if( !empty($authorEmail) )
						{
							$authorArr  = explode(' ', $authorEmail);

							if( isset( $authorArr[1] ) )
							{
							    $authorName     = $authorArr[1];
							    $authorName     = str_replace( array('(',')' ) , '', $authorName);
							    $text			.= '<div>' . JText::sprintf( 'COM_EASYBLOG_FEEDS_ORIGINAL_AUTHOR' , $authorName ) . '</div>';
							}
						}
					}

				}

			    if( $feedObj->item_content == 'intro' )
			    {
			        $blogObj->intro = $text;
			    }
			    else
				{
			        $blogObj->content	= $text;
			    }

			    $creationDate 		= $mydate;
				$blogObj->created 	= $mydate;
				$blogObj->modified	= $mydate;

				$blogObj->title			= $item->get_title();
				if( empty( $blogObj->title ) )
				{
					$blogObj->title = $this->_getTitleFromLink($item->get_link());
				}
				$blogObj->title = EasyBlogStringHelper::unhtmlentities( $blogObj->title );

				$blogObj->permalink		= EasyBlogHelper::getPermalink( $blogObj->title );
				$blogObj->publish_up 	= $mydate;
				$blogObj->isnew         = ( ! $feedObj->item_published ) ? true : false;

				$blog   = EasyBlogHelper::getTable( 'blog' );
				$blog->bind( $blogObj );

				if( $blog->store() )
				{
					$myCnt++;
					//update the history with blog id
					if( !empty( $newHistoryId ) )
					{
						$tmpHistory = EasyBlogHelper::getTable( 'FeedHistory' );
						$tmpHistory->load( $newHistoryId );
						$tmpHistory->post_id   = $blog->id;
						$tmpHistory->store();
					}

					$itemMigrated++;

				    if( $feedObj->item_published )
				    {
				    	// notify admins and subscribers
						$blog->notify();

					    //insert activity here.
					    EasyBlogHelper::addJomSocialActivityBlog($blog, true, true);

					    // Determines if admin wants to auto post this item to the social sites.
						if( $params->get( 'autopost' ) )
						{
							$allowed	= array( EBLOG_OAUTH_LINKEDIN , EBLOG_OAUTH_FACEBOOK , EBLOG_OAUTH_TWITTER );

							// @rule: Process centralized options first
							// See if there are any global postings enabled.

							$blog->autopost( $allowed , $allowed );
						}

					}
				} //end if

			}
		}

		return $itemMigrated;
	}

	function _getTitleFromLink( $link )
	{
		$segment    = explode('/', $link);
		$page       = '';

		if( count( $segment ) > 1 )
		{
			$page       = $segment[ count( $segment ) - 1];
			$page       = JString::str_ireplace( '.html', '', $page);
			$page       = JString::str_ireplace( '-', ' ', $page);
			$page       = ucwords($page);
		}

		return $page;
	}

	function _processRelLinktoAbs( $content, $absPath )
	{
		$dom = new DOMDocument();
		@$dom->loadHTML($content);

		// anchor links
		$links = $dom->getElementsByTagName('a');
		foreach($links as $link)
		{
			$oriUrlLink 	= $link->getAttribute('href');
			$urlLink    	= EasyBlogHelper::getHelper('string')->encodeURL( $oriUrlLink );
			$urlLink    	= EasyBlogHelper::getHelper('string')->rel2abs( $urlLink, $absPath );
            $link->setAttribute('href', $urlLink);

			$content    = str_replace( 'href="' . $oriUrlLink .'"', 'href="' . $urlLink .'"', $content );
		}


		// image src
		$imgs = $dom->getElementsByTagName('img');
		foreach($imgs as $img)
		{
			$oriImgLink = $img->getAttribute('src');
			$imgLink    = EasyBlogHelper::getHelper('string')->encodeURL( $oriImgLink );
			$imgLink    = EasyBlogHelper::getHelper('string')->rel2abs( $imgLink, $absPath );
			$content    = str_replace( 'src="' . $oriImgLink .'"', 'src="' . $imgLink .'"', $content );
		}

		return $content;
	}

	function cron()
	{
	    $db 		= EasyBlogHelper::db();
	    $itemCnt    = 0;

		$date       = EasyBlogHelper::getDate();

		$query  = 'select `id`';
		$query  .= ' from `#__easyblog_feeds`';
		$query  .= ' where `cron` = ' . $db->Quote('1');
		$query  .= ' and `flag` = ' . $db->Quote('0');
		$query  .= ' and `published` = ' . $db->Quote('1');
		$query  .= ' and ( ' . $db->Quote( $date->toMySQL() ) . ' >= DATE_ADD(`last_import`, INTERVAL `interval` MINUTE) OR `last_import` = ' . $db->Quote('0000-00-00 00:00:00') . ' )';
		$query  .= ' order by `last_import`';
		$query  .= ' LIMIT 1';

	    $db->setQuery($query);
	    $result = $db->loadObjectList();

		if( count( $result ) > 0)
		{
		    foreach( $result as $item)
		    {
		        $tbl    = EasyBlogHelper::getTable( 'Feed' );
		        $tbl->load( $item->id );

		        $tbl->flag  = '0';
				$date           	= EasyBlogHelper::getDate();
				$tbl->last_import 	= $date->toMySQL();
		        $tbl->store();

		        // $itemCnt	= EasyBlogHelper::getHelper('Feeds')->import( $tbl );
		       	$itemCnt	= $this->import( $tbl );


		    }
		}

	    $msg    = '';

	    if( $itemCnt == 0)
	    {
	        $msg = 'No item migrated from feed "' . $tbl->title . '" (' . $tbl->url . ')';
	    }
	    else
	    {
	        $msg = $itemCnt. ' items migrated from feed "' . $tbl->title . '" (' . $tbl->url . ')';
		}

	    echo $msg;
	    return true;
	}

	function tidyContent( $html )
	{
		return EasyBlogHelper::getHelper('string')->tidyHTMLContent( $html );
	}

}
