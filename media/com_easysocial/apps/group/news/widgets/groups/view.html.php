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

/**
 * Widgets for group
 *
 * @since	1.0
 * @access	public
 */
class NewsWidgetsGroups extends SocialAppsWidgets
{
	/**
	 * Display admin actions for the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function groupAdminStart( $group )
	{
		if (!$group->getParams()->get('news', true)) {
		    return;
		}

		$theme 		= FD::themes();
		$theme->set( 'app'	, $this->app );
		$theme->set( 'group' , $group );

		echo $theme->output( 'themes:/apps/group/news/widgets/widget.menu' );
	}

	/**
	 * Display user photos on the side bar
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function sidebarBottom( $groupId )
	{
		// Set the max length of the item
		$params 	= $this->app->getParams();
		$enabled 	= $params->get('widget', true);

		if (!$enabled) {
			return;
		}

		$theme 		= FD::themes();

		// Get the group
		$group		= FD::group( $groupId );


		$options 	= array( 'limit' => (int) $params->get( 'widget_total' , 5 ) );

		$model 		= FD::model( 'Groups' );
		$items 		= $model->getNews( $group->id , $options );

		$theme->set( 'group'	, $group );
		$theme->set( 'app'		, $this->app );
		$theme->set( 'items' 	, $items );

		echo $theme->output( 'themes:/apps/group/news/widgets/widget.news' );
	}
}
