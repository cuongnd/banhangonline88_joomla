<?php
// namespace administrator\components\com_jchat\models\fields;
/**  
 * @package JCHAT::components::com_jchat 
 * @subpackage models
 * @subpackage fields
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html   
 */ 
defined ( '_JEXEC' ) or die ( 'Restricted access' );
if(!class_exists('JFormFieldList')) {
	require_once JPATH_SITE . '/libraries/joomla/form/fields/list.php';
}

/**  
 * Templates selector
 * 
 * @package JCHAT::components::com_jchat 
 * @subpackage models
 * @subpackage fields
 */ 
class JFormFieldLoadTemplates extends JFormFieldList {
	function getOptions() {
		$options = array ();
		
		$path = JPATH_SITE . '/components/com_jchat/css/templates';
		$iterator = new DirectoryIterator ( $path );
		foreach ( $iterator as $fileEntity ) {
			$fileName = $fileEntity->getFilename ();
			if (! $fileEntity->isDot () && ! $fileEntity->isDir () && $fileName !== 'index.html' && strpos($fileName, 'default') !== 0) {
				$name = ucfirst ( $fileEntity->getBasename ( '.css' ) );
				$options [] = JHTML::_ ( 'select.option', $fileEntity->getFilename (), $name );
			}
		}
		$options = array_merge ( $options, parent::getOptions () );
		sort($options);
		array_unshift($options, JHTML::_ ( 'select.option', 'default.css', '  -Default - ' ));
		return $options;
	}
}
