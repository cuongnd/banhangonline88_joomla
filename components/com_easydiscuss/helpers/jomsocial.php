<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.filesystem.file' );

require_once DISCUSS_HELPERS . '/parser.php';

class DiscussJomsocialHelper
{
	private $_access = array();

	private function getActivityAccess( $access = '', $type = 'category' )
	{
		if( empty($this->_access) )
		{
			$this->_access['public'] = defined('PRIVACY_PUBLIC') ? PRIVACY_PUBLIC : 10;
			$this->_access['members'] = defined('PRIVACY_MEMBERS') ? PRIVACY_MEMBERS : 20;
			$this->_access['friends'] = defined('PRIVACY_FRIENDS') ? PRIVACY_FRIENDS : 30;
			$this->_access['private'] = defined('PRIVACY_PRIVATE') ? PRIVACY_PRIVATE : 40;
		}

		$result = 0;

		switch ($access)
		{
			case '1': // Private
				$result = $this->_access['members'];
				break;
			case '2': // ACL
				$result = $this->_access['private'];
				break;
			default:
				$result = $this->_access['public'];
				break;
		}

		return $result;
	}

	private function getActivityTitle( $title )
	{
		$config = DiscussHelper::getConfig();

		if( $config->get( 'integration_jomsocial_activity_title_length' ) == 0 )
		{
			return $title;
		}

		return JString::substr( $title , 0 , $config->get( 'integration_jomsocial_activity_title_length' ) ) . '...';
	}

