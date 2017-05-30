<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

FD::import( 'admin:/includes/apps/apps' );

require_once( dirname( __FILE__ ) . '/helper.php' );

class SocialGroupAppLinks extends SocialAppItem
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Responsible to return the favicon object
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFavIcon()
	{
		$obj 			= new stdClass();
		$obj->color		= '#5580BE';
		$obj->icon 		= 'ies-link';
		$obj->label 	= 'APP_USER_GROUPS_STREAM_TOOLTIP';

		return $obj;
	}

	/**
	 * Fixed legacy issues where the app is displayed on apps list of a group.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function appListing( $view , $id , $type )
	{
		return false;
	}

	/**
	 * Processes a saved story.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAfterStorySave( &$stream , &$streamItem , &$template )
	{
		// Get the link information from the request
		$link 		= JRequest::getVar( 'links_url' , '' );
		$title 		= JRequest::getVar( 'links_title' , '' );
		$content 	= JRequest::getVar( 'links_description' , '' );
		$image 		= JRequest::getVar( 'links_image' , '' );
		$video 		= JRequest::getVar( 'links_video' , '' );

		// If there's no data, we don't need to store in the assets table.
		if( empty( $title ) && empty( $content ) && empty( $image ) )
		{
			return;
		}

		$registry		= FD::registry();
		$registry->set( 'title'		, $title );
		$registry->set( 'content'	, $content );
		$registry->set( 'image'		, $image );
		$registry->set( 'link'		, $link );

		return true;
	}

	/**
	 * Triggered to validate the stream item whether should put the item as valid count or not.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	jos_social_stream, boolean
	 * @return  0 or 1
	 */
	public function onStreamCountValidation( &$item, $includePrivacy = true )
	{
		// If this is not it's context, we don't want to do anything here.
		if( $item->context_type != 'links' )
		{
			return false;
		}

		// if this is a cluster stream, let check if user can view this stream or not.
		$params = FD::registry( $item->params );
		$group = FD::group( $params->get( 'group' ) );

		if (!$group) {
			return;
		}

		$item->cnt = 1;

		$my = FD::user();

		if ($group->type != SOCIAL_GROUPS_PUBLIC_TYPE && !$group->isMember($my->id)) {
			$item->cnt = 0;
		}

		return true;
	}


	/**
	 * Generates the stream title of group.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	object	$params		A standard object with key / value binding.
	 *
	 * @return	none
	 */
	public function onPrepareStream( SocialStreamItem &$stream, $includePrivacy = true )
	{
		if ($stream->context != 'links') {
			return;
		}

		// Group access
		$group = FD::group($stream->cluster_id);

		if (!$group) {
			return;
		}

		if (!$group->canViewItem()) {
			return;
		}

		//get links object, in this case, is the stream_item
		$uid = $stream->uid;

		$stream->color = '#5580BE';
		$stream->fonticon = 'ies-link';
		$stream->label = JText::_('APP_GROUP_LINKS_STREAM_TOOLTIP');

		// Apply likes on the stream
		$likes = FD::likes();
		$likes->get( $stream->uid , $stream->context, $stream->verb, SOCIAL_APPS_GROUP_GROUP, $stream->uid );
		$stream->likes	= $likes;

		// Apply comments on the stream
		$comments = FD::comments( $stream->uid , $stream->context , $stream->verb, SOCIAL_APPS_GROUP_GROUP , array( 'url' => FRoute::stream( array( 'layout' => 'item', 'id' => $stream->uid ) ) ), $stream->uid );
		$stream->comments = $comments;

		// Apply repost on the stream
		$repost = FD::get( 'Repost', $stream->uid , SOCIAL_TYPE_STREAM, SOCIAL_APPS_GROUP_GROUP );
		$stream->repost	= $repost;

		$my = FD::user();
		$privacy = FD::privacy($my->id);

		if ($includePrivacy && !$privacy->validate( 'story.view', $uid, SOCIAL_TYPE_LINKS, $stream->actor->id)) {
			return;
		}

		$actor = $stream->actor;
		$target = count( $stream->targets ) > 0 ? $stream->targets[0] : '';

		$stream->display = SOCIAL_STREAM_DISPLAY_FULL;

		$assets = $stream->getAssets();

		if (empty($assets)) {
			return;
		}

		$assets = $assets[ 0 ];
		$videoHtml = '';

		// Retrieve the link that is stored.
		$hash = md5($assets->get('link'));

		$link = FD::table('Link');
		$link->load(array( 'hash' => $hash ) );

		$linkObj = FD::json()->decode( $link->data );

		// Determine if there's any embedded object
		$oembed = isset( $linkObj->oembed ) ? $linkObj->oembed : '';

		// Get app params
		$params = $this->getParams();

		$this->set('group', $group);
		$this->set('params', $params);
		$this->set('oembed', $oembed);
		$this->set('assets', $assets);
		$this->set('actor', $actor);
		$this->set('target', $target);
		$this->set('stream', $stream);

		$stream->title 		= parent::display( 'streams/title.' . $stream->verb );
		$stream->preview	= parent::display( 'streams/preview.' . $stream->verb );

		return true;
	}


	/**
	 * Responsible to generate the activity logs.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	object	$params		A standard object with key / value binding.
	 *
	 * @return	none
	 */
	public function onPrepareActivityLog( SocialStreamItem &$item, $includePrivacy = true )
	{
		if( $item->context != 'links' )
		{
			return;
		}

		//get story object, in this case, is the stream_item
		$tbl = FD::table( 'StreamItem' );
		$tbl->load( $item->uid ); // item->uid is now streamitem.id

		$uid = $tbl->uid;

		//get story object, in this case, is the stream_item
		$my         = FD::user();
		$privacy	= FD::privacy( $my->id );

		$actor 				= $item->actor;
		$target 			= count( $item->targets ) > 0 ? $item->targets[0] : '';

		$assets 			= $item->getAssets( $uid );
		if( empty( $assets ) )
		{
			return;
		}

		$assets 	= $assets[ 0 ];

		$this->set( 'assets', $assets );
		$this->set( 'actor' , $actor );
		$this->set( 'target', $target );
		$this->set( 'stream', $item );


		$item->display 	= SOCIAL_STREAM_DISPLAY_MINI;
		$item->title	= parent::display( 'logs/' . $item->verb );

		return true;

	}

	/**
	 * Prepares what should appear in the story form.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onPrepareStoryPanel( $story )
	{
		// Create plugin object
		$plugin		= $story->createPlugin( 'links' , 'panel');

		// We need to attach the button to the story panel
		$theme 		= FD::themes();

		$plugin->button->html 	= $theme->output('themes:/apps/group/links/story/panel.button');
		$plugin->content->html 	= $theme->output( 'themes:/apps/group/links/story/panel.content' );

		// Attachment script
		$script				= FD::get('Script');
		$plugin->script		= $script->output('apps:/group/links/story');

		return $plugin;
	}
}
