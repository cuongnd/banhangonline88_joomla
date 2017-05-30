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
FD::import('site:/controllers/controller');

class EasySocialControllerAccount extends EasySocialController
{
	/**
	 * Processes username reminder
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function remindUsername()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view 	= $this->getCurrentView();

		// Get the current logged in user.
		$my 	= FD::user();

		if( $my->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILE_YOU_ARE_ALREADY_LOGGED_IN' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Get the email address
		$email 	= JRequest::getVar( 'es-email' );

		$model 	= FD::model( 'Users' );
		$state	= $model->remindUsername( $email );

		if( !$state )
		{
			$view->setMessage( $model->getError() , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		$view->setMessage( JText::sprintf( 'COM_EASYSOCIAL_PROFILE_USERNAME_SENT' , $email ) );
		return $view->call( __FUNCTION__ );
	}


	/**
	 * Processes username reminder
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function remindPassword()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view 	= $this->getCurrentView();

		// Get the current logged in user.
		$my 	= FD::user();

		if ($my->id) {
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILE_YOU_ARE_ALREADY_LOGGED_IN' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Get the email address
		$email 	= JRequest::getVar( 'es-email' );

		$model 	= FD::model( 'Users' );
		$state	= $model->remindPassword($email);

		if( !$state )
		{
			$view->setMessage($model->getError(), SOCIAL_MSG_ERROR);

			return $view->call(__FUNCTION__);
		}

		$view->setMessage( JText::sprintf( 'COM_EASYSOCIAL_PROFILE_USERNAME_SENT' , $email ) );
		return $view->call( __FUNCTION__ );
	}

	/**
	 * Password reset confirmation
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmResetPassword()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view 	= $this->getCurrentView();

		// Get the current logged in user.
		$my 	= FD::user();

		if( $my->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILE_YOU_ARE_ALREADY_LOGGED_IN' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		$username 	= JRequest::getVar( 'es-username' );
		$code 		= JRequest::getVar( 'es-code' );

		$model 	= FD::model( 'Users' );
		$state	= $model->verifyResetPassword( $username , $code );

		if( !$state )
		{
			$view->setMessage( $model->getError() , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		return $view->call( __FUNCTION__ );
	}

	/**
	 * Completes password reset
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function completeResetPassword()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view 	= $this->getCurrentView();

		// Get the current logged in user.
		$my 	= FD::user();

		if( $my->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILE_YOU_ARE_ALREADY_LOGGED_IN' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		$password 		= JRequest::getVar( 'es-password' );
		$password2 		= JRequest::getVar( 'es-password2' );

		// Check if the password matches
		if( $password != $password2 )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILE_REMIND_PASSWORD_PASSWORDS_NOT_MATCHING' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		$model 	= FD::model( 'Users' );
		$state	= $model->resetPassword( $password , $password2 );

		if( !$state )
		{
			$view->setMessage( $model->getError() , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ );
		}

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILE_REMIND_PASSWORD_SUCCESSFUL' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ );
	}

	/**
	 * Replicate's Joomla login behavior
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function login()
	{
		JSession::checkToken('post') or jexit(JText::_('JInvalid_Token'));

		$app = JFactory::getApplication();

		// Populate the data array:
		$data = array();
		$data['return']		= base64_decode($app->input->post->get('return', '', 'BASE64'));
		$data['username']	= JRequest::getVar('username', '', 'method', 'username');
		$data['password']	= JRequest::getString('password', '', 'post', JREQUEST_ALLOWRAW);
		$data['secretkey']	= JRequest::getString('secretkey', '');

		// Get the user's state because there could be instances where Joomla is redirecting users
		$tmp 	= $app->getUserState('users.login.form.data');

		if (isset($tmp['return']) && !empty($tmp['return'])) {
			$data['return']	= $tmp['return'];
		}

		// Set the return URL if empty.
		if (empty($data['return']))
		{
			$data['return'] = 'index.php?option=com_easysocial&view=login';
		}

		// Set the return URL in the user state to allow modification by plugins
		$app->setUserState('users.login.form.return', $data['return']);

		// Get the log in options.
		$options = array();
		$options['remember'] = JRequest::getBool('remember', false);
		$options['return'] = $data['return'];

		// Silent! Kill you!
		$options['silent']	= true;

		// Get the log in credentials.
		$credentials = array();
		$credentials['username']  = $data['username'];
		$credentials['password']  = $data['password'];
		$credentials['secretkey'] = $data['secretkey'];


		// Perform the log in.
		if (true === $app->login($credentials, $options))
		{
			// Set the remember state
			if ($options['remember'] == true)
			{
				$app->setUserState('rememberLogin', true);
			}

			// Success
			$app->setUserState('users.login.form.data', array());

			// Redirect link should use the return data instead of relying it on getUserState('users.login.form.return')
			// Because EasySocial has its own settings of login redirection, hence this should respect the return link passed
			// We cannot fallback because the return link needs to be set in the options before calling login, and as such, the fallback has been set before calling $app->login, and no fallback is needed here.
			$app->redirect(JRoute::_($data['return'], false));
		}
		else
		{
			// Login failed !
			$data['remember'] = (int) $options['remember'];
			$app->setUserState('users.login.form.data', $data);

			$returnFailed 	= base64_decode($app->input->post->get('returnFailed', '', 'BASE64'));

			if( empty( $returnFailed ) )
			{
				$returnFailed 	= FRoute::login( array() , false );
			}

			FD::info()->set( null , JText::_( 'JGLOBAL_AUTH_INVALID_PASS' ) , SOCIAL_MSG_ERROR );
			$app->redirect( $returnFailed );
		}
	}

	/**
	 * Replicate Joomla's logout behavior
	 *
	 * @since   1.6
	 */
	public function logout()
	{
		JSession::checkToken('request') or jexit(JText::_('JInvalid_Token'));

		$app = JFactory::getApplication();

		// Perform the log in.
		$error = $app->logout();

		// Check if the log out succeeded.
		if (!($error instanceof Exception))
		{
			// Get the return url from the request and validate that it is internal.
			$return = JRequest::getVar('return', '', 'method', 'base64');
			$return = base64_decode($return);
			if (!JUri::isInternal($return))
			{
				$return = '';
			}

			// Redirect the user.
			$app->redirect(JRoute::_($return, false));
			$app->close();
		}

		$app->redirect(FRoute::login(array(), false));
	}


	/**
	 * Determines if the view should be visible on lockdown mode
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	public function isLockDown( $task )
	{
		$allowed 	= array( 'login' , 'confirmResetPassword' , 'completeResetPassword' , 'remindPassword' , 'remindUsername' );

		if( in_array( $task , $allowed ) )
		{
			return false;
		}

		return true;
	}
}
