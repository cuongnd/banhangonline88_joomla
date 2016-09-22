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

// Include the fields library
FD::import( 'admin:/includes/fields/dependencies' );

/**
 * Field application for Birthday
 *
 * @since	1.0
 * @author	Jason Rey <jasonrey@stackideas.com>
 */
class SocialFieldsUserRecaptcha extends SocialFieldItem
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Displays the sample html codes when the field is added into the profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array
	 * @param	SocialTableRegistration
	 * @return	string	The html output.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onSample()
	{
		$captcha 	= $this->getRecaptcha();

		$this->set( 'captcha'	, $captcha );

		return $this->display();
	}

	/**
	 * Determines if recaptcha has been configured
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool
	 */
	private function isCaptchaConfigured()
	{
		$params 	= $this->field->getApp()->getParams();
		$private 	= $params->get( 'private' );
		$public 	= $params->get( 'public' );

		if( !empty( $private ) && !empty( $public ) )
		{
			return true;
		}

		return false;
	}

	/**
	 * Retrieves the recaptcha library
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRecaptcha()
	{
		$app		= $this->field->getApp();
		$params 	= $app->getParams();

		$options 	= array(
							'public'	=> $params->get('public'),
							'private'	=> $params->get('private'),
							'ssl' 		=> $params->get('ssl'),
							'theme'		=> $params->get('theme'),
							'language'  => $params->get('language')
						);

		$captcha	= FD::get( 'Captcha' , 'Recaptcha' , $options );

		return $captcha;
	}

	/**
	 * Determines if the user has already validated with recaptcha
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasValidated( &$post )
	{
		$validated	= isset( $post[ $this->inputName ] ) ? $post[ $this->inputName ] : false;

		return $validated;
	}

	/**
	 * Displays the field input for user when they edit their account.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array
	 * @param	SocialTableRegistration
	 * @return	string	The html output.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onEdit( &$post, &$user, $errors )
	{
		// Check if recaptcha has been configured
		if( !$this->isCaptchaConfigured() )
		{
			return;
		}

		// The key for this element.
		$key 	= SOCIAL_FIELDS_PREFIX . $this->field->id;

		if( $this->hasValidated( $post ) )
		{
			return;
		}

		// Check for errors
		$error = $this->getError( $errors );

		// Get the captcha library.
		$captcha 	= $this->getRecaptcha();

		// Output to the template
		$this->set( 'captcha'	, $captcha );
		$this->set( 'error'		, $error );

		// Display the output.
		return $this->display();
	}

	/**
	 * Displays the field input for user when they register their account.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array
	 * @param	SocialTableRegistration
	 * @return	string	The html output.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onRegister( &$post , &$registration )
	{
		// Check if recaptcha has been configured
		if( !$this->isCaptchaConfigured() )
		{
			return;
		}

		if( $this->hasValidated( $post ) )
		{
			return;
		}

		// Check for errors
		$error		= $registration->getErrors( $this->inputName );
		$captcha	= $this->getRecaptcha();

		$this->set( 'error'		, $error );
		$this->set( 'captcha'	, $captcha );

		// Display the output.
		return $this->display();
	}

	/**
	 * Determines whether there's any errors in the submission in the registration form.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @param	SocialTableRegistration		The registration ORM table.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onRegisterValidate( &$post, &$registration )
	{
		return $this->validateCaptcha( $post );
	}

	/**
	 * Performs validation when a user updates their profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @param	SocialTableRegistration		The registration ORM table.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onEditValidate( &$post )
	{
		$state 	= $this->validateCaptcha( $post );

		return $state;
	}

	/**
	 * Performs validation of captcha text
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function validateCaptcha( &$post )
	{
		if( !$this->field->isRequired() || $this->hasValidated( $post ) )
		{
			return true;
		}

		$challenge 	= JRequest::getVar( 'recaptcha_challenge_field' , '' );
		$response 	= JRequest::getVar( 'recaptcha_response_field' , '' );

		if( empty( $response ) )
		{
			return $this->setError( JText::_( 'PLG_FIELDS_RECAPTCHA_VALIDATION_PLEASE_ENTER_CAPTCHA_RESPONSE' ) );
		}

		$captcha 	= $this->getRecaptcha();
		$state 		= $captcha->checkAnswer( $challenge , $response );

		if( !$state )
		{
			return $this->setError( JText::_( 'PLG_FIELDS_RECAPTCHA_VALIDATION_INVALID_RESPONSE' ) );
		}


		// Set a valid response to the registration object.
		$post[ $this->inputName ]	= true;

		return true;
	}

}
