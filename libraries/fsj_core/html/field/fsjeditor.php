<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('editor');

class JFormFieldFSJEditor extends JFormFieldEditor
{
	public $type = 'FSJEditor';

	static $hasJs = false;

	static function includeJS()
	{
		if (!self::$hasJs)
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

		self::$hasJs = true;
	}

	protected function getInput()
	{
		$mode = "joomla";
		if ($this->element['fsjeditor_mode'])
			$mode = $this->element['fsjeditor_mode'];
		
		if ($this->element['fsjeditor_modesetting'])
		{
			list($set, $key) = explode(".", $this->element['fsjeditor_modesetting'], 2);
			$asset_id = $this->form->getValue('asset_id');
			FSJ_Settings::LoadBaseSettings(JRequest::getVar('option'));
			$mode = FSJ_Settings::Get($set, $key, $asset_id);
		}
		
		if ($mode == "code")
		{
			return $this->CodeInput();	
		}
		
		if ($this->element['fsjeditor_width'])
		{
			return "<div style='width:".$this->element['fsjeditor_width']."%'>".parent::getInput()."</div>";
		} else {
			return parent::getInput();
		}
	}
	
	function CodeInput()
	{
		self::includeJS();

		$class = ' class="fsj_codemirror_editor html" ';
		
		$columns = $this->element['cols'] ? ' cols="' . (int) $this->element['cols'] . '"' : '';
		$rows = $this->element['rows'] ? ' rows="' . (int) $this->element['rows'] . '"' : '';

		$codetype = "htmlmixed";
		if (isset($this->element['fsjeditor_codetype']))
			$codetype = $this->element['fsjeditor_codetype'];
		
		$codetype = " codetype='$codetype' ";

		$styles = "display:inline-block;";
		if (isset($this->element['fsjeditor_clear']))
			$styles .= "clear: both;";
		
		if ($this->element['fsjeditor_width'])
			$styles .= "width:".$this->element['fsjeditor_width']."%;";
		else 
			$styles .= "width:60%;";
		
		return '<div style="'.$styles.'"><textarea name="' . $this->name . '" id="' . $this->id . '"' . $columns . $rows . $class . $codetype . '>'
			. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '</textarea></div>';		
	}
	
	function doSave($field, &$data, $set = 'jform')
	{
		$alldata = JRequest::getVar($set, '', 'post', null, JREQUEST_ALLOWRAW);	
		$data[$field] = $alldata[$field];
	}

	function AdminDisplay($value, $name, $item)
	{
		$limit = isset($this->fsjeditor->limit) ? $this->fsjeditor->limit : 250;

		if ($this->fsjeditor->mode == "code")
		{
			$codetype = "htmlmixed";
			if (isset($this->fsjeditor->codetype)) $codetype = $this->fsjeditor->codetype;
			$codetype = " codetype='$codetype' ";

			if (strlen($value) > $limit)
			{
				$value = substr($value, 0, $limit) . "&hellip;";
			}

			echo "<pre>" . $value . "</pre>";

		} else {
			$value = strip_tags($value);
			if (strlen($value) > $limit)
			{
				$value = substr($value, 0, $limit) . "&hellip;";
			}
		}
	}	
}
