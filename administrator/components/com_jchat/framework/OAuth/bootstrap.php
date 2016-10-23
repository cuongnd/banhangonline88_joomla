<?php
// namespace administrator\components\com_jchat\framework;
/**
 *
 * @package JCHAT::CONFIG::administrator::components::com_jchat
 * @subpackage framework
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace OAuth;
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/*
 * Bootstrap the library.
 */
if(!class_exists('OAuth\Common\AutoLoader')) {
	require_once __DIR__ . '/Common/AutoLoader.php';
	
	$autoloader = new Common\AutoLoader(__NAMESPACE__, dirname(__DIR__));
	
	$autoloader->register();
}