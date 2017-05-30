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

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'constants.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'router.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'image.php' );

class EasyBlogTableTeamBlog extends EasyBlogTable
{
	var $id 			= null;
	var $title			= null;
	var $description	= null;
	var $published		= 1;
	var $created		= null;
	var $alias			= null;
	var $access			= null;
	var $avatar			= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db )
	{
		parent::__construct( '#__easyblog_team' , 'id' , $db );
	}

	function load( $key = null , $permalink = false )
	{
		if( !$permalink )
		{
			return parent::load( $key );
		}

		$db		= $this->getDBO();

		$query	= 'SELECT id FROM ' . $this->_tbl . ' '
				. 'WHERE `alias`=' . $db->Quote( $key );
		$db->setQuery( $query );

		$id		= $db->loadResult();

		// Try replacing ':' to '-' since Joomla replaces it
		if( !$id )
		{
			$query	= 'SELECT id FROM ' . $this->_tbl . ' '
					. 'WHERE `alias`=' . $db->Quote( JString::str_ireplace( ':' , '-' , $key ) );
			$db->setQuery( $query );

			$id		= $db->loadResult();
		}

		return parent::load( $id );
	}

	function delete($pk = null)
	{
		if( parent::delete($pk) )
		{
			$this->deleteMembers();
			return true;
		}
	}

	function deleteGroup( $groupId = '')
	{
		// Delete existing members first so we dont have to worry what's changed
		if( $this->id != 0 )
		{
			$db		= EasyBlogHelper::db();
			$query	= 'DELETE FROM `#__easyblog_team_groups` ';
			$query	.= ' WHERE `team_id`=' . $db->Quote( $this->id );

			if(! empty($groupId))
				$query	.= ' AND `group_id`=' . $db->Quote( $groupId );

			$db->setQuery( $query );
			$db->Query();
		}
	}

	function deleteMembers($userId = '')
	{
		// Delete existing members first so we dont have to worry what's changed
		if( $this->id != 0 )
		{
			$db		= EasyBlogHelper::db();
			$query	= 'DELETE FROM #__easyblog_team_users ';
			$query	.= ' WHERE `team_id`=' . $db->Quote( $this->id );
			if(! empty($userId))
				$query	.= ' AND `user_id`=' . $db->Quote( $userId );

			$db->setQuery( $query );
			$db->Query();
		}
	}

	function getMembers()
	{
		if( $this->id != 0 )
		{
			$db		= EasyBlogHelper::db();
			$query	= 'SELECT user_id FROM #__easyblog_team_users '
					. 'WHERE `team_id`=' . $db->Quote( $this->id );
			$db->setQuery( $query );

			return $db->loadResultArray();
		}

		return false;
	}

	function getMemberCount()
	{
		if( $this->id != 0 )
		{
			$db		= EasyBlogHelper::db();
			$query	= 'SELECT count(user_id) FROM #__easyblog_team_users '
					. 'WHERE `team_id`=' . $db->Quote( $this->id );

			$db->setQuery( $query );
			return $db->loadResult();
		}

		return false;
	}

	/**
	 * Determines if the user is a team admin.
	 *
	 * @access	public
	 * @param	int		$userId		The user's id.
	 *
	 * @return	boolean				True if the user is an admin.
	 **/
	public function isTeamAdmin( $userId )
	{
		$db		= EasyBlogHelper::db();

		// We need to test if the user has admin access.
		$query	= 'SELECT COUNT(1) FROM `#__easyblog_team_users` WHERE `team_id`=' . $db->Quote( $this->id );
		$query	.= ' AND `user_id`=' . $db->Quote( $userId ) . ' AND `isadmin`= ' . $db->Quote( 1 );
		$db->setQuery( $query );

		$isAdmin	= $db->loadResult() > 0;

		return $isAdmin;
	}

	function isMember($userId, $gid = '', $findTeamAccess = true)
	{
		if( $this->id != 0 )
		{
			$db		= EasyBlogHelper::db();

			$gids   = '';
			if( !empty($gid) )
			{
				if( count( $gid ) > 0 )
				{
				    foreach( $gid as $id)
				    {
				        $gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
				    }
				}
			}

			$query	= 'SELECT `user_id` FROM `#__easyblog_team_users`';
			$query  .= ' WHERE `team_id`=' . $db->Quote( $this->id );
			$query  .= ' AND `user_id` = ' . $db->Quote( $userId );
			$db->setQuery( $query );

			$result = $db->loadResult();

			//if not found, lets find on the team access.
			if( empty($result) && !empty( $gids ) && $findTeamAccess )
			{
				$query  = 'SELECT count(1) FROM `#__easyblog_team_groups`';
				$query  .= ' WHERE `team_id` = ' . $db->Quote( $this->id );
				$query  .= ' AND `group_id` IN (' . $gids . ')';

				$db->setQuery( $query );
				$result = $db->loadResult();
			}

			return $result;
		}
		return false;
	}

	/**
	 * Overrides parent's bind method to add our own logic.
	 *
	 * @param Array $data
	 **/
	function bind( $data, $ignore = array() )
	{
		parent::bind( $data, $ignore );

		if( empty( $this->created ) )
		{
			$date			= EasyBlogHelper::getDate();
			$this->created	= $date->toMySQL();
		}

		jimport( 'joomla.filesystem.filter.filteroutput');

		$i	= 1;
		while( $this->aliasExists() || empty($this->alias) )
		{
			$this->alias	= empty($this->alias) ? $this->title : $this->alias . '-' . $i;
			$i++;
		}

		$this->alias 	= EasyBlogRouter::generatePermalink( $this->alias );
	}

	function aliasExists()
	{
		$db		= $this->getDBO();

		$query	= 'SELECT COUNT(1) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_team' ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'alias' ) . '=' . $db->Quote( $this->alias );

		if( $this->id != 0 )
		{
			$query	.= ' AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'id' ) . '!=' . $db->Quote( $this->id );
		}
		$db->setQuery( $query );

		return $db->loadResult() > 0 ? true : false;
	}

	function getAvatar()
	{
	    $avatar_link    = '';

        if($this->avatar == 'tdefault.png' || $this->avatar == 'default_teamblog.png' || $this->avatar == 'components/com_easyblog/assets/images/default_teamblog.png' || $this->avatar == 'components/com_easyblog/assets/images/tdefault.png' || empty($this->avatar))
        {
            $avatar_link   = 'components/com_easyblog/assets/images/default_teamblog.png';
        }
        else
        {
    		$avatar_link   = EasyImageHelper::getAvatarRelativePath('team') . '/' . $this->avatar;
    	}

		return rtrim(JURI::root(), '/') . '/' . $avatar_link;
	}

	/**
	 * @deprecated since 3.5
	 *
	 */
	function getTeamAdminEmails()
	{
		return EasyBlogHelper::getHelper( 'Notification' )->getTeamAdminEmails( $this->id );
	}

	function allowSubscription($access, $userid, $ismember, $aclallowsubscription=false)
	{
		$allowSubscription = false;

		$config = EasyBlogHelper::getConfig();

		if($config->get('main_teamsubscription', false))
		{
			switch($access)
			{
				case EBLOG_TEAMBLOG_ACCESS_MEMBER:
					if($ismember && $aclallowsubscription)
						$allowSubscription = true;
					else
						$allowSubscription = false;
					break;
				case EBLOG_TEAMBLOG_ACCESS_REGISTERED:
					if($userid != 0 && $aclallowsubscription)
						$allowSubscription = true;
					else
						$allowSubscription = false;
					break;
				case EBLOG_TEAMBLOG_ACCESS_EVERYONE:
					if($aclallowsubscription || (empty($userid) && $config->get('main_allowguestsubscribe')))
						$allowSubscription = true;
					else
						$allowSubscription = false;
					break;
				default:
					$allowSubscription = false;
			}
		}

		return $allowSubscription;
	}

	/**
	 * Retrieve a list of tags created by this team
	 **/
	public function getTags()
	{
		$db			= EasyBlogHelper::db();

		$query		= 'SELECT a.* FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_tag' ) .  ' AS a '
					. 'INNER JOIN ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_post_tag' ) . ' AS b '
					. 'ON b.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'tag_id' ) . '=a.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'id' ) . ' '
					. 'INNER JOIN ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_team_post' ) . ' AS c '
					. 'ON c.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'post_id' ) . '=b.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'post_id' ) . ' '
					. 'INNER JOIN ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_post' ) . ' AS d '
					. 'ON d.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'id' ) . '=c.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'post_id' ) . ' '
					. 'WHERE c.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'team_id' ) . '=' . $db->Quote( $this->id ) . ' '
					. 'AND d.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote( POST_ID_PUBLISHED ) . ' '
					. 'GROUP BY a.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'id' );

		$db->setQuery( $query );

		$rows	= $db->loadObjectList();
		$tags	= array();

		foreach( $rows as $row )
		{
			$tag	= EasyBlogHelper::getTable( 'Tag' , 'Table' );
			$tag->bind( $row );
			$tags[]	= $tag;
		}

		return $tags;
	}

	/**
	 * Retrieve a list of tags created by this team
	 **/
	public function getPostCount()
	{
		$db     = EasyBlogHelper::db();

		$query	= 'SELECT COUNT(1) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_post' ) . ' AS a '
				. 'INNER JOIN ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_team_post' ) . ' AS b '
				. 'ON b.' .  EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'post_id' ) . '=a.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'id' ) . ' '
				. 'WHERE b.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'team_id' ) . '=' . $db->Quote( $this->id ) . ' '
				. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'published' ) . '=' . $db->Quote( 1 );
		$db->setQuery( $query );
		return $db->loadResult();
	}

	/**
	 * Retrieve a list of categories used and created by this team members
	 **/
	public function getCategories()
	{
		$db			= EasyBlogHelper::db();

		$query	= 'SELECT DISTINCT a.*, COUNT( b.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'id' ) . ' ) AS ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'post_count' ) . ' '
				. 'FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_category' ) . ' AS a '
				. 'INNER JOIN ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_post' ) . ' AS b '
				. 'ON a.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'id' ) . '=b.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'category_id' ) . ' '
				. 'INNER JOIN ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_team_post' ) . ' AS c '
				. 'ON c.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'post_id' ) . '=b.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'id' ) . ' '
				. 'WHERE c.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'team_id' ) . '=' . $db->Quote( $this->id ) . ' '
				. 'GROUP BY a.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'id' );

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/*
	 * Determines whether the current blog entry belongs to the team.
	 *
	 * @param	int		$entryId	The subject's id.
	 * @return	boolean		True if entry was contributed to the team and false otherwise.
	 */
	public function isPostOwner( $postId )
	{
		if( empty( $postId ) )
		{
		    return false;
		}

	    $db		= $this->getDBO();

		$query	= 'SELECT COUNT(1) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_team_post' ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'post_id' ) . '=' . $db->Quote( $postId ) . ' '
				. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'team_id' ) . '=' . $db->Quote( $this->id );

		$db->setQuery( $query );
		$result	= $db->loadResult();

		return $result > 0;
	}

	function getRSS()
	{
		return EasyBlogHelper::getHelper( 'Feeds' )->getFeedURL( 'index.php?option=com_easyblog&view=teamblog&id=' . $this->id );
	}

	function getAtom()
	{
		return EasyBlogHelper::getHelper( 'Feeds' )->getFeedURL( 'index.php?option=com_easyblog&view=teamblog&id=' . $this->id , true );
	}

	function getDescription($raw = false)
	{
		return $this->description;
	}
}
