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

// Necessary to import the custom view.
FD::import( 'site:/views/views' );

class EasySocialViewActivities extends EasySocialSiteView
{
	/**
	 * Responsible to output the single stream layout.
	 *
	 * @access	public
	 * @return	null
	 */
	public function display( $tpl = null )
	{
		// Unauthorized users should not be allowed to access this page.
		FD::requireLogin();

		// Check for user profile completeness
		FD::checkCompleteProfile();

		$config		= FD::config();

		// Get the current logged in user.
		$user 		= FD::user();

		$filterType		= JRequest::getVar( 'type' , 'all' );
		$context		= SOCIAL_STREAM_CONTEXT_TYPE_ALL;
		$active 		= $filterType;

		switch( $filterType )
		{
			case 'all':
				$title = JText::_( 'COM_EASYSOCIAL_ACTIVITY_YOUR_LOGS' );
				break;
			case 'hidden':
				$title = JText::_( 'COM_EASYSOCIAL_ACTIVITY_YOUR_HIDDEN_ACTIVITIES' );
				break;
			case 'hiddenapp':
				$title = JText::_( 'COM_EASYSOCIAL_ACTIVITY_YOUR_HIDDEN_APPS' );
				break;
			case 'hiddenactor':
				$title = JText::_( 'COM_EASYSOCIAL_ACTIVITY_YOUR_HIDDEN_ACTORS' );
				break;
			default:
				$title = JText::sprintf( 'COM_EASYSOCIAL_ACTIVITY_ITEM_TITLE', ucfirst( $filterType ) );
				break;
		}

		// Set the page title
		FD::page()->title($title);

		// Set the page breadcrumb
		FD::page()->breadcrumb($title);

		if( $filterType != 'all' && $filterType != 'hidden' && $filterType != 'hiddenapp' && $filterType != 'hiddenactor' ) {
			$context    	= $filterType;
			$filterType		= 'all';
		}

		// Load up activities model
		$model 		= FD::model( 'Activities' );


		if ($filterType == 'hiddenapp') {
			$activities		= $model->getHiddenApps( $user->id );
			$nextLimit		= $model->getNextLimit( '0' );
		}
		else if($filterType == 'hiddenactor') {
			$activities		= $model->getHiddenActors( $user->id );
			$nextLimit		= $model->getNextLimit( '0' );
		} else {
			// Retrieve user activities.
			$stream		= FD::stream();
			$options 	= array( 'uId' => $user->id, 'context' => $context, 'filter' => $filterType );

			$activities = $stream->getActivityLogs($options);
			$nextLimit  = $stream->getActivityNextLimit();
		}

		// Get a list of apps
		$tmpApps		= $model->getApps();
		$apps 			= array();

		foreach( $tmpApps as $app )
		{
			if( $app->hasActivityLog() )
			{
				$app->favicon 	= '';
				$app->image 	= $app->getIcon();
				$favicon 		= $app->getFavIcon();

				if ($favicon) {
					$app->favicon	= $favicon;
				}

				// Load app's css
				$app->loadCss();

				$apps[]		= $app;
			}
		}

		$this->set( 'active'		, $active );
		$this->set( 'title'			, $title );
		$this->set( 'apps'			, $apps );
		$this->set( 'user'	 		, $user );
		$this->set( 'activities'	, $activities );
		$this->set( 'nextlimit'		, $nextLimit );

		$this->set( 'filtertype', $filterType );

		echo parent::display( 'site/activities/default' );


	}

}
