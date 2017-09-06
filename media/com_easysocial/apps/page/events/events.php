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

class SocialPageAppEvents extends SocialAppItem
{
    public function onNotificationLoad(SocialTableNotification &$item)
    {
        // there is nothing to process.
        return false;
    }

    public function onPrepareStoryPanel($story)
    {
        if ($story->clusterType != SOCIAL_TYPE_PAGE) {
            return;
        }

        $params = $this->getParams();

        // Determine if we should attach ourselves here.
        if (!$params->get('story_event', true)) {
            return;
        }

        // If events is disabled, we shouldn't display this
        if (!$this->config->get('events.enabled')) {
            return;
        }

        // Ensure that the page category has access to create events
        $page = ES::page($story->cluster);
        $access = $page->getAccess();

        // We don't allow non-admin to create event
        if (!$access->get('events.pageevent') || !$page->isAdmin() || !$this->getApp()->hasAccess($page->category_id)) {
            return;
        }

        // Create plugin object
        $plugin = $story->createPlugin('event', 'panel');

        // Get the theme class
        $theme = ES::themes();

        // Get the available event category
        $categories = ES::model('EventCategories')->getCreatableCategories(ES::user()->getProfile()->id);

        $theme->set('categories', $categories);

        $plugin->button->html = $theme->output('site/story/events/button');
        $plugin->content->html = $theme->output('site/story/events/form');

        $script = ES::get('Script');
        $plugin->script = $script->output('site/story/events/plugin');

        return $plugin;
    }

    public function onBeforeStorySave(&$template, &$stream, &$content)
    {
        $params = $this->getParams();

        // Determine if we should attach ourselves here.
        if (!$params->get('story_event', true)) {
            return;
        }

        $input = ES::input();

        $title = $input->getString('event_title');
        $description = $input->getString('event_description');
        $categoryid = $input->getInt('event_category');
        $start = $input->getString('event_start');
        $end = $input->getString('event_end');
        $timezone = $input->getString('event_timezone');

        // If no category id, then we don't proceed
        if (empty($categoryid)) {
            return;
        }

        // Perhaps in the future we use ES::model('Event')->createEvent() instead.
        // For now just hardcode it here to prevent field triggering and figuring out how to punch data into the respective field data because the form is not rendered through field trigger.

        $my = ES::user();
        $event = ES::event();
        $event->title = $title;
        $event->description = $description;

        // Set a default params for this event first
        $event->params = '{"photo":{"albums":true},"news":true,"discussions":true,"allownotgoingguest":false,"allowmaybe":true,"guestlimit":0}';

        // event type will always follow page type
        $event->type = ES::page($template->cluster_id)->type;
        $event->creator_uid = $my->id;
        $event->creator_type = SOCIAL_TYPE_USER;
        $event->category_id = $categoryid;
        $event->cluster_type = SOCIAL_TYPE_EVENT;
        $event->alias = ES::model('Events')->getUniqueAlias($title);
        $event->created = ES::date()->toSql();
        $event->key = md5($event->created . $my->password . uniqid());

        $event->state = SOCIAL_CLUSTER_PUBLISHED;

        // Since only page admins are allowed to create event, we dont really need this
        // if ($my->isSiteAdmin() || !$my->getAccess()->get('events.moderate')) {
        //     $event->state = SOCIAL_CLUSTER_PUBLISHED;
        // }

        // Trigger apps
        ES::apps()->load(SOCIAL_TYPE_USER);

        $dispatcher = ES::dispatcher();
        $triggerArgs = array(&$event, &$my, true);

        // @trigger: onEventBeforeSave
        $dispatcher->trigger(SOCIAL_TYPE_USER, 'onEventBeforeSave', $triggerArgs);

        $state = $event->save();

        // Set the meta for start end timezone
        $meta = $event->meta;
        $meta->cluster_id = $event->id;
        $meta->start = ES::date($start)->toSql();
        $meta->end = ES::date($end)->toSql();
        $meta->timezone = $timezone;

        // Set the page id
        $meta->page_id = $template->cluster_id;

        $meta->store();

        // Recreate the event object
        SocialEvent::$instances[$event->id] = null;
        $event = ES::event($event->id);

        // Create a new owner object
        $event->createOwner($my->id);

        // @trigger: onEventAfterSave
        $triggerArgs = array(&$event, &$my, true);
        $dispatcher->trigger(SOCIAL_TYPE_USER, 'onEventAfterSave' , $triggerArgs);

        // Due to inconsistency, we don't use SOCIAL_TYPE_EVENT.
        // Instead we use "events" because app elements are named with 's', namely users, groups, events.
        $template->context_type = 'events';

        $template->context_id = $event->id;
        $template->cluster_access = $event->type;
        $template->cluster_type = $event->cluster_type;
        $template->cluster_id = $event->id;

        $params = array(
            'event' => $event
        );

        $template->setParams(ES::json()->encode($params));
    }

    public function onBeforeGetStream(&$options, $view = '')
    {
        if ($view != 'pages') {
            return;
        }

        $layout = JRequest::getVar('layout', '');

        if ($layout == 'category') {
            // if this is viewing page category page, we ignore the events stream for pages.
            return;
        }

        // Check if there are any page events
        $pageEvents = ES::model('Events')->getEvents(array(
            'page_id' => $options['clusterId'],
            'state' => SOCIAL_STATE_PUBLISHED,
            'idonly' => true
        ));

        if (count($pageEvents) == 0) {
            return;
        }

        // Support in getting event stream as well
        if (!is_array($options['clusterType'])) {
            $options['clusterType'] = array($options['clusterType']);
        }

        if (!in_array(SOCIAL_TYPE_EVENT, $options['clusterType'])) {
            $options['clusterType'][] = SOCIAL_TYPE_EVENT;
        }

        if (!is_array($options['clusterId'])) {
            $options['clusterId'] = array($options['clusterId']);
        }

        $options['clusterId'] = array_merge($options['clusterId'], $pageEvents);
    }

    /**
     * Determines if this app should be visible in the page page
     *
     * @since   2.0
     * @access  public
     * @param   string
     * @return
     */
    public function appListing($view, $pageId, $type)
    {
        $page = ES::page($pageId);

        if (!$this->config->get('events.enabled')) {
            return false;
        }

        return $page->getAccess()->get('events.pageevent', true);
    }
}
