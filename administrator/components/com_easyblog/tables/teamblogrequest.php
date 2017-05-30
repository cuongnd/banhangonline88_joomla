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

class EasyBlogTableTeamBlogRequest extends EasyBlogTable
{
	var $id			= null;
	var $team_id	= null;
	var $user_id	= null;
	var $ispeding   = null;
	var $created    = null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db )
	{
		parent::__construct( '#__easyblog_team_request' , 'id' , $db );
	}


	function exists()
	{
		$db		= EasyBlogHelper::db();

		$query	= 'SELECT COUNT(1) FROM #__easyblog_team_request '
				. 'WHERE `team_id`=' . $db->Quote( $this->team_id ) . ' '
				. 'AND `user_id`=' . $db->Quote( $this->user_id ) . ' '
				. 'AND `ispending` = ' . $db->Quote('1');
		$db->setQuery( $query );

		return $db->loadResult() > 0 ? true : false;
	}

	public function sendModerationEmail()
	{
		// @rule: Send email to the team admin's.
		$team 	= EasyBlogHelper::getTable( 'TeamBlog' );
		$team->load( $this->team_id );

		$notification	= EasyBlogHelper::getHelper( 'Notification' );
		$emails 		= array();

		$config			= EasyBlogHelper::getConfig();

		if( $config->get( 'custom_email_as_admin' ) )
		{
			$notification->getCustomEmails( $emails );
		}
		else
		{
			$notification->getAdminEmails( $emails );
		}
		$notification->getTeamAdminEmails( $emails , $team->id );

		$user 			= EasyBlogHelper::getTable( 'Profile' );
		$user->load( $this->user_id );

		$date			= EasyBlogDateHelper::dateWithOffSet( $this->created );

		if( count( $emails ) > 0 )
		{
			$data 		= array(
							'teamName'		=> $team->title,
							'authorAvatar'	=> $user->getAvatar(),
							'authorLink'	=> EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $user->id , false , true ),
							'authorName'	=> $user->getName(),
							'requestDate'	=> EasyBlogDateHelper::toFormat( $date , '%A, %B %e, %Y' ),
							'reviewLink'	=> EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=dashboard&layout=teamblogs' , false , true )
			);

			// If blog post is being posted from the back end and SH404 is installed, we should just use the raw urls.
			$sh404exists	= EasyBlogRouter::isSh404Enabled();

			if( JFactory::getApplication()->isAdmin() && $sh404exists )
			{
				$data[ 'authorLink' ]	= JURI::root() . 'index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $user->id;
				$data[ 'reviewLink' ]	= JURI::root() . 'index.php?option=com_easyblog&view=dashboard&layout=teamblogs';
			}

			$emailTitle	= JText::_( 'COM_EASYBLOG_TEAMBLOG_NEW_REQUEST' );

			$notification->send( $emails , $emailTitle , 'email.teamblog.request' , $data );
		}
	}

	public function sendApprovalEmail( $approvalType )
	{
		$user 		= EasyBlogHelper::getTable( 'Profile' );
		$user->load( $this->user_id );

		$team 		= EasyBlogHelper::getTable( 'TeamBlog' );
		$team->load( $this->team_id );

		$template 	= ( $approvalType ) ? 'email.teamblog.approved' : 'email.teamblog.rejected';


		$obj 				= new stdClass();
		$obj->unsubscribe	= false;
		$obj->email 		= $user->user->email;

		$emails				= array( $obj->email => $obj );

		$data 				= array(
										'teamName'			=> $team->title,
										'teamDescription'	=> $team->getDescription(),
										'teamAvatar'		=> $team->getAvatar(),
										'teamLink'			=> EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=teamblog&layout=listings&id=' . $this->team_id , false , true )
		);

		// If blog post is being posted from the back end and SH404 is installed, we should just use the raw urls.
		$sh404exists	= EasyBlogRouter::isSh404Enabled();

		if( JFactory::getApplication()->isAdmin() && $sh404exists )
		{
			$data[ 'teamLink' ]	= JURI::root() . 'index.php?option=com_easyblog&view=teamblog&layout=listings&id=' . $team->id;
		}

		$notification 		= EasyBlogHelper::getHelper( 'Notification' );
		$notification->send( $emails , JText::_('COM_EASYBLOG_TEAMBLOG_JOIN_REQUEST') , $template , $data );
	}

	/**
	 * Override parent's store method.
	 */
	public function store()
	{
		return parent::store();
	}
}
