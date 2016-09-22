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

// Include main view file.
FD::import('site:/views/views');

class EasySocialViewProfile extends EasySocialSiteView
{
	/**
	 * Determines if the view should be visible on lockdown mode
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	public function isLockDown()
	{
		$config 	= FD::config();
		$layout 	= $this->getLayout();

		return true;
	}

	/**
	 * Displays a user profile to a 3rd person perspective.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 **/
	public function display( $tpl = null )
	{
		// Get the user's id.
		$id = $this->input->get('id', 0, 'int');

		// The current logged in user might be viewing their own profile.
		if ($id == 0) {
			$id = FD::user()->id;
		}

		// When the user tries to view his own profile but if he isn't logged in, throw a login page.
		if ($id == 0) {
			return FD::requireLogin();
		}

		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Get the user's object.
		$user = FD::user( $id );

		// If the user still don't exist, throw a 404
		if (!$user->id) {
			return JError::raiseError(404, JText::_('COM_EASYSOCIAL_PROFILE_INVALID_USER'));
		}

		if (Foundry::user()->id != $user->id) {
			if(FD::user()->isBlockedBy($user->id)) {
				return JError::raiseError(404, JText::_('COM_EASYSOCIAL_PROFILE_INVALID_USER'));
			}
		}


		if ($user->isBlock()) {
			FD::info()->set( JText::sprintf( 'COM_EASYSOCIAL_PROFILE_USER_NOT_EXIST', $user->getName() ), SOCIAL_MSG_ERROR );
			return $this->redirect( FRoute::dashboard( array() , false ) );
		}

		// Set the page title
		FD::page()->title(FD::string()->escape($user->getName()));

		// Set the page breadcrumb
		FD::page()->breadcrumb(FD::string()->escape($user->getName()));

		// Apply opengraph tags.
		FD::opengraph()->addProfile($user);

		// Get the current logged in user's object.
		$my 	= FD::user();

		// Do not assign badge if i view myself.
		if ($user->id != $my->id && $my->id) {
			// @badge: profile.view
			$badge 	= FD::badges();
			$badge->log( 'com_easysocial' , 'profile.view' , $my->id , JText::_( 'COM_EASYSOCIAL_PROFILE_VIEWED_A_PROFILE' ) );
		}

		$startlimit 	= JRequest::getInt( 'limitstart' , 0 );

		// Determine if the current request is to load an app
		$appId 		= JRequest::getInt( 'appId' );

		// Get site configuration
		$config 	= FD::config();

		// Get the apps library.
		$appsLib 	= FD::apps();

		$contents = '';

		if($appId) {
			// Load the app
			$app 	= FD::table( 'App' );
			$app->load( $appId );

			// Check if the user has access to this app
			if( !$app->accessible( $user->id ) )
			{
				FD::info()->set( null , JText::_( 'COM_EASYSOCIAL_PROFILE_APP_IS_NOT_INSTALLED_BY_USER' ) , SOCIAL_MSG_ERROR );
				return $this->redirect( FRoute::profile( array( 'id' => $user->getAlias() ) , false ) );
			}

			// Set the page title
			FD::page()->title( FD::string()->escape( $user->getName() ) . ' - ' . $app->get( 'title' ) );

			$contents 	= $appsLib->renderView( SOCIAL_APPS_VIEW_TYPE_EMBED , 'profile' , $app , array( 'userId' => $user->id ) );
		}

		$layout = JRequest::getCmd('layout');

		// @since 1.3.7
		// If layout is empty, means we want to get the default view
		// Previously timeline is always the default
		if (empty($appId) && empty($layout)) {
			$defaultDisplay = FD::config()->get('users.profile.display', 'timeline');

			$layout = $defaultDisplay;
		}

		if ($layout === 'about') {
			FD::language()->loadAdmin();

			$currentStep = JRequest::getInt('step', 1);

			$steps = FD::model('Steps')->getSteps($user->profile_id, SOCIAL_TYPE_PROFILES, SOCIAL_PROFILES_VIEW_DISPLAY);

			$fieldsLib = FD::fields();

			$fieldsModel = FD::model('Fields');

			$index = 1;

			foreach ($steps as $step) {
				$step->fields = $fieldsModel->getCustomFields(array('step_id' => $step->id, 'data' => true, 'dataId' => $user->id, 'dataType' => SOCIAL_TYPE_USER, 'visible' => SOCIAL_PROFILES_VIEW_DISPLAY));

				if (!empty($step->fields)) {
					$args = array($user);

					$fieldsLib->trigger('onDisplay', SOCIAL_FIELDS_GROUP_USER, $step->fields, $args);
				}

				$step->hide = true;

				foreach ($step->fields as $field) {
					// As long as one of the field in the step has an output, then this step shouldn't be hidden
					// If step has been marked false, then no point marking it as false again
					// We don't break from the loop here because there is other checking going on
					if (!empty($field->output) && $step->hide === true ) {
						$step->hide = false;
					}
				}

				if ($index === 1) {
					$step->url = FRoute::profile(array('id' => $user->getAlias(), 'layout' => 'about'), false);
				} else {
					$step->url = FRoute::profile(array('id' => $user->getAlias(), 'layout' => 'about', 'step' => $index), false);
				}

				$step->title = $step->get('title');

				$step->active = !$step->hide && $currentStep == $index;

				if ($step->active) {
					$theme = FD::themes();

					$theme->set('fields', $step->fields);

					$contents = $theme->output('site/events/item.info');
				}

				$step->index = $index;

				$index++;
			}

			$this->set('infoSteps', $steps);
		}

		// If contents is still empty at this point, then we just get the stream items as the content
		if (empty($contents)) {

			// Mark timeline item as the active one
			$this->set('timeline', true);

			// Retrieve user's stream
			$theme 	= FD::themes();

			// Get story
			$story 			= FD::get( 'Story' , SOCIAL_TYPE_USER );
			$story->target 	= $user->id;

			$stream = FD::stream();
			$stream->get( array( 'userId' => $user->id, 'startlimit' => $startlimit ) );

			if( FD::user()->id )
			{
				// only logged in user can submit story.
				$stream->story = $story;
			}

			// Set stream to theme
			$theme->set( 'stream'	, $stream );

			$contents 	= $theme->output( 'site/profile/default.stream' );
		}

		$this->set( 'contents' , $contents );

		$privacy 	= $my->getPrivacy();

		// Let's test if the current viewer is allowed to view this profile.
		if( $my->id != $user->id )
		{
			if( !$privacy->validate( 'profiles.view' , $user->id , SOCIAL_TYPE_USER ) )
			{
				$this->set( 'user' , $user );
				parent::display( 'site/profile/restricted' );

				return;
			}
		}

		// Get user's cover object
		$cover = $user->getCoverData();
		$this->set( 'cover'	, $cover );

		// If we're setting a cover
		$coverId = JRequest::getInt('cover_id', null);

		if( $coverId )
		{
			// Load cover photo
			$newCover = FD::table( 'Photo' );
			$newCover->load( $coverId );

			// If the cover photo belongs to the user
			if ($newCover->isMine()) {

				// Then allow replacement of cover
				$this->set('newCover', $newCover);
			}
		}

		$photosModel 	= FD::model( 'Photos' );
		$photos 		= array();
		// $photos 		= $photosModel->getPhotos( array( 'uid' => $user->id ) ); // not using? it seems like no one is referencing this photos.
		$totalPhotos 	= 0;

		// Retrieve list of apps for this user
		$appsModel 	= FD::model( 'Apps' );
		$options	= array( 'view' => 'profile' , 'uid' => $user->id , 'key' => SOCIAL_TYPE_USER );
		$apps 		= $appsModel->getApps( $options );

		// Set the apps lib
		$this->set( 'appsLib' , $appsLib );
		$this->set( 'totalPhotos' , $totalPhotos );
		$this->set( 'photos' 	, $photos );
		$this->set( 'apps'		, $apps );
		$this->set( 'activeApp'	, $appId );
		$this->set( 'privacy', $privacy );
		$this->set( 'user'		, $user );

		// Load the output of the profile.
		echo parent::display( 'site/profile/default' );
	}

