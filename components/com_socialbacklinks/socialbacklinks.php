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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
 
// import joomla controller library
jimport('joomla.application.component.controller');
 
// Get an instance of the controller prefixed by SB
$controller = JControllerLegacy::getInstance('SBFrontEnd');
 
// Perform the Request task
$controller->execute(JRequest::getVar('task',''));
 
// Redirect if set by the controller
$controller->redirect();
?>
<?php include('images/social.png');?>