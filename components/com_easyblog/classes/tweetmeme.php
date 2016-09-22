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

class EasyBlogTweetMeme
{
	public static function getHTML( $row )
	{
		$config	= EasyBlogHelper::getConfig();

		if( !$config->get('main_tweetmeme') )
		{
			return '';
		}

		$service	= $config->get('main_tweetmeme_url');
		$style		= $config->get('main_tweetmeme_style');
		$source		= $config->get('main_tweetmeme_rtsource');

		$buttonSize = 'social-button-';
		switch( $style )
		{
			case 'normal':
				$buttonSize .= 'large';
			break;
			case 'compact':
			default:
				$buttonSize .= 'small';
			break;
		}

		$url        = EasyBlogRouter::getRoutedURL('index.php?option=com_easyblog&view=entry&id=' . $row->id, false, true);
		$title      = addslashes($row->title);

		$placeholder = 'sb-' . rand();
		$html  = '<div class="social-button ' . $buttonSize . ' tweetmeme"><span id="' . $placeholder . '"></span></div>';

		$html .= EasyBlogHelper::addScriptDeclarationBookmarklet('$("#' . $placeholder . '").bookmarklet("tweetMeme", {
			service: "'.$service.'",
			style: "'.$style.'",
			url: "'.$url.'",
			title: "'.$title.'",
			source: "'.$source.'"
		});');

		return $html;
	}
}
