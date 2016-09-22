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

// Include helper file.
FD::import( 'fields:/user/permalink/helper' );

/**
 * Processes ajax calls for the permalink field.
 *
 * @since	1.0
 * @author	Jason Rey <jasonrey@stackideas.com>
 */
class SocialFieldsUserPermalink extends SocialFieldItem
{
	/**
	 * Validates the username.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	JSON	A jsong encoded string.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function isValid()
	{
		// Render the ajax lib.
		$ajax 	= FD::ajax();

		// Get the userid
		$userid	= JRequest::getInt( 'userid', 0 );

		// Set the current username
		$current	= '';

		if( !empty( $userid ) )
		{
			$user		= FD::user( $userid );
			$current	= $user->permalink;
		}

		// Get the provided permalink
		$permalink = JRequest::getVar( 'permalink' , '' );

		// Check if the field is required
		if( !$this->field->isRequired() && empty( $permalink ))
		{
			return true;
		}

		// Check if the permalink provided is valid
		if( !SocialFieldsUserPermalinkHelper::valid( $permalink, $this->params ) )
		{
			return $ajax->reject( JText::_( 'PLG_FIELDS_PERMALINK_INVALID_PERMALINK' ) );
		}

		// Test if permalink exists
		if( SocialFieldsUserPermalinkHelper::exists( $permalink, $current ) )
		{
			return $ajax->reject( JText::_( 'PLG_FIELDS_PERMALINK_NOT_AVAILABLE' ) );
		}

		$text		= JText::_( 'PLG_FIELDS_PERMALINK_AVAILABLE' );

		return $ajax->resolve( $text );
	}
}
