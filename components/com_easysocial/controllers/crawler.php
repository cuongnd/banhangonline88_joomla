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

class EasySocialControllerCrawler extends EasySocialController
{
	/**
	 * Does a remote call to the server to fetch contents of a given url.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function fetch()
	{
		// Check for request forgeries!
		$urls		= JRequest::getVar( 'urls' );

		// Ensure that the urls are in an array
		FD::makeArray( $urls );

		// Get the current view.
		$view 		= $this->getCurrentView();

		// Result placeholder
		$result 	= array();

		if( !$urls || empty( $urls ) )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_CRAWLER_INVALID_URL_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		$crawler 	= FD::get( 'Crawler' );

		foreach ($urls as $url) {
			$hash = md5($url);

			$link 		= FD::table( 'Link' );
			$exists = $link->load(array('hash' => $hash));

			// If it doesn't exist, store it.
			if( !$exists )
			{
				$crawler->crawl( $url );
				$data 			= $crawler->getData();
				
				$link->hash 	= $hash;
				$link->data		= FD::json()->encode( $data );

				// Store the new link
				$link->store();
			}

			$result[ $url ]	= FD::json()->decode( $link->data );
		}


		return $view->call( __FUNCTION__ , $result );
	}
}
