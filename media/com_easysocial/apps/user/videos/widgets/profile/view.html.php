<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class VideosWidgetsProfile extends SocialAppsWidgets
{
	/**
	 * Display user videos on the side bar
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function sidebarBottom($user)
	{
		// Get the user params
		$params = $this->getParams();

		if (!$this->config->get('video.enabled')) {
			return;
		}

		// User might not want to show this app in their profile.
		if (!$params->get('showvideos', true)) {
			return;
		}

		echo $this->getVideos($user, $params);
	}


	/**
	 * Display the list of videos a user has uploaded /shared
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getVideos($user, $params)
	{
		// Get photos model
		$model = ES::model('Videos');

		// Get the photo limit from the app setting
		$limit = $params->get('video_widget_listing_total', 20);
		$sort = $params->get('ordering', 'latest');

		// limit <- get from the getPhotos function
		$options = array('userid' => $user->id, 'filter' => SOCIAL_TYPE_USER, 'maxlimit' => $limit, 'sort' => $sort);
		$videos = $model->getVideos($options);

		if (!$videos) {
			return;
		}
		
		$total = '0';

		if ($params->get('showcount')) {
			$total = $model->getTotalUserVideos($user->id);
		}

		$theme = ES::themes();
		$theme->set('params', $params);
		$theme->set('total', $total);
		$theme->set('limit', $limit);
		$theme->set('user', $user);
		$theme->set('videos', $videos);

		return $theme->output('themes:/site/videos/widgets/profile/videos');
	}
}
