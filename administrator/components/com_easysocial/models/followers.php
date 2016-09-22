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

jimport('joomla.application.component.model');

FD::import( 'admin:/includes/model' );

/**
 * Object mapping for lists.
 *
 * @author	Mark Lee <mark@stackideas.com>
 * @since	1.0
 */
class EasySocialModelFollowers extends EasySocialModel
{
	private $data			= null;

	function __construct()
	{
		parent::__construct( 'followers' );
	}

	/**
	 * Retrieve a list of followers
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalFollowers( $userId , $options = array() )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_subscriptions', 'a' );
		$sql->column( 'COUNT(1)' );
		$sql->where( 'a.type' , SOCIAL_TYPE_USER . '.' . SOCIAL_TYPE_USER  );
		$sql->where( 'a.uid' , $userId );

		$sql->join( '#__users' , 'uu' , 'INNER' );
		$sql->on( 'a.user_id' , 'uu.id' );

		if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			$sql->leftjoin( '#__social_block_users' , 'bus');
			$sql->on( 'uu.id' , 'bus.user_id' );
			$sql->on( 'bus.target_id', JFactory::getUser()->id );
			$sql->isnull('bus.id');
		}

		$sql->where( 'uu.block' , '0' );

		$db->setQuery( $sql );

		$total 	= $db->loadResult();

		return $total;
	}

	/**
	 * Retrieve a list of following items
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalFollowing( $userId , $options = array() )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_subscriptions', 'a' );
		$sql->column( 'COUNT(1)' );
		$sql->where( 'a.type' , SOCIAL_TYPE_USER . '.' . SOCIAL_TYPE_USER );
		$sql->where( 'a.user_id' , $userId );

		$sql->join( '#__users' , 'uu' , 'INNER' );
		$sql->on( 'a.uid' , 'uu.id' );

		if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			$sql->leftjoin( '#__social_block_users' , 'bus');
			$sql->on( 'uu.id' , 'bus.user_id' );
			$sql->on( 'bus.target_id', JFactory::getUser()->id );
			$sql->isnull('bus.id');
		}

		$sql->where( 'uu.block' , '0' );

		$db->setQuery( $sql );

		$total 	= $db->loadResult();

		return $total;
	}


	/**
	 * Retrieve a list of followers
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFollowers( $userId , $options = array() )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_subscriptions', 'a' );
		$sql->column('a.user_id', 'id');
		$sql->where( 'a.type' , SOCIAL_TYPE_USER . '.' . SOCIAL_TYPE_USER );
		$sql->where( 'a.uid' , $userId );

		$sql->join( '#__users' , 'uu' , 'INNER' );
		$sql->on( 'a.user_id' , 'uu.id' );

		if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			$sql->leftjoin( '#__social_block_users' , 'bus');
			$sql->on( 'uu.id' , 'bus.user_id' );
			$sql->on( 'bus.target_id', JFactory::getUser()->id );
			$sql->isnull('bus.id');
		}

		$sql->where( 'uu.block' , '0' );

		$limit 	= isset( $options[ 'limit' ] ) ? $options[ 'limit' ] : '';

		if( $limit != 0 )
		{
			$this->setState( 'limit' , $limit );

			// Get the limitstart.
			$limitstart 	= $this->getUserStateFromRequest( 'limitstart' , 0 );
			$limitstart 	= ( $limit != 0 ? ( floor( $limitstart / $limit ) * $limit ) : 0 );

			$this->setState( 'limitstart' , $limitstart );

			// Set the total number of items.
			$this->setTotal( $sql->getTotalSql() );

			// Get the list of users
			$rows 	= $this->getData( $sql->getSql() );

		}
		else
		{
			$db->setQuery( $sql );
			$rows 	= $db->loadObjectList();
		}

		if( !$rows )
		{
			return $rows;
		}

		$followers	= array();
		$ids 		= array();

		foreach ($rows as $row) {
			$ids[]	= $row->id;
		}

		$followers 	= FD::user($ids);

		return $followers;
	}

	/**
	 * Retrieve a list of followers
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFollowing( $userId , $options = array() )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_subscriptions', 'a' );
		$sql->column('a.uid', 'id');
		$sql->where( 'a.type' , SOCIAL_TYPE_USER . '.' . SOCIAL_TYPE_USER );
		$sql->where( 'a.user_id' , $userId );

		$sql->join( '#__users' , 'uu' , 'INNER' );
		$sql->on( 'a.uid' , 'uu.id' );

		if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			$sql->leftjoin( '#__social_block_users' , 'bus');
			$sql->on( 'uu.id' , 'bus.user_id' );
			$sql->on( 'bus.target_id', JFactory::getUser()->id );
			$sql->isnull('bus.id');
		}

		$sql->where( 'uu.block' , '0' );

		$limit 	= isset( $options[ 'limit' ] ) ? $options[ 'limit' ] : '';

		if( $limit != 0 )
		{
			$this->setState( 'limit' , $limit );

			// Get the limitstart.
			$limitstart 	= $this->getUserStateFromRequest( 'limitstart' , 0 );
			$limitstart 	= ( $limit != 0 ? ( floor( $limitstart / $limit ) * $limit ) : 0 );

			$this->setState( 'limitstart' , $limitstart );

			// Set the total number of items.
			$this->setTotal( $sql->getTotalSql() );

			// Get the list of users
			$rows 	= $this->getData( $sql->getSql() );
		}
		else
		{
			$db->setQuery( $sql );

			$rows 	= $db->loadObjectList();
		}

		if( !$rows )
		{
			return $rows;
		}

		$followers = array();
		$ids 		= array();

		foreach ($rows as $row) {
			$ids[]	= $row->id;
		}

		$followers 	= FD::user($ids);

		return $followers;
	}
}
