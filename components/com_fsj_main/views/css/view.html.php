<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view' );
jimport('joomla.filesystem.file');
jimport('fsj_core.admin.update');

class fsj_mainViewcss extends JViewLegacy
{
	function display($tpl = null)
	{
		$file = JRequest::getVar('file');

		$file = str_replace("/", "", $file);
		$file = str_replace("\\", "", $file);
		
		header("Content-type: text/css");
		readfile(JPATH_ROOT . DS . "cache" . DS . "fsj" . DS . "css" . DS . $file);
		exit;
	}
}
