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

class FollowersControllerPages extends SocialAppsController
{
	/**
	 * Allows caller to filter followers
	 *
	 * @since	2.0
	 * @access	public
	 * @return
	 */
	public function filterFollowers()
	{
		// Check for request forgeriess
		ES::checkToken();

		// Ensure that the user is logged in.
		ES::requireLogin();

		// Get the page
		$id = $this->input->get('id', 0, 'int');
		$page = ES::page($id);

		$appParam = $this->getApp()->getParams();
		// Get the current filter
		$type = $this->input->get('type', '', 'word');

		// Check whether the viewer can really view the contents of pending
		if ($type == 'pending' && !$page->isAdmin()) {
			return $this->ajax->reject(JText::_('COM_EASYSOCIAL_NOT_ALLOWED_TO_VIEW_SECTION'));
		}

		$options = array();

		// Get the pagination settings
		$themes = ES::themes();
		$limit = (int) $appParam->get('follower.limit');

		// Followers to display per page.
		$options['limit'] = $limit;

		// List only page admins
		if ($type == 'admin') {
			$options['admin'] = true;
		}

		// List only pending users
		if ($type == 'pending') {
			$options['state'] = SOCIAL_PAGES_MEMBER_PENDING;
		}

		if ($type == 'followers') {
			$options['followers'] = true;
		}

		$model = ES::model('Pages');
		$users = $model->getMembers($page->id, $options);
		$pagination	= $model->getPagination();

		$pagination->setVar('view', 'pages');
		$pagination->setVar('layout', 'item');
		$pagination->setVar('id', $page->getAlias());
		$pagination->setVar('appId', $this->getApp()->getAlias() );
		$pagination->setVar('Itemid', ESR::getItemId('pages', 'item', $page->id));

		if ($pagination && $type && $type != 'all') {
			$pagination->setVar('filter', $type);
		}

		// Redirection url when an action is performed on a page follower
		$redirectionOptions = array('layout' => 'item', 'id' => $page->getAlias(), 'appId' => $this->getApp()->getAlias());

		if ($type) {
			$redirectionOptions['filter'] = $type;
		}

		$returnUrl = ESR::pages($redirectionOptions, false);
		$returnUrl = base64_encode($returnUrl);

		// Load the contents
		$theme = ES::themes();
		$theme->set('returnUrl', $returnUrl);
		$theme->set('pagination', $pagination);
		$theme->set('page', $page);
		$theme->set('users', $users);

		$contents = $theme->output('apps/page/followers/pages/list');

		return $this->ajax->resolve($contents);
	}

}
