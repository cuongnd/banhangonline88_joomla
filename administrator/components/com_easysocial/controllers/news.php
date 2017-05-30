<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Import main controller
FD::import( 'admin:/controllers/controller' );

class EasySocialControllerNews extends EasySocialController
{
	/**
	 * Get's the latest news from updater server.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getNews()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view 	= $this->getCurrentView();

		// Get the current model
		$model 	= FD::model( 'News' );

		// Get the manifest data
		$obj	= $model->getNews();

		// Get the news
		$news 	= $obj->news;

		// Get app news
		$appNews 	= $obj->apps;

		return $view->call( __FUNCTION__ , $news , $appNews );
	}
}
