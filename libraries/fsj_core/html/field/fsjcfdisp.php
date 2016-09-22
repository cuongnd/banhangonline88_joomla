<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('groupedlist');
jimport('fsj_core.lib.fields.fields');
jimport('fsj_core.lib.utils.plugin_handler');

class JFormFieldFSJCFDisp extends JFormField
{
	protected $type = 'FSJCFDisp';
	
	function getTabLabels()
	{
		$this->loadFieldData();

		$tabfield = $this->element['fsjcfdisp_tabs'];
	
		$this->tablist = array();

		foreach ($this->fields as $field)
		{
			if ($tabfield)
			{
				if (isset($field->$tabfield))
				{
					$tab_key = FSJ_Helper::MakeKey($field->$tabfield);
					
					if ($tab_key == "")
						$tab_key = "xxx-data-xxx";
					
					if (array_key_exists($tab_key, $this->tablist)) continue;
					
					$this->tablist[$tab_key] = $field->$tabfield;

					$tab_name = $field->$tabfield;
				
					if ($tab_name == "")
						$tab_name = $this->element['fsjcfdisp_deftab'];
					if ($tab_name == "")
						$tab_name = "Data Item";
				
					echo "<li><a href='#form_tab_{$tab_key}' data-toggle='tab'>".(string)$tab_name."</a></li>";
				}				
			}
		}
	}
	
	function getTabContent()
	{
		$tabfield = $this->element['fsjcfdisp_tabs'];
		foreach ($this->tablist as $tab_id => $tab_name)
		{
			// output the tab!	
			echo "<div class='tab-pane' id='form_tab_{$tab_id}'>";
			echo '	<div class="form-horizontal">';

			foreach ($this->fields as $field)
			{
				if (!isset($field->$tabfield)) continue;
				
				if ($field->$tabfield != $tab_name) continue;
				
				echo '<div class="control-group" id="cfform-'.$field->name.'">';
				echo '<label class="control-label" id="cfform-'.$field->name.'-label">';
				echo $this->form->getLabel($field->name, "data_form");
				echo '</label>';
				echo '<div class="controls" id="cfform-'.$field->name.'-input">';
				echo $this->form->getInput($field->name, "data_form");
				echo '</div>';
				echo '</div>';
			}
			
			echo '	</div>';
			echo '</div>';			
		}
	}
	
	function loadFieldData()
	{
		$this->data = json_decode($this->value);

		if (!is_object($this->data))
			$this->data = new stdClass();
	
		$this->fields = $this->getFields();
		
		$this->field_types = FSJ_Plugin_Handler::GetPlugins("custfield");

		// create a form xml based on the fields in $fields
		$xml = "<?xml version='1.0'?>";
		$xml .= "<form>";
		$xml .= "<fields name='data_form'>";
		
		// for all the fields, setup the xml required
		
		foreach ($this->fields as $field)
		{
			$xml .= $this->FieldToXML($field);	
		}

		$xml .= "  </fields>";
		$xml .= "</form>";
		
		try {
			$this->form = JForm::getInstance('data_form', $xml, array('control' => 'jform'), false);
		} catch (exception $e)
		{
			ob_clean();
			print_p($e);
			echo "<pre>";
			echo htmlentities($xml);
			echo "</pre>";
			exit;
		}
		$basedata = new stdClass();
		$basedata->data_form = $this->data;
		
		$this->form->bind($basedata);
	}
	
	protected function getInput()
	{
		return "";
	}
	
	function getFields()
	{
		$sql = $this->element['fsjcfdisp_sql'];
		$sql = FSJ_Helper::ParseDataFields($sql, $this->form, true);

		$name = $this->element['fsjcfdisp_name'];
		$type = $this->element['fsjcfdisp_type'];
		$params = $this->element['fsjcfdisp_params'];
		
		$db = JFactory::getDBO();
		$db->setQuery($sql);
		
		//echo $sql . "<br>";

		$fields = array();
		
		$rows = $db->loadObjectList();

		//print_p($rows);

		foreach ($rows as &$row)
		{
			$row->params = json_decode($row->params);
		}

		return $rows;
	}
	
	function FieldToXML($field)
	{
		$field_obj = $this->field_types[$field->fieldtype];

		if (isset($field_obj->params->xml) && $field_obj->params->xml == "code")
		{
			$field_code = FSJ_Plugin_Handler::GetInstance($field_obj);
			if ($field_code)
				return $field_code->ToXML($field);
		}

		$xml = "<field name=\"" . htmlspecialchars($field->name) . "\" type=\"" . htmlspecialchars($field->fieldtype) . "\" label=\"" . htmlspecialchars($field->title) . "\" ";	
	
		if (is_array($field->params) || is_object($field->params))
			foreach ($field->params as $key => $value)
				$xml .= " $key='$value' ";
		
		$xml .= "></field>";
		
		//echo htmlentities($xml);
		return $xml;
	}
	
	function doSave($field, &$data)
	{
		$params = JRequest::getVar('jform');
		$data[$field] = json_encode($params['data_form']);
	}
	
	function AdminDisplay($value, $name, $item)
	{
		return $value;
	}
}
