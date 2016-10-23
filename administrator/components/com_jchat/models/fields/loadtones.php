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
 * Ringtones selector
 * 
 * @package JCHAT::components::com_jchat 
 * @subpackage models
 * @subpackage fields
 */ 
class JFormFieldLoadTones extends JFormFieldList {
	function getOptions() {
		$options = array ();
		
		$path = JPATH_SITE . '/components/com_jchat/sounds/mp3/ringtones';
		$iterator = new DirectoryIterator ( $path );
		foreach ( $iterator as $fileEntity ) {
			$fileName = $fileEntity->getFilename ();
			if (! $fileEntity->isDot () && ! $fileEntity->isDir () && $fileName !== 'index.html' && strpos($fileName, 'default') !== 0) {
				$name = str_replace('_', ' ', ucfirst ( $fileEntity->getBasename ( '.mp3' ) ) );
				$options [] = JHTML::_ ( 'select.option', $fileEntity->getFilename (), $name );
			}
		}
		$options = array_merge ( $options, parent::getOptions () );
		return $options;
	}
}
