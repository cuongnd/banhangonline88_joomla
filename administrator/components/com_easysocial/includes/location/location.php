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

class SocialLocation
{
	/**
	 * The location table mapping
	 * @var SocialTableLocation
	 */
	private $table 	= null;

	/**
	 * Clas constructor
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function __construct( $uid = null , $type = null )
	{
		$this->table	= FD::table( 'Location' );

		if( !is_null( $uid ) && !is_null( $type ) )
		{
			$this->table->load( array( 'uid' => $uid , 'type' => $type ) );
		}
	}

	/**
	 * Location factory
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function factory( $uid = null , $type = null )
	{
		$obj 	= new self( $uid , $type );

		return $obj;
	}

	/**
	 * Creates a new location record
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function create( $uid , $type , $userId , $options = array() )
	{
		$address 	= isset( $options[ 'address' ] ) ? $options[ 'address' ] : JRequest::getVar( 'address' , '' );
		$latitude 	= isset( $options[ 'latitude' ] ) ? $options[ 'latitude' ] : JRequest::getVar( 'latitude' , '' );
		$longitude	= isset( $options[ 'longitude' ] ) ? $options[ 'longitude' ] : JRequest::getVar( 'longitude' , '' );

		$location 	= FD::table( 'Location' );
		$location->load( array( 'uid' => $uid , 'type' => $type ) );

		$location->user_id 		= $userId;
		$location->address 		= $address;
		$location->longitude	= $longitude;
		$location->latitude		= $latitude;
		$location->uid 			= $uid;
		$location->type 		= $type;

		$state 	= $location->store();

		return $state;
	}

	/**
	 * Retrieves the address
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAddress( $uid = null , $type = null )
	{
		if( !is_null( $uid ) && !is_null( $type ) )
		{
			$this->table->load( array( 'uid' => $uid , 'type' => $type ) );
		}

		return $this->table->getAddress();
	}
}