	/**
	 * Responsible to output the edit profile layout
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The name of the template file to parse; automatically searches through the template paths.
	 * @return	null
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function edit($errors = null)
	{
		// Unauthorized users should not be allowed to access this page.
		FD::requireLogin();

		// Set any messages here.
		FD::info()->set($this->getMessage());

		// Load the language file from the back end.
		FD::language()->loadAdmin();

		// Get list of steps for this user's profile type.
		$profile = $this->my->getProfile();

		// Get user's installed apps
		$appsModel = FD::model('Apps');
		$userApps = $appsModel->getUserApps($this->my->id);

		// Get the steps model
		$stepsModel = FD::model('Steps');
		$steps = $stepsModel->getSteps($profile->id, SOCIAL_TYPE_PROFILES, SOCIAL_PROFILES_VIEW_EDIT);

		// Get custom fields model.
		$fieldsModel = FD::model('Fields');

		// Get custom fields library.
		$fields = FD::fields();

		// Set page title
		FD::page()->title(JText::_('COM_EASYSOCIAL_PAGE_TITLE_ACCOUNT_SETTINGS'));

		// Set the page breadcrumb
		FD::page()->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_PROFILE'), FRoute::profile());
		FD::page()->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_ACCOUNT_SETTINGS'));

		// Check if there are any errors in the session
		// If session contains error, means that this is from the FD::fields()->checkCompleteProfile();
		if (empty($errors)) {
			$session = JFactory::getSession();
			$errors = $session->get('easysocial.profile.errors', '', SOCIAL_SESSION_NAMESPACE);

			if (!empty($errors)) {
				FD::info()->set(false, JText::_('COM_EASYSOCIAL_PROFILE_PLEASE_COMPLETE_YOUR_PROFILE'), SOCIAL_MSG_ERROR);
			}
		}

		// Set the callback for the triggered custom fields
		$callback = array( $fields->getHandler(), 'getOutput' );

		// Get the custom fields for each of the steps.
		foreach ($steps as &$step) {

			$step->fields = $fieldsModel->getCustomFields(array('step_id' => $step->id, 'data' => true, 'dataId' => $this->my->id, 'dataType' => SOCIAL_TYPE_USER, 'visible' => 'edit'));

			// Trigger onEdit for custom fields.
			if (!empty($step->fields)) {

				$post = JRequest::get('post');
				$args 	= array( &$post, &$this->my, $errors );
				$fields->trigger( 'onEdit' , SOCIAL_FIELDS_GROUP_USER , $step->fields , $args, $callback );
			}
		}

		// Determines if we should show the social tabs on the left.
		$showSocialTabs = false;

		// Determines if the user has associated
		$associatedFacebook = $this->my->isAssociated( 'facebook' );
		$facebookClient = false;
		$facebookMeta = array();
		$fbOAuth = false;
		$fbUserMeta = array();

		if ($associatedFacebook) {
			// We want to show the tabs
			$showSocialTabs = true;

			$facebookToken	= $this->my->getOAuthToken('facebook');
			$facebookClient = FD::oauth('facebook');

			// Set the access for the client.
			$facebookClient->setAccess($facebookToken);

			try {
				$fbUserMeta = $facebookClient->getUserMeta();
			} catch (Exception $e) {
				$message = (object) array(
					'message' => JText::sprintf('COM_EASYSOCIAL_OAUTH_FACEBOOK_ERROR_MESSAGE', $e->getMessage()),
					'type' => SOCIAL_MSG_ERROR
				);

				FD::info()->set($message);
			}

			$fbUserMeta = false;

			$fbOAuth = $this->my->getOAuth(SOCIAL_TYPE_FACEBOOK);

			$facebookMeta = FD::registry( $fbOAuth->params );
			$facebookPermissions = FD::makeArray( $fbOAuth->permissions );
		}

		$this->set('fbUserMeta', $fbUserMeta);
		$this->set('fbOAuth', $fbOAuth);
		$this->set('showSocialTabs', $showSocialTabs);
		$this->set('facebookMeta', $facebookMeta);
		$this->set('facebookClient', $facebookClient);
		$this->set('associatedFacebook', $associatedFacebook);
		$this->set('profile', $profile);
		$this->set('steps', $steps);
		$this->set('apps', $userApps);

		return parent::display('site/profile/default.edit.profile');
	}

	/**
	 * Edit privacy form
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function editNotifications()
	{
		// User needs to be logged in
		FD::requireLogin();

		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Get the current logged in user.
		$my 		= FD::user();

		// Get the user notification settings
		$alertLib 	= FD::alert();
		$alerts 	= $alertLib->getUserSettings( $my->id );

		// Set page title
		FD::page()->title( JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_NOTIFICATION_SETTINGS' ) );

		// Set the page breadcrumb
		FD::page()->breadcrumb( JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_PROFILE' ) , FRoute::profile() );
		FD::page()->breadcrumb( JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_NOTIFICATION_SETTINGS' ) );

		$this->set( 'alerts'	, $alerts );

		parent::display( 'site/profile/default.edit.notifications' );
	}

	/**
	 * Edit privacy form
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function editPrivacy()
	{
		// User needs to be logged in
		FD::requireLogin();

		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Get the current logged in user.
		$my = FD::user();

		// Get user's privacy
		$privacyLib = FD::privacy( $my->id );
		$result = $privacyLib->getData();

		// Set page title
		FD::page()->title(JText::_('COM_EASYSOCIAL_PAGE_TITLE_PRIVACY_SETTINGS'));

		// Set the page breadcrumb
		FD::page()->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_PROFILE'), FRoute::profile());
		FD::page()->breadcrumb(JText::_('COM_EASYSOCIAL_PAGE_TITLE_PRIVACY_SETTINGS'));

		$privacy = array();

		// Update the privacy data with proper properties.
		foreach ($result as $group => $items) {
			// We do not want to show field privacy rules here because it does not make sense for user to set a default value
			// Most of the fields only have 1 and it is set in Edit Profile page
			if ($group === 'field') {
				continue;
			}

			foreach ($items as &$item) {
				$rule 		= strtoupper( JString::str_ireplace( '.' , '_' , $item->rule ) );
				$groupKey 	= strtoupper( $group );

				$item->groupKey 	= $groupKey;
				$item->label 		= JText::_( 'COM_EASYSOCIAL_PRIVACY_LABEL_' . $groupKey . '_' . $rule );
				$item->tips 		= JText::_( 'COM_EASYSOCIAL_PRIVACY_TIPS_' . $groupKey . '_' . $rule );
			}

			$privacy[$group] = $items;
		}

		// Get a list of blocked users for this user
		$model   = FD::model('Blocks');
		$blocked = $model->getBlockedUsers($this->my->id);

		$this->set('blocked', $blocked);
		$this->set('privacy', $privacy);

		parent::display('site/profile/default.edit.privacy');
	}

	/**
	 * Handle save profiles.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function save()
	{
		$info 	= FD::info();
		$info->set( $this->getMessage() );

		$this->redirect( FRoute::profile( array( 'layout' => 'edit' ) , false ) );
	}

	/**
	 * Handle save notification.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function saveNotification()
	{
		$info 	= FD::info();
		$info->set( $this->getMessage() );

		$this->redirect( FRoute::profile( array( 'layout' => 'editNotifications' ) , false ) );
	}


	/**
	 * Handle save privacy.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function savePrivacy()
	{
		FD::info()->set( $this->getMessage() );

		$this->redirect( FRoute::profile( array( 'layout' => 'editPrivacy' ) , false ) );
	}


	/**
	 * Allows viewer to download a file from the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function downloadFile()
	{
		// Currently only registered users are allowed to view a file.
		FD::requireLogin();

		// Load the file object
		$id = $this->input->get('fileid', 0, 'int');
		$file = FD::table('File');
		$file->load($id);

		if (!$file->id || !$id) {
			// Throw error message here.
			$this->redirect(FRoute::dashboard(array(), false));
			$this->close();
		}

		// Add points for the user when they upload a file.
		FD::points()->assign('files.download', 'com_easysocial', $this->my->id);

		// @TODO: Check for the privacy.

		$file->download();
		exit;
	}

	/**
	 * Post process after removing an avatar
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function removeAvatar()
	{
		FD::info()->set( $this->getMessage() );

		$my 	= FD::user();

		$this->redirect( FRoute::profile( array( 'id' => $my->getAlias() ) , false ) );
	}


	/**
	 * Post processing after the user wants to delete their account
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete()
	{
		FD::info()->set( $this->getMessage() );


		$this->redirect( FRoute::dashboard( array() , false ) );
	}

}
