<?php
/**
 * @package JCHAT::FRAMEWORK::administrator::components::com_jchat
 * @subpackage framework
 * @subpackage composer
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname(dirname(dirname(dirname($vendorDir))));

return array(
    'Stichoza\\GoogleTranslate\\' => array($vendorDir . '/stichoza/google-translate-php/src/Stichoza/GoogleTranslate'),
    'React\\Promise\\' => array($vendorDir . '/react/promise/src'),
    'GuzzleHttp\\Stream\\' => array($vendorDir . '/guzzlehttp/streams/src'),
    'GuzzleHttp\\Ring\\' => array($vendorDir . '/guzzlehttp/ringphp/src'),
    'GuzzleHttp\\' => array($vendorDir . '/guzzlehttp/guzzle/src'),
);
