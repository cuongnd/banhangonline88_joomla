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

FD::import( 'admin:/controllers/controller' );

class EasySocialControllerEasySocial extends EasySocialController
{
	/**
	 * Checks to see if there are any new columns that are added to the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function sync()
	{
		// FD::checkToken();

		$affected	= FD::syncDB();

		$view 		= $this->getCurrentView();

		if( !$affected )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_NO_COLUMNS_TO_UPDATE' ) );
		}
		else
		{
			$view->setMessage( JText::sprintf( 'COM_EASYSOCIAL_UPDATED_COLUMNS' , $affected ) );
		}

		return $view->call( __FUNCTION__ );
	}

	/**
	 *
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCountries()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view 		= $this->getCurrentView();

		$model 		= FD::model( 'Users' );

		// Get a list of countries
		$countries 		= $model->getUniqueCountries();

		return $view->call( __FUNCTION__ , $countries );
	}

	/**
	 * Checks with the server for the current and latest version from the server.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function versionChecks()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view 		= $this->getCurrentView();

		// Get the current version.
		$localVersion	= FD::getLocalVersion();

		// Get the latest version online.
		$onlineVersion 	= FD::getOnlineVersion();

		return $view->call( __FUNCTION__ , $localVersion , $onlineVersion );
	}

	/**
	 * Purges the less cache files on the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function clearCache()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view 	= $this->getCurrentView();

		$purgeJS	= JRequest::getBool( 'script-cache' );

		if( $purgeJS )
		{
			// Clear javascript files
			$configuration	= FD::getInstance( 'Configuration' );
			$configuration->purge();

			$compiler = FD::getInstance( 'Compiler' );
			$compiler->purgeResources();
		}

		$purgeLess	= JRequest::getBool( 'stylesheet-cache' );

		if( $purgeLess )
		{
			// Compile site themes
			$templates = JFolder::folders(EASYSOCIAL_SITE_THEMES);

			foreach ($templates as $template) {
				$task = FD::stylesheet('site', $template)->purge();
			}

			// Compile admin themes
			$templates = JFolder::folders(EASYSOCIAL_ADMIN_THEMES);
			foreach ($templates as $template) {
				$task = FD::stylesheet('admin', $template)->purge();
			}

			// Compile modules
			$modules = FD::stylesheet('module')->modules();
			foreach ($modules as $module) {
				$task = FD::stylesheet('module', $module)->purge();
			}
		}

		$message 	= JText::sprintf( 'COM_EASYSOCIAL_CACHE_PURGED_FROM_SITE' );

		$view->setMessage( $message , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ );
	}
}
