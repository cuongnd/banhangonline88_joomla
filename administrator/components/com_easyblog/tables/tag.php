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
require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'constants.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'router.php' );

class EasyBlogTableTag extends EasyBlogTable
{
	var $id 			= null;
	var $created_by		= null;
	var $title			= null;
	var $alias			= null;
	var $created		= null;
	var $status			= null;
	var $published		= null;
	var $default		= null;
	var $ordering		= null;


	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db )
	{
		parent::__construct( '#__easyblog_tag' , 'id' , $db );
	}

	function load( $id = null , $loadByTitle = false)
	{
		if( !$loadByTitle)
		{
			static $titles	= null;

			if( !isset( $titles[ $id ] ) )
			{
				$titles[ $id ]	= parent::load( $id );
			}
			return $titles[ $id ];
		}

		static $tags	= null;

		if( !isset( $tags[ $id ] ) )
		{
			$db		= EasyBlogHelper::db();
			$query	= 'SELECT *';
			$query	.= ' FROM ' 	. EasyBlogHelper::getHelper( 'SQL' )->nameQuote('#__easyblog_tag');
			$query	.= ' WHERE (' 	. EasyBlogHelper::getHelper( 'SQL' )->nameQuote('title') . ' = ' .  $db->Quote( JString::str_ireplace( ':' , '-' , $id ) );
			$query	.= ' OR ' 	. EasyBlogHelper::getHelper( 'SQL' )->nameQuote('title') . ' = ' .  $db->Quote( JString::str_ireplace( '-' , ' ' , $id ) ) . ' ';
			$query	.= ' OR ' 	. EasyBlogHelper::getHelper( 'SQL' )->nameQuote('alias') . ' = ' .  $db->Quote( JString::str_ireplace( ':' , '-' , $id ) ) . ')';
			$query	.= ' LIMIT 1';

			$db->setQuery($query);
			$result	= $db->loadObject();

			if( $result )
			{
				$this->id		= $result->id;
				$this->title	= $result->title;
				$this->created_by	= $result->created_by;
				$this->alias		= $result->alias;
				$this->created		= $result->created;
				$this->status		= $result->status;
				$this->published	= $result->published;
				$this->ordering		= $result->ordering;
				
				$tags[ $id ]		= clone $this;

			}
			else
			{
				$tags[ $id ]		= false;
			}
		}
		else
		{
			parent::bind( $tags[ $id ] );
		}

		return $tags[ $id ];
	}

	function aliasExists()
	{
		$db		= $this->getDBO();

		$query	= 'SELECT COUNT(1) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_tag' ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'alias' ) . '=' . $db->Quote( $this->alias );

		if( $this->id != 0 )
		{
			$query	.= ' AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'id' ) . '!=' . $db->Quote( $this->id );
		}
		$db->setQuery( $query );

		return $db->loadResult() > 0 ? true : false;
	}

	function exists( $title , $isNew = true )
	{
		$db	= EasyBlogHelper::db();

		$query	= 'SELECT COUNT(1) '
				. 'FROM ' 	. EasyBlogHelper::getHelper( 'SQL' )->nameQuote('#__easyblog_tag') . ' '
				. 'WHERE ' 	. EasyBlogHelper::getHelper( 'SQL' )->nameQuote('title') . ' = ' . $db->quote($title);

		if( !$isNew )
		{
			$query	.= ' AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'id' ) . '!=' . $db->Quote( $this->id );
		}

		$query 		.= ' LIMIT 1';
		$db->setQuery($query);

		$result	= $db->loadResult() > 0 ? true : false;

		return $result;
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

	/**
	 * Overrides parent's delete method to add our own logic.
	 *
	 * @return boolean
	 * @param object $db
	 */
	function delete($pk = null)
	{
		$db		= $this->getDBO();

		// Ensure that tag associations are removed
		$this->deletePostTag();

		if( $this->created_by != 0 )
		{
	    	JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );
	    	$config 	= EasyBlogHelper::getConfig();

			// @rule: Integrations with EasyDiscuss
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->log( 'easyblog.delete.tag' , $this->created_by , JText::sprintf( 'COM_EASYBLOG_EASYDISCUSS_HISTORY_NEW_TAG' , $this->title ) );
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->addPoint( 'easyblog.delete.tag' , $this->created_by );
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->addBadge( 'easyblog.delete.tag' , $this->created_by );

			// Assign EasySocial points
			$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );
			$easysocial->assignPoints( 'tag.remove' , $this->created_by );

			if( $config->get('main_jomsocial_userpoint') )
			{
				$jsUserPoint	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'userpoints.php';
				if( JFile::exists( $jsUserPoint ) )
				{
					require_once( $jsUserPoint );
					CUserPoints::assignPoint( 'com_easyblog.tag.remove' , $this->created_by );
				}
			}

			// AlphaUserPoints
			// since 1.2
			if( EasyBlogHelper::isAUPEnabled() )
			{
				AlphaUserPointsHelper::newpoints( 'plgaup_easyblog_delete_tag', AlphaUserPointsHelper::getAnyUserReferreID( $this->created_by ) , '', JText::sprintf('COM_EASYBLOG_AUP_TAG_DELETED', $this->title) );
			}

		}

		$my = JFactory::getUser();
		//activity logging.
		$activity   = new stdClass();
		$activity->actor_id		= $my->id;
		$activity->target_id	= '0';
		$activity->context_type	= 'tag';
		$activity->context_id	= $this->id;
		$activity->verb         = 'delete';
		$activity->uuid         = $this->title;

		$state  = parent::delete();
		if( $state )
		{
			EasyBlogHelper::activityLog( $activity );
		}
		return $state;
	}

	// method to delete all the blog post that associated with the current tag
	function deletePostTag()
	{
		$db		= $this->getDBO();

		$query	= 'DELETE FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_post_tag' ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'tag_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );

		if($db->query($db))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function getPostCount()
	{
		$db		= $this->getDBO();

		$query  = 'select count(1) from `#__easyblog_post_tag`';
		$query  .= ' where `tag_id` = ' . $db->Quote( $this->id );

		$db->setQuery( $query );

		$result = $db->loadResult();
		return ( empty( $result ) ) ? 0 : $result;
	}

	public function store($updateNulls = false)
	{
		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );

		// @rule: Check for empty title
		if( empty( $this->title ) )
		{
			$this->setError( JText::_( 'COM_EASYBLOG_INVALID_TAG' ) );
			return false;
		}

		// @rule: Check if such tag exists.
		if( $this->exists( $this->title , !$this->id ) )
		{
			$this->setError( JText::_( 'COM_EASYBLOG_TAG_ALREADY_EXISTS' ) );

			return false;
		}

		// @task: If alias is null, we need to generate them here.
		jimport( 'joomla.filesystem.filter.filteroutput');

		$i	= 1;
		while( $this->aliasExists() || empty($this->alias) )
		{
			$this->alias	= empty($this->alias) ? $this->title : $this->alias . '-' . $i;
			$i++;
		}

		$this->alias 	= EasyBlogRouter::generatePermalink( $this->alias );

		if( !empty( $this->created ))
		{
			$offset     	= EasyBlogDateHelper::getOffSet();
			$newDate    = EasyBlogHelper::getDate($this->created, $offset);
			$this->created  = $newDate->toMySQL();
		}
		else
		{
			$newDate    = EasyBlogHelper::getDate();
			$this->created  = $newDate->toMySQL();
		}

	    $isNew	= !$this->id;
	    $state	= parent::store();
	    $my		= JFactory::getUser();

	    if( $isNew && $my->id != 0 )
	    {
	    	JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );
	    	$config 	= EasyBlogHelper::getConfig();

			// @rule: Integrations with EasyDiscuss
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->log( 'easyblog.new.tag' , $my->id , JText::sprintf( 'COM_EASYBLOG_EASYDISCUSS_HISTORY_NEW_TAG' , $this->title ) );
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->addPoint( 'easyblog.new.tag' , $my->id );
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->addBadge( 'easyblog.new.tag' , $my->id );

			// Assign EasySocial points
			$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );
			$easysocial->assignPoints( 'tag.create' , $my->id );

			if( $config->get('main_jomsocial_userpoint') )
			{
				$jsUserPoint	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'userpoints.php';
				if( JFile::exists( $jsUserPoint ) )
				{
					require_once( $jsUserPoint );
					CUserPoints::assignPoint( 'com_easyblog.tag.add' , $my->id );
				}
			}

			// AlphaUserPoints
			// since 1.2
			if( EasyBlogHelper::isAUPEnabled() )
			{
				AlphaUserPointsHelper::newpoints( 'plgaup_easyblog_add_tag', '', 'easyblog_add_tag_' . $this->id, JText::sprintf('COM_EASYBLOG_AUP_TAG_ADDED', $this->title) );
			}
	    }

		if( $state )
		{
			//activity logging.
			$activity   = new stdClass();
			$activity->actor_id		= $my->id;
			$activity->target_id	= '0';
			$activity->context_type	= 'tag';
			$activity->context_id	= $this->id;
			$activity->verb         = ( $isNew ) ? 'add' : 'update';
			$activity->uuid         = $this->title;

			EasyBlogHelper::activityLog( $activity );
		}

	    return $state;
	}
}
