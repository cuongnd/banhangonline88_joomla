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

jimport('joomla.mail.helper');

FD::import( 'site:/controllers/controller' );

class EasySocialControllerSharing extends EasySocialController
{
	/**
	 * Sends a new share to a user.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function send()
	{
		FD::checkToken();

		$token		= JRequest::getString( 'token', '' );
		$recipients	= JRequest::getVar( 'recipients', array() );
		$content	= JRequest::getVar( 'content', '' );

		// Get the current view.
		$view		= $this->getCurrentView();

		// Cleaning
		if( is_string( $recipients ) )
		{
			$recipients = explode( ',', FD::string()->escape( $recipients ) );
		}

		if( is_array( $recipients ) )
		{
			foreach( $recipients as &$recipient )
			{
				$recipient = FD::string()->escape( $recipient );

				if(!JMailHelper::isEmailAddress( $recipient ) )
				{
					return $view->call( __FUNCTION__, false, JText::_( 'COM_EASYSOCIAL_SHARING_EMAIL_INVALID_RECIPIENT' ) );
				}
			}
		}

		$content	= FD::string()->escape( $content );

		// Check for valid data
		if( empty( $recipients ) )
		{
			return $view->call( __FUNCTION__, false, JText::_( 'COM_EASYSOCIAL_SHARING_EMAIL_NO_RECIPIENTS' ) );
		}

		if( empty( $token ) )
		{
			return $view->call( __FUNCTION__, false, JText::_( 'COM_EASYSOCIAL_SHARING_EMAIL_INVALID_TOKEN' ) );
		}

		$session	= JFactory::getSession();

		$config		= FD::config();

		$limit		= $config->get( 'sharing.email.limit', 0 );

		$now		= FD::date()->toUnix();

		$time		= $session->get( 'easysocial.sharing.email.time' );

		$count		= $session->get( 'easysocial.sharing.email.count' );

		if( is_null( $time ) )
		{
			$session->set( 'easysocial.sharing.email.time', $now );
			$time = $now;
		}

		if( is_null( $count ) )
		{
			$session->set( 'easysocial.sharing.email.count', 0 );
		}

		$diff		= $now - $time;

		if( $diff <= 3600 )
		{
			if( $limit > 0 && $count >= $limit )
			{
				return $view->call( __FUNCTION__, false, JText::_( 'COM_EASYSOCIAL_SHARING_EMAIL_SHARING_LIMIT_MAXED' ) );
			}

			$count++;

			$session->set( 'easysocial.sharing.email.count', $count );
		}
		else
		{
			$session->set( 'easysocial.sharing.email.time', $now );
			$session->set( 'easysocial.sharing.email.count', 1 );
		}

		$library	= FD::get( 'Sharing' );

		$library->sendLink( $recipients, $token, $content );

		$view->call( __FUNCTION__, true );
	}

	// Debugging purposes
	// EasySocial.ajax('site/controllers/sharing/checkSession').done(function(time, diff, count, limit){console.log(time, diff, count, limit)})
	// public function checkSession()
	// {
	// 	$session = JFactory::getSession();

	// 	$ajax = FD::ajax();

	// 	$time = $session->get( 'easysocial.sharing.email.time' );
	// 	$count = $session->get( 'easysocial.sharing.email.count' );

	// 	$diff = FD::date()->toUnix() - $time;

	// 	$limit = FD::config()->get( 'sharing.email.limit' );

	// 	$ajax->resolve( $time, $diff, $count, $limit );
	// }
}
