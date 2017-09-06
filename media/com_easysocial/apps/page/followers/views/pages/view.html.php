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

// We need the router
require_once(JPATH_ROOT . '/components/com_content/helpers/route.php');

class FollowersViewPages extends SocialAppsView
{
	public function display($pageId = null, $docType = null)
	{
		$options = array();

		$page = ES::page($pageId);

		// Get the pagination settings
		$themes = ES::themes();
		$appParam = $this->app->getParams();
		$sorting = $appParam->get('follower.sorting');
		$ordering = $appParam->get('follower.ordering');
		$limit = (int) $appParam->get('follower.limit');

		$this->setTitle('APP_FOLLOWERS_APP_TITLE');

		// Followers sorting and ordering
		$options['ordering'] = $sorting;
		$options['direction'] = $ordering;

		// Followers to display per page.
		$options['limit'] = $limit; 

		// Get the current filter.
		$filter = $this->input->get('filter', '', 'word');

		// List only page admins
		if ($filter == 'admin') {
			$options['admin'] = true;
		}

		// List only pending users
		if ($filter == 'pending') {
			$options['state'] = SOCIAL_PAGES_MEMBER_PENDING;
		}

		// If the viewer is not admin, only show them followers
		if (!$page->isAdmin()) {
			$options['followers'] = true;
		}

		$model = ES::model('Pages');
		$users = $model->getMembers($page->id, $options);

		// Set pagination properties
		$pagination	= $model->getPagination();
		$pagination->setVar('view', 'pages');
		$pagination->setVar('layout', 'item');
		$pagination->setVar('id', $page->getAlias());
		$pagination->setVar('appId', $this->app->getAlias());
		$pagination->setVar('Itemid', ESR::getItemId('pages', 'item', $page->id));

		if ($pagination && $filter) {
			$pagination->setVar('filter', $filter);
		}

		// Redirection url when an action is performed on a page follower
		$redirectOptions = array('layout' => "item", 'id' => $page->getAlias(), 'appId' => $this->app->getAlias());

		if ($filter) {
			$redirectOptions['filter'] = $filter;
		}

		$returnUrl = ESR::pages($redirectOptions, false);
		$returnUrl = base64_encode($returnUrl);

		$this->set('returnUrl', $returnUrl);
		$this->set('active', $filter);
		$this->set('page', $page);
		$this->set('users', $users);
		$this->set('pagination', $pagination);

		echo parent::display('pages/default');
	}

}
