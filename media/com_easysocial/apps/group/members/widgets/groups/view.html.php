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
class MembersWidgetsGroups extends SocialAppsWidgets
{
	/**
	 * Display user photos on the side bar
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function afterCategory( $group )
	{
		$app 		= $this->getApp();
		$permalink 	= FRoute::groups( array('layout'=> 'item', 'id' => $group->getAlias(), 'appId' => $app->getAlias() ));

		$theme 		= FD::themes();
		$theme->set('permalink', $permalink);
		$theme->set('group', $group);

		echo $theme->output('themes:/apps/group/members/widgets/header');
	}
}
