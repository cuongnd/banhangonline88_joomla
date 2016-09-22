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


class SocialStreamItem
{
	public $uid			= null;
	public $title		= null;
	public $content		= null;
	public $preview		= null;
	public $display		= null;
	public $friendlyTS	= null;
	public $actor_id	= null;
	public $type		= null;
	public $with 		= null;
	public $location 	= null;
	public $isNew		= null;

	public function __construct()
	{
	}

	/**
	 * Determines if the stream item is posted in a cluster
	 *
	 * @since	1.3.8
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function isCluster()
	{
		return $this->cluster_id > 0;
	}

	/**
	 * Retrieves the cluster object
	 *
	 * @since	1.3.8
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getCluster()
	{
		if (!$this->isCluster()) {
			return false;
		}

        // Get the cluster object
        $cluster = FD::cluster($this->cluster_type, $this->cluster_id);

        return $cluster;
	}

	/**
	 * Sets the likes on the stream
	 *
	 * @since	1.3.8
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function setLikes($group, $useStreamId, $uid = null, $context = null, $verb = null)
	{
		$uid = is_null($uid) ? $this->uid : $uid;
		$context = is_null($context) ? $this->context : $context;
		$verb = is_null($verb) ? $this->verb : $verb;
		
		$likes = FD::likes();
		$likes->get($uid, $context, $verb, $group, $useStreamId);

		$this->likes = $likes;
	}

	/**
	 * Sets the comments on the stream
	 *
	 * @since	1.3.8
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function setComments($group, $useStreamId, $options = array(), $uid = null, $context = null, $verb = null)
	{
		$uid = is_null($uid) ? $this->uid : $uid;
		$context = is_null($context) ? $this->context : $context;
		$verb = is_null($verb) ? $this->verb : $verb;
		
		// Retrieve the comments object
		$comments = FD::comments($uid, $context, $verb, $group, $options, $useStreamId);
		$this->comments = $comments;
	}

	/**
	 * Sets the repost on the stream
	 *
	 * @since	1.3.8
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function setRepost($group, $element, $uid = null)
	{
		$uid = is_null($uid) ? $this->uid : $uid;

		// Get the repost object
		$repost = FD::get('Repost', $this->uid, $element, $group);
		$this->repost = $repost;
	}


	/**
	 * Retrieves the actor of the stream
	 *
	 * @since	1.3.8
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getActor()
	{
		return $this->actor;
	}

	/**
	 * Retrieves targets for this stream
	 *
	 * @since	1.3.8
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getTargets()
	{
		if (!isset($this->targets) || !$this->targets) {
			return false;
		}

		if (count($this->targets) == 1) {
			return $this->targets[0];
		}

		return $this->targets;
	}

	/**
	 * Retrieves a set of assets associated with this stream item.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAssets( $uid = '')
	{
		$model 	= FD::model( 'Stream' );

		$uid = ( $uid ) ? $uid : $this->uid;

		if( !$this->type || !$uid )
		{
			return array();
		}

		$result	= $model->getAssets( $uid , $this->type );


		$assets	= array();

		foreach( $result as $row )
		{
			$assets[]	= FD::registry( $row->data );
		}
		return $assets;
	}
}

/**
 * Any tables that wants to implement a stream interface will need to implement this.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
interface ISocialStreamItemTable
{
	public function addStream( $verb );
	public function removeStream();
}

/**
 * Action interface.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
interface ISocialStreamAction
{
	/**
	 * Class Constructor.
	 *
	 * @since	1.0
	 * @param	SocialStreamItem
	 */
	public function __construct( SocialStreamItem &$item );

	/**
	 * Responsible to output the title of the stream action.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getTitle();

	/**
	 * Responsible to output the contents of the stream action
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getContents();

	/**
	 * Responsible to output the action link
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getLink();

	/**
	 * Responsible to return the unique key
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getKey();

	/**
	 * Responsible to determine if the content should be hidden
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function isHidden();
}
