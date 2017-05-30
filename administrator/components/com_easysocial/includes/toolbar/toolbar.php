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

jimport( 'joomla.filesystem.file' );

/**
 * Toolbar class
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialToolbar
{
	public function factory()
	{
		$toolbar 	= new self();

		return $toolbar;
	}

	/**
	 * Retrieves the redirection url for sign in / sign out.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRedirectionUrl( $menuId )
	{
		$menu 		= JFactory::getApplication()->getMenu();
		$menuItem	= $menu->getItem( $menuId );

		// Set the default return URL.
		$return 	= FRoute::dashboard( array() , false );

		if( $menuItem )
		{
			if( $menuItem->component != 'com_easysocial' )
			{
				if( strpos( $menuItem->link, '?' ) > 0 )
				{
					$return	= JRoute::_( $menuItem->link . '&Itemid=' . $menuItem->id , false );
				}
				else
				{
					$return	= JRoute::_( $menuItem->link . '?Itemid=' . $menuItem->id , false );
				}

				// If the logout return is null, it means the menu item is on the home page.
				if( !$return || ( isset( $menuItem->home ) && $menuItem->home ) )
				{
					$return	= JURI::root();
				}
			}

			if( $menuItem->component == 'com_easysocial' )
			{
				$view 	= isset( $menuItem->query[ 'view' ] ) ? $menuItem->query[ 'view' ] : '';

				if( $view )
				{
					$queries 	= $menuItem->query;

					unset( $queries[ 'option' ] );
					unset( $queries[ 'view' ] );

					$arguments 		= array( $queries , false );
					$return 		= call_user_func_array( array( 'FRoute' , $view ) , $arguments );
				}
			}
		}

		return $return;
	}

	/**
	 * Renders the HTML block for the notification bar.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function render( $options = array() )
	{
		// The current logged in user.
		$my 	= FD::user();

		$theme 	= FD::themes();

		if( $my->id )
		{
			// Get a list of new conversations
			$newConversations 	= $my->getTotalNewConversations();

			// Get total pending request count
			$newRequests		= $my->getTotalFriendRequests();

			// Get new system notifications
			$model 				= FD::model('Notifications');
			$newNotifications 	= $model->getCount( array( 'unread' => true , 'target' => array( 'id' => $my->id , 'type' => SOCIAL_TYPE_USER ) ) );

			$theme->set( 'newConversations'	, $newConversations );
			$theme->set( 'newRequests'		, $newRequests );
			$theme->set( 'newNotifications'	, $newNotifications );
		}

		// Only render facebook codes if user is not logged in
		$facebook = false;

		if (!$my->id) {
			// Facebook codes.
			$facebook 	= FD::oauth('Facebook');
		}

		$config 		= FD::config();

		// Get login redirection url
		$loginMenu 		= $config->get( 'general.site.login' );

		if( $loginMenu == 'null' )
		{
			$loginReturn 	= base64_encode( JRequest::getURI() );
		}
		else
		{
			$loginReturn 	= $this->getRedirectionUrl( $loginMenu );
			$loginReturn 	= base64_encode( $loginReturn );
		}

		// Get logout redirection url
		$logoutMenu 	= $config->get( 'general.site.logout' );
		$logoutReturn 	= $this->getRedirectionUrl( $logoutMenu );
		$logoutReturn	= base64_encode( $logoutReturn );


		// Determines if there's any force display options passed in arguments
		$forceOption 	= isset( $options[ 'forceoption' ] ) ? $options[ 'forceoption' ] : false;

		// default this two is enabled.
		$friends 		= isset( $options[ 'friends' ] ) ? $options[ 'friends' ] : true;
		$notifications	= isset( $options[ 'notifications' ] ) ? $options[ 'notifications' ] : true;

		// from arguments.
		$toolbar 		= isset( $options[ 'toolbar' ] ) ? $options[ 'toolbar'] : false;
		$dashboard 		= isset( $options[ 'dashboard' ] ) ? $options[ 'dashboard' ] : false;
		$conversations 	= isset( $options[ 'conversations' ] ) ? $options[ 'conversations' ] : false;
		$search 		= isset( $options[ 'search' ] ) ? $options[ 'search' ] : false;
		$login 			= isset( $options[ 'login' ] ) ? $options[ 'login' ] : false;
		$profile 		= isset( $options[ 'profile' ] ) ? $options[ 'profile' ] : false;

		// Get template settings
		$template 		= FD::themes()->getConfig();

		// Determines if the current viewer is a guest user.
		$isGuest 		= $my->id == 0 ? true : false;

		if( $isGuest && !$template->get( 'toolbar_guests' ) )
		{
			$toolbar 	= false;
		}

		if( !$forceOption )
		{
			$dashboard 		= $template->get( 'toolbar_dashboard' ) || $dashboard;
			$conversations 	= $config->get( 'conversations.enabled' ) || $conversations;
			$search 		= $template->get( 'toolbar_search' ) || $search;
			$login 			= $template->get( 'toolbar_login') || $login;
			$profile 		= $template->get( 'toolbar_account' ) || $profile;
			$toolbar 		= $template->get( 'toolbar' ) || $toolbar;

			if( $isGuest && ( !$template->get( 'toolbar_guests' ) ) )
			{
				$toolbar 	= false;
			}
		}

		// Get the current view
		$view 	= JRequest::getCmd( 'view' );
		$layout	= JRequest::getCmd( 'layout' );

		$userId	= JRequest::getInt( 'id' );

		$theme->set('facebook', $facebook);
		$theme->set( 'userId'		, $userId );
		$theme->set( 'view'			, $view );
		$theme->set( 'layout'		, $layout );
		$theme->set( 'login'		, $login );
		$theme->set( 'profile'		, $profile );
		$theme->set( 'search'		, $search );
		$theme->set( 'dashboard' 	, $dashboard );
		$theme->set( 'friends'		, $friends );
		$theme->set( 'conversations'	, $conversations );
		$theme->set( 'notifications'	, $notifications );
		$theme->set( 'toolbar'		, $toolbar );
		$theme->set( 'loginReturn'	, $loginReturn );
		$theme->set( 'logoutReturn' , $logoutReturn );

		$html 	= $theme->output( 'site/toolbar/default' );

		return $html;
	}
}
