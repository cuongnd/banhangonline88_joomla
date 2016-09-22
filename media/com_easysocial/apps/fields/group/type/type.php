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
FD::import( 'fields:/user/textarea/textarea' );

class SocialFieldsGroupType extends SocialFieldItem
{
	/**
	 * Displays the sample html codes when the field is added into the profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array
	 * @param	SocialTableRegistration
	 * @return	string	The html output.
	 */
	public function onSample()
	{
		return $this->display();
	}

	/**
	 * Displays the field input for user when they register their account.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array	The post data
	 * @param	SocialTableRegistration
	 * @return	string	The html output.
	 */
	public function onRegister( &$post )
	{
		$config = FD::config();

		return $this->display();
	}

	/**
	 * Displays the field input for user when they register their account.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array	The post data
	 * @param	SocialTableRegistration
	 * @return	string	The html output.
	 *
	 */
	public function onEdit( &$post , &$group )
	{
		$this->set('node', $group);
		$this->set('group' , $group );

		return $this->display();
	}

	/**
	 * Executes before the group is created
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onEditBeforeSave( &$data , &$group )
	{
		$type 	= isset( $data[ 'group_type' ] ) ? $data[ 'group_type' ] : 1;

		// Set the title on the group
		$group->type 	= $type;
	}

	/**
	 * Executes before the group is created
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onRegisterBeforeSave( &$data , &$group )
	{
		$type 	= isset( $data[ 'group_type' ] ) ? $data[ 'group_type' ] : 1;

		// Set the title on the group
		$group->type 	= $type;
	}
}
