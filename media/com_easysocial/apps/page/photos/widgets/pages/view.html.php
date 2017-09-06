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

class PhotosWidgetsPages extends SocialAppsWidgets
{
	public function pageAdminStart($page)
	{
		$category = $page->getCategory();
		$config = ES::config();

        if (!$config->get('photos.enabled', true) || !$category->getAcl()->get('photos.enabled', true) || !$page->getParams()->get('photo.albums', true)) {
            return;
        }

		$this->set('page', $page);
		$this->set('app', $this->app);

		echo parent::display('widgets/widget.menu');
	}

	/**
	 * Display user photos on the side bar
	 *
	 * @since	2.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function sidebarBottom($pageId, $page)
	{
		// Get recent albums
		$output = $this->getAlbums($page);

		echo $output;
	}


	/**
	 * Display the list of photo albums
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function getAlbums(&$page)
	{
		$params = $this->getParams();

		// If the app is disabled, do not continue
		if (!$params->get('widgets_album', true) || !$page->getCategory()->getAcl()->get('photos.enabled', true) || !$page->getParams()->get('photo.albums', true)) {
			return;
		}

		$model = ES::model('Albums');

		// Determines the total number of albums to retrieve
		$limit = $params->get('limit', 10);

		$options = array(
			'order' => 'assigned_date',
			'direction' => 'desc',
			'limit' => $limit
		);

		// Get the list of albums from this page
		$albums = $model->getAlbums($page->id, SOCIAL_TYPE_PAGE, $options);
		$options = array('uid' => $page->id, 'type' => SOCIAL_TYPE_PAGE);

		// Get the total number of albums
		$total = $model->getTotalAlbums($options);

		$this->set('total', $total);
		$this->set('albums', $albums);
		$this->set('page', $page);

		return parent::display('widgets/widget.albums');
	}
}
