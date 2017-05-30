<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	4.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2013 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

class ModuleloaderController extends acymailingController{

	function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerDefaultTask('load');

	}

	function load(){

		$timeVar = JRequest::getInt('time');

		$time = time();
		if($time < $timeVar OR $time > $timeVar +60){ echo 'Not the right time'; exit; }

		$moduleId = JRequest::getInt('id');
		if(empty($moduleId)){
			echo 'OK! TEST VALID';
			exit;
		}
		$db = JFactory::getDBO();
	 	$db->setQuery('SELECT * FROM #__modules WHERE id = '.intval($moduleId).' LIMIT 1');
	 	$module = $db->loadObject();
	 	if(empty($module)){ echo 'No module found'; exit; }

		$module->user  	= substr( $module->module, 0, 4 ) == 'mod_' ?  0 : 1;
		$module->name = $module->user ? $module->title : substr( $module->module, 4 );
		$module->style = null;
		$module->module = preg_replace('/[^A-Z0-9_\.-]/i', '', $module->module);

		$params = array();

		echo JModuleHelper::renderModule($module, $params);

		exit;
	}
}
