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

class EasyBlogTableRatings extends EasyBlogTable
{
	/*
	 * The primary key for this table.
	 * @var int
	 */
	var $id 					= null;

	/**
	 * Universal id
	 * @var int
	 */
	var $uid		        = null;

	/**
	 * Rating type
	 * @var string
	 */
	var $type					= null;

	/*
	 * site member id
	 * @var int
	 */
	var $created_by				= null;

	/*
	 * Session id (optional)
	 * @var string
	 */
	var $sessionid				= null;

	/*
	 * Contains the value of the rating
	 * @var string
	 */
	var $value					= null;

	/*
	 * IP address of voter
	 * @var string
	 */
	var $ip						= null;

	/*
	 * Created datetime of the tag
	 * @var datetime
	 */
	var $published				= null;

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
		parent::__construct( '#__easyblog_ratings' , 'id' , $db );
	}

	public function fill( $userId , $postId , $type , $hash = '' )
	{
		static $objects	= array();

		if( !isset( $objects[ $type ][ $postId ][ $userId ] ) )
		{
			$db		= $this->getDBO();
			$query	= 'SELECT * FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( $this->_tbl ) . ' '
					. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'created_by' ) . '=' . $db->Quote( $userId ) . ' '
					. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'uid' ) . '=' . $db->Quote( $postId ) . ' '
					. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'type' ) . '=' . $db->Quote( $type );

			if( !empty($hash) )
			{
				$query	.= ' AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'sessionid' ) . '=' . $db->Quote( $hash );
			}
			$db->setQuery( $query );

			$result	= $db->loadObject();

			if (is_null($result))
			{
				return false;
			}

			$objects[ $type ][ $postId ][ $userId ] = $result;
		}

		return parent::bind( $objects[ $type ][ $postId ][ $userId ] );
	}

	public function store($updateNulls = false)
	{
		$config		= EasyBlogHelper::getConfig();
		$state		= parent::store();

		if( $this->type == 'entry' && $this->created_by )
		{
			$blog 	= EasyBlogHelper::getTable( 'Blog' );
			$blog->load( $this->uid );

			$author	= EasyBlogHelper::getTable( 'Profile' );
			$author->load( $this->created_by );

			// Get list of users who subscribed to this blog.
			$link	= $blog->getExternalBlogLink( 'index.php?option=com_easyblog&view=entry&id='. $blog->id );

			$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );
			
			if( $config->get( 'integrations_easysocial_notifications_ratings' ) && $easysocial->exists() )
			{
				$easysocial->notifySubscribers( $blog , 'ratings.add' );
			}

			// Assign EasySocial points
			$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );
			$easysocial->assignPoints( 'blog.rate' );

			$easysocial->assignPoints( 'blog.rated' , $blog->created_by );
			
			// @rule: Add notifications for jomsocial 2.6
			if( $config->get( 'integrations_jomsocial_notification_rating' ) )
			{
				$target	= array( $blog->created_by );
				EasyBlogHelper::getHelper( 'JomSocial' )->addNotification( JText::sprintf( 'COM_EASYBLOG_JOMSOCIAL_NOTIFICATIONS_NEW_RATING_FOR_YOUR_BLOG' , str_replace("administrator/","", $author->getProfileLink()), $author->getName() , $link  , $blog->title ) , 'easyblog_new_blog' , $target , $author , $link );
			}

			// @rule: Add notifications for easydiscuss
			if( $config->get( 'integrations_jomsocial_notification_rating' ) )
			{
				$target	= array( $blog->created_by );

				EasyBlogHelper::getHelper( 'EasyDiscuss' )
						->addNotification( $blog ,
									JText::sprintf( 'COM_EASYBLOG_EASYDISCUSS_NOTIFICATIONS_NEW_RATING_FOR_YOUR_BLOG' , $author->getName() , $blog->title) ,
									EBLOG_NOTIFICATIONS_TYPE_RATING ,
									array( $blog->created_by ) ,
									$this->created_by,
									$link );
			}
		}

		return $state;
	}
}
