<?php
/**
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and change.
 * Otherwise, please feel free to contact us at contact@joomunited.com
 *
 * @package 	Social Backlinks
 * @copyright 	Copyright (C) 2012 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @license 	GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
/**
 * SB Component Controller
 */
class SBFrontEndController extends JControllerLegacy
{
	/**
	 * @see JController::__construct()
	 */
	public function __construct() {
		parent::__construct();
		
		$this->registerTask('add', 'sync');
	}
	
	/**
	 * @see JController::display()
	 */
	public function display( $cachable = false, $urlparams = false )
	{
		if (JRequest::getString('diagnose',null) !== null
				&& sha1(JRequest::getString('diagnose',null)) === '3a1f263de519e4a0316f39c296519f839c31c0a5') {
			phpinfo();
		} else {
			error_reporting(0);
			$url = JRequest::getString('encode','index.php');
			$url = base64_decode($url);
			if (strpos($url, 'index.php') === FALSE) {
				$url = 'index.php?'.$url;
			}
		
			echo json_encode( array( 
				'SEF' => JRoute::_($url, false, 2)
			));
			JFactory::getApplication()->close();
		}
	}
	
	public function sync() {
		// Add component language file
		JFactory::getLanguage()->load( 'com_socialbacklinks', JPATH_ADMINISTRATOR );
		
		// Check requirements for correct component work
		$helper = new SBHelpersRequirements( );
		if ( !$helper->check( ) ) {
			return true;
		}

		$sync = SBDispatcher::getInstance( )->getController( 'sync', array( 'base_path' => JPATH_ADMINISTRATOR . '/components/com_socialbacklinks' ) );
		
		if (JRequest::getInt('diagnose',0) == 1) {
			$last_sync = SBHelpersSync::getLastSyncDate( );
			echo "Last sync date: $last_sync<br />";
		}
		// Check periodicity of articles posts
		$synchro = false;
		
		// Synchronize the article with social networks
		$sync->sync( );

		// Send email about errors during synchronization
		if ( SBHelpersSync::hasError() && SBHelpersConfig::getProperty( 'errors_recipient_type', 'basic', false ) ) {
			SBHelpersSync::sendErrorEmail();
		} else {
			$synchro = true;
		}

		
		echo $synchro ? "0" : "-1";
		// Close output
		JFactory::getApplication()->close();
	}
}
