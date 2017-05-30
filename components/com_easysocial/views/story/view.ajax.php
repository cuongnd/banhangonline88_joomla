<?php
/**
* @package		Social
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

FD::import( 'site:/views/views' );

class EasySocialViewStory extends EasySocialSiteView
{
	/**
	 * Post processes after a user submits a story.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function create($streamTable = '')
	{
		// Only logged in users allowed here
		FD::requireLogin();

		$ajax 		= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		$stream 	= FD::stream();
		$stream->getItem( $streamTable->uid, true );

		$output 	= $stream->html();

		return $ajax->resolve( $output, $streamTable->uid );
	}

	/**
	 * Post processes after a user submits a story.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function update($streamTable = '')
	{
		// Only logged in users allowed here
		FD::requireLogin();

		$ajax 		= FD::ajax();

		if ($this->hasErrors()) {
			return $ajax->reject( $this->getMessage() );
		}

		$stream 	= FD::stream();
		$stream->getItem($streamTable->uid);

		$output 	= $stream->html(false, '', array('contentOnly' => true));

		return $ajax->resolve($output, $streamTable->uid);
	}
}
