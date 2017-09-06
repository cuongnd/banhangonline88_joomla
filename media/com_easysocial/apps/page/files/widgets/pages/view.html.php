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

/**
 * Widgets for page
 *
 * @since	1.0
 * @access	public
 */
class FilesWidgetsPages extends SocialAppsWidgets
{
	/**
	 * Display user photos on the side bar
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function sidebarBottom($pageId)
	{
		// Get the params of the page
		$params = $this->app->getParams();

		// If the widget has been disabled we shouldn't display anything
		if (!$params->get('widget')) {
			return;
		}

		$page = ES::page($pageId);
		
		$theme = ES::themes();
		$limit = $params->get('widget_total', 5);

		$model = ES::model('Files');
		$options = array('limit' => $limit);
		$files = $model->getFiles($page->id, SOCIAL_TYPE_PAGE, $options);

		if (!$files) {
			return;
		}
		
		$total = $model->getTotalFiles($page->id, SOCIAL_TYPE_PAGE);

		$theme->set('total', $total);
		$theme->set('files', $files);

		echo $theme->output('themes:/apps/page/files/widgets/files');
	}
}
