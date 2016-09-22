<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2011 Stack Ideas Private Limited. All rights reserved.
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
jimport('joomla.html.parameter' );
jimport('joomla.application.component.model');
jimport('joomla.access.access');

require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'constants.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'router.php' );


class EasyBlogSocialButtonHelper
{
	var $blog   		= null;
	var $isFrontend   	= null;
	var $position   	= null;
	var $teamLink     	= null;
	var $isBottom       = null;

	public function __construct()
	{
		//empty contructor.
	}

	public function setBlog( $blog )
	{
		$table 		= EasyBlogHelper::getTable( 'Blog' );
		$table->load( $blog->id );
		$this->blog = $table;
	}

	public function setFrontend( $isFrontend )
	{
		$this->isFrontend = $isFrontend;
	}

	public function setPosition( $position )
	{
		$this->position = $position;
	}

	public function setTeamId( $teamId )
	{
		$this->teamLink = '';

		if( !empty( $teamId ) )
			$this->teamLink = $teamId;
	}

	public function setBottom( $isBottom )
	{
		$this->isBottom = $isBottom;
	}

	public function tweetmeme()
	{
		return '';

		$config 	= EasyBlogHelper::getConfig();
		$document   = JFactory::getDocument();

		$frontpage  = $this->isFrontend;
		$pos        = $this->position;
		$tweetmeme	= $config->get('main_tweetmeme') && !$frontpage || $frontpage && $config->get('main_tweetmeme_frontpage' , $config->get('social_show_frontpage') ) && $config->get('main_tweetmeme');	// TweetMeme
		$html   	= '';

		if( $tweetmeme )
		{
			require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'tweetmeme.php' );

			$html	= EasyBlogTweetmeme::getHTML( $this->blog );
			if($this->isBottom)
			{
				$html  = '<div class="socialbutton-vertical align' . $pos . '">'.$html.'</div>';
			}
		}

