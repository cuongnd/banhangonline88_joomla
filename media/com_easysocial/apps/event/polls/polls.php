<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ES::import('admin:/includes/apps/apps');

require_once(__DIR__ . '/helper.php');

class SocialEventAppPolls extends SocialAppItem
{
    public function onNotificationLoad(SocialTableNotification &$item)
    {
        // there is nothing to process.
        return false;
    }

    /**
     * Processes a before saved story.
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function onBeforeStorySave(&$template, &$stream, &$content)
    {
        $params = $this->getParams();

        // Determine if we should attach ourselves here.
        if (!$params->get('story_polls', true)) {
            return;
        }

        $in = FD::input();

        $title = $in->getString('polls_title', '');
        $multiple = $in->getInt('polls_multiple', 0);
        $expiry = $in->getString('polls_expirydate', '');
        $items = $in->get('polls_items', array(), 'array');
        $element = $in->getString('polls_element', 'stream');
        $uid = $in->getInt('polls_uid', 0);
        $sourceid = $in->getInt('polls_sourceid', 0);

        if (empty($title) || empty($items)) {
            return;
        }

        $my = FD::user();

        $poll = FD::get('Polls');
        $polltmpl = $poll->getTemplate();

        $polltmpl->setTitle($title);
        $polltmpl->setCreator($my->id);
        $polltmpl->setContext($uid, $element);
        $polltmpl->setMultiple($multiple);
        $polltmpl->setCluster($sourceid);

        if ($items) {
            foreach($items as $itemOption) {
                $polltmpl->addOption($itemOption);
            }
        }

        // polls creation option
        $saveOptions = array('createStream' => false);

        $pollTbl = $poll->create($polltmpl, $saveOptions);

        $template->context_type = SOCIAL_TYPE_POLLS;
        $template->context_id = $pollTbl->id;

        $params = array(
            'poll' => $pollTbl
        );

        $template->setParams(FD::json()->encode($params));
    }

	/**
	 * Processes a saved story.
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAfterStorySave(&$stream, &$streamItem, &$template)
	{
		$params = $this->getParams();

		// Determine if we should attach ourselves here.
		if (!$params->get('story_polls', true)) {
			return;
		}

        if ( ($streamItem->context_type != SOCIAL_TYPE_POLLS) || (! $streamItem->context_id)) {
            return;
        }

        //load poll item and assign uid.
        $poll = FD::table('Polls');
        $state = $poll->load($streamItem->context_id);

        if ($state) {
            $poll->uid = $streamItem->uid;
            $poll->store();

            // We need to notify the participants of the event.
            $this->notify($stream, $streamItem, $template);

            // reset the stream privacy to use polls.view privacy instead of story.view
            // $poll->updateStreamPrivacy($streamItem->uid);
        }

		return true;
	}


	/**
	 * Responsible to return the favicon object
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFavIcon()
	{
		$obj = new stdClass();
		$obj->color = '#5580BE';
		$obj->icon = 'ies-pie';
		$obj->label = 'APP_USER_GROUPS_STREAM_TOOLTIP';

		return $obj;
	}


    /**
     * Prepares the stream item.
     *
     * @since   1.4
     * @access  public
     */
    public function onPrepareStream(SocialStreamItem &$item, $includePrivacy = true)
    {
        if ($item->context != SOCIAL_TYPE_POLLS) {
            return;
        }

        // Determines if the stream should be generated
        $params     = $this->getParams();

        if ( !$params->get( 'stream_' . $item->verb, true)) {
            return;
        }

        $my = FD::user();
        $privacy = $my->getPrivacy();

        // Get the event
        $event = ES::event($item->cluster_id);

        // privacy validation
        if ($includePrivacy && !$privacy->validate( 'polls.view', $item->contextId , SOCIAL_TYPE_POLLS, $item->actor->id)) {
            return;
        }

        $permalink  = FRoute::stream(array('layout' => 'item', 'id' => $item->uid));
        $pollId = $item->contextId;
        $actor = $item->actor;

        $poll = FD::get('Polls');
        $contents = $poll->getDisplay($pollId);

        $item->display = SOCIAL_STREAM_DISPLAY_FULL;
        $item->label = JText::_('APP_POLLS_STREAM', true);

        $pollTbl = FD::table('Polls');
        $pollTbl->load($pollId);

        $this->set('cluster', $event);
        $this->set('actor', $actor);
        $this->set('poll', $pollTbl);
        $this->set('contents', $contents);
        $this->set('permalink', $permalink);

        $item->title = parent::display('themes:/site/streams/polls/cluster/title.' . $item->verb);
        $item->preview  = parent::display('themes:/site/streams/polls/preview');

        // we need to determine if current user can edit this poll or not.
        $item->editablepoll = ($my->id == $pollTbl->created_by || $my->isSiteAdmin()) ? true : false;

        if ($includePrivacy) {
            $item->privacy  = $privacy->form( $item->contextId, $item->context, $item->actor->id, 'polls.view', false, $item->uid );
        }

        return true;
    }


