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

class PollsViewPages extends SocialAppsView
{
	/**
	 * Displays the application output in the canvas.
	 *
	 * @since	2.0
	 * @access	public
	 * @param	int		The user id that is currently being viewed.
	 */
	public function display($pageId = null, $docType = null)
	{
		$page = ES::page($pageId);

		// Check if the viewer is allowed here.
		if (!$page->canViewItem()) {
			return $this->redirect($page->getPermalink(false));
		}

		$this->setTitle('APP_POLLS_APP_TITLE');
		
		// Get app params
		$params = $this->app->getParams();
		
		$options = array('cluster_id' => $pageId);

		$model = ES::model('Polls');
		$polls = $model->getPolls($options);

		$pollLib = ES::get('Polls');

		foreach ($polls as $poll) {	
			// Load the author
			$author = ES::user($poll->created_by);
			$poll->author = $author;

			$poll->content = $pollLib->getDisplay($poll->id);
		}
	
		$this->set('polls', $polls);
		$this->set('params', $params);
		$this->set('page', $page);

		echo parent::display('views/default');
	}

}
