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

FD::import( 'site:/views/views' );

class EasySocialViewLogin extends EasySocialSiteView
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
		return false;
	}

	/**
	 * Responsible to display the generic login form via ajax
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function form( $tpl = null )
	{
		$ajax 	= FD::ajax();

		$my 	= FD::user();

		// If user is already logged in, they should not see this page.
		if( $my->id > 0 )
		{
			$this->setMessage( JText::_( 'COM_EASYSOCIAL_LOGIN_ALREADY_LOGGED_IN' ) , SOCIAL_MSG_ERROR );
			return $ajax->reject( $this->getMessage() );
		}

		// Facebook codes.
		$facebook 	= FD::oauth( 'Facebook' );

		// Get any callback urls.
		$return 	= FD::getCallback();

		// If return value is empty, always redirect back to the dashboard
		if( !$return )
		{
			$return	= FRoute::dashboard( array() , false );
		}

		// Determine if there's a login redirection
		$config 		= FD::config();
		$loginMenu 		= $config->get( 'general.site.login' );

		if( $loginMenu != 'null' )
		{
			$return 	= FD::get( 'toolbar' )->getRedirectionUrl( $loginMenu );
		}

		$return 	= base64_encode( $return );

		$this->set( 'return'	, $return );
		$this->set( 'facebook' 	, $facebook );

		if( $config->get( 'registrations.enabled' ) )
		{
			$profileId = $config->get('registrations.mini.profile', 'default');

			if ($profileId === 'default') {
				$profileId = Foundry::model( 'profiles' )->getDefaultProfile()->id;
			}


			$options = array(
				'visible' => SOCIAL_PROFILES_VIEW_MINI_REGISTRATION,
				'profile_id' => $profileId
			);

			$fieldsModel = FD::model('Fields');
			$fields = $fieldsModel->getCustomFields($options);

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

		$contents	= parent::display( 'site/login/dialog.login' );

		return $ajax->resolve( $contents );
	}
}
