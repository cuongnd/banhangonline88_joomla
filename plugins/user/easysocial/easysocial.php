<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2012 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

/**
 * User plugin that performs necessary clean up on users.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class plgUserEasySocial extends JPlugin
{
	protected $app;
	protected $db;
	public function onUserLogout($user, $options = array())
	{
		$my = JFactory::getUser();
		$session = JFactory::getSession();
		// Make sure we're a valid user first
		if ($user['id'] == 0 && !$my->get('tmp_user')) {
			return true;
		}
		// Check to see if we're deleting the current session
		if ($my->id == $user['id'] && $options['clientid'] == $this->app->getClientId()) {
			// Hit the user last visit field
			$my->setLastVisit();
			// Destroy the php session for this user
			$session->destroy();
		}
		// Enable / Disable Forcing logout all users with same userid
		$forceLogout = $this->params->get('forceLogout', 1);
		if ($forceLogout) {
			$query = $this->db->getQuery(true)
				->delete($this->db->quoteName('#__session'))
				->where($this->db->quoteName('userid') . ' = ' . (int)$user['id'])
				->where($this->db->quoteName('client_id') . ' = ' . (int)$options['clientid']);
			try {
				$this->db->setQuery($query)->execute();
			} catch (RuntimeException $e) {
				return false;
			}
		}
		// Delete "user state" cookie used for reverse caching proxies like Varnish, Nginx etc.
		$conf = JFactory::getConfig();
		$cookie_domain = $conf->get('cookie_domain', '');
		$cookie_path = $conf->get('cookie_path', '/');
		if ($this->app->isSite()) {
			$this->app->input->cookie->set("joomla_user_state", "", time() - 86400, $cookie_path, $cookie_domain, 0);
		}
		return true;
	}

	/**
	 * Triggered when user logs into the site
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function onUserLogin($user, $options = array())
	{
		$instance = $this->_getUser($user, $options);
		// If _getUser returned an error, then pass it back.
		if ($instance instanceof Exception) {
			return false;
		}
		// If the user is blocked, redirect with an error
		if ($instance->block == 1) {
			$this->app->enqueueMessage(JText::_('JERROR_NOLOGIN_BLOCKED'), 'warning');
			return false;
		}
		// Authorise the user based on the group information
		if (!isset($options['group'])) {
			$options['group'] = 'USERS';
		}
		// Check the user can login.
		$result = $instance->authorise($options['action']);
		if (!$result) {
			$this->app->enqueueMessage(JText::_('JERROR_LOGIN_DENIED'), 'warning');
			return false;
		}
		// Mark the user as logged in
		$instance->guest = 0;
		$session = JFactory::getSession();
		// Grab the current session ID
		$oldSessionId = $session->getId();
		// Fork the session
		$session->fork();
		$session->set('user', $instance);
		// Ensure the new session's metadata is written to the database
		$this->app->checkSession();
		// Purge the old session
		$query = $this->db->getQuery(true)
			->delete('#__session')
			->where($this->db->quoteName('session_id') . ' = ' . $this->db->quote($oldSessionId));
		try {
			$this->db->setQuery($query)->execute();
		} catch (RuntimeException $e) {
			// The old session is already invalidated, don't let this block logging in
		}
		// Hit the user last visit field
		$instance->setLastVisit();
		// Add "user state" cookie used for reverse caching proxies like Varnish, Nginx etc.
		$conf = JFactory::getConfig();
		$cookie_domain = $conf->get('cookie_domain', '');
		$cookie_path = $conf->get('cookie_path', '/');
		if ($this->app->isSite()) {
			$this->app->input->cookie->set("joomla_user_state", "logged_in", 0, $cookie_path, $cookie_domain, 0);
		}
		return true;
	}
	protected function _getUser($user, $options = array())
	{
		$instance = JUser::getInstance();
		$id = (int)JUserHelper::getUserId($user['username']);
		if ($id) {
			$instance->load($id);
			return $instance;
		}
		// TODO : move this out of the plugin
		$config = JComponentHelper::getParams('com_users');
		// Hard coded default to match the default value from com_users.
		$defaultUserGroup = $config->get('new_usertype', 2);
		$instance->id = 0;
		$instance->name = $user['fullname'];
		$instance->username = $user['username'];
		$instance->password_clear = $user['password_clear'];
		// Result should contain an email (check).
		$instance->email = $user['email'];
		$instance->groups = array($defaultUserGroup);
		// If autoregister is set let's register the user
		$autoregister = isset($options['autoregister']) ? $options['autoregister'] : $this->params->get('autoregister', 1);
		if ($autoregister) {
			if (!$instance->save()) {
				JLog::add('Error in autoregistration for user ' . $user['username'] . '.', JLog::WARNING, 'error');
			}
		} else {
			// No existing user and autoregister off, this is a temporary user.
			$instance->set('tmp_user', true);
		}
		return $instance;
	}

	/**
	 * Performs various clean ups when a user is deleted
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onUserBeforeDelete( $user )
	{
		// Include main file.
		jimport( 'joomla.filesystem.file' );

		$path 	= JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php';

		if( !JFile::exists( $path ) )
		{
			return false;
		}

		// Include the foundry engine
		require_once( $path );

		$success 	= true;

		// Check if Foundry exists
		if( !Foundry::exists() )
		{
			Foundry::language()->loadSite();
			echo JText::_( 'COM_EASYSOCIAL_FOUNDRY_DEPENDENCY_MISSING' );
			return;
		}

		if( !$success )
		{
			return false;
		}

		$model 	= Foundry::model( 'Users' );

		$state 	= $model->delete( $user[ 'id' ] );

		if( !$state )
		{
			Foundry::logError( __FILE__ , __LINE__ , 'USER_PLUGIN: Error when deleting a user' );
		}

		// Internal Trigger for onUserBeforeDelete
		$dispatcher 	= Foundry::dispatcher();
		$args 			= array( &$user );

		$dispatcher->trigger( SOCIAL_APPS_GROUP_USER, __FUNCTION__ , $args );

		return true;
	}

	public function onUserAfterSave($user, $isnew, $success, $msg)
	{
		if( $isnew )
		{
			// Initialise EasySocial's Foundry Framework

			// Include main file.
			jimport( 'joomla.filesystem.file' );

			$path 	= JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php';

			if( !JFile::exists( $path ) )
			{
				return false;
			}

			// Include the foundry engine
			require_once( $path );

			$success 	= true;

			// Check if Foundry exists
			if( !Foundry::exists() )
			{
				Foundry::language()->loadSite();
				echo JText::_( 'COM_EASYSOCIAL_FOUNDRY_DEPENDENCY_MISSING' );
				return;
			}

			if( !$success )
			{
				return false;
			}

			// Things that need to do here
			// 1. Insert user record into #__social_users
			// 2. Get the default profile
			// 3. Insert mapping into #__social_profiles_maps

			$userTable = Foundry::table( 'users' );
			$state = $userTable->load( $user['id'] );

			// If no user is found in #__social_users, then only we insert
			// If user is found, means the registration is coming from EasySocial itself.
			// The purpose here is to insert the user data if the registration is handled by other services
			if( !$state )
			{
				// Assign the user id
				$userTable->user_id = $user['id'];

				// Filter the username so that it becomes a valid alias
				$alias 			= JFilterOutput::stringURLSafe( $user['username'] );

				// Check if the alias exists.
				$userModel 			= Foundry::model( 'Users' );

				// Keep the original state of the alias
				$tmp 			= $alias;

				while( $userModel->aliasExists( $alias , $user['id'] ) )
				{
					// Generate a new alias for the user.
					$alias	= $tmp . '-' . rand( 1 , 150 );
				}

				$userTable->alias = $alias;

				$userTable->state = $user['block'] === SOCIAL_JOOMLA_USER_BLOCKED ? SOCIAL_USER_STATE_PENDING : SOCIAL_USER_STATE_ENABLED;

				$userTable->type = 'joomla';

				$userTable->store();

				$profileModel = Foundry::model( 'Profiles' );

				$defaultProfile = $profileModel->getDefaultProfile();

				if( $defaultProfile )
				{
					$defaultProfile->addUser( $user['id'] );
				}

				$controller = JRequest::getCmd('controller','');
				if ($controller != 'registration') {
					// if this user saving is coming from registration, then we dont add the user into finder. let the registration controller do the job.

					// Get the user object now
					$esUser = Foundry::user($user['id']);

					// Sync the index
					$esUser->syncIndex();
				}

			}
		}

		return true;
	}
}
