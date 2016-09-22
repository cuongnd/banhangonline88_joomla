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

jimport('joomla.application.component.controller');

class EasyBlogControllerTeamBlogs extends EasyBlogController
{	
	function __construct()
	{
		parent::__construct();
		
		$this->registerTask( 'add' , 'edit' );
		$this->registerTask( 'apply' , 'save' );

		$this->registerTask( 'publish'	 ,'publish' );
		$this->registerTask( 'unpublish' , 'unpublish' );
	}
	
	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'teamblog' );

		$post	= JRequest::get('post');
		
		$team_desc	= JRequest::getVar('write_description', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$post['description']    = $team_desc;
		
		if(!empty($post['title']))
		{
			$team	= EasyBlogHelper::getTable( 'TeamBlog' , 'Table' );
			
			$date	= EasyBlogHelper::getDate();
			$team->created	= $date->toMySQL();
			$team->bind( $post );
				
			$team->title = JString::trim($team->title);
			$team->alias = JString::trim($team->alias);
			
			$msgStatus 	= 'message';
			$message	= JText::_('COM_EASYBLOG_TEAM_BLOG_ADDED');
			
			if( $team->id != 0 )
				$message = JText::_('COM_EASYBLOG_TEAMBLOG_SAVED_SUCCESSFULLY');
				
			if($team->store())
			{
			
				//meta post info
				$metapost	= array();
				$metapost['keywords']		= JRequest::getVar('keywords', '');
				$metapost['description']	= JRequest::getVar('description', '');
				$metapost['content_id']		= $team->id;
				$metapost['type']			= META_TYPE_TEAM;

				$metaId		= JRequest::getVar( 'metaid' , '' );

				$meta		= EasyBlogHelper::getTable( 'Meta', 'Table' );
				$meta->load($metaId);
				$meta->bind($metapost);
				$meta->store();

				// @rule: Process groups
				if( isset( $post[ 'groups' ] ) )
				{
					foreach( $post[ 'groups' ] as $id )
					{
						$group			= EasyBlogHelper::getTable( 'TeamBlogGroup' , 'Table' );
						$group->team_id	= $team->id;
						$group->group_id	= $id;

						if( !$group->exists() )
						{
							$group->store();
						}
					}
				}

				if( isset( $post['deletegroups'] ) )
				{
					$delGroups	= explode(',', $post['deletegroups']);
					
					if(count($delGroups) > 0)
					{
					    foreach($delGroups as $id)
					    {
					        if( !empty($id) )
							{
								$team->deleteGroup( $id );
							}
					    }
					}
				}
				
				if( isset( $post['deletemembers'] ) )
				{
					$delMember	= explode(',', $post['deletemembers']);
					
					if(count($delMember) > 0)
					{
					    foreach($delMember as $id)
					    {
					        if( !empty($id) )
								$team->deleteMembers($id);
					    }
					}
				}
				
				// @rule: Process members
				if( isset( $post['members']) )
				{
					foreach( $post['members'] as $id )
					{
						$member				= EasyBlogHelper::getTable( 'TeamBlogUsers' , 'Table' );
						$member->team_id	= $team->id;
						$member->user_id	= $id;

						if( !$member->exists() )
						{
							$member->addMember();
						}
					}
				}
				
				$file = JRequest::getVar( 'Filedata', '', 'files', 'array' );
				if(! empty($file['name']))
				{
					$newAvatar  		= EasyBlogHelper::uploadTeamAvatar($team, true);
					$team->avatar   	= $newAvatar;
					$team->store(); //now update the avatar.
				}
				
			}
		}
		else
		{
			$msgStatus 	= 'error';
			$message	= JText::_('COM_EASYBLOG_INVALID_TEAM_BLOG_TITLE');
		}

		if( JRequest::getVar( 'task' ) == 'apply' )
		{
			$this->setRedirect( 'index.php?option=com_easyblog&c=teamblogs&task=edit&id=' . $team->id , $message , $msgStatus );
			return;
		}
		
		$this->setRedirect(  'index.php?option=com_easyblog&view=teamblogs' , $message , $msgStatus );
		return;		
	}

	function cancel()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'teamblog' );

		$this->setRedirect( 'index.php?option=com_easyblog&view=teamblogs' );
		
		return;
	}

	function edit()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'teamblog' );

		JRequest::setVar( 'view', 'teamblog' );
		JRequest::setVar( 'id' , JRequest::getVar( 'id' , '' , 'REQUEST' ) );
		
		parent::display();
	}

	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'teamblog' );

		$teams	= JRequest::getVar( 'cid' , '' , 'POST' );
		
		$message	= '';
		$type		= 'message';
		
		if( empty( $teams ) )
		{
			$message	= JText::_('Invalid Team id');
			$type		= 'error';
		}
		else
		{
			$table		= EasyBlogHelper::getTable( 'TeamBlog' , 'Table' );
			foreach( $teams as $id )
			{
				$table->load( $id );
				
				if( !$table->delete() )
				{
					$message	= JText::_( 'Error removing Team.' );
					$type		= 'error';
					$this->setRedirect( 'index.php?option=com_easyblog&view=teamblogs' , $message , $type );
					return;
				}
			}
			
			$message	= JText::_('Team(s) deleted');
		}

		$this->setRedirect( 'index.php?option=com_easyblog&view=teamblogs' , $message , $type );
	}

	function publish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'teamblog' );

		$teams	= JRequest::getVar( 'cid' , array(0) , 'POST' );
		$message	= '';
		$type		= 'message';
		
		if( count( $teams ) <= 0 )
		{
			$message	= JText::_('Invalid team id');
			$type		= 'error';
		}
		else
		{
			$team	= EasyBlogHelper::getTable( 'TeamBlog' , 'Table' );
			$team->publish( $teams );

			$message	= JText::_('Team(s) published');
		}

		$this->setRedirect( 'index.php?option=com_easyblog&view=teamblogs' , $message , $type );
	}

	function unpublish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'teamblog' );

		$teams	= JRequest::getVar( 'cid' , array(0) , 'POST' );
		$message	= '';
		$type		= 'message';
		
		if( count( $teams ) <= 0 )
		{
			$message	= JText::_('Invalid team id');
			$type		= 'error';
		}
		else
		{
			$team	= EasyBlogHelper::getTable( 'TeamBlog' , 'Table' );
			$team->publish( $teams , 0 );

			$message	= JText::_('Team(s) unpublished');
		}

		$this->setRedirect( 'index.php?option=com_easyblog&view=teamblogs' , $message , $type );
	}
	
	function markAdmin()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'teamblog' );

	    $teamId	= JRequest::getVar( 'teamid', '' );
	    $userId	= JRequest::getVar( 'userid', '' );
	    
	    if(empty($teamId) || empty($userId))
	    {
	        $this->setRedirect( 'index.php?option=com_easyblog&view=teamblogs');
	    }
	    
		$this->setAsAdmin($teamId, $userId, true);
	    
	    $this->setRedirect( 'index.php?option=com_easyblog&c=teamblogs&task=edit&id=' . $teamId);
	}
	
	function removeAdmin()
	{
		// Check for request forgeries
		JRequest::checkToken( 'GET' ) or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'teamblog' );

	    $teamId	= JRequest::getVar( 'teamid', '' );
	    $userId	= JRequest::getVar( 'userid', '' );

	    if(empty($teamId) || empty($userId))
	    {
	        $this->setRedirect( 'index.php?option=com_easyblog&view=teamblogs');
	    }

		$this->setAsAdmin($teamId, $userId, false);

	    $this->setRedirect( 'index.php?option=com_easyblog&c=teamblogs&task=edit&id=' . $teamId);
	}
	
	function setAsAdmin($teamId, $userId, $isAdmin)
	{
		// Check for request forgeries
		JRequest::checkToken( 'GET' ) or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'teamblog' );

	    $db = EasyBlogHelper::db();

	    $query  = 'UPDATE `#__easyblog_team_users` SET ';
	    if($isAdmin)
			$query	.= ' `isadmin` = ' . $db->Quote('1');
		else
		    $query	.= ' `isadmin` = ' . $db->Quote('0');
	    $query  .= ' WHERE `team_id` = ' . $db->Quote($teamId);
	    $query  .= ' AND `user_id` = ' . $db->Quote($userId);

	    $db->setQuery($query);
	    $db->query();
	    
	    return true;
	}
	
	function teamApproval()
	{
		// Check for request forgeries
		JRequest::checkToken( 'GET' ) or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'teamblog' );
		
		$mainframe	= JFactory::getApplication();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$config 	= EasyBlogHelper::getConfig();
		$document	= JFactory::getDocument();
		$my			= JFactory::getUser();

		$teamId 	= JRequest::getInt('team', 0);
		$approval	= JRequest::getInt('approve');
		$requestId	= JRequest::getInt('id', 0);

		$ok 		= true;
		$message    = '';
		$type       = 'info';
		
	    $teamRequest    = EasyBlogHelper::getTable( 'TeamBlogRequest','Table' );
	    $teamRequest->load($requestId);

		if($approval)
		{
		    $teamUsers    = EasyBlogHelper::getTable( 'TeamBlogUsers','Table' );

		    $teamUsers->user_id    = $teamRequest->user_id;
		    $teamUsers->team_id    = $teamRequest->team_id;

		    if($teamUsers->store())
			{
		        $message    = JText::_('COM_EASYBLOG_TEAMBLOGS_APPROVAL_APPROVED');
		    }
		    else
		    {
		        $ok 		= false;
		        $message    = JText::_('COM_EASYBLOG_TEAMBLOGS_APPROVAL_FAILED');
		        $type       = 'error';
			}
		}
		else
		{
		    $message    = JText::_('COM_EASYBLOG_TEAMBLOGS_APPROVAL_REJECTED');
		}

		if($ok)
		{
			$teamRequest->ispending = 0;
			$teamRequest->store();

			$teamBlog = EasyBlogHelper::getTable( 'TeamBlog','Table' );
			$teamBlog->load($teamRequest->team_id);

			//now we send notification to requestor
			$requestor  = JFactory::getUser($teamRequest->user_id);
			$template   = ($approval) ? 'email.teamblog.approved' : 'email.teamblog.rejected';

			$toNotifyEmails 	= array();
			$obj 				= new StdClass();
			$obj->unsubscribe	= false;
			$obj->email 		= $requestor->email;
			$toNotifyEmails[]   = $obj;

			$notify	= EasyBlogHelper::getHelper( 'Notification' );
			$emailData  = array();
			$emailData['team']  	= $teamBlog->title;
			$notify->send($toNotifyEmails, JText::_('COM_EASYBLOG_TEAMBLOGS_JOIN_REQUEST'), $template, $emailData);
		}

		$this->setRedirect( 'index.php?option=com_easyblog&view=teamrequest' , $message , $type );
	}
}