	/**
	 * Prepares what should appear in the story form.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function onPrepareStoryPanel($story)
	{
        if (!$this->config->get('polls.enabled')) {
            return;
        }

        if (!$this->my->canCreatePolls()) {
            return;
        }
        
		$params = $this->getParams();

        // Load the event
        $event = ES::event($story->cluster);

        // We only allow polls creation on dashboard, which means if the story target and current logged in user is different, then we don't show this
        // Empty target is also allowed because it means no target.
        if (!empty($story->target) && $story->target != FD::user()->id) {
            return;
        }

		// Determine if we should attach ourselves here.
		if (!$params->get('story_polls', true) || !$this->getApp()->hasAccess($event->category_id)) {
			return;
		}

		// Create plugin object
		$plugin = $story->createPlugin('polls' , 'panel');

		// We need to attach the button to the story panel
		$theme = FD::themes();

		// content. need to get the form from poll lib.
		$poll = FD::get('Polls');
		$form = $poll->getForm(SOCIAL_TYPE_STREAM, 0, '', $event->id);

        $theme->set('event', $event);
		$theme->set('form', $form);

        // Attachment script
        $script = ES::script();

        $button = $theme->output('site/story/polls/button');
        $form = $theme->output('site/story/polls/form');

        $plugin->setHtml($button, $form);
        $plugin->setScript($script->output('site/story/polls/plugin'));

		return $plugin;
	}

    public function notify($stream, $streamItem, $template)
    {
        // When a user posts a new polls in a event, we need to notify the event members
        $event = ES::event($template->cluster_id);

        // Get the actor
        $actor = ES::user($streamItem->actor_id);

        // Get a list of event members
        $model = ES::model('Events');
        $targets = $model->getMembers($event->id, array('exclude' => $actor->id, 'state' => SOCIAL_STATE_PUBLISHED, 'users' => true));

        // If there's nothing to send skip this altogether.
        if (!$targets) {
            return;
        }

        // Get the item's permalink
        $permalink = ESR::stream(array('id' => $streamItem->uid, 'layout' => 'item', 'external' => true), true);

        // Prepare the email params
        $mailParams = array();
        $mailParams['actor'] = $actor->getName();
        $mailParams['posterAvatar'] = $actor->getAvatar(SOCIAL_AVATAR_SQUARE);
        $mailParams['posterLink'] = $actor->getPermalink(true, true);

        $contents = $template->content;

        $mailParams['message'] = $contents;
        $mailParams['event'] = $event->getName();
        $mailParams['eventLink'] = $event->getPermalink(true, true);
        $mailParams['permalink'] = ESR::stream(array('id' => $streamItem->uid, 'layout' => 'item', 'external' => true), true);
        $mailParams['title'] = 'APP_EVENT_STORY_EMAILS_NEW_POLLS_IN_EVENT';
        $mailParams['template'] = 'apps/event/polls/new.polls';

        // Prepare the system notification params
        $systemParams = array();
        $systemParams['context_type'] = 'events';
        $systemParams['title'] = JText::sprintf('APP_EVENT_STORY_POLLS_CREATED_IN_EVENT', $actor->getName(), $event->getName());
        $systemParams['url'] = ESR::stream(array('id' => $streamItem->uid, 'layout' => 'item', 'sef' => false));
        $systemParams['actor_id'] = $actor->id;
        $systemParams['uid'] = $streamItem->uid;
        $systemParams['context_ids'] = $event->id;
        $systemParams['content'] = $template->content;

        // Try to send the notification
        $state = ES::notify('events.polls.create', $targets, $mailParams, $systemParams);
    }

    /**
     * Triggers after a like is saved
     *
     * @since   1.0
     * @access  public
     * @param   object  $params     A standard object with key / value binding.
     *
     * @return  none
     */
    public function onAfterLikeSave(&$likes)
    {
        $allowed = array('polls.event.create');

        if (!in_array($likes->type, $allowed)) {
            return;
        }

        // Get the actor of the likes
        $actor = ES::user($likes->created_by);

        // Load the stream item
        $stream = ES::table('Stream');
        $stream->load($likes->stream_id);

        $polls = ES::table('Polls');
        $polls->load($likes->uid);

        // Load the current event.
        $event = ES::event($polls->cluster_id);

        // Prepare the email params
        $mailParams = array();
        $mailParams['actor'] = $actor->getName();
        $mailParams['posterAvatar'] = $actor->getAvatar(SOCIAL_AVATAR_SQUARE);
        $mailParams['posterLink'] = $actor->getPermalink(true, true);

        $mailParams['event'] = $event->getName();
        $mailParams['eventLink'] = $event->getPermalink(true, true);
        $mailParams['permalink'] = $stream->getPermalink();
        $mailParams['title'] = 'APP_EVENT_STORY_EMAILS_LIKES_YOUR_POLLS_INSIDE_EVENT';
        $mailParams['template'] = 'apps/event/polls/like.polls.item';

        // Prepare the system notification params
        $systemParams = array();
        $systemParams['context_type'] = 'events';
        $systemParams['title'] = JText::sprintf('APP_EVENT_STORY_SYSTEM_LIKES_YOUR_POLLS_INSIDE_EVENT', $actor->getName(), $event->getName());
        $systemParams['url'] = ESR::stream(array('id' => $stream->id, 'layout' => 'item', 'sef' => false));
        $systemParams['actor_id'] = $actor->id;
        $systemParams['uid'] = $stream->id;
        $systemParmas['context_ids'] = $event->id;

        // Notify other participating users
        ES::notify('likes.item', array($stream->actor_id), $mailParams, $systemParams);

        return;
    }

