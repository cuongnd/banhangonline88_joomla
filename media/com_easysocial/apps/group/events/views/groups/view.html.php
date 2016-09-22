<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EventsViewGroups extends SocialAppsView
{
    public function display($groupId = null, $docType = null)
    {
        $group = FD::group($groupId);

        // Check if the viewer is allowed here.
        if (!$group->canViewItem()) {
            return $this->redirect($group->getPermalink(false));
        }

        $params = $this->app->getParams();

        $this->set('group', $group);

        $model = FD::model('Events');

        $start = FD::input()->getInt('start', 0);

        // Get the events
        $events = $model->getEvents(array(
            'state' => SOCIAL_STATE_PUBLISHED,
            'ordering' => 'start',
            'limit' => 5,
            'limitstart' => $start,
            'start-after' => FD::date()->toSql(true),
            'group_id' => $group->id,
            'type' => 'all'
        ));

        $pagination = $model->getPagination();

        $pagination->setVar('option', 'com_easysocial');
        $pagination->setVar('view', 'groups');
        $pagination->setVar('layout', 'item');
        $pagination->setVar('id', $group->getAlias());
        $pagination->setVar('appId', $this->app->getAlias());

        $this->set('events', $events);
        $this->set('pagination', $pagination);

        // Parameters to work with site/event/default.list
        $this->set('filter', 'all');
        $this->set('delayed', false);
        $this->set('showSorting', false);
        $this->set('showDistanceSorting', false);
        $this->set('showPastFilter', false);
        $this->set('showDistance', false);
        $this->set('hasLocation', false);
        $this->set('includePast', false);
        $this->set('ordering', 'start');

        $this->set('isGroupOwner', true);

        $guestApp = FD::table('App');
        $guestApp->load(array('type' => SOCIAL_TYPE_APPS, 'group' => SOCIAL_TYPE_EVENT, 'element' => 'guests'));
        $this->set('guestApp', $guestApp);

        echo parent::display('groups/default');
    }
}
