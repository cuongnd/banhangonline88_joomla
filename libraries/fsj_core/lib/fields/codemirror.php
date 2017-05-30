<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class FSJ_CodeMirror 
{
	static function IncludeCodeMirror()
	{
		$document = JFactory::getDocument();
		FSJ_Page::Script('libraries/fsj_core/third/codemirror/js/codemirror.js'); 
		FSJ_Page::Script('libraries/fsj_core/third/codemirror/js/init.js'); 
		FSJ_Page::Script('libraries/fsj_core/third/codemirror/js/modes/css/css.js'); 
		FSJ_Page::Script('libraries/fsj_core/third/codemirror/js/modes/javascript/javascript.js'); 
		FSJ_Page::Script('libraries/fsj_core/third/codemirror/js/modes/xml/xml.js'); 
		FSJ_Page::Script('libraries/fsj_core/third/codemirror/js/modes/htmlmixed/htmlmixed.js'); 
		FSJ_Page::Script('libraries/fsj_core/third/codemirror/js/modes/sql/sql.js'); 
		FSJ_Page::Script('libraries/fsj_core/third/codemirror/js/modes/clike/clike.js'); 
		FSJ_Page::Script('libraries/fsj_core/third/codemirror/js/modes/php/php.js'); 
		FSJ_Page::Script('libraries/fsj_core/third/codemirror/js/modes/smarty/smarty.js'); 
		FSJ_Page::Script('libraries/fsj_core/third/codemirror/js/modes/smartymixed/smartymixed.js'); 
		FSJ_Page::Style('libraries/fsj_core/third/codemirror/css/codemirror.css'); 	
	}	
}