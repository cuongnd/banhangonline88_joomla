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

class EasySocialControllerLocation extends EasySocialController
{
	/**
	 * Delete's the location from the database.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function delete()
	{
		// Check for valid token
		FD::checkToken();

		// Guest users shouldn't be allowed to delete any location at all.
		FD::requireLogin();

		$my 	= FD::user();
		$id 	= JRequest::getInt( 'id' );
		$view 	= FD::getInstance( 'View' , 'Location' , false );

		$location 	= FD::table( 'Location' );
		$location->load( $id );

		// If id is invalid throw errors.
		if( !$location->id )
		{
			$view->setErrors( JText::_( 'COM_EASYSOCIAL_LOCATION_INVALID_ID' ) );
		}

		// If user do not own item, throw errors.
		if( $my->id !== $location->user_id )
		{
			$view->setErrors( JText::_( 'COM_EASYSOCIAL_LOCATION_ERROR_YOU_ARE_NOT_OWNER' ) );
		}

		// Try to delete location.
		if( !$location->delete() )
		{
			$view->setErrors( $location->getError() );
		}

		return $view->delete();
	}
}
