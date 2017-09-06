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

FD::import('admin:/includes/apps/apps');

class SocialUserAppPollsa extends SocialAppItem
{
    public function onNotificationLoad(SocialTableNotification &$item)
    {
        // there is nothing to process.
        return false;
    }

    /**
     * Prepares the stream item for polls
     *
     * @since   2.0
     * @access  public
     */
    public function onPrepareStream(SocialStreamItem &$item, $includePrivacy = true)
    {
        if ($item->context != SOCIAL_TYPE_POLLS) {
            return;
        }

        // Get the app params
        $params = $this->getParams();

        if (!$params->get('stream_' . $item->verb, true)) {
            return;
        }

        // Privacy
        $privacy = $this->my->getPrivacy();
        if ($includePrivacy && !$privacy->validate('polls.view', $item->contextId, SOCIAL_TYPE_POLLS, $item->actor->id)) {
            return;
        }

        // Get the permalink for the stream
        $permalink = $item->getPermalink();

        $polls = ES::polls();
        $contents = $polls->getDisplay((int) $item->contextId);

        $item->display = SOCIAL_STREAM_DISPLAY_FULL;

        $table = ES::table('Polls');
        $table->load((int) $item->contextId);

        $this->set('actor', $item->actor);
        $this->set('poll', $table);
        $this->set('contents', $contents);
        $this->set('permalink', $permalink);

        $item->title = parent::display('themes:/site/streams/polls/user/' . $item->verb . '.title');
        $item->preview = parent::display('themes:/site/streams/polls/preview');

        // Determines if current user can edit this poll or not.
        $item->editablepoll = ($this->my->id == $table->created_by || $this->my->isSiteAdmin()) ? true : false;

        if ($includePrivacy) {
            $item->privacy = $privacy->form($item->contextId, $item->context, $item->actor->id, 'polls.view', false, $item->uid, array(), array('iconOnly' => true));
        }

        return true;
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

        if (empty($title) || empty($items)) {
            return;
        }
        // var_dump($items);exit;

        $my = FD::user();

        $poll = FD::get('Polls');
        $polltmpl = $poll->getTemplate();

        $polltmpl->setTitle($title);
        $polltmpl->setCreator($my->id);
        $polltmpl->setContext($uid, $element);
        $polltmpl->setMultiple($multiple);
        if ($expiry) {
            $polltmpl->setExpiry($expiry);
        }

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

            // reset the stream privacy to use polls.view privacy instead of story.view
            // $poll->updateStreamPrivacy($streamItem->uid);
        }

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
	public function onPrepareStoryPanel($story)
	{
        // If the anywhereId exists, means this came from Anywhere module
        // We need to exclude polls form from it.
        if (!is_null($story->anywhereId)) {
            return;
        }

        if (!$this->config->get('polls.enabled')) {
            return;
        }
		$params = $this->getParams();
        if (!$this->my->canCreatePolls()) {
            return;
        }

        // We only allow polls creation on dashboard, which means if the story target and current logged in user is different, then we don't show this
        // Empty target is also allowed because it means no target.
        if (!empty($story->target) && $story->target != FD::user()->id) {
            return;
        }

		// Determine if we should attach ourselves here.
		if (!$params->get('story_polls', true)) {
			return;
		}

		// Create plugin object
		$plugin = $story->createPlugin('polls', 'panel');

		// We need to attach the button to the story panel
		$theme = FD::themes();

		// content. need to get the form from poll lib.
		$poll = FD::get('Polls');
		$form = $poll->getForm(SOCIAL_TYPE_STREAM, 0);
		$theme->set('form', $form);

		// Attachment script
        $script = ES::script();

        $button = $theme->output('site/story/polls/button');
        $form = $theme->output('site/story/polls/form');

        $plugin->setHtml($button, $form);
        $plugin->setScript($script->output('site/story/polls/plugin'));

		return $plugin;
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
        $allowed = array('polls.user.create');

        if (!in_array($likes->type, $allowed)) {
            return;
        }

        // Get the actor of the likes
        $actor = ES::user($likes->created_by);

        // Load the stream item
        $stream = ES::table('Stream');
        $stream->load($likes->stream_id);

        // Load the current group.
        $polls = ES::table('Polls');
        $polls->load($likes->uid);

        // Prepare the email params
        $mailParams = array();
        $mailParams['actor'] = $actor->getName();
        $mailParams['posterAvatar'] = $actor->getAvatar(SOCIAL_AVATAR_SQUARE);
        $mailParams['posterLink'] = $actor->getPermalink(true, true);

        $mailParams['title'] = 'APP_USER_STORY_EMAILS_LIKES_YOUR_POLLS';
        $mailParams['template'] = 'apps/user/polls/like.polls.item';

        // Prepare the system notification params
        $systemParams = array();
        $systemParams['title'] = JText::sprintf('APP_USER_STORY_SYSTEM_LIKES_YOUR_POLLS', $actor->getName());
        $systemParams['url'] = ESR::stream(array('id' => $stream->id, 'layout' => 'item', 'sef' => false));
        $systemParams['actor_id'] = $actor->id;
        $systemParams['uid'] = $stream->id;

        // Only notify if the actor is not the poll's owner
        if ($likes->created_by != $stream->actor_id) {
            ES::notify('likes.item', array($stream->actor_id), $mailParams, $systemParams);
        }       

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
        $allowed = array('polls.user.create');

        if (!in_array($comment->element, $allowed)) {
            return;
        }

        // Get the actor of the likes
        $actor = ES::user($comment->created_by);

        // Load the stream item
        $stream = ES::table('Stream');
        $stream->load($comment->stream_id);

        // Load the current group.
        $polls = ES::table('Polls');
        $polls->load($comment->uid);

        // Prepare the email params
        $mailParams = array();
        $mailParams['comment'] = $comment->comment;
        $mailParams['actor'] = $actor->getName();
        $mailParams['posterAvatar'] = $actor->getAvatar(SOCIAL_AVATAR_SQUARE);
        $mailParams['posterLink'] = $actor->getPermalink(true, true);
        $mailParams['actorAvatar'] = $actor->getAvatar(SOCIAL_AVATAR_SQUARE);
        $mailParams['actorLink'] = $actor->getPermalink(true, true);

        $mailParams['permalink'] = $stream->getPermalink();
        $mailParams['title'] = 'APP_USER_STORY_EMAILS_COMMENT_YOUR_POLLS';
        $mailParams['template'] = 'apps/user/polls/comment.polls.item';

        // Prepare the system notification params
        $systemParams = array();
        $systemParams['title'] = JText::sprintf('APP_USER_STORY_SYSTEM_COMMENT_YOUR_POLLS', $actor->getName());
        $systemParams['url'] = ESR::stream(array('id' => $stream->id, 'layout' => 'item', 'sef' => false));
        $systemParams['actor_id'] = $actor->id;
        $systemParams['uid'] = $stream->id;
        $systemParams['content'] = $comment->comment;

        // Notify the owner of the photo first
        if ($comment->created_by != $polls->created_by) {
            FD::notify('comments.item', array($polls->created_by), $mailParams, $systemParams);
        }

        // Get a list of recipients to be notified for this stream item
        // We exclude the owner of the note and the actor of the like here
        $recipients = $this->getStreamNotificationTargets($comment->uid, 'polls', 'user', 'create', array(), array($polls->created_by, $comment->created_by));

        $mailParams['title'] = 'APP_USER_POLLS_EMAILS_COMMENT_INVOLVED_SUBJECT';
        $mailParams['template'] = 'apps/user/polls/comment.polls.involved';

        $systemParams['title'] = JText::sprintf('APP_USER_STORY_SYSTEM_COMMENT_POLLS_INVOLVED_TITLE', $actor->getName());

        // Notify other participating users
        FD::notify('comments.involved', $recipients, $mailParams, $systemParams);

        return;
    }

}
