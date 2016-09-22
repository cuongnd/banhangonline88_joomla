<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Include main controller
FD::import( 'admin:/controllers/controller' );

class EasySocialControllerMigrators extends EasySocialController
{
	/**
	 * Class Constructor.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Runs the checking of the extension
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function check()
	{
		// Check for request forgeries
		FD::checkToken();

		// Retrieve the view.
		$view 		= $this->getCurrentView();

		// Get the component name
		$component	= JRequest::getCmd( 'component', '');

		$migrator	= FD::get( 'Migrators', $component );
		$obj		= $migrator->isComponentExist();

		// Return the data back to the view.
		return $view->call( __FUNCTION__ , $obj );
	}

	/**
	 * Processes the migration item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function process()
	{
		// Check for request forgeries
		FD::checkToken();

		// Retrieve the view.
		$view 		= $this->getCurrentView();

		$component 		= JRequest::getVar( 'component', '');
		$item 			= JRequest::getVar( 'item', '' );
		$mapping 		= JRequest::getVar( 'mapping', '' );
		$updateconfig 	= JRequest::getBool( 'updateconfig', 0 );

		$migrator	= FD::get( 'Migrators', $component );

		// Set the user mapping
		$migrator->setUserMapping( $mapping );

		// we need to check if we need to update config or not.
		if (empty($item)) {
			$configTable	= FD::table( 'Config' );
			$config 		= FD::registry();

			if ($configTable->load('site')) {
				$config->load($configTable->value);
				if( $config->get('points.enabled') == 1)
				{
					$config->set('points.enabled', 0);

					// Convert the config object to a json string.
					$jsonString = $config->toString();
					$configTable->set('value', $jsonString);

					// Try to store the configuration.
					if ($configTable->store()) {
						$updateconfig = true;

						// we need to reload the config
						$esConfig = new SocialConfig();
						$esConfig->reload();
					}
				}
			}
		}

		// Process the migration
		$obj 	  = $migrator->process( $item );

		if ($obj->continue == false && $updateconfig == true) {
			// now we need to re-enable back the points setting.
			$configTable	= FD::table( 'Config' );
			$config 		= FD::registry();

			if ($configTable->load('site')) {
				$config->load($configTable->value);
				$config->set('points.enabled', 1);

				// Convert the config object to a json string.
				$jsonString = $config->toString();
				$configTable->set('value', $jsonString);

				// Try to store the configuration.
				$configTable->store();
				$updateconfig = false;
			}
		}

		// Return the data back to the view.
		return $view->call( __FUNCTION__ , $obj, $updateconfig );
	}

	/**
	 * Scans for rules throughout the site.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function scan()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the allowed rule scan sections
		$config		= FD::config();

		// Retrieve info lib.
		$info 		= FD::info();

		// Retrieve the view.
		$view 		= FD::view( 'Privacy', true );

		// Get the current path that we should be searching for.
		$file 		= JRequest::getVar( 'file' , '' );

		// Log errors when invalid data is passed in.
		if( empty( $file ) )
		{
			FD::logError( __FILE__ , __LINE__ , 'Privacy Scan: Invalid file path given to scan.' );
		}

		// Retrieve the points model to scan for the path
		$model 	= FD::model( 'Privacy' );

		$obj 			= new stdClass();

		// Format the output to display the relative path.
		$obj->file		= str_ireplace( JPATH_ROOT , '' , $file );
		$obj->rules 	= $model->install( $file );

		return $view->call( __FUNCTION__ , $obj );
	}
}
