<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

require_once( DISCUSS_ROOT . '/views.php' );

class EasyDiscussViewAssigned extends EasyDiscussView
{
	public function display( $tpl = null )
	{
		$doc	= JFactory::getDocument();
		$my		= JFactory::getUser();

		DiscussHelper::setPageTitle( JText::_('COM_EASYDISCUSS_PAGETITLE_ASSIGNED') );

		$this->setPathway( JText::_('COM_EASYDISCUSS_BREADCRUMB_ASSIGNED') );

		if( !DiscussHelper::isModerator() )
		{
			$app 	= JFactory::getApplication();
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_YOU_ARE_NOT_ALLOWED_HERE' ) , DISCUSS_QUEUE_ERROR );
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=index' , false ) );
		}

		$subs		= array();

		// [Model:Assigned]
		$model 		= DiscussHelper::getModel( 'Assigned' );
		$posts		= $model->getPosts();
		$posts		= DiscussHelper::formatPost( $posts );

		$posts = Discusshelper::getPostStatusAndTypes( $posts );

		// Get total number of posts assigned to the current user.
		$totalAssigned	= $model->getTotalAssigned( $my->id );

		// Get total number of posts that is assigned to this user and resolved.
		// [Model:Assigned]
		$totalResolved	= $model->getTotalSolved( $my->id );

		// Calculate percentage
		$percentage 	= 0;

		if( $posts )
		{
			$percentage 	= round( ( $totalResolved / $totalAssigned ) * 100 , 2 );
		}


		$theme 		= new DiscussThemes();
		$theme->set( 'totalAssigned'	, $totalAssigned );
		$theme->set( 'totalResolved'	, $totalResolved );
		$theme->set( 'percentage'		, $percentage );
		$theme->set( 'posts'			, $posts );


		echo $theme->fetch( 'assigned.php' );
	}
}