    /**
     * Triggered when a comment save occurs
     *
     * @since   1.4
     * @access  public
     * @param   SocialTableComments The comment object
     * @return
     */
    public function onAfterCommentSave(&$comment)
    {
        $allowed = array('polls.event.create');

        if (!in_array($comment->element, $allowed)) {
            return;
        }

        // Get the actor of the likes
        $actor = ES::user($comment->created_by);

        // Load the stream item
        $stream = ES::table('Stream');
        $stream->load($comment->stream_id);

        // Load the current event.
        $polls = ES::table('Polls');
        $polls->load($comment->uid);

        $event = ES::event($polls->cluster_id);

        // Prepare the email params
        $mailParams = array();
        $mailParams['comment'] = $comment->comment;
        $mailParams['actor'] = $actor->getName();
        $mailParams['posterAvatar'] = $actor->getAvatar(SOCIAL_AVATAR_SQUARE);
        $mailParams['posterLink'] = $actor->getPermalink(true, true);
        $mailParams['actorAvatar'] = $actor->getAvatar(SOCIAL_AVATAR_SQUARE);
        $mailParams['actorLink'] = $actor->getPermalink(true, true);

        $mailParams['event'] = $event->getName();
        $mailParams['eventLink'] = $event->getPermalink(true, true);
        $mailParams['permalink'] = $stream->getPermalink();
        $mailParams['title'] = 'APP_EVENT_STORY_EMAILS_COMMENT_YOUR_POLLS_INSIDE_EVENT';
        $mailParams['template'] = 'apps/event/polls/comment.polls.item';

        // Prepare the system notification params
        $systemParams = array();
        $systemParams['context_type'] = 'events';
        $systemParams['title'] = JText::sprintf('APP_EVENT_STORY_SYSTEM_COMMENT_YOUR_POLLS_INSIDE_EVENT', $actor->getName(), $event->getName());
        $systemParams['url'] = ESR::stream(array('id' => $stream->id, 'layout' => 'item', 'sef' => false));
        $systemParams['actor_id'] = $actor->id;
        $systemParams['uid'] = $stream->id;
        $systemParams['content'] = $comment->comment;
        $systemParmas['context_ids'] = $event->id;

        // Notify the owner of the photo first
        if ($comment->created_by != $polls->created_by) {
            FD::notify('comments.item', array($polls->created_by), $mailParams, $systemParams);
        }

        // Get a list of recipients to be notified for this stream item
        // We exclude the owner of the note and the actor of the like here
        $recipients = $this->getStreamNotificationTargets($comment->uid, 'polls', 'event', 'create', array(), array($polls->created_by, $comment->created_by));

        $mailParams['title'] = 'APP_EVENT_POLLS_EMAILS_COMMENT_INVOLVED_SUBJECT';
        $mailParams['template'] = 'apps/event/polls/comment.polls.involved';

        $systemParams['title'] = JText::sprintf('APP_EVENT_STORY_SYSTEM_COMMENT_POLLS_INVOLVED_TITLE', $actor->getName(), $event->getName());

        // Notify other participating users
        FD::notify('comments.involved', $recipients, $mailParams, $systemParams);

        return;
    }
}
