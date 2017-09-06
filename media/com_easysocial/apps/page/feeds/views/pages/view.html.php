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

class FeedsViewPages extends SocialAppsView
{
	/**
	 * Renders the list of feeds from a group
	 *
	 * @since	2.0
	 * @access	public
	 * @param	int		The user id that is currently being viewed.
	 */
	public function display($pageId = null, $docType = null)
	{
		$page = ES::page($pageId);

		// Get the app params
		$params = $this->app->getParams();
		$limit = $params->get('total', 5);

		$this->setTitle('APP_FEEDS_APP_TITLE');

		// Render the rss model
		$model = ES::model('RSS');
		$result = $model->getItems($page->id, SOCIAL_TYPE_PAGE);

		// If there are tasks, we need to bind them with the table.
		$feeds = array();

		if ($result) {

			foreach ($result as $row) {

				// Bind the result back to the note object.
				$rss = FD::table('Rss');
				$rss->bind($row);
				
				// Initialize the parser.
				$parser	= $rss->getParser();

				if ($parser) {
					$rss->parser = $parser;
					$rss->total = @$parser->get_item_quantity();
					$rss->items = @$parser->get_items(0, $limit);
				}

				$feeds[] = $rss;
			}
		}

		$this->set('totalDisplayed', $limit);
		$this->set('appId', $this->app->id);
		$this->set('cluster', $page);
		$this->set('feeds', $feeds);

		echo parent::display('themes:/site/feeds/default/default');
	}
}
