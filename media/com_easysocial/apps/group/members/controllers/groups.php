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

class MembersControllerGroups extends SocialAppsController
{
	/**
	 * Filters the output of members
	 *
	 * @since	1.2
	 * @access	public
	 * @return
	 */
	public function filterMembers()
	{
		// Check for request forgeriess
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		// Load up ajax lib
		$ajax 	= FD::ajax();

		// Get the group
		$id 	= JRequest::getInt( 'id' );
		$group	= FD::group( $id );

		// @TODO: Check whether the viewer can really view the contents


		// Get the current filter
		$filter 	= JRequest::getWord( 'filter' );
		$options 	= array();

		if( $filter == 'admin' )
		{
			$options[ 'admin' ]	= true;
		}

		if( $filter == 'pending' )
		{
			$options[ 'state' ]	= SOCIAL_GROUPS_MEMBER_PENDING;
		}

		$model		= FD::model( 'Groups' );
		$users		= $model->getMembers( $group->id , $options );
		$pagination	= $model->getPagination();

		// Load the contents
		$theme 		= FD::themes();
		$theme->set( 'pagination'	, $pagination );
		$theme->set( 'group'			, $group );
		$theme->set( 'users'			, $users );

		$contents	= $theme->output( 'apps/group/members/groups/default.list' );

		return $ajax->resolve( $contents );
	}

}