		return $html;
	}

	public function linkedin()
	{
		$config 	= EasyBlogHelper::getConfig();
		$document   = JFactory::getDocument();

		$frontpage  = $this->isFrontend;
		$pos        = $this->position;
		$linkedin	= ( ( !$frontpage && $config->get( 'main_linkedin_button' ) ) || ( $frontpage && $config->get( 'main_linkedin_button_frontpage' , $config->get('social_show_frontpage') ) ) )  && $config->get( 'main_linkedin_button' );

		$html   	= '';

		if( $linkedin )
		{
			$dataURL		= $this->_getDataURL();
			$dataTitle		= $this->_getDataTitle();

			$style		=  $config->get( 'main_linkedin_button_style' );
			$counter    = '';
			$buttonSize = 'social-button-';

			switch( $style )
			{
				case 'vertical':
					$counter	= 'top';
					$buttonSize .= 'large';
				break;
				case 'horizontal':
					$counter	= 'right';
					$buttonSize .= 'small';
				break;
				default:
					$counter	= '';
					$buttonSize .= 'plain';
				break;
			}

			$placeholder = 'sb-' . rand();

			$html	= '<div class="social-button ' . $buttonSize. ' linkedin-share"><span id="' . $placeholder . '"></span></div>';

			if($this->isBottom)
			{
				$html  = '<div class="socialbutton-vertical align' . $pos . '">'.$html.'</div>';
			}

			$html .= EasyBlogHelper::addScriptDeclarationBookmarklet('$("#' . $placeholder . '").bookmarklet("linkedIn", {
				url: "'.$dataURL.'",
				counter: "'.$counter.'"
			});');
		}

		return $html;
	}

	public function digg()
	{
		$config 	= EasyBlogHelper::getConfig();
		$document   = JFactory::getDocument();

		$frontpage  = $this->isFrontend;
		$pos        = $this->position;

		$digg		= !$frontpage && $config->get( 'main_digg_button' ) || $frontpage && $config->get( 'main_digg_button_frontpage' , $config->get('social_show_frontpage') ) && $config->get( 'main_digg_button');
		$html   	= '';

		if( $digg )
		{

			$dataURL		= $this->_getDataURL();
			$dataTitle		= $this->_getDataTitle();
			$class          = '';

			$buttonSize 	= 'social-button-';
			switch( $config->get( 'main_digg_button_style' ) )
			{
				case 'compact':
					$class 	= 'DiggCompact';
					$buttonSize .= 'small';
				break;
				case 'medium':
				default:
					$class 	= 'DiggMedium';
					$buttonSize .= 'large';
				break;
			}

			$placeholder = 'sb-' . rand();

			$html	= '<div class="social-button ' . $buttonSize . ' digg-share"><span id="' . $placeholder . '"></span></div>';

			if( $this->isBottom )
			{
				$html  = '<div class="socialbutton-vertical align' . $pos . '">'.$html.'</div>';
			}

			$html .= EasyBlogHelper::addScriptDeclarationBookmarklet('$("#' . $placeholder . '").bookmarklet("digg", {
				url: "'.$dataURL.'",
				title: "'.$dataTitle.'",
				classname: "'.$class.'"
			});');
		}

		return $html;
	}

	public function stumbleupon()
	{
		$config 	= EasyBlogHelper::getConfig();
		$document   = JFactory::getDocument();

		$frontpage  = $this->isFrontend;
		$pos        = $this->position;

		$stumbleupon	= !$frontpage && $config->get( 'main_stumbleupon_button' ) || $frontpage && $config->get( 'main_stumbleupon_button_frontpage', $config->get('social_show_frontpage')) && $config->get( 'main_stumbleupon_button' );
		$html   	= '';

		if( $stumbleupon )
		{

			$dataURL		= $this->_getDataURL();
			$dataTitle		= $this->_getDataTitle();
			$counter        = '';

			$style		=  $config->get( 'main_stumbleupon_button_style' );
			$buttonSize = 'social-button-';

			switch( $style )
			{
				case 'vertical':
					$counter	= '5';
					$buttonSize .= 'large';
				break;
				case 'horizontal':
					$counter	= '1';
					$buttonSize .= 'small';
				break;
				default:
					$counter	= '6';
					$buttonSize .= 'plain';
				break;
			}

			$placeholder = 'sb-' . rand();
			$html	= '<div class="social-button ' .$buttonSize.' stumbleupon-share"><span id="' . $placeholder . '"></span></div>';

			if($this->isBottom)
			{
				$html  = '<div class="socialbutton-vertical align' . $pos . '">'.$html.'</div>';
			}

			$html .= EasyBlogHelper::addScriptDeclarationBookmarklet('$("#' . $placeholder . '").bookmarklet("stumbleUpon", {
				url: "'.$dataURL.'",
				layout: "'.$counter.'"
			});');
		}

		return $html;
	}

	public function twitter()
	{
		$config 	= EasyBlogHelper::getConfig();
		$document   = JFactory::getDocument();

		$frontpage  = $this->isFrontend;
		$pos        = $this->position;

		$twitter	= ( !$frontpage && $config->get('main_twitter_button') ) || ($frontpage && $config->get('main_twitter_button_frontpage', $config->get('social_show_frontpage')) && $config->get('main_twitter_button'));
		$html   	= '';

		if( $twitter )
		{

			$dataURL		= $this->_getDataURL();
			$dataTitle		= $this->_getDataTitle();

			$buttonSize 	= 'social-button-';
			$style			= $config->get( 'main_twitter_button_style' );
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

			$placeholder 	= 'sb-' . rand();
			$html			= '<div class="social-button ' . $buttonSize . ' retweet"><span id="' . $placeholder . '"></span></div>';

			if($this->isBottom)
			{
				$html  = '<div class="socialbutton-vertical align' . $pos . '">'.$html.'</div>';
			}

			$screenname = $config->get( 'main_twitter_button_via_screen_name', '' );
			if( $screenname )
				$screenname = JString::substr( $screenname, 1 );

			$html .= EasyBlogHelper::addScriptDeclarationBookmarklet('$("#' . $placeholder . '").bookmarklet("twitter", {
				text: "'. EasyBlogHelper::getHelper('string')->escape($this->blog->title) . '",
				url: "'.$dataURL.'",
				via: "' . $screenname . '",
				count: "'.$style.'"
			});');
		}

		return $html;
	}

	public function facebook()
	{
		$config 	= EasyBlogHelper::getConfig();
		$document   = JFactory::getDocument();

		$frontpage  = $this->isFrontend;
		$pos        = $this->position;
		$html   	= '';

		$fb			= ( !$frontpage && $config->get('main_facebook_like') ) || ( $frontpage && $config->get( 'integrations_facebook_show_in_listing', $config->get('social_show_frontpage') ) && $config->get('main_facebook_like'));

		if( !$fb )
		{
			return;
		}

		$fblayout			= $config->get('main_facebook_like_layout');
		$fbLikesButton		= $config->get('main_facebook_like');

		$buttonSize 	= 'social-button-';
		switch( $fblayout )
		{
			case 'box_count':
				$buttonSize .= 'large';
			break;
			case 'button_count':
				$buttonSize .= 'small';
			break;
			case 'standard':
			default:
				$buttonSize .= 'standard';
			break;
		}

		if( $config->get('main_facebook_like') )
		{
			if($fblayout != 'standard')
			{
				//if the layout is box_count then only we stick the fb likes with others social button.
				require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'facebook.php' );
				$html	= EasyBlogFacebookLikes::getLikeHTML( $this->blog );

				if( !empty( $html ) )
				{
					$html	= '<div class="social-button ' . $buttonSize . ' facebook-like">' . $html . '</div>';
					if($this->isBottom)
					{
						$html  = '<div class="socialbutton-vertical align' . $pos . '">'.$html.'</div>';
					}
				}
			}
		}

		return $html;
	}


	public function googleone()
	{
		$config 	= EasyBlogHelper::getConfig();
		$document   = JFactory::getDocument();

		$frontpage  = $this->isFrontend;
		$pos        = $this->position;

		$googleone	= ( !$frontpage && $config->get('main_googleone') ) || ( $frontpage && $config->get( 'main_googleone_frontpage', $config->get('social_show_frontpage') ) && $config->get('main_googleone'));
		$socialFrontEnd	= $config->get( 'main_googleone_frontpage', 0 );
		$html   	= '';

		if( $googleone )
		{
			$size		= $config->get('main_googleone_layout');
			$dataURL    = $this->_getDataURL();
			$dataTitle	= $this->_getDataTitle();

			$buttonSize 	= 'social-button-';
			switch( $size )
			{
				case 'tall':
					$buttonSize .= 'large';
				break;
				case 'medium':
				default:
					$buttonSize .= 'small';
				break;
			}

			// Add snippet info into headers
			$document	= JFactory::getDocument();

			$meta	= EasyBlogHelper::getTable( 'Meta' , 'Table' );
			$meta->loadByType( META_TYPE_POST , $this->blog->id );

			$document->addCustomTag( '<meta itemprop="name" content="' . $this->blog->title . '" />' );

			if( !empty( $meta->description ) )
			{
				$meta->description	= EasyBlogStringHelper::escape( $meta->description );

				// Remove JFBConnect codes.
				$pattern 			= '/\{JFBCLike(.*)\}/i';
				$meta->description	= preg_replace( $pattern , '' , $meta->description );

				$document->addCustomTag( '<meta itemprop="description" content="' . $meta->description . '" />' );
			}
			else
			{
				$maxContentLen	= 350;
				$text			= strip_tags( $this->blog->intro . $this->blog->content );
				$text			= str_ireplace( "&nbsp;" , " " , $text );
				$text			= trim($text);
				$text			= ( JString::strlen( $text ) > $maxContentLen ) ? JString::substr( $text, 0, $maxContentLen) . '...' : $text;

				// Remove JFBConnect codes.
				$pattern 		= '/\{JFBCLike(.*)\}/i';
				$text			= preg_replace( $pattern , '' , $text );

				$text           = EasyBlogStringHelper::escape( $text );
				$document->addCustomTag( '<meta itemprop="description" content="' . $text . '" />' );
			}

			$image	= EasyBlogHelper::getFirstImage( $this->blog->intro . $this->blog->content );

			if( $image !== false )
			{
				$document->addCustomTag( '<meta itemprop="image" content="' . $image . '" />' );
			}

			$placeholder = 'sb-' . rand();
			$html	.= '<div class="social-button ' . $buttonSize . ' google-plusone"><span id="' . $placeholder . '"></span></div>';

			// TODO: Verify $socialFrontEnd, what is it used for.
			// if( ! $socialFrontEnd )
			// $googleHTML	.= '<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>';
			// $googleHTML	.= '<g:plusone size="' . $size . '" href="' . $dataURL . '"></g:plusone>';

			if($this->isBottom)
			{
				$html	= '<div class="socialbutton-vertical align' . $pos . '">' . $html . '</div>';
			}

			$html .= EasyBlogHelper::addScriptDeclarationBookmarklet('$("#' . $placeholder . '").bookmarklet("googlePlusOne", {
				href: "'.$dataURL.'",
				size: "'.$size.'"
			});');
		}

		return $html;
	}

	public function pinit()
	{
		$config 	= EasyBlogHelper::getConfig();
		$document   = JFactory::getDocument();

		$frontpage  = $this->isFrontend;
		$pos        = $this->position;

		$html	= EasyBlogHelper::getHelper( 'Pinterest' )->getHTML( $frontpage , $pos , $this->blog , '' );

		if( $html !== false )
		{
			if($this->isBottom)
			{
				$html	= '<div class="socialbutton-vertical align' . $pos . '">' . $html . '</div>';
			}
		}

		return $html;
	}


	private function _getDataURL()
	{
		$dataURL		= EasyBlogRouter::getRoutedURL('index.php?option=com_easyblog&view=entry&id=' . $this->blog->id, false, true);
		return $dataURL;
	}

	private function _getDataTitle()
	{
		$dataTitle		= urlencode( $this->blog->title );
		return $dataTitle;
	}

}
