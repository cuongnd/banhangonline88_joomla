<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'table.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR .'helper.php' );

class EasyBlogTableBloggerSubscription extends EasyBlogTable
{
	/*
	 * The id of the subscription
	 * @var int
	 */
	var $id 					= null;

	/*
	 * blog id
	 * @var int
	 */
	var $blogger_id		        = null;

	/*
	 * site member id (optional)
	 * @var string
	 */
	var $user_id				= null;

	/*
	 * subscriber email
	 * @var string
	 */
	var $email					= null;

	/*
	 * subscriber name (optional)
	 * @var string
	 */
	var $fullname				= null;

	/*
	 * Created datetime of the tag
	 * @var datetime
	 */
	var $created				= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db )
	{
		parent::__construct( '#__easyblog_blogger_subscription' , 'id' , $db );
	}

	public function store($updateNulls = false)
	{
		$isNew  = ( empty( $this->id ) ) ? true : false;
	    $state	= parent::store();

		if( $state && $isNew)
		{
			$profile   = EasyBlogHelper::getTable('Profile');
			$profile->load( $this->blogger_id );

			$obj    = new stdClass();
			$obj->bloggername		= $profile->getName();
			$obj->subscribername 	= $this->fullname;
			$obj->subscriberemail 	= $this->email;


			$activity   = new stdClass();
			$activity->actor_id		= ( empty( $this->user_id ) ) ? '0' : $this->user_id;
			$activity->target_id	= $this->blogger_id;
			$activity->context_type	= 'blogger';
			$activity->context_id	= $this->blogger_id;
			$activity->verb         = 'subscribe';
			$activity->source_id    = $this->id;
			$activity->uuid    		= serialize( $obj );


			EasyBlogHelper::activityLog( $activity );
		}

		return $state;
	}

}
