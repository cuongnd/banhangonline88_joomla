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

class EasySocialViewSharing extends EasySocialSiteView
{
	/**
	 * Displays the share dialog
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function shareDialog()
	{
		$ajax		= FD::ajax();

		$url		= JRequest::getVar( 'url' );
		$title		= JRequest::getVar( 'title' );
		$summary	= JRequest::getVar( 'summary' );

		$sharing	= FD::get( 'Sharing' , array( 'url' => $url, 'title' => $title, 'summary' => $summary ) );
		$contents	= $sharing->getContents();

		return $ajax->resolve( $contents );
	}

	public function send( $state, $msg = '' )
	{
		if( $state )
		{
			FD::ajax()->resolve();
		}
		else
		{
			FD::ajax()->reject( $msg );
		}

		return true;
	}
}
