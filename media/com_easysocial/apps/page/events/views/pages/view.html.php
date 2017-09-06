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

class EventsViewPages extends SocialAppsView
{
    public function display($pageId = null, $docType = null)
    {
        $page = ES::page($pageId);

        // Check access
        if (!$page->getAccess()->get('events.pageevent', true)) {
            return $this->redirect($page->getPermalink(false));
        }

        // Check if the viewer is allowed here.
        if (!$page->canViewItem()) {
            return $this->redirect($page->getPermalink(false));
        }

        $params = $this->app->getParams();

        $this->setTitle('APP_EVENTS_APP_TITLE');

        // Retrieve event's model
        $model = ES::model('Events');

        $start = $this->input->get('limitstart', 0, 'int');

        // Ordering of the events
        $ordering = $this->input->get('ordering', 'start', 'string');

        // Get featured events
        $featuredOptions = array(
            'state' => SOCIAL_STATE_PUBLISHED,
            'featured' => true,
            'ordering' => 'start',
            'limit' => 4,
            'limitstart' => $start,
            'page_id' => $page->id,
            'type' => 'all',
            'ordering' => $ordering
        );

        $featuredEvents = $model->getEvents($featuredOptions);

        // Default options
        $options = array(
            'state' => SOCIAL_STATE_PUBLISHED,
            'featured' => false,
            'ordering' => 'start',
            'limit' => 4,
            'limitstart' => $start,
            'page_id' => $page->id,
            'type' => 'all',
            'ordering' => $ordering
        );

        // Get the events
        $events = $model->getEvents($options);

        $pagination = $model->getPagination();

        $pagination->setVar('option', 'com_easysocial');
        $pagination->setVar('view', 'pages');
        $pagination->setVar('layout', 'item');
        $pagination->setVar('id', $page->getAlias());
        $pagination->setVar('appId', $this->app->getAlias());

        // Merge featured events into events
        $events = array_merge($events, $featuredEvents);
        
        // Parameters to work with site/event/default/items
        $this->set('featuredEvents', $featuredEvents);
        $this->set('events', $events);
        $this->set('pagination', $pagination);
        $this->set('page', $page);
        $this->set('filter', 'all');
        $this->set('delayed', false);
        $this->set('showDistance', false);
        $this->set('hasLocation', false);
        $this->set('includePast', false);
        $this->set('ordering', $ordering);
        $this->set('activeCategory', false);

        // We need to showsidebar to use the es-card--2 class
        $this->set('showSidebar', true);
        $this->set('isPageOwner', true);
        $this->set('emptyText', 'COM_EASYSOCIAL_EVENTS_PAGE_EMPTY');

        echo parent::display('pages/default');
    }
}
