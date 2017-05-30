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

FD::import( 'admin:/includes/cluster/cluster' );
FD::import( 'admin:/includes/indexer/indexer' );

/**
 * This class allows caller to fetch a group object easily.
 * Brief example of use:
 *
 * <code>
 * // Loading a group
 * $group	= FD::group( $id );
 *
 * // Loading of multiple users based on an array of id's.
 * $users	= FD::get( 'User' , array( 42 , 43 , 44 ) );
 *
 * </code>
 *
 * @since	1.0
 * @access	public
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialGroup extends SocialCluster
{
	public $cluster_type 	= SOCIAL_TYPE_GROUP;
	/**
	 * Keeps a list of groups that are already loaded so we
	 * don't have to always reload the user again.
	 * @var Array
	 */
	static $instances	= array();


	/**
	 * Class Constructor
	 *
	 * @since	1.0
	 * @access	public
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function __construct( $params = array() , $debug = false )
	{
		// Create the user parameters object
		$this->_params = FD::registry();

		// Initialize user's property locally.
		$this->initParams( $params );

		$this->table 	= FD::table( 'Group' );
		$this->table->bind( $this );
	}

	public function initParams(&$params)
	{
		// We want to map the members data
		$this->members		= isset( $params->members ) ? $params->members : array();
		$this->admins 		= isset( $params->admins ) ? $params->admins : array();
		$this->pending 		= isset( $params->pending ) ? $params->pending : array();

		return parent::initParams($params);
	}

	/**
	 * Object initialisation for the class to fetch the appropriate user
	 * object.
	 *
	 * @since	1.0
	 * @access	public
	 * @param   $id     int/Array     Optional parameter
	 * @return  SocialUser   The person object.
	 */
	public static function factory( $ids = null , $debug = false )
	{
		$items	= self::loadGroups( $ids , $debug );

		return $items;
	}

	/**
	 * Loads a given group id or an array of id's.
	 *
	 * Example:
	 * <code>
	 * <?php
	 * // Loads current logged in user.
	 * $my 		= FD::get( 'User' );
	 * // Shorthand
	 * $my 		= FD::user();
	 *
	 * // Loads a single user.
	 * $user	= FD::get( 'User' , 42 );
	 * // Shorthand
	 * $user 	= FD::user( 42 );
	 *
	 * // Loads multiple users.
	 * $users 	= FD::get( 'User' , array( 42 , 43 ) );
	 * // Shorthand
	 * $users 	= FD::user( array( 42 , 43 ) );
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int|Array	Either an int or an array of id's in integer.
	 * @return	SocialUser	The user object.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function loadGroups( $ids = null , $debug = false )
	{
		if( is_object( $ids ) )
		{
			$obj 	= new self;
			$obj->bind( $ids );

			self::$instances[ $ids->id ]	= $obj;

			return self::$instances[ $ids->id ];
		}

		// Determine if the argument is an array.
		$argumentIsArray	= is_array( $ids );

		// Ensure that id's are always an array
		$ids = FD::makeArray($ids);

		// Reset the index of ids so we don't load multiple times from the same user.
		$ids = array_values($ids);

		if (empty($ids)) {
			return false;
		}

		// Get the metadata of all groups
		$model 	= FD::model('Groups');
		$groups	= $model->getMeta($ids);

		if( !$groups )
		{
			return false;
		}

		// Format the return data
		$result 	= array();

		foreach( $groups as $group )
		{
			if( $group === false )
			{
				continue;
			}

			// Set the cover for the group
			$group->cover 	= self::getCoverObject( $group );

			// Pre-load list of members for the group
			$members 		= $model->getMembers( $group->id , array( 'users' => false ));
			$group->members		= array();
			$group->admins 		= array();
			$group->pending 	= array();

			if( $members )
			{
				foreach( $members as $member )
				{
					if( $member->state == SOCIAL_GROUPS_MEMBER_PUBLISHED )
					{
						$group->members[ $member->uid ]	= $member->uid;
					}

					if( $member->admin )
					{
						$group->admins[ $member->uid ]	= $member->uid;
					}

					if( $member->state == SOCIAL_GROUPS_MEMBER_PENDING )
					{
						$group->pending[ $member->uid ]	= $member->uid;
					}
				}
			}


			// Attach custom fields for this group
			// $group->fileds 	= $model->getCustomFields( $group->id );

			// Create an object
			$obj 	= new SocialGroup( $group );

			self::$instances[ $group->id ]	= $obj;

			$result[]	= self::$instances[ $group->id ];
		}

		if( !$result )
		{
			return false;
		}

		if( !$argumentIsArray && count( $result ) == 1 )
		{
			return $result[ 0 ];
		}

		return $result;
	}

	/**
	 * Return the total number of members in this group
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalMembers()
	{
		// Since the $this->members property is cached, we just calculate this.
		$total	= count( $this->members );

		return $total;
	}

	/**
	 * Retrieves a list of apps for a user
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getApps()
	{
		static $apps 	= null;

		if( !$apps )
		{
			$model 	= FD::model( 'Apps' );
			$data 	= $model->getGroupApps( $this->id );

			$apps	= $data;
		}

		return $apps;
	}

	/**
	 * Centralized method to retrieve a person's profile link.
	 * This is where all the magic happens.
	 *
	 * @access	public
	 * @param	null
	 *
	 * @return	string	The url for the person
	 */
	public function getPermalink( $xhtml = true , $external = false , $layout = 'item', $sef = true )
	{
		$options	= array( 'id' => $this->getAlias() , 'layout' => $layout );

		if( $external )
		{
			$options[ 'external' ]	= true;
		}

		$options['sef'] = $sef;

		$url 	= FRoute::groups( $options , $xhtml );

		return $url;
	}

    /**
     * Retrieves the description about an event
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return
     */
    public function getDescription()
    {
        return nl2br($this->description);
    }

	/**
	 * Centralized method to retrieve a person's profile link.
	 * This is where all the magic happens.
	 *
	 * @access	public
	 * @param	null
	 *
	 * @return	string	The url for the person
	 */
	public function getEditPermalink( $xhtml = true , $external = false , $layout = 'edit' )
	{
		$url 	= $this->getPermalink( $xhtml , $external , $layout );

		return $url;
	}

	/**
	 * Create bind method
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function bind( $data )
	{
		// Bind the table data first.
		$this->table->bind( $data );

		$keyToArray = array( 'avatars', 'members', 'admins', 'pending' );

		foreach( $data as $key => $value )
		{
			if( property_exists( $this, $key ) )
			{
				if( in_array( $key, $keyToArray) && is_object( $value ) )
				{
					$value = FD::makeArray( $value );
				}

				$this->$key 	= $value;
			}
		}
	}

	/**
	 * Retrieve the creator of this group
	 *
	 * @since	1.2
	 * @access	public
	 * @return	SocialUser
	 */
	public function getInvitor( $userId )
	{
		static $invites 	= array();

		if( !isset( $invites[ $userId ] ) )
		{
			$member	= FD::table( 'GroupMember' );
			$member->load( array( 'uid' => $userId , 'cluster_id' => $this->id ) );

			$invitor	= FD::user( $member->invited_by );

			$invites[ $userId ]	= $invitor;
		}


		return $invites[ $userId ];
	}


	public function deleteMemberStream($userId)
	{

		$model = FD::model('Groups');
		$model->deleteUserStreams($this->id, $userId);
	}

	/**
	 * Allows caller to remove a member from the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteMember( $userId )
	{
		$state 	= $this->deleteNode( $userId , SOCIAL_TYPE_USER );

		if ($state) {
			$this->deleteMemberStream($userId);
		}

		return $state;
	}

	/**
	 * Allows caller to depart the user from the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	int		The user's id.
	 * @return
	 */
	public function leave( $id = null )
	{
		$my 	= FD::user( $id );

		$state 	= $this->deleteNode( $my->id );
		if( $state )
		{
			// delete stream from this user.
			$this->deleteMemberStream($my->id);

			// Additional triggers to be processed when the page starts.
			FD::apps()->load(SOCIAL_TYPE_GROUP);
			$dispatcher = FD::dispatcher();

			// Trigger: onComponentStart
			$dispatcher->trigger('user', 'onLeaveGroup', array($userId, $this));

			// @points: groups.leave
			// Deduct points when user leaves the group
			$points = FD::points();
			$points->assign( 'groups.leave' , 'com_easysocial' , $my->id );

			// Add activity stream
			$this->createStream( $my->id , 'leave' );
		}

		return $state;
	}

	/**
	 * Logics for deleting a group
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete()
	{
		// Load group apps.
		FD::apps()->load( SOCIAL_TYPE_GROUP );

		// @trigger onBeforeDelete
		$dispatcher		= FD::dispatcher();

		// @points: groups.remove
		// Deduct points when a group is deleted
		$points = FD::points();
		$points->assign( 'groups.remove' , 'com_easysocial' , $this->getCreator()->id );

		// Set the arguments
		$args 	= array( &$this );

		// @trigger onBeforeStorySave
		$dispatcher->trigger( SOCIAL_TYPE_GROUP , 'onBeforeDelete' , $args );

		// Delete all members from the cluster nodes.
		$this->deleteNodes();

		// Delete custom fields data for this cluster.
		$this->deleteCustomFields();

        // Delete photos albums for this cluster.
        $this->deletePhotoAlbums();

		// Delete stream items for this group
		$this->deleteStream();

		// Delete all group news
		$this->deleteNews();

		// delete all user notification associated with this group.
		$this->deleteNotifications();

		// Delete from the cluster
		$state 	= parent::delete();

		$args[]	= $state;

		// @trigger onAfterDelete
		$dispatcher->trigger( SOCIAL_TYPE_GROUP , 'onAfterDelete' , $args );

		return $state;
	}

	/**
	 * Delete notifications related to this group
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteNotifications()
	{
		$model		= FD::model( 'Clusters' );
		$state		= $model->deleteClusterNotifications( $this->id, $this->cluster_type, SOCIAL_TYPE_GROUPS);

		return $state;
	}

	/**
	 * Creates a new member for the group
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createMember( $userId )
	{
		$member = FD::table('GroupMember');

		// Try to load the user record if it exists
		$member->load( array( 'uid' => $userId , 'type' => SOCIAL_TYPE_USER , 'cluster_id' => $this->id) );

		$member->cluster_id = $this->id;
		$member->uid = $userId;
		$member->type = SOCIAL_TYPE_USER;
		$member->admin = false;
		$member->owner = false;

		// If the group type is open group, just add the member
		if ($this->isOpen()) {
			$member->state = SOCIAL_GROUPS_MEMBER_PUBLISHED;
		}

		// If the group type is closed group, we need the group admins to approve the application.
		if( $this->isClosed() ) {
			$member->state = SOCIAL_GROUPS_MEMBER_PENDING;
		}

		$state 	= $member->store();

		if ($state) {
			if ($this->isOpen()) {
				// Additional triggers to be processed when the page starts.
				FD::apps()->load(SOCIAL_TYPE_GROUP);
				$dispatcher = FD::dispatcher();

				// Trigger: onComponentStart
				$dispatcher->trigger('user', 'onJoinGroup', array($userId, $this));

				// @points: groups.join
				// Add points when user joins a group
				$points = FD::points();
				$points->assign('groups.join', 'com_easysocial', $userId);

				// If it is an open group, notify members
				$this->notifyMembers( 'join' , array( 'userId' => $userId ) );

				// Create a stream for the user
				$this->createStream( $userId , 'join' );
			}

			// Send notification e-mail to the admin
			if ($this->isClosed()) {
				$this->notifyGroupAdmins( 'request' , array( 'userId' => $userId ) );
			}
		}

		return $member;
	}

	/**
	 * Invites another user to join this group
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function invite( $userId , $invitorId )
	{
		// Get the actor's user object
		$actor 		= FD::user( $invitorId );

		// Get the target user's object
		$target		= FD::user( $userId );

		$node 				= FD::table( 'ClusterNode' );

		$node->cluster_id 	= $this->id;
		$node->uid 			= $userId;
		$node->type 		= SOCIAL_TYPE_USER;
		$node->state 		= SOCIAL_GROUPS_MEMBER_INVITED;
		$node->invited_by	= $invitorId;

		$node->store();

		$params 				= new stdClass();
		$params->invitorName	= $actor->getName();
		$params->invitorLink	= $actor->getPermalink( false , true );
		$params->groupName		= $this->getName();
		$params->groupAvatar 	= $this->getAvatar();
		$params->groupLink 		= $this->getPermalink( false , true );
		$params->acceptLink		= FRoute::controller( 'groups' , array( 'external' => true , 'task' => 'respondInvitation' , 'id' => $this->id, 'email' => 1) );
		$params->group 			= $this->getName();

		// Send notification e-mail to the target
		$options 			= new stdClass();
		$options->title 	= 'COM_EASYSOCIAL_EMAILS_USER_INVITED_YOU_TO_JOIN_GROUP_SUBJECT';
		$options->template 	= 'site/group/invited';
		$options->params 	= $params;

		// Set the system alerts
		$system 				= new stdClass();
		$system->uid 			= $this->id;
		$system->actor_id 		= $actor->id;
		$system->target_id		= $target->id;
		$system->context_type	= 'groups';
		$system->type 			= SOCIAL_TYPE_GROUP;
		$system->url 			= $this->getPermalink(true, false, 'item', false);

		// @points: groups.invite
		// Assign points when user invites another user to join the group
		$points = FD::points();
		$points->assign( 'groups.invite' , 'com_easysocial' , $invitorId );

		FD::notify( 'groups.invited' , array( $target->id ) , $options , $system );

		// Send
		return $node;
	}

	/**
	 * Determines if the provided user can view the group's items
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canViewItem( $userId = null )
	{
		$user 	= FD::user( $userId );

		if (($this->isInviteOnly() || $this->isClosed()) && !$this->isMember($user->id) && !$user->isSiteAdmin()) {
			return false;
		}

		return true;
	}

	/**
	 * Determines if the provided user id is a member of this group
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user's id to check against.
	 * @return	bool	True if he / she is a member already.
	 */
	public function isOpen()
	{
		return $this->type == SOCIAL_GROUPS_PUBLIC_TYPE;
	}

	/**
	 * Determines if the provided user id is a member of this group
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user's id to check against.
	 * @return	bool	True if he / she is a member already.
	 */
	public function isClosed()
	{
		return $this->type == SOCIAL_GROUPS_PRIVATE_TYPE;
	}

	/**
	 * Determines if the group is invite only
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool	True if invite only.
	 */
	public function isInviteOnly()
	{
		return $this->type == SOCIAL_GROUPS_INVITE_TYPE;
	}

	/**
	 * Determines if the user is pending invitation
	 *
	 * @access	private
	 * @param	null
	 * @return	boolean	True on success false otherwise.
	 */
	public function isPendingInvitationApproval( $uid = null )
	{
		static $pending	= array();

		if( !isset( $pending[ $uid ] ) )
		{
			$user 	= FD::user( $uid );

			$node 	= FD::table( 'ClusterNode' );
			$node->load( array( 'uid' => $user->id , 'type' => SOCIAL_TYPE_USER , 'cluster_id' => $this->id ) );

			$pending[ $uid ]	= false;

			if( $node->invited_by && $node->state == SOCIAL_GROUPS_MEMBER_INVITED )
			{
				$pending[ $uid ]	= true;
			}
		}

		return $pending[ $uid ];
	}


	/**
	 * Determines if the node is invited by another user
	 *
	 * @access	private
	 * @param	null
	 * @return	boolean	True on success false otherwise.
	 */
	public function isInvited( $uid = null )
	{
		static $invited 	= array();

		if( !isset( $invited[ $uid ] ) )
		{
			$user 	= FD::user( $uid );

			$node 	= FD::table( 'ClusterNode' );
			$node->load( array( 'uid' => $user->id , 'type' => SOCIAL_TYPE_USER , 'cluster_id' => $this->id ) );

			$invited[ $uid ]	= false;

			if( $this->isInviteOnly() && $node->invited_by )
			// if( $node->invited_by )
			{
				$invited[ $uid ]	= true;
			}
		}

		return $invited[ $uid ];
	}

	/**
	 * Approves a user's registration application
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function approve( $email = true )
	{
		// Upda the group's state first.
		$this->state	= SOCIAL_CLUSTER_PUBLISHED;

		$state 	= $this->save();

		// Activity logging.
		// Announce to the world when a new user registered on the site.
		$config 			= FD::config();

		// If we need to send email to the user, we need to process this here.
		if( $email )
		{
			FD::language()->loadSite();

			// Push arguments to template variables so users can use these arguments
			$params 	= array(
									'title'			=> $this->getName(),
									'name'			=> $this->getCreator()->getName(),
									'avatar'		=> $this->getAvatar( SOCIAL_AVATAR_LARGE ),
									'groupUrl'		=> $this->getPermalink( false, true ),
									'editUrl'		=> FRoute::groups( array( 'external' => true , 'layout' => 'edit' , 'id' => $this->getAlias() ) , false )
							);

			// Get the email title.
			$title      = JText::sprintf( 'COM_EASYSOCIAL_EMAILS_GROUP_APPLICATION_APPROVED' , $this->getName() );

			// Immediately send out emails
			$mailer 	= FD::mailer();

			// Get the email template.
			$mailTemplate	= $mailer->getTemplate();

			// Set recipient
			$mailTemplate->setRecipient( $this->getCreator()->getName() , $this->getCreator()->email );

			// Set title
			$mailTemplate->setTitle( $title );

			// Set the contents
			$mailTemplate->setTemplate( 'site/group/approved' , $params );

			// Set the priority. We need it to be sent out immediately since this is user registrations.
			$mailTemplate->setPriority( SOCIAL_MAILER_PRIORITY_IMMEDIATE );

			// Try to send out email now.
			$mailer->create( $mailTemplate );
		}

		// Once a group is approved, generate a stream item for it.
		// Add activity logging when a user creates a new group.
		if( $config->get( 'groups.stream.create' ) )
		{
			$stream				= FD::stream();
			$streamTemplate		= $stream->getTemplate();

			// Set the actor
			$streamTemplate->setActor( $this->creator_uid , SOCIAL_TYPE_USER );

			// Set the context
			$streamTemplate->setContext( $this->id , SOCIAL_TYPE_GROUPS );

			$streamTemplate->setVerb( 'create' );
			$streamTemplate->setSiteWide();
			$streamTemplate->setAccess( 'core.view' );

			// Set the params to cache the group data
			$registry	= FD::registry();
			$registry->set( 'group' , $this );

			// Set the params to cache the group data
			$streamTemplate->setParams( $registry );

			$streamTemplate->setCluster( $this->id, SOCIAL_TYPE_GROUP, $this->type );

			// Add stream template.
			$stream->add( $streamTemplate );
		}

		return true;
	}

	/**
	 * Approves the user application
	 *
	 * @since	1.2
	 * @access	public
	 * @param	int		The user id
	 * @return
	 */
	public function approveUser( $userId )
	{
		$member 	= FD::table( 'GroupMember' );
		$member->load( array( 'cluster_id' => $this->id , 'uid' => $userId ) );

		$member->state 	= SOCIAL_GROUPS_MEMBER_PUBLISHED;

		$state 	= $member->store();

		// Additional triggers to be processed when the page starts.
		FD::apps()->load(SOCIAL_TYPE_GROUP);
		$dispatcher = FD::dispatcher();

		// Trigger: onComponentStart
		$dispatcher->trigger('user', 'onJoinGroup', array($userId, $this));

		// @points: groups.join
		// Add points when user joins a group
		$points = FD::points();
		$points->assign( 'groups.join' , 'com_easysocial' , $userId );

		// Publish on the stream
		if ($state) {
			// Add stream item so the world knows that the user joined the group
			$this->createStream( $userId , 'join' );
		}

		// Notify the user that his request to join the group has been approved
		$this->notifyMembers('approved', array('targets' => array($userId)));

		// Send notifications to group members when a new member joined the group
		$this->notifyMembers('join' , array( 'userId' => $userId ));

		return $state;
	}

	/**
	 * Notify admins of the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function notifyGroupAdmins( $action , $data = array() )
	{
		$model 		= FD::model( 'Groups' );
		$targets 	= $model->getMembers( $this->id , array( 'admin' => true ) );

		if( $action == 'request' )
		{
			$actor 	= FD::user( $data[ 'userId' ] );

			$params 				= new stdClass();
			$params->userName		= $actor->getName();
			$params->userLink		= $actor->getPermalink( false , true );
			$params->userAvatar		= $actor->getAvatar( SOCIAL_AVATAR_LARGE );
			$params->groupName		= $this->getName();
			$params->groupAvatar 	= $this->getAvatar();
			$params->groupLink 		= $this->getPermalink( false , true );
			$params->approve 		= FRoute::controller( 'groups' , array( 'external' => true , 'task' => 'approve' , 'userId' => $actor->id , 'id' => $this->id , 'key' => $this->key ) );
			$params->reject 		= FRoute::controller( 'groups' , array( 'external' => true , 'task' => 'reject' , 'userId' => $actor->id , 'id' => $this->id , 'key' => $this->key ) );
			$params->group 			= $this->getName();

			// Send notification e-mail to the target
			$options 			= new stdClass();
			$options->title 	= 'COM_EASYSOCIAL_EMAILS_USER_REQUESTED_TO_JOIN_GROUP_SUBJECT';
			$options->template 	= 'site/group/moderate.member';
			$options->params 	= $params;

			// Set the system alerts
			$system 				= new stdClass();
			$system->uid 			= $this->id;
			$system->actor_id 		= $actor->id;
			$system->target_id		= $this->id;
			$system->context_type	= 'groups';
			$system->type 			= SOCIAL_TYPE_GROUP;
			$system->url 			= $this->getPermalink(false, true, 'item', false);

			FD::notify( 'groups.requested' , $targets , $options , $system );
		}
	}

	/**
	 * Notify members of the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function notifyMembers( $action , $data = array() )
	{
		$model 		= FD::model( 'Groups' );
		$targets 	= isset( $data[ 'targets' ] ) ? $data[ 'targets' ] : false;

		if( $targets === false )
		{
			$exclude 	= isset( $data[ 'userId' ] ) ? $data[ 'userId' ] : '';
			$options 	= array( 'exclude' => $exclude, 'state' => SOCIAL_GROUPS_MEMBER_PUBLISHED);
			$targets 	= $model->getMembers( $this->id , $options );
		}

		// If there is nothing to send, just skip this altogether
		if( empty( $targets ) )
		{
			return;
		}

		if( $action == 'task.completed' )
		{
			$actor 					= FD::user( $data[ 'userId' ] );
			$params 				= new stdClass();
			$params->actor			= $actor->getName();
			$params->userName		= $actor->getName();
			$params->userLink		= $actor->getPermalink( false , true );
			$params->userAvatar		= $actor->getAvatar( SOCIAL_AVATAR_LARGE );
			$params->groupName		= $this->getName();
			$params->groupAvatar 	= $this->getAvatar();
			$params->groupLink 		= $this->getPermalink( false , true );
			$params->milestoneName	= $data[ 'milestone' ];
			$params->title 			= $data[ 'title' ];
			$params->content 		= $data[ 'content' ];
			$params->permalink 		= $data[ 'permalink' ];

			// Send notification e-mail to the target
			$options 			= new stdClass();
			$options->title		= 'COM_EASYSOCIAL_EMAILS_GROUP_TASK_COMPLETED_SUBJECT';
			$options->template 	= 'site/group/task.completed';
			$options->params 	= $params;

			// Set the system alerts
			$system 				= new stdClass();
			$system->uid 			= $this->id;
			$system->title 			= '';
			$system->actor_id 		= $actor->id;
			$system->context_ids 	= $data[ 'id' ];
			$system->context_type	= 'groups';
			$system->type 			= SOCIAL_TYPE_GROUP;
			$system->url 			= $params->permalink;
			$system->image 			= $this->getAvatar();

			FD::notify( 'groups.task.completed' , $targets , $options , $system );
		}

		if( $action == 'task.create' )
		{
			$actor 					= FD::user( $data[ 'userId' ] );
			$params 				= new stdClass();
			$params->actor			= $actor->getName();
			$params->userName		= $actor->getName();
			$params->userLink		= $actor->getPermalink( false , true );
			$params->userAvatar		= $actor->getAvatar( SOCIAL_AVATAR_LARGE );
			$params->groupName		= $this->getName();
			$params->groupAvatar 	= $this->getAvatar();
			$params->groupLink 		= $this->getPermalink( false , true );
			$params->milestoneName	= $data[ 'milestone' ];
			$params->title 			= $data[ 'title' ];
			$params->content 		= $data[ 'content' ];
			$params->permalink 		= $data[ 'permalink' ];

			// Send notification e-mail to the target
			$options 			= new stdClass();
			$options->title 	= 'COM_EASYSOCIAL_EMAILS_GROUP_TASK_CREATED_SUBJECT';
			$options->template 	= 'site/group/task.create';
			$options->params 	= $params;

			// Set the system alerts
			$system 				= new stdClass();
			$system->uid 			= $this->id;
			$system->title 			= '';
			$system->actor_id 		= $actor->id;
			$system->context_ids 	= $data[ 'id' ];
			$system->context_type	= 'groups';
			$system->type 			= SOCIAL_TYPE_GROUP;
			$system->url 			= $params->permalink;
			$system->image 			= $this->getAvatar();

			FD::notify( 'groups.task.create' , $targets , $options , $system );
		}

		if( $action == 'milestone.create' )
		{
			$actor 					= FD::user( $data[ 'userId' ] );
			$params 				= new stdClass();
			$params->actor			= $actor->getName();
			$params->userName		= $actor->getName();
			$params->userLink		= $actor->getPermalink( false , true );
			$params->userAvatar		= $actor->getAvatar( SOCIAL_AVATAR_LARGE );
			$params->groupName		= $this->getName();
			$params->groupAvatar 	= $this->getAvatar();
			$params->groupLink 		= $this->getPermalink( false , true );
			$params->title 			= $data[ 'title' ];
			$params->content 		= $data[ 'content' ];
			$params->permalink 		= $data[ 'permalink' ];

			// Send notification e-mail to the target
			$options 			= new stdClass();
			$options->title		= 'COM_EASYSOCIAL_EMAILS_GROUP_TASK_CREATED_MILESTONE_SUBJECT';
			$options->template 	= 'site/group/milestone.create';
			$options->params 	= $params;

			// Set the system alerts
			$system 				= new stdClass();
			$system->uid 			= $this->id;
			$system->title 			= '';
			$system->actor_id 		= $actor->id;
			$system->context_ids 	= $data[ 'id' ];
			$system->context_type	= 'groups';
			$system->type 			= SOCIAL_TYPE_GROUP;
			$system->url 			= $params->permalink;
			$system->image 			= $this->getAvatar();

			FD::notify( 'groups.milestone.create' , $targets , $options , $system );
		}

		if( $action == 'discussion.reply' )
		{
			$actor 					= FD::user( $data[ 'userId' ] );
			$params 				= new stdClass();
			$params->actor			= $actor->getName();
			$params->userName		= $actor->getName();
			$params->userLink		= $actor->getPermalink( false , true );
			$params->userAvatar		= $actor->getAvatar( SOCIAL_AVATAR_LARGE );
			$params->groupName		= $this->getName();
			$params->groupAvatar 	= $this->getAvatar();
			$params->groupLink 		= $this->getPermalink( false , true );
			$params->title 			= $data[ 'title' ];
			$params->content 		= $data[ 'content' ];
			$params->permalink 		= $data[ 'permalink' ];

			// Send notification e-mail to the target
			$options 			= new stdClass();
			$options->title		= 'COM_EASYSOCIAL_EMAILS_GROUP_REPLIED_TO_DISCUSSION_SUBJECT';
			$options->template 	= 'site/group/discussion.reply';
			$options->params 	= $params;

			// Set the system alerts
			$system 				= new stdClass();
			$system->uid 			= $this->id;
			$system->title 			= JText::sprintf( 'COM_EASYSOCIAL_GROUPS_NOTIFICATION_REPLY_DISCUSSION' , $actor->getName() );
			$system->actor_id 		= $actor->id;
			$system->target_id		= $this->id;
			$system->context_type	= 'groups';
			$system->type 			= SOCIAL_TYPE_GROUP;
			$system->url 			= $params->permalink;
			$system->context_ids 	= $data['discussionId'];

			FD::notify( 'groups.discussion.reply' , $targets , $options , $system );
		}

		if( $action == 'discussion.create' )
		{
			$actor 					= FD::user( $data[ 'userId' ] );
			$params 				= new stdClass();
			$params->actor			= $actor->getName();
			$params->userName		= $actor->getName();
			$params->userLink		= $actor->getPermalink( false , true );
			$params->userAvatar		= $actor->getAvatar( SOCIAL_AVATAR_LARGE );
			$params->groupName		= $this->getName();
			$params->groupAvatar 	= $this->getAvatar();
			$params->groupLink 		= $this->getPermalink( false , true );
			$params->title 			= $data[ 'discussionTitle' ];
			$params->content 		= $data[ 'discussionContent' ];
			$params->permalink 		= $data[ 'permalink' ];

			// Send notification e-mail to the target
			$options 			= new stdClass();
			$options->title		= 'COM_EASYSOCIAL_EMAILS_GROUP_NEW_DISCUSSION_SUBJECT';
			$options->template 	= 'site/group/discussion.create';
			$options->params 	= $params;

			// Set the system alerts
			$system 				= new stdClass();
			$system->uid 			= $this->id;
			$system->title 			= JText::sprintf( 'COM_EASYSOCIAL_GROUPS_NOTIFICATION_NEW_DISCUSSION' , $actor->getName() , $this->getName() );
			$system->actor_id 		= $actor->id;
			$system->target_id		= $this->id;
			$system->context_type	= 'groups';
			$system->type 			= SOCIAL_TYPE_GROUP;
			$system->url 			= $params->permalink;
			$system->context_ids 	= $data['discussionId'];

			FD::notify( 'groups.discussion.create' , $targets , $options , $system );
		}

		if( $action == 'file.uploaded' )
		{
			$actor 		= FD::user( $data[ 'userId' ] );

			$params 				= new stdClass();
			$params->actor			= $actor->getName();
			$params->actorLink 		= $actor->getPermalink(false, true);
			$params->actorAvatar	= $actor->getAvatar( SOCIAL_AVATAR_LARGE );
			$params->group			= $this->getName();
			$params->groupAvatar 	= $this->getAvatar();
			$params->groupLink 		= $this->getPermalink( false , true );
			$params->fileTitle 		= $data['fileName'];
			$params->fileSize 		= $data['fileSize'];
			$params->permalink 		= $data[ 'permalink' ];

			// Send notification e-mail to the target
			$options 			= new stdClass();
			$options->title 	= 'COM_EASYSOCIAL_EMAILS_GROUP_NEW_FILE_SUBJECT';
			$options->template 	= 'site/group/file.uploaded';
			$options->params 	= $params;

			// Set the system alerts
			$system 				= new stdClass();
			$system->uid 			= $this->id;
			$system->actor_id 		= $actor->id;
			$system->target_id		= $this->id;
			$system->context_type	= 'file.group.uploaded';
			$system->context_ids 	= $data['fileId'];
			$system->type 			= 'groups';
			$system->url 			= $params->permalink;

			FD::notify('groups.updates' , $targets, $options, $system);
		}

		if( $action == 'news.create' )
		{
			$actor 		= FD::user( $data[ 'userId' ] );

			$params 				= new stdClass();
			$params->actor 			= $actor->getName();
			$params->group 			= $this->getName();
			$params->userName		= $actor->getName();
			$params->userLink		= $actor->getPermalink( false , true );
			$params->userAvatar		= $actor->getAvatar( SOCIAL_AVATAR_LARGE );
			$params->groupName		= $this->getName();
			$params->groupAvatar 	= $this->getAvatar();
			$params->groupLink 		= $this->getPermalink( false , true );
			$params->newsTitle 		= $data[ 'newsTitle' ];
			$params->newsContent 	= $data[ 'newsContent' ];
			$params->permalink 		= $data[ 'permalink' ];

			// Send notification e-mail to the target
			$options 			= new stdClass();
			$options->title		= 'COM_EASYSOCIAL_EMAILS_GROUP_NEW_ANNOUNCEMENT_SUBJECT';
			$options->template 	= 'site/group/news';
			$options->params 	= $params;

			// Set the system alerts
			$system 				= new stdClass();
			$system->uid 			= $this->id;
			$system->actor_id 		= $actor->id;
			$system->target_id		= $this->id;
			$system->context_type	= 'groups';
			$system->context_ids 	= $data['newsId'];
			$system->type 			= SOCIAL_TYPE_GROUP;
			$system->url 			= $params->permalink;

			FD::notify( 'groups.news' , $targets , $options , $system );
		}

		if( $action == 'leave' )
		{
			$actor 	= FD::user( $data[ 'userId' ] );

			$params 				= new stdClass();
			$params->actor 			= $actor->getName();
			$params->group 			= $this->getName();
			$params->userName		= $actor->getName();
			$params->userLink		= $actor->getPermalink( false , true );
			$params->userAvatar		= $actor->getAvatar( SOCIAL_AVATAR_LARGE );
			$params->groupName		= $this->getName();
			$params->groupAvatar 	= $this->getAvatar();
			$params->groupLink 		= $this->getPermalink( false , true );

			// Send notification e-mail to the target
			$options 			= new stdClass();
			$options->title 	= 'COM_EASYSOCIAL_EMAILS_SUBJECT_GROUPS_LEFT_GROUP';
			$options->template 	= 'site/group/leave';
			$options->params 	= $params;

			// Set the system alerts
			$system 				= new stdClass();
			$system->uid 			= $this->id;
			$system->actor_id 		= $actor->id;
			$system->target_id		= $this->id;
			$system->context_type	= 'groups';
			$system->type 			= SOCIAL_TYPE_GROUP;
			$system->url 			= $this->getPermalink();

			FD::notify( 'groups.leave' , $targets , $options , $system );
		}

		if( $action == 'user.remove' )
		{
			$actor 	= FD::user( $data[ 'userId' ] );

			// targets should be the user being removed.
			$targets = array($actor->id);

			$params 				= new stdClass();
			$params->actor 			= $actor->getName();
			$params->group 			= $this->getName();
			$params->userName		= $actor->getName();
			$params->userLink		= $actor->getPermalink( false , true );
			$params->userAvatar		= $actor->getAvatar( SOCIAL_AVATAR_LARGE );
			$params->groupName		= $this->getName();
			$params->groupAvatar 	= $this->getAvatar();
			$params->groupLink 		= $this->getPermalink( false , true );

			// Send notification e-mail to the target
			$options 			= new stdClass();
			$options->title 	= 'COM_EASYSOCIAL_EMAILS_SUBJECT_GROUPS_YOU_REMOVED_FROM_GROUP';
			$options->template 	= 'site/group/user.removed';
			$options->params 	= $params;

			// Set the system alerts
			$system 				= new stdClass();
			$system->uid 			= $this->id;
			$system->actor_id 		= $actor->id;
			$system->target_id		= $this->id;
			$system->context_type	= 'groups';
			$system->type 			= SOCIAL_TYPE_GROUP;
			$system->cmd 			= 'groups.user.removed';
			$system->url 			= $this->getPermalink();

			FD::notify( 'groups.user.removed' , $targets , $options, $system );
		}


		// Admin approves the user
		if ($action == 'approved') {

			// The actor is always the current user.
			$actor 	= FD::user();

			$params 				= new stdClass();
			$params->actor 			= $actor->getName();
			$params->group 			= $this->getName();
			$params->userName		= $actor->getName();
			$params->userLink		= $actor->getPermalink( false , true );
			$params->userAvatar		= $actor->getAvatar( SOCIAL_AVATAR_LARGE );
			$params->groupName		= $this->getName();
			$params->groupAvatar 	= $this->getAvatar();
			$params->groupLink 		= $this->getPermalink( false , true );

			// Send notification e-mail to the target
			$options 			= new stdClass();
			$options->title		= 'COM_EASYSOCIAL_EMAILS_SUBJECT_GROUPS_APPROVED_JOIN_GROUP';
			$options->template 	= 'site/group/user.approved';
			$options->params 	= $params;

			// Set the system alerts
			$system 				= new stdClass();
			$system->uid 			= $this->id;
			$system->actor_id 		= $actor->id;
			$system->target_id		= $this->id;
			$system->context_type	= 'groups';
			$system->type 			= SOCIAL_TYPE_GROUP;
			$system->url 			= $this->getPermalink();

			FD::notify('groups.approved' , $targets , $options , $system );
		}

		if( $action == 'join' )
		{
			$actor 	= FD::user( $data[ 'userId' ] );

			$params 				= new stdClass();
			$params->actor 			= $actor->getName();
			$params->group 			= $this->getName();
			$params->userName		= $actor->getName();
			$params->userLink		= $actor->getPermalink( false , true );
			$params->userAvatar		= $actor->getAvatar( SOCIAL_AVATAR_LARGE );
			$params->groupName		= $this->getName();
			$params->groupAvatar 	= $this->getAvatar();
			$params->groupLink 		= $this->getPermalink( false , true );

			// Send notification e-mail to the target
			$options 			= new stdClass();
			$options->title 	= 'COM_EASYSOCIAL_EMAILS_GROUP_JOINED_GROUP_SUBJECT';
			$options->template 	= 'site/group/joined';
			$options->params 	= $params;

			// Set the system alerts
			$system 				= new stdClass();
			$system->uid 			= $this->id;
			$system->title 			= JText::sprintf( 'COM_EASYSOCIAL_GROUPS_NOTIFICATION_JOIN_GROUP' , $actor->getName() , $this->getName() );
			$system->actor_id 		= $actor->id;
			$system->target_id		= $this->id;
			$system->context_type	= 'groups';
			$system->type 			= SOCIAL_TYPE_GROUP;
			$system->url 			= $this->getPermalink();

			FD::notify( 'groups.joined' , $targets , $options , $system );
		}

	}

	public function createStream( $actorId = null , $verb )
	{
		$stream		= FD::stream();
		$tpl		= $stream->getTemplate();
		$actor 		= FD::user( $actorId );

		// this is a cluster stream and it should be viewable in both cluster and user page.
		$tpl->setCluster( $this->id, SOCIAL_TYPE_GROUP, $this->type );

		// Set the actor
		$tpl->setActor( $actor->id , SOCIAL_TYPE_USER );

		// Set the context
		$tpl->setContext( $this->id , SOCIAL_TYPE_GROUPS );

		// Set the verb
		$tpl->setVerb( $verb );

		// Set the params to cache the group data
		$registry	= FD::registry();
		$registry->set( 'group' , $this );

		// Set the params to cache the group data
		$tpl->setParams( $registry );

		// since this is a cluster and user stream, we need to call setPublicStream
		// so that this stream will display in unity page as well
		// This stream should be visible to the public
		$tpl->setAccess( 'core.view' );

		$stream->add( $tpl );
	}

	/**
	 * Rejects the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function reject( $reason = '' , $email = false , $delete = false )
	{
		// Announce to the world when a new user registered on the site.
		$config 			= FD::config();

		// If we need to send email to the user, we need to process this here.
		if( $email )
		{
			// Push arguments to template variables so users can use these arguments
			$params 	= array(
									'title'			=> $this->getName(),
									'name'			=> $this->getCreator()->getName(),
									'reason'		=> $reason,
									'manageAlerts'	=> false
								);

			// Load front end language file.
			FD::language()->loadSite();

			// Get the email title.
			$title      = JText::_( 'COM_EASYSOCIAL_EMAILS_GROUP_REJECTED_EMAIL_TITLE' );

			// Immediately send out emails
			$mailer 	= FD::mailer();

			// Get the email template.
			$mailTemplate	= $mailer->getTemplate();

			// Set recipient
			$mailTemplate->setRecipient( $this->getCreator()->getName() , $this->getCreator()->email );

			// Set title
			$mailTemplate->setTitle( $title );

			// Set the contents
			$mailTemplate->setTemplate( 'site/group/rejected' , $params );

			// Set the priority. We need it to be sent out immediately since this is user registrations.
			$mailTemplate->setPriority( SOCIAL_MAILER_PRIORITY_IMMEDIATE );

			// Try to send out email now.
			$mailer->create( $mailTemplate );
		}

		// If required, delete the user from the site.
		if( $delete )
		{
			$this->delete();
		}

		return true;
	}

	/**
	 * Rejects the user application
	 *
	 * @since	1.2
	 * @access	public
	 * @param	int		The user id
	 * @return
	 */
	public function rejectUser($userId)
	{
		$member 	= FD::table( 'GroupMember' );
		$member->load( array( 'cluster_id' => $this->id , 'uid' => $userId ) );

		$state 		= $member->delete();

		// Notify the user that they have been rejected :(
		$mailOptions	= array();
		$mailOptions['title']		= 'COM_EASYSOCIAL_GROUPS_APPLICATION_REJECTED';
		$mailOptions['template']	= 'site/group/user.rejected';


		$systemOptions 	= array();
		$systemOptions['context_type']	= 'groups';
		$systemOptions['cmd']			= 'groups.user.rejected';
		$systemOptions['url']			= $this->getPermalink(true, false, 'item', false);
		$systemOptions['actor_id']		= FD::user()->id;
		$systemOptions['uid']			= $this->id;

		FD::notify('groups.user.rejected', array($userId), $mailOptions, $systemOptions);

		return $state;
	}

	/**
	 * Determines if the provided user id is a pending member of this group
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user's id to check against.
	 * @return	bool	True if he / she is a member already.
	 */
	public function isPendingMember( $userId = null )
	{
		$userId	= FD::user( $userId )->id;

		if( isset( $this->pending[ $userId ] ) )
		{
			return true;
		}

		return false;
	}

	/**
	 * Determines if the provided user id is a member of this group
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user's id to check against.
	 * @return	bool	True if he / she is a member already.
	 */
	public function isMember( $userId = null )
	{
		$userId	= FD::user( $userId )->id;

		if( isset( $this->members[ $userId ] ) )
		{
			return true;
		}

		return false;
	}

	/**
	 * Gets group member's filter.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	null
	 * @return	SocialAccess
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getFilters( $userId )
	{
		$model		= FD::model( 'Groups' );
		$filters	= $model->getFilters( $this->id,  $userId );

		return $filters;
	}

	public function canCreateEvent($userId = null)
	{
		if (is_null($userId)) {
			$userId = FD::user()->id;
		}

		if ($this->isOwner($userId) || FD::user($userId)->isSiteAdmin()) {
			return true;
		}

		$allowed = FD::makeArray($this->getParams()->get('eventcreate', '[]'));

		if (in_array('admin', $allowed) && $this->isAdmin($userId)) {
			return true;
		}

		if (in_array('member', $allowed) && $this->isMember($userId)) {
			return true;
		}

		return false;
	}
}
