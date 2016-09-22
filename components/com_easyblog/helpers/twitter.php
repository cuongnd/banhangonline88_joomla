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


class EasyBlogTwitterHelper
{
	public static function getImage( &$blog , $rawIntroText = '' )
	{
		$cfg 		= EasyBlogHelper::getConfig();

		// @task: First, we try to search to see if there's a blog image. If there is already, just ignore the rest.
		if( $blog->getImage() )
		{
			return $blog->getImage()->getSource( 'thumbnail' );
		}

		// @legacy: If there's no image for this blog post, then we do this the legacy way.
		// First let's try to find for an image.
		$img            = '';
		$pattern		= '#<img[^>]*>#i';
		preg_match( $pattern , $blog->content , $matches );

		if($matches )
		{
			$img    = $matches[0];
		}
		else
		{
			$text		= ( $cfg->get( 'main_hideintro_entryview' ) ) ? $rawIntroText : $blog->intro;
			preg_match( $pattern , $text , $matches );
			if($matches )
			{
				$img    = $matches[0];
			}
		}

		// Default image
		$source	= '';

		//image found. now we process further to get the absolute image path.
		if( $img )
		{
			//get the img source
			$pattern = '/src=[\"\']?([^\"\']?.*(png|jpg|jpeg|gif))[\"\']?/i';
			preg_match($pattern, $img, $matches);
			if($matches)
			{
				$imgPath   = $matches[1];
				$source    = EasyImageHelper::rel2abs($imgPath, JURI::root());
			}
		}

		return $source;
	}

	public static function addCard( &$blog , $rawIntroText )
	{
		$cfg 			= EasyBlogHelper::getConfig();

		// @rule: Check if user really wants to append the opengraph tags on the headers.
		if( !$cfg->get( 'main_twitter_cards' ) )
		{
			return false;
		}

		// Get the absolute permalink for this blog item.
		$url	= EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry&id=' . $blog->id , false , true );

		// Get the image of the blog post.
		$image	= self::getImage( $blog , $rawIntroText );

		// @task: Get Joomla's document object.
		$doc 	= JFactory::getDocument();

		// Add card definition.
		$doc->addCustomTag( '<meta property="twitter:card" content="summary" />' );

		$doc->addCustomTag( '<meta property="twitter:url" content="' . $url . '" />' );
		$doc->addCustomTag( '<meta property="twitter:title" content="' . $blog->title . '" />' );

		// Try to get the stored meta for the blog post
		$meta	= EasyBlogHelper::getTable( 'Meta' , 'Table' );
		$meta->loadByType( META_TYPE_POST , $blog->id );

		if( !empty( $meta->description ) )
		{
			$text 		= $meta->description;
		}
		else
		{
			$text		= EasyBlogHelper::stripEmbedTags( $rawIntroText );
		}

		$text			= strip_tags( $text );
		$text			= str_ireplace( "\r\n" , "" , $text );
		$text			= str_ireplace( "&nbsp;" , " " , $text );
		$text			= trim($text);

		// Remove any " in the content as this would mess up the headers.
		$text 			= str_ireplace( '"' , '' , $text );

		$maxLength = 137;

		if( !empty( $maxLength ) )
		{
			$text		= ( JString::strlen( $text ) > $maxLength ) ? JString::substr( $text, 0, $maxLength ) . '...' : $text;
		}

		$text 	= strip_tags( $text );
		$text 	= addslashes( $text );

		$doc->addCustomTag( '<meta property="twitter:description" content="' . $text . '" />' );

		if( $image )
		{
			$doc->addCustomTag( '<meta property="twitter:image" content="' . $image . '"/> ');
		}

		return true;
	}
}
