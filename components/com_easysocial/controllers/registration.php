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

FD::import( 'site:/controllers/controller' );

class EasySocialControllerRegistration extends EasySocialController
{
	/**
	 * Allows user to activate their account.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function activate()
	{
		$my 	= FD::user();

		// Get current view.
		$view 	= $this->getCurrentView();

		// Get the id from the request
		$id 			= JRequest::getInt( 'userid' );
		$currentUser 	= FD::user( $id );

		// If user is already logged in, redirect to the dashboard.
		if ($my->isLoggedIn()) {
			return $view->call( __FUNCTION__ , $currentUser );
		}

		$token 	= JRequest::getVar('token' , '');

		// If token is empty, warn the user.
		if (empty($token) || strlen($token) !== 32) {
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_REGISTRATION_ACTIVATION_TOKEN_INVALID' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $currentUser );
		}

		// Retrieve registration model
		$model 	= FD::model( 'Registration' );

		// Activate the token.
		$user 	= $model->activate( $token );

		if( $user === false )
		{
			$view->setMessage( $model->getError() , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ , $currentUser );
		}

		// @points: user.register
		// Assign points when user registers on the site.
		$points = Foundry::points();
		$points->assign('user.registration', 'com_easysocial', $this->id);

		// @badge: registration.create
		// Assign badge for the person that initiated the friend request.
		$badge 	= FD::badges();
		$badge->log( 'com_easysocial' , 'registration.create' , $user->id , JText::_( 'COM_EASYSOCIAL_REGISTRATION_BADGE_REGISTERED' ) );

		// Get configuration object.
		$config 	= FD::config();

		// Add activity logging when a uer registers on the site.
		// Get the application params
		$app 	= FD::table( 'App' );
		$app->load( array( 'element' => 'profiles' , 'group' => SOCIAL_TYPE_USER ) );
		$params = $app->getParams();

		// If not allowed, we will not want to proceed here.
		if( $params->get( 'stream_register' , true ) )
		{
			$stream				= FD::stream();
			$streamTemplate		= $stream->getTemplate();

			// Set the actor
			$streamTemplate->setActor( $user->id , SOCIAL_TYPE_USER );

			// Set the context
			$streamTemplate->setContext( $user->id , SOCIAL_TYPE_PROFILES );

			// Set the verb
			$streamTemplate->setVerb( 'register' );

			// set sitewide
			$streamTemplate->setSiteWide();

			$streamTemplate->setAccess( 'core.view' );


			// Add stream template.
			$stream->add( $streamTemplate );
		}

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_REGISTRATION_ACTIVATION_COMPLETED_SUCCESS' ) , SOCIAL_MSG_SUCCESS );
		return $view->call( __FUNCTION__ , $user );
	}

	/**
	 * This adds information about the current profile that the user selected during registration.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function selectType()
	{
		$config 	= FD::config();
		$view 		= FD::view( 'Registration' , false );

		// @task: Ensure that registrations is enabled.
		if( !$config->get( 'registrations.enabled' ) )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_ERROR_REGISTRATION_DISABLED' , SOCIAL_MSG_ERROR ) );
			return $view->call( __FUNCTION__ );
		}

		$id 	= JRequest::getInt( 'profile_id' , 0 );

		// If there's no profile id selected, throw an error.
		if( !$id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_ERROR_REGISTRATION_EMPTY_PROFILE_ID' ) , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		// @task: Let's set some info about the profile into the session.
		$session		= JFactory::getSession();
		$session->set( 'profile_id' , $id , SOCIAL_SESSION_NAMESPACE );

		// @task: Try to load more information about the current registration procedure.
		$registration				= FD::table( 'Registration' );
		$registration->load( $session->getId() );
		$registration->profile_id	= $id;

		// When user accesses this page, the following will be the first page
		$registration->set( 'step' , 1 );

		// Add the first step into the accessible list.
		$registration->addStepAccess( 1 );
		$registration->store();

		// After a profile type is selected, ensure that the cache are cleared.
		$cache	= JFactory::getCache();
		$cache->clean();

		// Check in the session if quick is flagged as true
		if ($session->get('quick', false, SOCIAL_SESSION_NAMESPACE)) {
			return $this->quickRegister();
		}

		return $view->call( __FUNCTION__ );
	}

	/**
	 * Each time the user clicks on the next button, this method is invoked.
	 *
	 * @since	1.0
	 * @access	public
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function saveStep()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Get configuration object.
		$config 	= FD::config();

		// Get the current view
		$view 		= $this->getCurrentView();

		// Registrations must be enabled.
		if( !$config->get( 'registrations.enabled' ) )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_REGISTRATIONS_DISABLED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Retrieve all file objects if needed
		$files 		= JRequest::get( 'FILES' );
		$post		= JRequest::get( 'POST' );
		$token      = FD::token();

		// Get current user's info
		$session    = JFactory::getSession();

		// Get necessary info about the current registration process.
		$registration		= FD::table( 'Registration' );
		$state = $registration->load( $session->getId() );

		// There are cases where the registration page is not loaded through display function in view.html.php due to cache, then the session is not created in registration table
		if (!$state) {
			$registration->set( 'session_id', 	$session->getId() );
			$registration->set( 'created', 		FD::get( 'Date' )->toMySQL() );
			$registration->set( 'profile_id', 	$post['profileId'] );
			$registration->set( 'step', 1 );
			$registration->addStepAccess(1);

			$registration->store();
		}

		// Load the profile object.
		$profile    = FD::table( 'Profile' );
		$profile->load( $registration->get( 'profile_id' ) );

		// Get the sequence
		$sequence = $profile->getSequenceFromIndex($registration->get('step'), SOCIAL_PROFILES_VIEW_REGISTRATION);

		// Load the current step.
		$step 		= FD::table( 'FieldStep' );
		$step->loadBySequence( $profile->id , SOCIAL_TYPE_PROFILES , $sequence );

		// Merge the post values
		$registry 	= FD::get( 'Registry' );
		$registry->load( $registration->values );

		// Load registration model
		$registrationModel	= FD::model( 'Registration' );

		// Get all published fields apps that are available in the current form to perform validations
		$fieldsModel 		= FD::model( 'Fields' );
		$fields				= $fieldsModel->getCustomFields( array( 'step_id' => $step->id, 'visible' => SOCIAL_PROFILES_VIEW_REGISTRATION ) );

		// Load json library.
		$json 	= FD::json();

		// Process $_POST vars
		foreach ($post as $key => $value) {

			if ($key != $token) {
				if (is_array($value)) {
					$value  = $json->encode($value);
				}
				$registry->set($key, $value);
			}
		}

		// Convert the values into an array.
		$data		= $registry->toArray();

		$args       = array(&$data, &$registration);

		// Perform field validations here. Validation should only trigger apps that are loaded on the form
		// @trigger onRegisterValidate
		$fieldsLib	= FD::fields();

		// Get the trigger handler
		$handler = $fieldsLib->getHandler();

		// Get error messages
		$errors  = $fieldsLib->trigger( 'onRegisterValidate' , SOCIAL_FIELDS_GROUP_USER , $fields , $args, array( $handler, 'validate' ) );

		// The values needs to be stored in a JSON notation.
		$registration->values   = $json->encode($data);

		// Store registration into the temporary table.
		$registration->store();

		// Get the current step (before saving)
		$currentStep    = $registration->get( 'step' );

		// Add the current step into the accessible list
		$registration->addStepAccess( $currentStep );

		// Bind any errors into the registration object
		$registration->setErrors( $errors );

		// Saving was intercepted by one of the field applications.
		if (is_array($errors) && count($errors) > 0) {
			// @rule: If there are any errors on the current step, remove access to future steps to avoid any bypass
			$registration->removeAccess($currentStep);

			// @rule: Reset steps to the current step
			$registration->step = $currentStep;
			$registration->store();

			$view->setMessage(JText::_('COM_EASYSOCIAL_REGISTRATION_SOME_ERRORS_IN_THE_REGISTRATION_FORM') , SOCIAL_MSG_ERROR);

			return $view->call('saveStep', $registration, $currentStep);
		}

		// Determine whether the next step is completed. It has to be before updating the registration table's step
		// Otherwise, the step doesn't exist in the site.

		// Determine if this is the last step.
		$completed = $step->isFinalStep( SOCIAL_PROFILES_VIEW_REGISTRATION );

		// Update creation date
		$registration->created = FD::date()->toMySQL();

		// Since user has already came through this step, add the step access
		$nextSequence = $step->getNextSequence( SOCIAL_PROFILES_VIEW_REGISTRATION );

		if ($nextSequence !== false) {
			$nextIndex = $profile->getIndexFromSequence($nextSequence, SOCIAL_PROFILES_VIEW_REGISTRATION);
			$registration->addStepAccess( $nextIndex );
			$registration->step = $nextIndex;
		}

		// Save the temporary data.
		$registration->store();

		// If this is the last step, we try to save all user's data and create the necessary values.
		if ($completed) {

			// Create user object.
			$user 	= $registrationModel->createUser($registration);

			// If there's no id, we know that there's some errors.
			if (empty($user->id)) {
				$errors 		= $registrationModel->getError();

				$view->setMessage( $errors , SOCIAL_MSG_ERROR );

				return $view->call('saveStep', $registration , $currentStep);
			}

			// Get the registration data
			$registrationData 	= FD::registry($registration->values);

			// Clear existing registration objects once the creation is completed.
			$registration->delete();

			// Clear cache as soon as the user registers on the site.
			$cache 		= JFactory::getCache();
			$cache->clean('page');
			$cache->clean('_system');

			// Force unset on the user first to reload the user object
			SocialUser::$userInstances[$user->id] = null;

			// Get the current registered user data.
			$my = FD::user($user->id);

			// Check if this user was invited
			$inviteId = $session->get('invite', false, SOCIAL_SESSION_NAMESPACE);

			if ($inviteId) {
				$inviteTable = FD::table('FriendInvite');
				$inviteTable->load($inviteId);

				if ($inviteId && $inviteTable->id) {
					$inviteTable->registered_id = $my->id;
					$inviteTable->store();

					// Make them both friends
					$inviteTable->makeFriends();
				}
			}

			// We need to send the user an email with their password
			$my->password_clear	= $user->password_clear;

			// Convert the data into an array of result.
			$mailerData		= FD::registry( $registration->values )->toArray();

			// Send notification to admin if necessary.
			if ($profile->getParams()->get('email.moderators', true)) {
				$registrationModel->notifyAdmins( $mailerData , $my , $profile, false );
			}

			// If everything goes through fine, we need to send notification emails out now.
			$registrationModel->notify($mailerData, $my, $profile);

			// We need to log the user in after they have successfully registered.
			if ($profile->getRegistrationType() == 'auto' || $profile->getRegistrationType() == 'login') {

				// @points: user.register
				// Assign points when user registers on the site.
				$points = FD::points();
				$points->assign('user.registration', 'com_easysocial', $my->id);

				// @badge: registration.create
				// Assign badge for the person that initiated the friend request.
				$badge 	= FD::badges();
				$badge->log('com_easysocial', 'registration.create' , $my->id , JText::_( 'COM_EASYSOCIAL_REGISTRATION_BADGE_REGISTERED' ) );

				// Add activity logging when a uer registers on the site.
				if( $config->get( 'registrations.stream.create' ) )
				{
					$stream				= FD::stream();
					$streamTemplate		= $stream->getTemplate();

					// Set the actor
					$streamTemplate->setActor( $my->id , SOCIAL_TYPE_USER );

					// Set the context
					$streamTemplate->setContext( $my->id , SOCIAL_TYPE_PROFILES );

					// Set the verb
					$streamTemplate->setVerb( 'register' );

					$streamTemplate->setSiteWide();

					$streamTemplate->setAccess( 'core.view' );


					// Add stream template.
					$stream->add( $streamTemplate );
				}

				$app 			= JFactory::getApplication();

				$credentials	= array( 'username' => $my->username , 'password' => $my->password_clear );

				// Try to log the user in
				$app->login($credentials);
			}

			// add new registered user into indexer
			$my->syncIndex();


			// Store the user's custom fields data now.
			return $view->complete($user, $profile);
		}

		return $view->saveStep($registration, $currentStep, $completed);
	}

	/**
	 * Normal oauth registration or if the user has an invalid email or username in simplified process.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function oauthCreateAccount()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view.
		$view 		= $this->getCurrentView();

		// Get component's configuration
		$config 	= FD::config();

		// Get allowed clients
		$allowedClients	= array_keys( (array) $config->get( 'oauth' ) );

		// Get the client type
		$clientType 	= JRequest::getWord( 'client' , '' );

		// Check if the client is valid.
		if( !$clientType || !in_array( $clientType , $allowedClients ) )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_OAUTH_INVALID_CLIENT' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Get the profile
		$profileId 	= JRequest::getInt( 'profile' );
		$profile 	= FD::table( 'Profile' );
		$profile->load( $profileId );

		// Check if the profile id is provided.
		if (!$profileId || !$profile->id) {
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_OAUTH_INVALID_PROFILEID' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Get the access token from session
		$client 		= FD::oauth( $clientType );
		$session 		= JFactory::getSession();
		$accessToken 	= $client->getAccess();

		// Check if the profile id is provided.
		if (!$accessToken) {
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_OAUTH_ACCESS_TOKEN_NOT_FOUND' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Determines if the oauth id is already registered on the site.
		$isRegistered 	= $client->isRegistered();

		// If user has already registered previously, just log them in.
		if ($isRegistered) {
			// Throw an error message here because they shouldn't be coming through this page.
			return $view->call( __FUNCTION__ );
		}

		// Get the user's meta
		try {
			$meta = $client->getUserMeta();
		} catch (Exception $e) {
			$app = JFactory::getApplication();

			// Use dashboard here instead of login because api error calls might come from after user have successfully logged in
			$url = FRoute::dashboard( array(), false );

			$message = (object) array(
				'message' => JText::sprintf( 'COM_EASYSOCIAL_OAUTH_FACEBOOK_ERROR_MESSAGE', $e->getMessage() ),
				'type' => SOCIAL_MSG_ERROR
			);

			FD::info()->set( $message );

			$app->redirect( $url );
			$app->close();
		}

		$import		= JRequest::getBool( 'import' );
		$sync 		= JRequest::getBool( 'stream' );
		$username 	= JRequest::getVar( 'oauth-username' );
		$email 		= JRequest::getVar( 'oauth-email' );

		// If emailasusername is on, then we manually assign email into username
		if ($config->get('registrations.emailasusername')) {
			$username = $email;
		}

		// Detect if user has set a password.
		$password 	= JRequest::getVar( 'password' , '' );

		if (!empty($password)) {
			$meta[ 'password' ]	= $password;
		}

		// Reset the profile id
		$meta[ 'profileId' ]	= $profile->id;

		// Re-apply the username to the meta.
		$meta[ 'username' ]		= $username;
		$meta[ 'email' ]		= $email;

		// Retrieve the model.
		$model 		= FD::model( 'Registration' );

		// Double check to see if the email and username still exists.
		if( $model->isUsernameExists( $meta[ 'username' ] ) )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_OAUTH_USERNAME_ALREADY_USED' ) , SOCIAL_MSG_ERROR );
			return $view->call( 'oauthPreferences' , $profile->id , $meta[ 'username' ] , $meta[ 'email' ] , $client );
		}

		// Double check to see if the email and username still exists.
		if( $model->isEmailExists( $meta[ 'email' ] ) )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_OAUTH_EMAIL_ALREADY_USED' ) , SOCIAL_MSG_ERROR );
			return $view->call( 'oauthPreferences' , $profile->id , $meta[ 'username' ] , $meta[ 'email' ] , $client );
		}


		// Create the user account in Joomla
		$user 		= $model->createOauthUser( $accessToken , $meta , $client , $import , $sync );

		// If there's a problem creating user, throw message.
		if( !$user )
		{
			$view->setMessage( $model->getError() , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}


		// Check if the profile type requires activation. Only log the user in when user is supposed to automatically login.
		$type 	= $profile->getRegistrationType(false, true);

		// Send notification to admin if necessary.
		$model->notifyAdmins($meta , $user , $profile, true);

		// Only log the user in if the profile allows this.
		if ($type == 'auto') {
			// Log the user in
			$client->login();

			// Once the user is logged in, get the new user object.
			$my 	= FD::user();

			// @points: user.register
			// Assign points when user registers on the site.
			$points = FD::points();
			$points->assign( 'user.registration' , 'com_easysocial' , $my->id );

			// Add activity logging when a uer registers on the site.
			if( $config->get( 'registrations.stream.create' ) )
			{
				$stream				= FD::stream();
				$streamTemplate		= $stream->getTemplate();

				// Set the actor
				$streamTemplate->setActor( $my->id , SOCIAL_TYPE_USER );

				// Set the context
				$streamTemplate->setContext( $my->id , SOCIAL_TYPE_PROFILES );

				// Set the verb
				$streamTemplate->setVerb( 'register' );

				$streamTemplate->setSiteWide();

				$streamTemplate->setAccess( 'core.view' );

				// Add stream template.
				$stream->add( $streamTemplate );
			}

		} else {
			// Send notification to user
			$model->notify( $meta , $user , $profile, true);
		}

		return $view->call( __FUNCTION__ , $user );
	}

	/**
	 * Links a previously registered account with an oauth account.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function oauthLinkAccount()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view 			= $this->getCurrentView();

		// Get the current client type.
		$clientType		= JRequest::getVar( 'client' );

		// Get the client library.
		$client 		= FD::oauth( $clientType );

		// Get the user's username and password
		$username 		= JRequest::getVar( 'username' );
		$password 		= JRequest::getVar( 'password' );
		$credentials 	= array( 'username' => $username , 'password' => $password );

		$app 			= JFactory::getApplication();
		$state 			= $app->login( $credentials );

		if( !$state )
		{
			// We do not need to set any messages since Joomla will automatically display this in the queue.
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_OAUTH_USERNAME_PASSWORD_ERROR' ) , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ , $clientType );
		}

		$my 	= FD::user();

		// If user logged in successfully, link the oauth account to this user account.
		$model 	= FD::model( 'Registration' );
		$state	= $model->linkOAuthUser( $client , $my );

		if( !$state )
		{
			$view->setMessage( $model->getError() , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ , $clientType );
		}

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_OAUTH_ACCOUNT_LINK_SUCCESS' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ , $clientType );
	}

	/**
	 * This is when user clicks on Create account which we will automatically register them on the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function oauthSignup()
	{
		// Load our own configuration.
		$config = FD::config();

		// Retrieve current view.
		$view 	= $this->getCurrentView();

		// Get the current client
		$client = JRequest::getWord( 'client' );

		// Get allowed clients
		$allowedClients	= array_keys( (array) $config->get( 'oauth' ) );

		// Check for allowed clients.
		if( !in_array( $client , $allowedClients ) )
		{
			$view->setMessage( JText::sprintf( 'COM_EASYSOCIAL_OAUTH_INVALID_OAUTH_CLIENT_PROVIDED' , $client ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Load up oauth library
		$oauthClient 	= FD::oauth( $client );

		// Get the external user id.
		$oauthUserId	= $oauthClient->getUser();

		// Determines if the oauth id is already registered on the site.
		$isRegistered 	= $oauthClient->isRegistered();

		// If user has already registered previously, just log them in.
		if ($isRegistered) {
			$state	= $oauthClient->login();

			if ($state) {
				$view->setMessage('COM_EASYSOCIAL_OAUTH_AUTHENTICATED_ACCOUNT_SUCCESS', SOCIAL_MSG_SUCCESS);
			}

			return $view->call(__FUNCTION__);
		}

		// Get the access tokens.
		$accessToken 	= $oauthClient->getAccess();

		// Retrieve user's information
		try {
			$meta = $oauthClient->getUserMeta();
		} catch (Exception $e) {
			$app = JFactory::getApplication();

			// Use dashboard here instead of login because api error calls might come from after user have successfully logged in
			$url = FRoute::dashboard( array(), false );

			$message = (object) array(
				'message' => JText::sprintf( 'COM_EASYSOCIAL_OAUTH_FACEBOOK_ERROR_MESSAGE', $e->getMessage() ),
				'type' => SOCIAL_MSG_ERROR
			);

			FD::info()->set( $message );

			$app->redirect( $url );
			$app->close();
		}

		// Get the registration type.
		$registrationType 	= $config->get( 'oauth.' . $client . '.registration.type' );

		// Load up registration model
		$model 		= FD::model( 'Registration' );

		// If this is a simplified registration, check if the user name exists.
		if ($registrationType == 'simplified') {

			// If the username or email exists
			if ($model->isEmailExists( $meta[ 'email' ] ) || $model->isUsernameExists($meta['username'])) {
				return $view->call( 'oauthPreferences' , $meta[ 'profileId'] , $meta[ 'username' ] , $meta[ 'email' ] , $client );
			}

		}

		// Create user account
		$user 		= $model->createOauthUser($accessToken, $meta, $oauthClient);

		// @badge: registration.create
		// Assign badge for the person that initiated the friend request.
		$badge 	= FD::badges();
		$badge->log( 'com_easysocial' , 'registration.create' , $user->id , JText::_( 'COM_EASYSOCIAL_REGISTRATION_BADGE_REGISTERED' ) );

		if (!$user) {
			$view->setMessage( $model->getError() , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		// If the profile type is auto login, we need to log the user in
		$profile 		= FD::table( 'Profile' );
		$profile->load( $meta[ 'profileId' ] );

		// Check if the profile type requires activation. Only log the user in when user is supposed to automatically login.
		$type 	= $profile->getRegistrationType(false, true);

		// Send notification to admin if necessary.
		$model->notifyAdmins($meta , $user , $profile, true);

		JFactory::getSession()->clear('user');

		// Only log the user in if the profile allows this.
		if ($type == 'auto') {
			// Log the user in
			$oauthClient->login();

			// Once the user is logged in, get the new user object.
			$my 	= FD::user();

			// @points: user.register
			// Assign points when user registers on the site.
			$points = FD::points();
			$points->assign( 'user.registration' , 'com_easysocial' , $my->id );

			// Add activity logging when a uer registers on the site.
			if( $config->get( 'registrations.stream.create' ) )
			{
				$stream				= FD::stream();
				$streamTemplate		= $stream->getTemplate();

				// Set the actor
				$streamTemplate->setActor( $my->id , SOCIAL_TYPE_USER );

				// Set the context
				$streamTemplate->setContext( $my->id , SOCIAL_TYPE_PROFILES );

				// Set the verb
				$streamTemplate->setVerb( 'register' );

				$streamTemplate->setSiteWide();

				$streamTemplate->setAccess( 'core.view' );

				// Add stream template.
				$stream->add( $streamTemplate );
			}

		} else {

			// Send notification to user
			$model->notify($meta , $user , $profile, true);
		}

		return $view->call( 'oauthCreateAccount' , $user );
	}


	/**
	 * Allows admin to approve a user via email
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function approveUser()
	{
		$key 	= JRequest::getVar( 'key' );
		$id 	= JRequest::getInt( 'id' );

		$user 	= FD::user( $id );

		$view 	= $this->getCurrentView();

		// Re-generate the hash
		$hash	= md5( $user->password . $user->email . $user->name . $user->username );

		// If the key provided is not valid, we do not do anything
		if( $hash != $key )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_REGISTRATION_MODERATION_FAILED_KEY_DOES_NOT_MATCH' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Approve the user now.
		$user->approve();

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_REGISTRATION_USER_ACCOUNT_APPROVED_SUCCESSFULLY' ) , SOCIAL_MSG_SUCCESS );
		return $view->call( __FUNCTION__ );
	}

	/**
	 * Allows admin to reject a user via email
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function rejectUser()
	{
		$key 	= JRequest::getVar( 'key' );
		$id 	= JRequest::getInt( 'id' );

		$user 	= FD::user( $id );

		$view 	= $this->getCurrentView();

		// Re-generate the hash
		$hash	= md5( $user->password . $user->email . $user->name . $user->username );

		// If the key provided is not valid, we do not do anything
		if( $hash != $key )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_REGISTRATION_MODERATION_FAILED_KEY_DOES_NOT_MATCH' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Approve the user now.
		$user->reject();

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_REGISTRATION_USER_ACCOUNT_REJECTED_SUCCESSFULLY' ) , SOCIAL_MSG_SUCCESS );
		return $view->call( __FUNCTION__ );
	}

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

		if( $config->get( 'general.site.lockdown.registration' ) )
		{
			return false;
		}

		return true;
	}

	/**
	 * This is the registration API for modules. We allow modules to allow quick registration
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function quickRegister()
	{
		// Get the current view
		$view 	= $this->getCurrentView();

		// Get current user's session
		$session = JFactory::getSession();

		// Get necessary info about the current registration process.
		$registration		= FD::table( 'Registration' );
		$registration->load( $session->getId() );

		// Get a new registry object
		$params = FD::get( 'Registry' );

		if (!empty($registration->values)) {
			$params->load( $registration->values );
		}

		// The profile id is definitely required otherwise we will skip this.
		$profileId 	= $registration->profile_id;

		if (empty($profileId)) {
			$view->setMessage(JText::_('COM_EASYSOCIAL_REGISTRATIONS_MODULE_PROFILE_TYPE_REQUIRED' ), SOCIAL_MSG_ERROR);
			return $view->call('selectProfile');
		}

		$data = $params->toArray();

		// Trigger onRegisterValidate first

		// Get the fields
		$fieldsModel = FD::model('Fields');
		$fields = $fieldsModel->getCustomFields(array('profile_id' => $profileId, 'visible' => SOCIAL_PROFILES_VIEW_MINI_REGISTRATION));

		$fieldsLib = FD::fields();

		// Get the trigger handler
		$handler = $fieldsLib->getHandler();

		$args = array(&$data, &$registration);

		// Get error messages
		$errors = $fieldsLib->trigger('onRegisterMiniValidate', SOCIAL_FIELDS_GROUP_USER , $fields , $args );

		$registration->setErrors($errors);

		// The values needs to be stored in a JSON notation.
		$registration->values   = FD::json()->encode( $data );

		// Store registration into the temporary table.
		$registration->store();

		// Saving was intercepted by one of the field applications.
		if (is_array($errors) && count($errors) > 0) {
			$view->setMessage(JText::_('COM_EASYSOCIAL_REGISTRATION_FORM_ERROR_PROCEED_WITH_REGISTRATION'), SOCIAL_MSG_ERROR);

			return $view->call(__FUNCTION__);
		}

		// Load up the registration model
		$model 	= FD::model( 'Registration' );
		$user	= $model->createUser($registration);

		if (!$user) {
			$view->setMessage( $model->getError() , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Redirection will be dependent on the profile type's registration behavior.
		// If the profile type is auto login, we need to log the user in
		$profile 		= FD::table( 'Profile' );
		$profile->load($profileId);

		// Force unset on the user first to reload the user object
		SocialUser::$userInstances[$user->id] = null;

		// Get the current registered user data.
		$my 		= FD::user( $user->id );

		// We need to send the user an email with their password
		$my->password_clear	= $user->password_clear;

		// Send notification to admin if necessary.
		if ($profile->getParams()->get('email.moderators', true)) {
			$model->notifyAdmins($data, $my, $profile);
		}

		// If everything goes through fine, we need to send notification emails out now.
		$model->notify( $data , $my , $profile );

		// add new registered user into indexer
		$my->syncIndex();

		// We need to log the user in after they have successfully registered.
		if( $profile->getRegistrationType() == 'auto' )
		{
			// @points: user.register
			// Assign points when user registers on the site.
			$points = FD::points();
			$points->assign( 'user.registration' , 'com_easysocial' , $my->id );

			// @badge: registration.create
			// Assign badge for the person that initiated the friend request.
			$badge 	= FD::badges();
			$badge->log( 'com_easysocial' , 'registration.create' , $my->id , JText::_( 'COM_EASYSOCIAL_REGISTRATION_BADGE_REGISTERED' ) );

			// Get configuration object.
			$config 	= FD::config();

			// Add activity logging when a uer registers on the site.
			if( $config->get( 'registrations.stream.create' ) )
			{
				$stream				= FD::stream();
				$streamTemplate		= $stream->getTemplate();

				// Set the actor
				$streamTemplate->setActor( $my->id , SOCIAL_TYPE_USER );

				// Set the context
				$streamTemplate->setContext( $my->id , SOCIAL_TYPE_PROFILES );

				// Set the verb
				$streamTemplate->setVerb( 'register' );

				$streamTemplate->setSiteWide();

				$streamTemplate->setAccess( 'core.view' );


				// Add stream template.
				$stream->add( $streamTemplate );
			}

			$app 			= JFactory::getApplication();

			$credentials	= array( 'username' => $my->username , 'password' => $my->password_clear );

			// Try to log the user in
			$app->login( $credentials );

			// TODO: Trigger the apps to check if fields are complete
			// If not complete then we call view to redirect this user to the edit profile page
			// $view->setMessage(JText::_('COM_EASYSOCIAL_REGISTRATIONS_COMPLETE_REGISTRATION'), SOCIAL_MSG_INFO);
		}

		// Store the user's custom fields data now.
		return $view->complete( $my , $profile );
	}

	/**
	 * Processes quick registrations
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function miniRegister()
	{
		FD::checkToken();

		// Get current user's info
		$session = JFactory::getSession();

		// Get necessary info about the current registration process.
		$registration		= FD::table( 'Registration' );
		$registration->load( $session->getId() );

		// Get a new registry object
		$registry = FD::get( 'Registry' );

		if (!empty($registration->values)) {
			$registry->load( $registration->values );
		}

		// Load json library
		$json	= FD::json();

		// Get the token string
		$token	= FD::token();

		// Get post values
		$post	= JRequest::get( 'POST' );

		// Keys to exclude
		$exclude = array( $token, 'option', 'controller', 'task' );

		// Process $_POST vars
		foreach ($post as $key => $value) {

			if (!in_array($key, $exclude)) {

				if (is_array($value)) {
					$value  = $json->encode( $value );
				}

				$registry->set($key, $value);
			}
		}

		$profileModel 	= FD::model('profiles');
		$totalProfiles 	= $profileModel->getTotalProfiles();

		$config			= FD::config();

		$minimode		= $config->get('registrations.mini.mode', 'quick');
		$miniprofile	= $config->get('registrations.mini.profile', 'default');

		// Might be coming from module, in which we have to respect module settings
		if (isset($post['modRegisterType']) && isset($post['modRegisterProfile'])) {
			$minimode = $post['modRegisterType'];
			$miniprofile = $post['modRegisterProfile'];
		}

		$profileId = 0;

		// If selected profile is default, then we check how many profiles are there
		if ($miniprofile === 'default') {

			// If only 1 profile is found, then we assign it directly
			// if ($totalProfiles == 1) {
			// 	$profileId = $profileModel->getDefaultProfile()->id;
			// }

			// We no longer allow the ability for user to select profile
			// This is because the rendered field might be different from user selected profile
			// Under that case, the mapping of the fields will be off and unable to validate/store accordingly
			// EG. Profile 1 has a password field with id 3, while Profile 2 has a password field id 5, if the rendered field is 3, but user selected profile 2, validation will fail because of field mismatch
			// Hence if the settings is set to default profile, then we always use default profile
			$profileId = $profileModel->getDefaultProfile()->id;

		} else {
			$profileId = $miniprofile;
		}

		if (!empty($profileId)) {
			// Set the profile id directly
			$registration->profile_id = $profileId;
			$registry->set('profile_id', $profileId);

			// Directly set the registration step as 1
			$registration->step = 1;
			$registration->addStepAccess(1);
		}

		$registration->values = $registry->toString();

		$state = $registration->store();

		$view = $this->getCurrentView();

		// Decide what to do here based on the configuration
		// FULL -> Registration page, registration page then decides if there is 1 or more profile to choose
		// QUICK && profile id assigned -> quickRegistration
		// QUICK && no profile id -> Registration page with parameter quick=1

		// If mode is set to full, then we redirect to registration page
		if ($minimode === 'full') {
			$view->setMessage(JText::_('COM_EASYSOCIAL_REGISTRATIONS_COMPLETE_REGISTRATION'), SOCIAL_MSG_INFO);

			return $view->call('fullRegister', $profileId);
		}

		if ($minimode === 'quick') {
			if (empty($profileId)) {
				return $view->call('selectProfile');
			} else {
				return $this->quickRegister();
			}
		}

		return $view->call(__FUNCTION__);
	}
}