	public function addActivityQuestion( $post )
	{
		$core	= JPATH_ROOT . '/components/com_community/libraries/core.php';
		$config	= DiscussHelper::getConfig();

		if( !JFile::exists( $core ) )
		{
			return false;
		}

		require_once( $core );

		// @rule: Insert points for user.
		if( $config->get( 'integration_jomsocial_points' ) )
		{
			CFactory::load( 'libraries' , 'userpoints' );
			CUserPoints::assignPoint( 'com_easydiscuss.new.discussion' , $post->user_id );
		}

		$lang	= JFactory::getLanguage();
		$lang->load( 'com_easydiscuss' , JPATH_ROOT );

		// We do not want to add activities if new blog activity is disabled.
		if( !$config->get( 'integration_jomsocial_activity_new_question' ) )
		{
			return false;
		}

		$link		= DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&view=post&id=' . $post->id , false , true );

		$title		= $this->getActivityTitle( $post->title );
		$content	= '';

		if( $config->get( 'integration_jomsocial_activity_new_question_content' ) )
		{
			$content	= $post->content;

			$pattern	= '#<img[^>]*>#i';
			preg_match_all( $pattern , $content , $matches );

			$imgTag = '';

			if( $matches && count( $matches[0] ) > 0 )
			{
				foreach( $matches[0] as $match )
				{
					// exclude bbcodes from markitup
					if( stristr($match, '/markitup/') === false )
					{
						$imgTag = $match;
						break;
					}
				}
			}

			$content	= EasyDiscussParser::bbcode( $content );
			$content	= strip_tags( $content );
			$content	= JString::substr( $content , 0 , $config->get( 'integration_jomsocial_activity_content_length') ) . '...';

			if( $imgTag )
			{
				$imgTag		= JString::str_ireplace( 'img ' , 'img style="margin: 0 5px 5px 0;float:left;height:auto;width: 120px !important;"' , $imgTag );
				$content	= $imgTag . $content . '<div style="clear:both;"></div>';
			}

			$content	.= '<div style="text-align: right;"><a href="' . $link . '">' . JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_NEW_QUESTION_REPLY_QUESTION' ) . '</a></div>';
		}

		//get category privacy.
		$category	= DiscussHelper::getTable( 'Category' );
		$category->load( $post->category_id );

		$obj				= new stdClass();
		//$obj->access		= $this->getActivityAccess( $category->private );
		$obj->access		= $this->getActivityAccess();
		$obj->title			= JText::sprintf( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_NEW_QUESTION' , $link , $title );
		$obj->content		= $content;
		$obj->cmd			= 'easydiscuss.question.add';
		$obj->actor			= $post->user_id;
		$obj->target		= 0;
		$obj->like_id		= $post->id;
		$obj->like_type		= 'com_easydiscuss';
		$obj->comment_id	= $post->id;
		$obj->comment_type	= 'com_easydiscuss';
		$obj->app			= 'easydiscuss';
		$obj->cid			= $post->id;

		// add JomSocial activities
		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add($obj);
	}

	public function addActivityReply( $post )
	{
		$core	= JPATH_ROOT . '/components/com_community/libraries/core.php';
		$config	= DiscussHelper::getConfig();

		if( !JFile::exists( $core ) )
		{
			return false;
		}

		require_once( $core );

		// @rule: Insert points for user.
		if( $config->get( 'integration_jomsocial_points' ) )
		{
			CFactory::load( 'libraries' , 'userpoints' );
			CUserPoints::assignPoint( 'com_easydiscuss.reply.discussion' , $post->user_id );
		}

		$lang	= JFactory::getLanguage();
		$lang->load( 'com_easydiscuss' , JPATH_ROOT );

		// We do not want to add activities if new blog activity is disabled.
		if( !$config->get( 'integration_jomsocial_activity_reply_question' ) )
		{
			return false;
		}

		$link		= DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&view=post&id=' . $post->parent_id , false , true );
		$replyLink  = $link . '#' . JText::_('COM_EASYDISCUSS_REPLY_PERMALINK') . '-' . $post->id;

		$parent		= DiscussHelper::getTable( 'Post' );
		$parent->load( $post->parent_id );

		$title		= $this->getActivityTitle( $parent->title );
		$content	= '';

		if( $config->get( 'integration_jomsocial_activity_reply_question_content' ) )
		{
			$content	= $post->content;

			$pattern	= '#<img[^>]*>#i';
			preg_match_all( $pattern , $content , $matches );

			$imgTag = '';

			if( $matches && count( $matches[0] ) > 0 )
			{
				foreach( $matches[0] as $match )
				{
					// exclude bbcodes from markitup
					if( stristr($match, '/markitup/') === false )
					{
						$imgTag = $match;
						break;
					}
				}
			}

			$content	= EasyDiscussParser::bbcode( $content );
			$content	= html_entity_decode(strip_tags($content));
			$content	= JString::substr( $content , 0 , $config->get( 'integration_jomsocial_activity_content_length') ) . '...';

			if( $imgTag )
			{
				$imgTag		= JString::str_ireplace( 'img ' , 'img style="margin: 0 5px 5px 0;float:left;height:auto;width: 120px !important;"' , $imgTag );
				$content	= $imgTag . $content . '<div style="clear:both;"></div>';
			}
			$content	.= '<div style="text-align: right;"><a href="' . $link . '">' . JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_REPLY_QUESTION_PARTICIPATE' ) . '</a></div>';
		}

		//get category privacy.
		$category_id = $post->category_id;
		if( !$post->category_id && $post->parent_id )
		{
			$postTable = DiscussHelper::getTable( 'Posts' );
			$postTable->load( $post->parent_id );
			$category_id = $postTable->category_id;
		}

		$category	= DiscussHelper::getTable( 'Category' );
		$category->load( $category_id );

		$obj				= new stdClass();
		$obj->access		= $this->getActivityAccess();
		$obj->title			= JText::sprintf( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_REPLY_QUESTION' , $replyLink, $link , $title );
		$obj->content		= $content;
		$obj->cmd			= 'easydiscuss.question.reply';
		$obj->actor			= $post->user_id;
		$obj->target		= 0;
		$obj->like_id		= $post->id;
		$obj->like_type		= 'com_easydiscuss';
		$obj->comment_id	= $post->id;
		$obj->comment_type	= 'com_easydiscuss';
		$obj->app			= 'easydiscuss';
		$obj->cid			= $post->id;

		// add JomSocial activities
		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add($obj);
	}

	public function addActivityLikes( $post , $question )
	{
		$core	= JPATH_ROOT . '/components/com_community/libraries/core.php';
		$config	= DiscussHelper::getConfig();
		$my		= JFactory::getUser();

		if( !JFile::exists( $core ) )
		{
			return false;
		}

		require_once( $core );

		// @rule: Insert points for user.
		if( $config->get( 'integration_jomsocial_points' ) )
		{
			CFactory::load( 'libraries' , 'userpoints' );
			CUserPoints::assignPoint( 'com_easydiscuss.like.discussion' , $my->id );
		}

		$lang	= JFactory::getLanguage();
		$lang->load( 'com_easydiscuss' , JPATH_ROOT );

		// We do not want to add activities if new blog activity is disabled.
		if( !$config->get( 'integration_jomsocial_activity_likes' ) )
		{
			return false;
		}

		//get category privacy.
		$category	= DiscussHelper::getTable( 'Category' );
		$category->load( $question->category_id );

		$link				= DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&view=post&id=' . $question->id , false , true );

		$title				= $this->getActivityTitle( $question->title );
		$obj				= new stdClass();
		$obj->access		= $this->getActivityAccess();
		$obj->title			= JText::sprintf( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_LIKE_ITEM' , $link , $title );
		$obj->content		= '';
		$obj->cmd			= 'easydiscuss.question.like';
		$obj->actor			= $my->id;
		$obj->target		= 0;
		$obj->like_id		= $question->id;
		$obj->like_type		= 'com_easydiscuss';
		$obj->comment_id	= $question->id;
		$obj->comment_type	= 'com_easydiscuss';
		$obj->app			= 'easydiscuss';
		$obj->cid			= $question->id;

		// add JomSocial activities
		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add($obj);
	}

	public function addActivityComment( $post , $question )
	{
		$core	= JPATH_ROOT . '/components/com_community/libraries/core.php';
		$config	= DiscussHelper::getConfig();
		$my		= JFactory::getUser();

		if( !JFile::exists( $core ) )
		{
			return false;
		}

		require_once( $core );

		$lang	= JFactory::getLanguage();
		$lang->load( 'com_easydiscuss' , JPATH_ROOT );

		// We do not want to add activities if new comment activity is disabled.
		if( !$config->get( 'integration_jomsocial_activity_comment' ) )
		{
			return false;
		}

		//get category privacy.
		$category	= DiscussHelper::getTable( 'Category' );
		$category->load( $question->category_id );

		$link				= DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&view=post&id=' . $question->id , false , true );
		$replyLink  		= $link . '#' . JText::_('COM_EASYDISCUSS_REPLY_PERMALINK') . '-' . $post->id;

		$title				= $this->getActivityTitle( $question->title );

		$streamTitle        = '';
		if(! empty( $post->parent_id ) )
		{
			//this comment added into discussion.
			$streamTitle    = JText::sprintf( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_COMMENT_ITEM_REPLY' , $replyLink, $link , $title );
		}
		else
		{
			//this comment added into discussion.
			$streamTitle    = JText::sprintf( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_COMMENT_ITEM_QUESTION' , $link , $title );
		}


		$obj				= new stdClass();
		$obj->access		= $this->getActivityAccess();
		$obj->title			= $streamTitle;
		$obj->content		= '';
		$obj->cmd			= 'easydiscuss.question.comment';
		$obj->actor			= $my->id;
		$obj->target		= 0;
		$obj->like_id		= $post->id;
		$obj->like_type		= 'com_easydiscuss';
		$obj->comment_id	= $post->id;
		$obj->comment_type	= 'com_easydiscuss';
		$obj->app			= 'easydiscuss';
		$obj->cid			= $post->id;

		// add JomSocial activities
		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add($obj);
	}

	public function addActivityBadges( $badge )
	{
		$core	= JPATH_ROOT . '/components/com_community/libraries/core.php';
		$config	= DiscussHelper::getConfig();
		$my		= JFactory::getUser();

		if( !JFile::exists( $core ) )
		{
			return false;
		}

		require_once( $core );

		$lang	= JFactory::getLanguage();
		$lang->load( 'com_easydiscuss' , JPATH_ROOT );

		// We do not want to add activities if new badges activity is disabled.
		if( !$config->get( 'integration_jomsocial_activity_badges', 0 ) )
		{
			return false;
		}

		$link				= DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&view=badges&layout=listings&id=' . $badge->id );
		$content			= '<img src="' . $badge->getAvatar() . '" />';

		$title				= $this->getActivityTitle( $badge->title );
		$obj				= new stdClass();
		$obj->title			= JText::sprintf( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_BADGES_ITEM' , $link , $title );
		$obj->content		= $content;
		$obj->cmd			= 'easydiscuss.badges.earned';
		$obj->actor			= $my->id;
		$obj->target		= 0;
		$obj->like_id		= $badge->uniqueId;
		$obj->like_type		= 'com_easydiscuss_badge';
		$obj->comment_id	= $badge->uniqueId;
		$obj->comment_type	= 'com_easydiscuss_badge';
		$obj->app			= 'easydiscuss';
		$obj->cid			= $badge->uniqueId;

		// add JomSocial activities
		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add($obj);
	}

	public function addActivityRanks( $userRanks )
	{
		$core	= JPATH_ROOT . '/components/com_community/libraries/core.php';
		$config	= DiscussHelper::getConfig();
		$my		= JFactory::getUser();

		if( !JFile::exists( $core ) )
		{
			return false;
		}

		require_once( $core );

		$lang	= JFactory::getLanguage();
		$lang->load( 'com_easydiscuss' , JPATH_ROOT );

		// We do not want to add activities if ranking activity is disabled.
		if( !$config->get( 'integration_jomsocial_activity_ranks', 0 ) )
		{
			return false;
		}

		$title				= $this->getActivityTitle( $userRanks->title );
		$obj				= new stdClass();
		$obj->title			= JText::sprintf( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_RANKS_ITEM' , $title );
		$obj->content		= '';
		$obj->cmd			= 'easydiscuss.rank.up';
		$obj->actor			= $my->id;
		$obj->target		= 0;
		$obj->like_id		= $userRanks->uniqueId;
		$obj->like_type		= 'com_easydiscuss_rank';
		$obj->comment_id	= $userRanks->uniqueId;
		$obj->comment_type	= 'com_easydiscuss_rank';
		$obj->app			= 'easydiscuss';
		$obj->cid			= $userRanks->uniqueId;

		// add JomSocial activities
		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add($obj);
	}
}
