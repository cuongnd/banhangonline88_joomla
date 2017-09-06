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

class FilesViewPages extends SocialAppsView
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
		// Only allow registered user to access file explorer system		
		ES::requireLogin();

		$page = ES::page($pageId);

		// Get app params
		$params = $this->app->getParams();

		$this->setTitle('APP_FILES_APP_TITLE');

		// Only allow page members access here.
		if (!$page->isMember()) {
			return $this->redirect($page->getPermalink(false));
		}

		// Load up the explorer library.
		$explorer = ES::explorer($page->id, SOCIAL_TYPE_PAGE);

		// Get total number of files that are already uploaded in the page
		$model = ES::model('Files');
		$total = (int) $model->getTotalFiles($page->id, SOCIAL_TYPE_PAGE);

		$access = $page->getAccess();
		
		$allowUpload = true;

		if (!$page->isAdmin() && !$params->get('allow_members_upload')) {
			$allowUpload = false;
		}

		if ($access->get('files.max') != 0 && $total >= $access->get('files.max')) {
			$allowUpload = false;
		}

		$uploadLimit = $access->get('files.maxsize');
 
		$this->set('uploadLimit', $uploadLimit);
		$this->set('allowUpload', $allowUpload);
		$this->set('explorer', $explorer);
		$this->set('page', $page);

		echo parent::display('pages/default');
	}
}
