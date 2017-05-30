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

// Necessary to import the custom view.
FD::import( 'site:/views/views' );

class EasySocialViewDashboard extends EasySocialSiteView
{
	/**
	 * Responsible to output the dashboard layout for the current logged in user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The name of the template file to parse; automatically searches through the template paths.
	 * @return	null
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function display($tpl = null)
	{
		// Get the current logged in user.
		$user 	= FD::user();

		// If the user is not logged in, display the dashboard's unity layout.
		if (!$user->id) {
			return $this->displayGuest();
		}

		//debug
		// $privacyLib 	= FD::privacy( $user->id );
		// $data = $privacyLib->getData();

		// $core = $data['story']['view'];

		// echo '<pre>';print_r( $core->custom );echo '</pre>';
		// // echo '<pre>';print_r( $tmp );echo '</pre>';
		// exit;
		//debug end






		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Default page title
		$title		= $user->getName() . ' - ' . JText::_('COM_EASYSOCIAL_PAGE_TITLE_DASHBOARD');

		// Set the page breadcrumb
		FD::page()->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_DASHBOARD'));

		// Get list of apps
		$model	 = FD::model('Apps');
		$options = array('view' => 'dashboard', 'uid' => $user->id, 'key' => SOCIAL_TYPE_USER);

		// Retrieve apps
		$apps = $model->getApps($options);

		// We need to load the app's own css file.
		if ($apps) {
			foreach ($apps as $app) {
				// Load app's css
				$app->loadCss();
			}
		}

		// Check if there is an app id in the current request as we need to show the app's content.
		$appId 	   = $this->input->get('appId', 0, 'default');
		$contents  = '';
		$isAppView = false;

		if ($appId) {
			$appId 	= (int) $appId;

			if (!$appId) {
				return JError::raiseError(404, JText::_('COM_EASYSOCIAL_PAGE_IS_NOT_AVAILABLE'));
			}

			// Load the application.
			$app 		= FD::table( 'App' );
			$app->load($appId);

			if (!$app->id) {
				return JError::raiseError(404, JText::_('COM_EASYSOCIAL_APP_NOT_FOUND'));
			}

			// Check if the user has access to this app
			if( !$app->accessible( $user->id ) )
			{
				FD::info()->set( null , JText::_( 'COM_EASYSOCIAL_DASHBOARD_APP_IS_NOT_INSTALLED' ) , SOCIAL_MSG_ERROR );
				return $this->redirect( FRoute::dashboard( array() , false ) );
			}

			$app->loadCss();

			$title 		= $user->getName() . ' - ' . $app->get( 'title' );

			// Load the library.
			$lib		= FD::apps();
			$contents 	= $lib->renderView( SOCIAL_APPS_VIEW_TYPE_EMBED , 'dashboard' , $app , array( 'userId' => $user->id ) );

			$isAppView 	= true;
		}

		$startlimit 	= JRequest::getInt( 'limitstart' , 0 );



		$start	= $this->config->get('users.dashboard.start');

		//check if there is any stream filtering or not.
		$filter	= $this->input->get('type', $start, 'word');
		if(!$filter) {
			$filter = $start;
		}

		if ($filter == 'all') {
			// the all is taken from the menu item the setting. all == user & friend, which mean in this case, is the 'me' filter.
			$filter = 'me';
		}


		$listId = $this->input->get('listId', '', 'int');
		$fid 	= '';

		// Used in conjunction with type=appFilter
		$filterId 		= '';


		// Determine if the current request is for "tags"
		$hashtag		= $this->input->get('tag', '', 'default');
		$hashtagAlias	= $hashtag;

		if (!empty($hashtag)) {
			$filter = 'hashtag';
		}

		// Retrieve user's groups
		$groupModel  = FD::model('Groups');
		$groups      = $groupModel->getUserGroups($user->id);

		// Retrieve user's events
		$eventModel  = FD::model('Events');
		$events      = $eventModel->getEvents(array('creator_uid' => $user->id, 'creator_type' => SOCIAL_TYPE_USER, 'start-after' => FD::date()->toSql()));

		// Retrieve user's status
		$story 			= FD::get('Story', SOCIAL_TYPE_USER);
		$story->setTarget($user->id);

		// Retrieve user's stream
		$stream = FD::stream();
		$stream->story  = $story;

		// Determines if we should be rendering the group streams
		$groupId = false;
		$eventId = false;

		$tags = array();

		// Filter by specific list item
		if ($filter == 'list' && !empty($listId)) {
			$list 		= FD::table( 'List' );
			$list->load( $listId );

			$title 		= $user->getName() . ' - ' . $list->get( 'title' );


			// Get list of users from this list.
			$friends 	= $list->getMembers();

			if( $friends )
			{
				$stream->get( array( 'listId' => $listId, 'startlimit' => $startlimit ) );
			}
			else
			{
				$stream->filter 	= 'list';
			}
		}

		// Filter by specific #hashtag
		if ($filter == 'hashtag') {
			$tag = $this->input->get('tag', '', 'default');

			$hashtag = $tag;

			$title 	= $user->getName() . ' - #' . $tag;

			$stream->get(array('tag' => $tag));
			$tags = array($tag);
		}


		// Filter by everyone
		if ($filter == 'everyone') {
			$stream->get(array('guest' => true, 'ignoreUser' => true, 'startlimit' => $startlimit));
		}

		// Filter by following
		if ($filter == 'following') {

			// Set the page title
			$title 	= $user->getName() . ' - ' . JText::_( 'COM_EASYSOCIAL_DASHBOARD_FEED_FOLLLOW' );

			$stream->get(array('context' => SOCIAL_STREAM_CONTEXT_TYPE_ALL, 'type' => 'follow', 'startlimit' => $startlimit));
		}

		// Filter by bookmarks
		if ($filter == 'bookmarks') {

			// Set the page title
			$title 	= $user->getName() . ' - ' . JText::_( 'COM_EASYSOCIAL_DASHBOARD_FEED_DASHBOARD_BOOKMARKS' );

			$stream->get(array('guest' => true, 'type' => 'bookmarks', 'startlimit' => $startlimit));
		}

		// Filter by apps
		if ($filter == 'appFilter') {

			$appType  = $this->input->get('filterid', '', 'string');
			$filterId = $appType;

			$stream->get(array('context' => $appType, 'startlimit' => $startlimit));

			$stream->filter	= 'custom';
		}

		// Filter by custom filters
		if ($filter == 'filter') {

			$fid 	= $this->input->get('filterid', 0, 'int');
			$sfilter = FD::table('StreamFilter');
			$sfilter->load($fid);

			// Set the page title
			$title 		= $user->getName() . ' - ' . $sfilter->title;

			if( $sfilter->id )
			{
				$hashtags	= $sfilter->getHashTag();
				$tags 		= explode( ',', $hashtags );

				if( $tags )
				{
					$stream->get( array( 'context' 	=> SOCIAL_STREAM_CONTEXT_TYPE_ALL, 'tag' => $tags, 'startlimit' => $startlimit ) );
				}
			}

			$stream->filter = 'custom';
		}

		// Stream filter form
		if ($filter == 'filterForm') {
			// Set the page title
			$title 	= $user->getName() . ' - ' . JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_FILTER_FORM');

			$id 	= JRequest::getInt( 'id' );

			// Load up the theme lib so we can output the contents
			$theme 	= FD::themes();

			$filter = FD::table( 'StreamFilter' );
			$filter->load( $id );

			$theme->set( 'filter', $filter );

			$contents	= $theme->output( 'site/stream/form.edit' );
		}

		// Filter by groups
		if ($filter == 'group') {
			$id = $this->input->get('groupId', 0, 'int');
			$group   = FD::group($id);
			$groupId = $group->id;

			// Check if the user is a member of the group
			if (!$group->isMember()) {

				$this->setMessage( JText::_( 'COM_EASYSOCIAL_STREAM_GROUPS_NO_PERMISSIONS' ) , SOCIAL_MSG_ERROR );
				FD::info()->set( $this->getMessage() );
				return $this->redirect( FRoute::dashboard( array() , false ) );
			}

			// When posting stories into the stream, it should be made to the group
			$story 			= FD::get( 'Story' , SOCIAL_TYPE_GROUP );
			$story->setCluster( $group->id, SOCIAL_TYPE_GROUP );
			$story->showPrivacy( false );
			$stream->story 	= $story;

			$stream->get( array( 'clusterId' => $group->id , 'clusterType' => SOCIAL_TYPE_GROUP, 'startlimit' => $startlimit ) );
		}

		if ($filter == 'event') {
			$id = $this->input->get('eventId', 0, 'int');
			$event   = FD::event($id);
			$eventId = $event->id;

			// Check if the user is a member of the group
			if (!$event->getGuest()->isGuest()) {
				$this->setMessage(JText::_('COM_EASYSOCIAL_STREAM_GROUPS_NO_PERMISSIONS'), SOCIAL_MSG_ERROR);
				$this->info->set($this->getMessage());
				return $this->redirect(FRoute::dashboard(array(), false));
			}

			// When posting stories into the stream, it should be made to the group
			$story = FD::get('Story', SOCIAL_TYPE_EVENT);
			$story->setCluster($event->id, SOCIAL_TYPE_EVENT);
			$story->showPrivacy(false);
			$stream->story 	= $story;

			$stream->get(array('clusterId' => $event->id , 'clusterType' => SOCIAL_TYPE_EVENT, 'startlimit' => $startlimit));
		}

		if ($filter == 'me') {
			$stream->get( array('startlimit' => $startlimit) );
		}

		// Set the page title.
		FD::page()->title($title);

		// Set hashtags
		$story->setHashtags($tags);

		// Retrieve lists model
		$listsModel	 = FD::model('Lists');

		// Only fetch x amount of list to be shown by default.
		$limit = $this->config->get('lists.display.limit');

		// Get the friend's list.
		$lists = $listsModel->setLimit($limit)->getLists(array('user_id' => $user->id, 'showEmpty' => $this->config->get('friends.list.showEmpty')));

		// Get stream filter list
		$model = FD::model('Stream');
		$filterList = $model->getFilters($user->id);

		// Get a list of application filters
		$appFilters = $model->getAppFilters(SOCIAL_TYPE_USER);

		$this->set('title'	, $title);
		$this->set('eventId', $eventId);
		$this->set('events', $events);
		$this->set('filterId', $filterId );
		$this->set( 'appFilters'	, $appFilters );
		$this->set( 'groupId'		, $groupId );
		$this->set( 'groups'		, $groups );
		$this->set( 'hashtag'		, $hashtag );
		$this->set( 'hashtagAlias'	, $hashtagAlias );
		$this->set( 'listId'		, $listId );
		$this->set( 'filter'		, $filter );
		$this->set( 'isAppView'		, $isAppView );
		$this->set( 'apps'			, $apps );
		$this->set( 'lists'			, $lists );
		$this->set( 'appId'			, $appId );
		$this->set( 'contents'		, $contents );
		$this->set( 'user'			, $user );
		$this->set( 'stream'		, $stream );
		$this->set( 'filterList'	, $filterList );
		$this->set( 'fid'			, $fid );

		echo parent::display('site/dashboard/default');
	}

	/**
	 * Displays the guest view for the dashboard
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function displayGuest()
	{
		$config = Foundry::config();

		// stream filter
		$filter = 'everyone';

		// Determine if the current request is for "tags"
		$hashtag		= $this->input->get('tag', '');
		$hashtagAlias	= $hashtag;

		if (!empty($hashtag)) {
			$filter = 'hashtag';
		}

		// Get the layout to use.
		$stream 	= FD::stream();
		$stream->getPublicStream( $config->get('stream.pagination.pagelimit', 10), 0, $hashtag );

		// Get any callback urls.
		$return 	= FD::getCallback();

		// In guests view, there shouldn't be an app id
		$appId = $this->input->get('appId', '', 'default');

		if ($appId) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_PAGE_IS_NOT_AVAILABLE'));
		}

		// If return value is empty, always redirect back to the dashboard
		if (!$return) {
			$return	= FRoute::dashboard( array() , false );
		}

		$return 	= base64_encode( $return );
		$facebook	= FD::oauth( 'Facebook' );

		$this->set( 'filter'	, $filter );
		$this->set( 'facebook'	, $facebook );
		$this->set( 'hashtag'		, $hashtag );
		$this->set( 'hashtagAlias'	, $hashtagAlias );
		$this->set( 'stream'	, $stream );
		$this->set( 'return'	, $return );

		if( $config->get( 'registrations.enabled' ) )
		{
			$fieldsModel = FD::model( 'fields' );

			$config = FD::config();

			$profileId = $config->get('registrations.mini.profile', 'default');

			if ($profileId === 'default') {
				$profileId = FD::model( 'profiles' )->getDefaultProfile()->id;
			}

			$options = array(
				'visible' => SOCIAL_PROFILES_VIEW_MINI_REGISTRATION,
				'profile_id' => $profileId
			);

			$fields = $fieldsModel->getCustomFields( $options );

			if( !empty( $fields ) )
			{
				FD::language()->loadAdmin();

				$fieldsLib = FD::fields();

				$session    	= JFactory::getSession();
				$registration	= FD::table( 'Registration' );
				$registration->load( $session->getId() );

				$data           = $registration->getValues();

				$args = array( &$data, &$registration );

				$fieldsLib->trigger( 'onRegisterMini', SOCIAL_FIELDS_GROUP_USER, $fields, $args );

				$this->set( 'fields', $fields );
			}
		}

		echo parent::display( 'site/dashboard/default.guests' );
	}
}
