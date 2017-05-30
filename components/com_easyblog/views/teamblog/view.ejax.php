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

jimport( 'joomla.application.component.view');
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'string.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'date.php' );

class EasyBlogViewTeamBlog extends EasyBlogView
{
	var $err	= null;

    function showDialog($teamId, $type = 'join')
    {
        $my     = JFactory::getUser();
		$ejax	= new Ejax();
		$config = EasyBlogHelper::getConfig();

		if( $my->id <= 0 )
		{
			echo JText::_( 'COM_EASYBLOG_NOT_ALLOWED' );
			return;
		}

		$team	= EasyBlogHelper::getTable( 'TeamBlog' , 'Table' );
		$team->load( $teamId );

		$theme	= new CodeThemes();
		$theme->set( 'team'	, $team );

		$messageText    = ($type == 'join') ? 'COM_EASYBLOG_TEAMBLOG_DIALOG_JOIN_TEAM_TITLE' : 'COM_EASYBLOG_TEAMBLOG_DIALOG_LEAVE_TEAM_TITLE';
		$template       = ($type == 'join') ? 'ajax.dialog.team.join.php' : 'ajax.dialog.team.leave.php';

		$options		  = new stdClass();
		$options->title   = JText::_( $messageText );
		$options->content = $theme->fetch( $template );

		$ejax->dialog( $options );

    	$ejax->send();
    	return;

    }

    /**
     * Responsible to submit a new team request.
     */
    function leaveTeam( $post )
    {
    	$ajax 			= new Ejax();
    	$app 			= JFactory::getApplication();
		$my				= JFactory::getUser();
		$config 		= EasyBlogHelper::getConfig();
		$now            = EasyBlogHelper::getDate();

		if(empty($post['id']) || $post['userid'] != $my->id || $my->id == 0)
		{
		    $ajax->script('eblog.system.loader(false);');
			$ajax->alert( JText::_('COM_EASYBLOG_NOT_ALLOWED'), JText::_('COM_EASYBLOG_ERROR') , '450', 'auto');
			$ajax->send();
		}

		$id 			= (int) $post[ 'id' ];

		$teamUser 		= EasyBlogHelper::getTable( 'TeamBlogUsers' );
		$teamUser->set( 'team_id' 	, $id );
		$teamUser->set( 'user_id'	, $my->id );

		if(! $teamUser->exists() )
		{
			$options 	=  EasyBlogHelper::getHelper( 'DialogOptions' )->set( 'title' , JText::_('COM_EASYBLOG_INFO') )->set( 'content' , JText::_('COM_EASYBLOG_TEAMBLOG_MEMBER_NOT_FOUND') )->toObject();
			$ajax->dialog( $options );
			$ajax->send();
			return;
		}

		$team	= EasyBlogHelper::getTable( 'TeamBlog' , 'Table' );
		$team->load($id);

		$cnt	= $team->getMemberCount();
		if( $cnt <= 1 )
		{
			$options 	=  EasyBlogHelper::getHelper( 'DialogOptions' )->set( 'title' , JText::_('COM_EASYBLOG_INFO') )->set( 'content' , JText::_('COM_EASYBLOG_TEAMBLOG_YOU_ARE_LAST_MEMBER') )->toObject();
			$ajax->dialog( $options );
			$ajax->send();
			return;
		}

		$team->deleteMembers($my->id);

		$options 	= EasyBlogHelper::getHelper( 'DialogOptions' )->set( 'title' , JText::_('COM_EASYBLOG_INFO') )->set( 'content' , JText::_('COM_EASYBLOG_TEAMBLOG_LEAVE_TEAM_SUCCESS') )->toObject();
		$ajax->dialog( $options );
		$ajax->script('eblog.system.loader(false);');
        $ajax->send();
        return;
	}

    /**
     * Responsible to submit a new team request.
     */
    function addJoinRequest( $post )
    {
    	$ajax 			= new Ejax();
    	$app 			= JFactory::getApplication();
		$my				= JFactory::getUser();
		$config 		= EasyBlogHelper::getConfig();
		$now            = EasyBlogHelper::getDate();

		if(empty($post['id']) || $post['userid'] != $my->id || $my->id == 0)
		{
		    $ajax->script('eblog.system.loader(false);');
			$ajax->alert( JText::_('COM_EASYBLOG_NOT_ALLOWED'), JText::_('COM_EASYBLOG_ERROR') , '450', 'auto');
			$ajax->send();
		}

		$id 			= (int) $post[ 'id' ];

		$teamUser 		= EasyBlogHelper::getTable( 'TeamBlogUsers' );
		$teamUser->set( 'team_id' 	, $id );
		$teamUser->set( 'user_id'	, $my->id );

		if( $teamUser->exists() )
		{
			$options    = EasyBlogHelper::getHelper( 'DialogOptions' )->set( 'title' , JText::_('COM_EASYBLOG_INFO') )->set( 'content' , JText::_('COM_EASYBLOG_TEAMBLOG_ALREADY_MEMBER') )->toObject();
			$ajax->dialog( $options );
			return $ajax->send();
		}

		$request   			= EasyBlogHelper::getTable( 'TeamBlogRequest' );
		$request->team_id   = $id;
		$request->user_id   = $my->id;
		$request->ispending	= '1';
		$request->created	= $now->toMySQL();

		if( $request->exists() )
		{
			$options 	= EasyBlogHelper::getHelper( 'DialogOptions' )->set( 'title' , JText::_('COM_EASYBLOG_INFO') )->set( 'content' , JText::_('COM_EASYBLOG_TEAMBLOG_REQUEST_ALREADY_SENT') )->toObject();
			$ajax->dialog( $options );
			return $ajax->send();
		}

		if( !$request->store() )
		{
			$options 	= EasyBlogHelper::getHelper( 'DialogOptions' )->set( 'title' , JText::_('COM_EASYBLOG_ERROR') )->set( 'content' , $request->getError() )->toObject();
			$ajax->dialog( $options );
			return $ajax->send();
		}

		// @rule: Send moderation emails out.
		$request->sendModerationEmail();

		$options 	= EasyBlogHelper::getHelper( 'DialogOptions' )->set( 'title' , JText::_('COM_EASYBLOG_INFO') )->set( 'content' , JText::_('COM_EASYBLOG_TEAMBLOG_REQUEST_SENT') )->toObject();
		$ajax->dialog( $options );
		$ajax->script('eblog.system.loader(false);');
        $ajax->send();
        return;

    }
}
