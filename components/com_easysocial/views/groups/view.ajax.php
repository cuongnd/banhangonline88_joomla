<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Import parent view
FD::import( 'site:/views/views' );

class EasySocialViewGroups extends EasySocialSiteView
{
	/**
	 * Retrieves groups
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array 	An array of groups
	 */
	public function getGroups( $groups = array() , $pagination = null , $featuredGroups = array() )
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		// Determines if we should add the category header
		$categoryId		= JRequest::getInt( 'categoryId' );
		$category 		= false;

		$theme 	= FD::themes();

		if( $categoryId )
		{
			$category 	= FD::table( 'GroupCategory' );
			$category->load( $categoryId );
		}

		// Filter
		$filter 		= JRequest::getVar( 'filter' );

		$theme->set( 'activeCategory' , $category );
		$theme->set( 'filter'			, $filter );
		$theme->set( 'pagination' 		, $pagination );
		$theme->set( 'featuredGroups'	, $featuredGroups );
		$theme->set( 'groups' 			, $groups );

		// Retrieve items from the template
		$content	= $theme->output( 'site/groups/default.items' );

		return $ajax->resolve( $content );
	}

	/**
	 * Responsible to output the application contents.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialAppTable	The application ORM.
	 */
	public function getAppContents( $app )
	{
		$ajax 	= FD::ajax();

		// If there's an error throw it back to the caller.
		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		// Get the current logged in user.
		$groupId 	= JRequest::getInt( 'groupId' );
		$group 		= FD::group( $groupId );

		// Load the library.
		$lib		= FD::getInstance( 'Apps' );
		$contents 	= $lib->renderView( SOCIAL_APPS_VIEW_TYPE_EMBED , 'groups' , $app , array( 'groupId' => $group->id ) );

		// Return the contents
		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the invite friend form
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function inviteFriends()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the group id from request
		$id 	= JRequest::getInt( 'id' );

		// Load up the group
		$group 	= FD::group( $id );
		$my 	= FD::user();

		// Get a list of friends that are already in this group
		$model 		= FD::model('Groups');
		$friends	= $model->getFriendsInGroup( $group->id , array( 'userId' => $my->id ) );
		$exclusion	= array();

		if ($friends) {

			foreach ($friends as $friend) {
				$exclusion[]	= $friend->id;
			}
		}

		$theme 	= FD::themes();
		$theme->set('exclusion', $exclusion);
		$theme->set('group', $group);

		$contents 	= $theme->output( 'site/groups/dialog.invite' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the confirmation dialog to set a group as featured
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setFeatured()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the group id from request
		$id 	= JRequest::getInt( 'id' );

		// Load up the group
		$group 	= FD::group( $id );

		$theme 	= FD::themes();
		$theme->set( 'group' , $group );

		$contents 	= $theme->output( 'site/groups/dialog.featured' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the confirmation dialog to set a group as featured
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeFeatured()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the group id from request
		$id 	= JRequest::getInt( 'id' );

		// Load up the group
		$group 	= FD::group( $id );

		$theme 	= FD::themes();
		$theme->set( 'group' , $group );

		$contents 	= $theme->output( 'site/groups/dialog.unfeature' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the respond to invitation dialog
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function respondInvitation()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the group id from request
		$id 	= JRequest::getInt( 'id' );

		// Load up the group
		$group 	= FD::group( $id );

		// Get the current user.
		$my 	= FD::user();

		// Load the member
		$member = FD::table( 'GroupMember' );
		$member->load( array( 'cluster_id' => $group->id , 'uid' => $my->id ) );

		// Get the invitor
		$invitor	= FD::user( $member->invited_by );

		$theme 		= FD::themes();
		$theme->set( 'group' 	, $group );
		$theme->set( 'invitor'	, $invitor );

		$contents 	= $theme->output( 'site/groups/dialog.respond' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the confirmation to delete a group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmDelete()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the group id from request
		$id 	= JRequest::getInt( 'id' );

		// Load up the group
		$group 	= FD::group( $id );

		$theme 	= FD::themes();
		$theme->set( 'group' , $group );

		$contents 	= $theme->output( 'site/groups/dialog.delete' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the confirmation to delete a group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmUnpublishGroup()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the group id from request
		$id 	= JRequest::getInt( 'id' );

		// Load up the group
		$group 	= FD::group( $id );

		$theme 	= FD::themes();
		$theme->set( 'group' , $group );

		$contents 	= $theme->output( 'site/groups/dialog.unpublish' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the confirmation to withdraw application
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmWithdraw()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the group id from request
		$id 	= JRequest::getInt( 'id' );

		// Load up the group
		$group 	= FD::group( $id );

		$theme 	= FD::themes();
		$theme->set( 'group' , $group );

		$contents 	= $theme->output( 'site/groups/dialog.withdraw' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the confirmation to approve user application
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmApprove()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the group id from request
		$id 	= JRequest::getInt( 'id' );

		// Load up the group
		$group 	= FD::group( $id );

		// Get the user id
		$userId = JRequest::getInt( 'userId' );
		$user 	= FD::user( $userId );

		$theme 	= FD::themes();
		$theme->set( 'group'	, $group );
		$theme->set( 'user'		, $user );

		$contents 	= $theme->output( 'site/groups/dialog.approve' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the confirmation to remove user from group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmRemoveMember()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the group id from request
		$id 	= JRequest::getInt( 'id' );

		// Load up the group
		$group 	= FD::group( $id );

		// Get the user id
		$userId = JRequest::getInt( 'userId' );
		$user 	= FD::user( $userId );

		$theme 	= FD::themes();
		$theme->set( 'group'	, $group );
		$theme->set( 'user'		, $user );

		$contents 	= $theme->output( 'site/groups/dialog.remove.member' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the confirmation to reject user application
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmReject()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the group id from request
		$id 	= JRequest::getInt( 'id' );

		// Load up the group
		$group 	= FD::group( $id );

		// Get the user id
		$userId = JRequest::getInt( 'userId' );
		$user 	= FD::user( $userId );

		$theme 	= FD::themes();
		$theme->set( 'group'	, $group );
		$theme->set( 'user'		, $user );

		$contents 	= $theme->output( 'site/groups/dialog.reject' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the join group exceeded notice
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function exceededJoin()
	{
		$ajax 	= FD::ajax();

		$my 		= FD::user();
		$allowed 	= $my->getAccess()->get( 'groups.join' );

		$theme 	= FD::themes();
		$theme->set( 'allowed'	, $allowed );
		$contents	= $theme->output( 'site/groups/dialog.join.exceeded' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the join group dialog
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function joinGroup()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the group id from request
		$id 	= JRequest::getInt( 'id' );

		// Load up the group
		$group 	= FD::group( $id );

		if( !$id || !$group )
		{
			return $ajax->reject();
		}

		$theme 	= FD::themes();
		$theme->set( 'group' , $group );

		// Check if the group is open or closed
		if( $group->isClosed() )
		{
			$contents 	= $theme->output( 'site/groups/dialog.join.closed' );
		}

		if( $group->isOpen() )
		{
			$contents 	= $theme->output( 'site/groups/dialog.join.open' );
		}

		return $ajax->resolve( $contents );
	}

	/**
	 * Post process after a user is made an admin
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function makeAdmin()
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		return $ajax->resolve();
	}

	/**
	 * Displays the make admin confirmation dialog
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmRevokeAdmin()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the group id from request
		$id 	= JRequest::getInt( 'id' );

		// Load up the group
		$user 	= FD::user($id);

		$theme 	= FD::themes();
		$theme->set( 'user' , $user );

		// Check if the group is open or closed
		$contents 	= $theme->output( 'site/groups/dialog.revoke.admin' );

		return $ajax->resolve( $contents );
	}


	/**
	 * Displays the make admin confirmation dialog
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmMakeAdmin()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the group id from request
		$id 	= JRequest::getInt( 'id' );

		// Load up the group
		$user 	= FD::user( $id );

		$theme 	= FD::themes();
		$theme->set( 'user' , $user );

		// Check if the group is open or closed
		$contents 	= $theme->output( 'site/groups/dialog.admin' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the join group dialog
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmLeaveGroup()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the group id from request
		$id 	= JRequest::getInt( 'id' );

		// Load up the group
		$group 	= FD::group( $id );

		$theme 	= FD::themes();
		$theme->set( 'group' , $group );

		// Check if the group is open or closed
		$contents 	= $theme->output( 'site/groups/dialog.leave' );

		return $ajax->resolve( $contents );
	}

	public function getStream( $stream = null )
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		$contents 	= $stream->html();

		return $ajax->resolve( $contents );
	}


	/**
	 * Displays the stream filter form
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFilter( $filter, $groupId )
	{
		$ajax 		= FD::ajax();
		$group 		= FD::group( $groupId );

		$theme 		= FD::themes();

		$theme->set( 'controller'	, 'groups' );
		$theme->set( 'filter'		, $filter );
		$theme->set( 'uid'			, $group->id );

		$contents	= $theme->output( 'site/stream/form.edit' );

		return $ajax->resolve( $contents );
	}

	/**
	 * post processing for quicky adding group filter.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function addFilter( $filter, $groupId )
	{
		$ajax 	= FD::ajax();

		FD::requireLogin();

		$theme 		= FD::themes();

		$group 		= FD::group( $groupId );


		$theme->set( 'filter'	, $filter );
		$theme->set( 'group'	, $group );
		$theme->set( 'filterId'	, '0' );

		$content	= $theme->output( 'site/groups/item.filter' );

		return $ajax->resolve( $content, JText::_( 'COM_EASYSOCIAL_STREAM_FILTER_SAVED' ) );
	}

	/**
	 * post processing after group filter get deleted.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteFilter( $groupId )
	{
		$ajax 	= FD::ajax();

		FD::requireLogin();
		FD::info()->set( $this->getMessage() );

		$group 	= FD::group( $groupId );
		$url 	= FRoute::groups( array( 'layout' => 'item' , 'id' => $group->getAlias() ), false );

		return $ajax->redirect( $url );
	}

	public function initInfo($steps = null)
	{
		$ajax = FD::ajax();

		if ($this->hasErrors()) {
			return $ajax->reject($this->getMessage());
		}

		return $ajax->resolve($steps);
	}

	public function getInfo($fields = null)
	{
		$ajax = FD::ajax();

		if ($this->hasErrors()) {
			return $ajax->reject($this->getMessage());
		}

		$theme = FD::themes();

		$theme->set('fields', $fields);

		$contents = $theme->output('site/groups/item.info');

		return $ajax->resolve($contents);
	}
}
