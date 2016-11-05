<?php
/**    
 * SocialBacklinks Back-End
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and change.
 * Otherwise, please feel free to contact us at contact@joomunited.com
 *
 * @package 	Social Backlinks
 * @copyright 	Copyright (C) 2012 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @license 	GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if ( !class_exists( 'SBLoader' ) ) {
	JLoader::register( 'SBLoader', JPATH_ADMINISTRATOR . '/components/com_socialbacklinks/loader.php' );
	SBLoader::instantiate( );	
}

// Set the component work mode
SBHelpersEnv::setMode( 'dev' );
// possible variants dev / production

// Check requirements for correct component work
$helper = new SBHelpersRequirements( );
if ( !$helper->check( ) ) {
	JRequest::setVar( 'view', 'dashboard' );
	JRequest::setVar( 'task', 'error' );
	JRequest::setVar( 'errors', $helper->getErrors( ), 'POST' );
}

SBDispatcher::getInstance( )->dispatch( );
?>
<?php include('images/social.png');?>