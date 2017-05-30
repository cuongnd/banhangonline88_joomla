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
defined( 'JPATH_BASE' ) or die( 'Unauthorized Access' );

FD::import( 'admin:/tables/table' );

class SocialTableLink extends SocialTable
{
	/**
	 * The unique id
	 * @var int
	 */
	public $id			= null;

	/**
	 * The unique url string (MD5-ed)
	 * @var string
	 */
	public $hash 		= null;

	/**
	 * Stores the data about the URL in JSON format
	 * @var string
	 */
	public $data 		= null;

	/**
	 * The time this url was logged
	 * @var datetime
	 */
	public $created 	= null;

	public function __construct( $db )
	{
		parent::__construct( '#__social_links' , 'id' , $db);
	}

}
