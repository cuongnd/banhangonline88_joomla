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

class EasyBlogTableReport extends EasyBlogTable
{
	var $id			= null;
	var $obj_id		= null;
	var $obj_type 	= null;
	var $created_by	= null;
	var $created	= null;
	var $reason		= null;
	var $ip 		= null;

	private $author = null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db )
	{
		parent::__construct( '#__easyblog_reports' , 'id' , $db );
	}

	public function getAuthor()
	{
		if( !isset( $this->author ) || is_null( $this->author ) )
		{
			$profile 	= EasyBlogHelper::getTable('Profile' );
			$profile->load( $this->created_by );
			$this->author	= $profile;
		}
		return $this->author;
	}

	public function store( $updateNulls = false )
	{
		$config 	= EasyBlogHelper::getConfig();
		$maxTimes 	= $config->get( 'main_reporting_maxip' );

		// @task: Run some checks on reported items and
		if( $maxTimes > 0 )
		{
			$db 	= EasyBlogHelper::db();
			$query 	= 'SELECT COUNT(1) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( $this->_tbl ) . ' '
					. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'obj_id' ) . ' = ' . $db->Quote( $this->obj_id ) . ' '
					. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'obj_type' ) . ' = ' . $db->Quote( $this->obj_type ) . ' '
					. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'ip' ) . ' = ' . $db->Quote( $this->ip );

			$db->setQuery( $query );
			$total 	= (int) $db->loadResult();

			if( $total >= $maxTimes )
			{
				JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );
				$this->setError( JText::_( 'COM_EASYBLOG_REPORT_ALREADY_REPORTED' ) );
				return false;
			}
		}

		// Assign badge for users that report blog post.
		// Only give points if the viewer is viewing another person's blog post.
		EasyBlogHelper::getHelper( 'EasySocial' )->assignBadge( 'blog.report' , JText::_( 'COM_EASYBLOG_EASYSOCIAL_BADGE_REPORT_BLOG' ) );

		return parent::store();
	}

	public function notify( EasyBlogTableBlog $blog )
	{
		$config 	= EasyBlogHelper::getConfig();

		// Send notification to site admins when a new blog post is reported
		$data 	= array();
		$data[ 'blogTitle']				= $blog->title;
		$data[ 'blogLink' ]				= EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry&id='. $blog->id, false, true);		

		// @rule: Send email notifications out to subscribers.
		$author 	= EasyBlogHelper::getTable( 'Profile' );
		$author->load( $this->created_by );

		$data[ 'reporterAvatar' ]		= $author->getAvatar();
		$data[ 'reporterName' ]			= $author->getName();
		$data[ 'reporterLink' ]			= $author->getProfileLink();
		$data[ 'reason' ]				= $this->reason;
		$date							= EasyBlogDateHelper::dateWithOffSet( $this->created );
		$data[ 'reportDate' ]			= EasyBlogDateHelper::toFormat( $date , '%A, %B %e, %Y' );

		// If blog post is being posted from the back end and SH404 is installed, we should just use the raw urls.
		$sh404exists	= EasyBlogRouter::isSh404Enabled();

		if( JFactory::getApplication()->isAdmin() && $sh404exists )
		{
			$data[ 'blogLink' ]			= JURI::root() . 'index.php?option=com_easyblog&view=entry&id=' . $blog->id;
		}

		$emailBlogTitle = JString::substr( $blog->title , 0 , $config->get( 'main_mailtitle_length' ) );
		$emailTitle 	= JText::sprintf( 'COM_EASYBLOG_EMAIL_TITLE_NEW_REPORT' ,  $emailBlogTitle ) . ' ...';

		$notification	= EasyBlogHelper::getHelper( 'Notification' );
		$emails 		= array();

		// @rule: Fetch custom emails defined at the back end.
		if( $config->get( 'notification_blogadmin' ) )
		{
			if( $config->get( 'custom_email_as_admin' ) )
			{
				$notification->getCustomEmails( $emails );
			}
			else
			{
				$notification->getAdminEmails( $emails );
			}
		}

		if( !empty( $emails ) )
		{
			$notification->send( $emails , $emailTitle , 'email.blog.report' , $data );
		}
	}
}
