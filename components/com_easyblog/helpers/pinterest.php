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

class EasyBlogPinterestHelper
{
	public function getHTML( $frontpage , $position , $blog , $teamIdLink )
	{
		$config		= EasyBlogHelper::getConfig();

		$enabled	= ( !$frontpage && $config->get('main_pinit_button') ) || ( $frontpage && $config->get( 'main_pinit_button_frontpage', $config->get('social_show_frontpage') ) && $config->get('main_pinit_button'));

		if( !$enabled )
		{
			return false;
		}

		$style		= $config->get( 'main_pinit_button_style' );
		$url 		= EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry&id=' . $blog->id  , false , true );

		// @task: Test for blog image first.
		$image		= '';

		if( $blog->getImage() )
		{
			$image	= $blog->getImage()->getSource( 'frontpage' );
		}

		if( empty( $image ) )
		{
			// Fetch the first image of the blog post
			$image 		= EasyBlogHelper::getFirstImage( $blog->intro . $blog->content );

			// @rule: Test if there's any ->images
			if( isset( $blog->images ) && $blog->images )
			{
				$image 	= $blog->images[0];
			}
		}

		// @rule: If post doesn't contain any images, do not show button.
		if( !$image )
		{
			return false;
		}

		$buttonSize 	= 'social-button-';
		switch( $style )
		{
			case 'vertical':
				$buttonSize .= 'large';
			break;
			case 'horizontal':
				$buttonSize .= 'small';
			break;
			default:
				$buttonSize .= 'plain';
			break;
		}

		// @TODO: Configurable maximum length
		$contentLength	= 350;

		$text           = $blog->intro . $blog->content;
		$text           = nl2br($text);
		$text			= strip_tags( $text );
		$text 			= trim( preg_replace( '/\s+/', ' ', $text ) );
		$text			= ( JString::strlen( $text ) > $contentLength ) ? JString::substr( $text, 0, $contentLength) . '...' : $text;

		$theme 			= new CodeThemes();

		$title = $blog->title;

		// Urlencode all the necessary properties.
		$url 			= urlencode( $url );
		$text 			= urlencode( $text );
		$image 			= urlencode( $image );

		$placeholder 	= 'sb-' . rand();

		// Avoid issues with quotes surrounding title
		$title = addslashes($title);

		$output			= '<div class="social-button ' . $buttonSize . ' pinterest"><span id="' . $placeholder . '"></span></div>';
		$output			.= EasyBlogHelper::addScriptDeclarationBookmarklet('$("#' . $placeholder . '").bookmarklet("pinterest", {
					url: "'.$url.'",
					style: "'.$style.'",
					media: "' . $image . '",
					title: "' . $title . '",
					description: "' . $text . '"
				});');

		return $output;
	}
}
