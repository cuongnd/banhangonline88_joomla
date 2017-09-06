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

class NewsViewForm extends SocialAppsView
{
	/**
	 * Displays the application output in the canvas.
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function display($uid = null, $docType = null)
	{
		$page = ES::page($uid);

		// Get the editor
		$editor = ES::getEditor();

		// Only allow page admin to create or edit news
		if (!$page->isAdmin()) {
			ES::info()->set(false, JText::_('COM_EASYSOCIAL_PAGES_ONLY_PAGE_ADMIN_ARE_ALLOWED'), SOCIAL_MSG_ERROR);
			return $this->redirect($page->getPermalink(false));
		}

		$this->setTitle('APPS_PAGE_NEWS_TITLE_CREATE_ANNOUNCEMENT');

		$id = $this->input->get('newsId', 0, 'int');
		$news = ES::table('ClusterNews');
		$news->load($id);

		$this->page->title(JText::_('APP_PAGE_NEWS_FORM_UPDATE_PAGE_TITLE'));

		// Determine if this is a new record or not
		if (!$id) {
			$news->comments = true;
			$this->page->title(JText::_('APP_PAGE_NEWS_FORM_CREATE_PAGE_TITLE'));
		}

		// Get app params
		$params = $this->app->getParams();

		$this->set('params', $params);
		$this->set('news', $news);
		$this->set('editor', $editor);
		$this->set('cluster', $page);

		echo parent::display('themes:/site/news/form/default');
	}
}
