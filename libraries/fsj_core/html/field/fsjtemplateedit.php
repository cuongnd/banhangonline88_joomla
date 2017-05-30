<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('text');

jimport('fsj_core.lib.utils.format');

class JFormFieldFSJTemplateEdit extends JFormField
{
	function getTabLabels()
	{
		$this->CodeMirrorJS();
		$this->loadType();

		foreach ($this->xml->tabs->tab as $tab)
		{
			$tip = (string)$tab->attributes()->tip;
			$tab_id = (string)$tab->attributes()->name;
			echo "<li><a href='#form_tab_$tab_id' data-toggle='tab'";
			if ($tip)
				echo " class='fsjTip' title='" . htmlspecialchars($tip, ENT_QUOTES) . "'";
			
			echo ">".(string)$tab->attributes()->title."</a></li>";
		}
		
		echo "<li><a href='#form_tab_css' data-toggle='tab'>CSS</a></li>";
	}
	
	function getTabContent()
	{
		$this->loadType();
		$this->com = $this->form->getValue("component");
		$this->type = $this->form->getValue("type");
		
		$js[] = "var fsjeditor_tabs = fsjeditor_tabs || {};";
		
		foreach ($this->xml->tabs->tab as $tab)
		{
			$tab_id = (string)$tab->attributes()->name;
			
			echo "<div class='tab-pane' id='form_tab_{$tab_id}'>";
			echo '	<div>';
			$js[] = "fsjeditor_tabs['form_tab_{$tab_id}'] = fsjeditor_tabs['form_tab_{$tab_id}'] || [];";

			$tip = (string)$tab->attributes()->tip;
			if ($tip)
				echo "<h4>" . $tip . "</h4>";	
			
			if ($tab->help)
				echo $tab->help;
			
			$this->process_sections($tab_id, $this->xml);
			
			$this->process_section_groups($tab_id, $this->xml);
			
			echo '	</div>';
			echo '</div>';
		}
		
		$js[] = "fsjeditor_tabs['form_tab_css'] = fsjeditor_tabs['form_tab_css'] || [];";

		echo '<div class="tab-pane" id="form_tab_css">';
		echo '	<div class="">';
		
		
		echo '<div class="control-group">';
		echo '<label class="control-label">CSS</label>';
		echo '<div class="controls">';
		
		echo "		<textarea class='fsj_codemirror_editor html' codetype='css' name='css' id='edit_css'>";
		if (isset($this->value['css']))
			echo htmlentities($this->value['css']);
		echo "		</textarea>";	
		
		echo '		<span class="help-inline"></span>';
		
		echo '</div>';
		echo '</div>';
		
		echo '	</div>';
		echo '</div>';
		
		$js[] = "fsjeditor_tabs['form_tab_css'].push('edit_css');";

		FSJ_Page::ScriptDec(implode("\n", $js));
	}
	
	function process_section_groups($tab_id, $xml)
	{
		foreach ($xml->section_group as $group)
		{
			if ($group->section)
				$this->process_sections($tab_id, $group);
			if ($group->section_group)
				$this->process_section_groups($tab_id, $group);
		}
	}
	
	function process_sections($tab_id, $xml)
	{
		$group_shown = false;
		
		foreach ($xml->section as $section)
		{
			$sec_tab = (string)$section->attributes()->tab;	
			if ($sec_tab != $tab_id)
				continue;
			
			if (!$group_shown && $xml->attributes()->id != $group_shown)
			{
				echo "<h5>" . $xml->attributes()->id . "</h5>";	
				$group_shown = $xml->attributes()->id;
			}
			
			$this->output_section($section);
		}
	}
	
	function output_section($section)
	{
		$tab_id = (string)$section->attributes()->tab;	
		
		echo '<div class="control-group">';
		echo '<label class="control-label">'.$section->attributes()->title.' <small>('.$this->com.".".$this->type.".".(string)$section->attributes()->name.')</small></label>';
		echo '<div class="controls">';
		
		echo "<table width='100%'><tr><td width='100%' valign='top'>";
		echo "<textarea class='fsj_codemirror_editor html' codetype='text/x-smarty' name='template[".(string)$section->attributes()->name."]' id='template_".(string)$section->attributes()->name."'>";
		if (isset($this->value['tmpl']) && isset($this->value['tmpl'][(string)$section->attributes()->name]))
			echo htmlentities($this->value['tmpl'][(string)$section->attributes()->name]);
		echo "</textarea>";
		echo "</td><td width='00%' valign='top'>";
		echo $this->DisplayHelp($section);
		echo "</td></tr></table>";
		
		$js[] = "fsjeditor_tabs['form_tab_{$tab_id}'].push('template_".(string)$section->attributes()->name."');";

		echo '</div>';
		echo '</div>';
	}
	
	function getInput()
	{
	}
	
	function DisplayHelp($section)
	{
		if (!$section->help)
			return;
		
		if (isset($section->help->text))
			echo $section->help->text;
		
		if (!$section->help->item)
			return;
		
		echo "<dl class=''>";
		
		foreach ($section->help->item as $item)
		{
			echo "<dt>";
			
			echo "{" . $item->attributes()->name . "}"; 
			
			echo "&nbsp;&nbsp;";
			
			if ($item->attributes()->type == "bool")
			{
				echo "B";	
			} else if ($item->attributes()->type == "section") {
				echo "S";
			}
			
			if ($item->example)
			{
				echo "X";		
			}
			
			echo "</dt>";
			echo "<dd>";
			echo $item->text;
			echo "</dd>";	
		}
		
		echo "</dl>";
	}
	
	function CodeMirrorJS()
	{
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
	
	function loadType()
	{
		$com = $this->form->getValue("component");
		$type = $this->form->getValue("type");
		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->select("*")
			->from("#__fsj_tpl_type")
			->where("component = '" . $db->escape($com) . "'")
			->where("type = '" . $db->escape($type) . "'");
			
		$db->setQuery($query);
		
		$this->type = $db->loadObject();
		
		if (!$this->type)
		{
			echo "Unable to load type : $com / $type<br>";
			exit;	
		}
		
		$this->xml = simplexml_load_file(JPATH_ROOT.DS.$this->type->xmlfile);
	}
	
	function doSave($field, &$data)
	{
		$data['params'] = json_encode(
								array(
									'tmpl' => JRequest::getVar('template', array(), 'post', 'array'),
									'css' => JRequest::getVar('css', '', 'post', 'string', JREQUEST_ALLOWRAW)
								)
							);
	}
	
}
