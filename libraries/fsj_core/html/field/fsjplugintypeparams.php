<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('text');
jimport('fsj_core.lib.utils.plugin_handler');

class JFormFieldFSJPluginTypeParams extends JFormFieldText
{
	protected $type = 'FSJPluginTypeParams';
	
	function __construct()
	{
		parent::__construct();
	}

	protected function getLabel()
	{
		return "";	
	}
	
	protected function getInput()
	{
		$plugin_type_id = $this->form->getValue('name');
		$params = @json_decode($this->form->getValue('settings'), true);
		
		$data = array();
		if (is_array($params))
		{
			foreach ($params as $key => $value)
			{
				$data['plugin_' . $key] = $value;	
			}
		}
		
		$plugin_type = FSJ_Plugin_Handler::GetPluginType($plugin_type_id);
		
		$xmlfile = JPATH_ROOT.DS.$plugin_type->path . DS . "{$plugin_type_id}.xml";
		
		$xml = simplexml_load_file($xmlfile);
		
		//print_p($xml->type_settings);
		
		$text = $xml->type_settings->asXML();
		$text = str_replace("<type_settings>", "<form>", $text);
		$text = str_replace("</type_settings>", "</form>", $text);
		
		$form_xml = @simplexml_load_string($text);
		
		if (!$form_xml)
			return "";
		
		foreach ($form_xml->fields as $fields)
			$fields->attributes()->name = "plugin_" . $fields->attributes()->name;
		
		$form = JForm::getInstance("plugin.{$plugin_type_id}", $form_xml->asXML());
		$form->bind($data);
		
		//echo $form->getInput("yesno_1","params");
	
		$output = array();
		$output[] = "<div class='fsj'>";
	
		foreach ($form_xml->fields as $fields)
		{
			$output[] = '<div class="settings_block" style="clear: both;">';
			$output[] = '<h3>' . JText::_($fields->attributes()->display) .'</h3>';

			$output[] = '<table cellpadding="0" cellspacing="0" width="100%" class="fsj_settings_table">';
			$output[] = '<tbody>';
						
			foreach ($fields->field as $field)
			{	
				//print_p($field);
				$output[] = '<tr><td class="row0" width="200">';
				
				$output[] = $form->getLabel($field->attributes()->name, $fields->attributes()->name);
				
				$output[] = '</td><td class="row0" width="320">';
				
				$output[] = $form->getInput($field->attributes()->name, $fields->attributes()->name);
				
				$output[] = '</td><td class="row0">';
				
				$output[] = '<div class="fsj_settings_description">' . JText::_($field->attributes()->description) . '</div>';
				
				//<div class="fsj_settings_description">**Show page title when displaying an introduction text**</div>
				$output[] = '</td></tr>';
			}	
			$output[] = '</tbody>';
			$output[] = '</table>';
		
			$output[] = "</div>";
			//$output[] = "</div>";
		}
	
		return implode($output);
	}
	
	function AdminDisplay($value, $name, $item)
	{
		return "";	
	}
	
	
	function doSave($field, &$data)
	{
		$result = array();
		
		foreach ($_POST as $key => $value)
		{
			if (!is_array($value))
				continue;
			
			$parts = explode("_", $key, 2);
			
			if (count($parts) != 2)
				continue;
			
			$result[$parts[1]] = JRequest::getVar($key);	
		}
		
		$data['settings'] = json_encode($result);
	}	
}
