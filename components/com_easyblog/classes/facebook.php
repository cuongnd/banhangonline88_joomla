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

require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR .'helper.php' );

class EasyBlogFacebookLikes
{
	public static function getImage( &$blog , $rawIntroText = '' )
	{
		$cfg 		= EasyBlogHelper::getConfig();

		// @task: First, we try to search to see if there's a blog image. If there is already, just ignore the rest.
		if( $blog->getImage() )
		{
			return $blog->getImage()->getSource( 'thumbnail' );
		}

		// For image posts.
		if( isset( $blog->images[0] ) )
		{
			return $blog->images[0 ];
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
		$source	= rtrim( JURI::root() , '/' ) . '/components/com_easyblog/assets/images/default_facebook.png';

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

	public static function addOpenGraphTags( &$blog , $rawIntroText = '' )
	{
		$cfg 			= EasyBlogHelper::getConfig();

		// @rule: Check if user really wants to append the opengraph tags on the headers.
		if( !$cfg->get( 'main_facebook_opengraph' ) )
		{
			return false;
		}

		// Get the absolute permalink for this blog item.
		$url	= EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry&id=' . $blog->id , false , true );

		// Get the image of the blog post.
		$image	= self::getImage( $blog , $rawIntroText );

		// @task: Get Joomla's document object.
		$doc 	= JFactory::getDocument();

		// Add the blog image.
		$doc->addCustomTag( '<meta property="og:image" content="' . $image . '"/> ');

		if( $cfg->get('main_facebook_like_appid') )
		{
			$doc->addCustomTag( '<meta property="fb:app_id" content="' . $cfg->get('main_facebook_like_appid') . '"/> ');
		}

		if( $cfg->get('main_facebook_like_admin') )
		{
			$doc->addCustomTag( '<meta property="fb:admins" content="' . $cfg->get('main_facebook_like_admin') . '"/>' );
		}

		$meta	= EasyBlogHelper::getTable( 'Meta' , 'Table' );
		$meta->loadByType( META_TYPE_POST , $blog->id );

		$doc->addCustomTag( '<meta property="og:title" content="' . $blog->title . '" />' );

		// @task: Add description of the blog.
		if( !empty( $meta->description ) )
		{
			$meta->description 	= strip_tags( $meta->description );
			$meta->description 	= addslashes( $meta->description );

			$doc->addCustomTag( '<meta property="og:description" content="' .  $meta->description . '" />' );
		}
		else
		{
			$maxLength		= $cfg->get( 'integrations_facebook_blogs_length' );
			$text			= EasyBlogHelper::stripEmbedTags( $rawIntroText );
			$text			= strip_tags( $text );
			$text			= str_ireplace( "\r\n" , "" , $text );
			$text			= str_ireplace( "&nbsp;" , " " , $text );
			$text			= trim($text);

			// Remove any " in the content as this would mess up the headers.
			$text 			= str_ireplace( '"' , '' , $text );

			if( !empty( $maxLength ) )
			{
				$text		= ( JString::strlen( $text ) > $maxLength ) ? JString::substr( $text, 0, $maxLength ) . '...' : $text;
			}

			$text	= EasyBlogStringHelper::escape( $text );
			$doc->addCustomTag( '<meta property="og:description" content="' . $text . '" />' );
		}

		$doc->addCustomTag( '<meta property="og:type" content="article" />' );
		$doc->addCustomTag( '<meta property="og:url" content="' . $url . '" />' );

		return true;
	}

	public static function getLikeHTML( $row )
	{
		$config	= EasyBlogHelper::getConfig();

		if( !$config->get('main_facebook_like') )
		{
			return '';
		}


		$views		= JRequest::getCmd(	'view' , '');
		$layout		= JRequest::getCmd( 'layout' , '' );

		if( ! $config->get('integrations_facebook_show_in_listing') )
		{
			if($views == 'latest' || $views == 'blogger' || $views == 'teamblog' || $layout == 'tag' || ( $views == 'categories' && $layout == 'listings' ) )
			{
				return '';
			}
		}
		$document	= JFactory::getDocument();
		$language	= $document->getLanguage();
		$language	= explode( '-' , $language );

		if( count( $language ) != 2 )
		{
			$language	= array( 'en' , 'GB' );
		}

		$layout		= $config->get('main_facebook_like_layout');
		$faces		= $config->get('main_facebook_like_faces') ? 'true' : 'false';
		$width		= $config->get('main_facebook_like_width');
		$verb		= $config->get('main_facebook_like_verb' );
		$theme		= $config->get('main_facebook_like_theme' );
		$send		= $config->get( 'main_facebook_like_send' ) ? 'true' : 'false';

		$sbPosition		= $config->get('main_socialbutton_position' );
		$fbPosition		= $config->get('main_facebook_like_position' );

		if( $fbPosition == '1' )
		{
			$faces  = false;
		}

		require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'router.php' );
		$url		= EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry&id=' . $row->id , true , true );

		// if the layout == box_count, the we hard code it to 50 the with.
		$height     = ($faces == 'true') ? '70' : '30';
		$width      = ($layout == 'standard') ? $width : 'auto';


		if( $layout != 'standard' && $send == 'true' && $sbPosition != 'right' && $sbPosition != 'left')
		{
			$width	= 'auto';
		}

		$locale     = $language[0] . '_' . JString::strtoupper( $language[1] );

		$html 		= '';

		$placeholder = 'sb-' . rand();
		$html  = '<span id="' . $placeholder . '"></span>';

		$html .= EasyBlogHelper::addScriptDeclarationBookmarklet('$("#' . $placeholder . '").bookmarklet("facebookLike", {
			url: "'.$url.'",
			send: "'.$send.'",
			layout: "'.$layout.'",
			verb: "'.$verb.'",
			locale: "'.$locale.'",
			faces: "'.$faces.'",
			theme: "'.$theme.'",
			height: "'.$height.'",
			width: "'.$width.'"
		});');

		return $html;
	}
}
