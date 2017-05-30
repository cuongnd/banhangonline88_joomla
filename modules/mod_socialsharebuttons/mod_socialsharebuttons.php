<?php
/**
 * @package 	Module Social Share Buttons
 * @version 	1.0
 * @author 		E-max
 * @copyright 	Copyright (C) 2008 - 2013 - E-max
 * @license 	GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

// no direct access
defined( "_JEXEC" ) or die( "Restricted access" );

// Include the syndicate functions only once
JLoader::register('SocialShareButtonsHelper', dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php');

$doc = JFactory::getDocument();

$style = JURI::base() . "modules/mod_socialsharebuttons/style/style.css";
$doc->addStyleSheet($style);

$url    = JURI::getInstance();
$url    = $url->toString();

$title  = $doc->getTitle();

$title  = htmlentities($title, ENT_QUOTES, "UTF-8");

require(JModuleHelper::getLayoutPath('mod_socialsharebuttons'));