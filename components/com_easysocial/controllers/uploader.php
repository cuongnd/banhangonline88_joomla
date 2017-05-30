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

class EasySocialControllerUploader extends EasySocialController
{

	/**
	 * Responsible to handle temporary file uploads. This is useful for services that may want
	 * to upload files before their real items are created.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function uploadTemporary()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Only registered users are allowed here.
		FD::requireLogin();

		// Get the view.
		$view 	= $this->getCurrentView();

		$type 	= JRequest::getVar( 'type' );

		$config = FD::config();
		$limit 	= $config->get( $type . '.attachments.maxsize' );

		// Set uploader options
		$options = array(
			'name'        => 'file',
			'maxsize' => $limit . 'M'
		);

		// Get uploaded file
		$data	= FD::uploader( $options )->getFile();

		// If there was an error getting uploaded file, stop.
		if ($data instanceof SocialException)
		{
			$view->setMessage($data);
			return $view->call(__FUNCTION__);
		}

		if( !$data )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_UPLOADER_FILE_DID_NOT_GET_UPLOADED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Get the current logged in user.
		$my 	= FD::user();

		// Let's get the temporary uploader table.
		$uploader 			= FD::table( 'Uploader' );
		$uploader->user_id	= $my->id;

		// Pass uploaded data to the uploader.
		$uploader->bindFile( $data );

		$state 	= $uploader->store();

		if( !$state )
		{
			$view->setMessage( $uploader->getError() , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ , $uploader );
		}

		return $view->call( __FUNCTION__ , $uploader );
	}

	/**
	 * Deletes a file from the system.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function delete()
	{
		// Check for request forgeries
		FD::checkToken();

		// Only logged in users are allowed to delete anything
		FD::requireLogin();

		// Get the current view
		$view	= $this->getCurrentView();

		// Get the current user
		$my 	= FD::user();

		// Get the uploader id
		$id 	= JRequest::getInt( 'id' );

		$uploader	= FD::table( 'Uploader' );
		$uploader->load( $id );

		// Check if the user is really permitted to delete the item
		if( !$id || !$uploader->id || $uploader->user_id != $my->id )
		{
			return $view->call( __FUNCTION__ );
		}

		$state 	= $uploader->delete();

		// If deletion fails, silently log the error
		if( !$state )
		{
			FD::logError( __FILE__ , __LINE__ , JText::_( 'UPLOADER: Unable to delete the item [' . $uploader->id . '] because ' . $uploader->getError() ) );
		}

		return $view->call( __FUNCTION__ );
	}

}
