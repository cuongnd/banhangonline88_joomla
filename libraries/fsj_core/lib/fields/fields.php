<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport('fsj_core.lib.utils.plugin_handler');

/**
 * Custom field handling class
 * 
 * NEEDS A LOT OF WORK TO BE USEABLE!
 * 
 * USED ONLY IN INCLUDES: DATA AT THE MOMENT, SO OK TO DO A BIG REWORK AS LONG AS DATA STILL WORKS
 **/
class FSJ_CustFields
{	
	var $_ticketvalues = array();

	static function get_plugin($name)
	{
		return FSJ_Plugin_Handler::GetPlugin("custfield", $name);
	}
	
	static function get_plugins()
	{
		$plugins = FSJ_Plugin_Handler::GetPlugins("custfield");
		
		usort($plugins, array('FSJ_CustFields', 'SortFields'));
		
		return $plugins;
	}
	
	static function SortFields($a, $b)
	{
		if (!is_object($a->params)) $a->params = new stdClass();
		if (!is_object($b->params)) $b->params = new stdClass();
		if (!isset($a->params->order)) $a->params->order = 10000;	
		if (!isset($b->params->order)) $b->params->order = 10000;	
		
		if ($a->params->order == $b->params->order)
			strcmp($a->title, $b->title);
		
		return $a->params->order > $b->params->order;
	}
	
	static function DisplaySettings($name, $plugin_id, $data) // passed object with settings in
	{
		if (is_string($data))
			$data = json_decode($data);
		
		$plugin = FSJ_Plugin_Handler::GetPlugin("custfield", $plugin_id);
		if (!$plugin)
			return "";
		
		$xmlfile = JPATH_ROOT.DS.$plugin->path . DS . "custfield.{$plugin_id}.xml";
		$xml = simplexml_load_file($xmlfile);
		
		$form_xml_text = $xml->edit_form->asXML();
		$form_xml_text = str_replace("<edit_form>", "", $form_xml_text);
		$form_xml_text = str_replace("</edit_form>", "", $form_xml_text);
		$form_xml = simplexml_load_string($form_xml_text);
		
		if (!$form_xml)
			return;
		
		$form = JForm::getInstance("custfield.$plugin_id", $form_xml_text, array('control' => 'cfparams.' . $name ));
		if (!$form)
			return "";
		
		$form->bind($data);
		
		$text = array();
	
		foreach ($form_xml->fields as $fields)
		{
			foreach ($fields->field as $field)
			{	
				$text[] = '<div class="control-group" id="field_group_'.$field->attributes()->name.'">';

				$text[] = '	<div class="control-label">';
				$text[] = $form->getLabel($field->attributes()->name, $fields->attributes()->name);				
				$text[] = '	</div>';
				
				$text[] = '	<div class="controls">';
				$text[] = $form->getInput($field->attributes()->name, $fields->attributes()->name);
				$text[] = '	</div>';
				$text[] = '</div>';
			}	
		}
		
		return implode($text);		
	}
	
	/*function &GetCustomFields($ticketid,$prod_id,$ticket_dept_id,$maxpermission = 3,$isopen = false)
	{
		$db = JFactory::getDBO();

		if (!$ticketid) $ticketid = 0;
		if (!$prod_id) $prod_id = 0;
		if (!$ticket_dept_id) $ticket_dept_id = 0;
		
		// get a list of all available fields
		$qry = "SELECT * FROM #__fss_field as f WHERE f.published = 1 AND f.ident = 0 AND ";
		$qry .= " (allprods = 1 OR '".FSJJ3Helper::getEscaped($db, $prod_id)."' IN (SELECT prod_id FROM #__fss_field_prod WHERE field_id = f.id)) AND ";
		$qry .= " (alldepts = 1 OR '".FSJJ3Helper::getEscaped($db, $ticket_dept_id)."' IN (SELECT ticket_dept_id FROM #__fss_field_dept WHERE field_id = f.id)) AND ";
		if ($isopen)
		{
			$qry .= " (f.permissions <= '".FSJJ3Helper::getEscaped($db, $maxpermission)."' OR f.permissions = 4) ";
		} else {
			$qry .= " f.permissions <= '".FSJJ3Helper::getEscaped($db, $maxpermission)."' ";
		}
		
		$qry .= " ORDER BY f.grouping, f.ordering ";
		$db->setQuery($qry);
		$rows = $db->loadAssocList("id");

		$indexes = array();

		if (count($rows) > 0)
		{
			foreach ($rows as $index => &$row)
			{
				$indexes[] = FSJJ3Helper::getEscaped($db, $index);
			} 
		}
		
		$indexlist = implode(",",$indexes);
		if (count($indexes) == 0)
			$indexlist = "0";
		
		$qry = "SELECT * FROM #__fss_field_values WHERE field_id IN ($indexlist)";
		$db->setQuery($qry);
		$values = $db->loadAssocList();

		if (count($values) > 0)
		{
			foreach($values as &$value)
			{
				$field_id = $value['field_id'];
				$rows[$field_id]['values'][] = $value['value'];
			}
		}

		$this->_custfields = $rows;

		return $rows;
	}*/

	/*function &GetAllCustomFields($values = true)
	{
		$values = true;
		
		$db = JFactory::getDBO();
		
		if (empty($this->allfields))
		{
			// get a list of all available fields
			$qry = "SELECT * FROM #__fss_field as f WHERE f.published = 1 AND f.ident = 0 ";
			$qry .= " ORDER BY f.grouping, f.ordering ";
			$db->setQuery($qry);
			$rows = $db->loadAssocList("id");
		
			$indexes = array();

			if (count($rows) > 0)
			{
				foreach ($rows as $index => &$row)
				{
					$indexes[] = FSJJ3Helper::getEscaped($db, $index);
				} 
			}

			if ($values)
			{
				$indexlist = implode(",",$indexes);
				if (count($indexes) == 0)
					$indexlist = "0";
		
				$qry = "SELECT * FROM #__fss_field_values WHERE field_id IN ($indexlist)";
				$db->setQuery($qry);
				
				$values = $db->loadAssocList();

				if (count($values) > 0)
				{
					foreach($values as &$value)
					{
						$field_id = $value['field_id'];
						$rows[$field_id]['values'][] = $value['value'];
					}
				}

			}
			
			$this->allfields = $rows;
		}
		return $this->allfields;
	}*/
	
	/*function GetField($fieldid)
	{
		$db = JFactory::getDBO();
		$qry = "SELECT * FROM #__fss_field WHERE id = '".FSJJ3Helper::getEscaped($db, $fieldid)."'";
		$db->setQuery($qry);
		return $db->loadObject();
	}*/

	/*function FieldHeader(&$field, $showreq = false)
	{
		echo $field['description'];
		if ($showreq && $field['required'] == 1)
			echo " <font color='red'>*</font>";
		if ($field['peruser'])
			echo "<img src='". JURI::root( true ). "/components/com_fss/assets/images/user.png' style='position:relative;top:4px;' title='Global Field'>";
	}*/

	/*function GetValues(&$field)
	{
		if ($field['type'] == "text" || $field['type'] == "area" || $field['type'] == "plugin")
		{
			if (!array_key_exists('values',$field))
				return array();
			
			$output = array();
			if ($field['type'] == "plugin")
			{
				$output['plugindata'] = "";
				$output['plugin'] = "";	
			}
			if (array_key_exists('values',$field) && count($field['values']) > 0)
			{
				foreach ($field['values'] as &$value)
				{
					$bits = explode("=",$value);
					if (count($bits) == 2)
					{
						$output[$bits[0]] = $bits[1];	
					}
				}
			}
		
			return $output;
		}
		
		if ($field['type'] == "radio" || $field['type'] == "combo")
		{
			if (!array_key_exists('values',$field))
				return array();
			
			foreach ($field['values'] as $offset => $value)
			{
				if (strpos($value,"|") == 2)
				{
					$field['values'][$offset] = substr($value,3);	
				}
			}
			
			return $field['values'];	
		}
		
		if ($field['type'] == "checkbox")
			return array();
	}*/

	/*function FieldInput(&$field,&$errors,$errortype="ticket",$context = array())
	{
		$output = "";
		
		$id = $field['id'];
		
		$userid = 0;
		if (array_key_exists('userid',$context))
			$userid = $context['userid'];
		$ticketid = 0;
		if (array_key_exists('ticketid',$context))
			$ticketid = $context['ticketid'];
		
		// if its a per user field, try to load the value
		$current = $field['default'];

		if ($field['peruser'] && $errortype == "ticket")
		{
			
			$uservalues = FSJ_CustFields::GetUserValues($userid, $ticketid);
			
			if (array_key_exists($field['id'],$uservalues))
			{
				$current = $uservalues[$field['id']]['value'];
			}
		}
		
		$current = JRequest::getVar("custom_$id",$current);
		
		if ($field['type'] == "text")
		{
			$aparams = FSJ_CustFields::GetValues($field);
			$text_max = $aparams['text_max'];
			$text_size = $aparams['text_size'];
			$output = "<input name='custom_$id' id='custom_$id' value=\"".FSS_Helper::escape($current)."\" maxlength='$text_max' size='$text_size'>\n";
		}
		
		if ($field['type'] == "radio")
		{
			$values = FSJ_CustFields::GetValues($field);
			$output = "";
			if (count($values) > 0)
			{
				foreach ($values as $value)
				{
					$output .= "<input type='radio' id='custom_$id' name='custom_$id' value=\"".FSS_Helper::escape($value)."\"";
					if ($value == $current) $output .= " checked";
					$output .= ">$value<br>\n";
				}	
			}
		} 
		
		if ($field['type'] == "combo")
		{
			$values = FSJ_CustFields::GetValues($field);
			$output = "<select name='custom_$id' id='custom_$id'>\n";
			$output .= "<option value=''>".JText::_("PLEASE_SELECT")."</option>\n";
			if (count($values) > 0)
			{
				foreach ($values as $value)
				{
					$output .= "<option value=\"".FSS_Helper::escape($value)."\"";
					if ($value == $current) $output .= " selected";
					$output .= ">$value</option>\n";
				}	
			}
			$output .= "</select>";
		}
		
		if ($field['type'] == "area")
		{
			$aparams = FSJ_CustFields::GetValues($field);
			$area_width = $aparams['area_width'];
			$area_height = $aparams['area_height'];
			$output = "<textarea name='custom_$id' id='custom_$id' cols='$area_width' rows='$area_height' style='width:95%'>$current</textarea>\n";
		}
		
		if ($field['type'] == "checkbox")
		{	
			$output = "<input type='checkbox' name='custom_$id' id='custom_$id'";
			if ($current == "on") $output .= " checked";
			$output .= ">\n";
		}
		
		if ($field['type'] == "plugin")
		{
			$aparams = FSJ_CustFields::GetValues($field);
			$plugin = FSJ_CustFields::get_plugin($aparams['plugin']);
			
			$output = $plugin->Input($current, $aparams['plugindata'], $context, $id);
		}
		
		$id = "custom_" .$field['id'];
		if (array_key_exists($id,$errors))
		{
			if ($errortype == "ticket")
			{
				$output .= '<div class="fss_ticket_error" id="error_subject">' . $errors[$id] . '</div>';
			} else {
				$output .= '</td><td class="fss_must_have_field">' . $errors[$id];
			}
		}
		
		return $output;
	}*/
	
	/*function get_plugin_from_row(&$row)
	{
		$db	= JFactory::getDBO();
		
		$query = "SELECT value FROM #__fss_field_values WHERE field_id = " . FSJJ3Helper::getEscaped($db, $row->id);
		$db->setQuery($query);
		$values = $db->loadResultArray();
		
		$plugin_name = '';
		$plugin_data = '';
		
		foreach ($values as $value)
		{
			$bits = explode("=",$value);
			if (count($bits == 2))
			{
				if ($bits[0] == "plugin")
					$plugin_name = $bits[1];
				if ($bits[0] == "plugindata")
					$plugin_data = $bits[1];
			}
		}
		
		return FSJ_CustFields::get_plugin($plugin_name);
	}	*/
	
	/*function get_filenames($source_dir, $include_path = FALSE, $_recursion = FALSE)
	{
		static $_filedata = array();

		if ($fp = @opendir($source_dir))
		{
			// reset the array and make sure $source_dir has a trailing slash on the initial call
			if ($_recursion === FALSE)
			{
				$_filedata = array();
				$source_dir = rtrim(realpath($source_dir), DS).DS;
			}

			while (FALSE !== ($file = readdir($fp)))
			{
				if (@is_dir($source_dir.$file) && strncmp($file, '.', 1) !== 0)
				{
					FSJ_CustFields::get_filenames($source_dir.$file.DS, $include_path, TRUE);
				}
				elseif (strncmp($file, '.', 1) !== 0)
				{
					$_filedata[] = ($include_path == TRUE) ? $source_dir.$file : $file;
				}
			}
			return $_filedata;
		}
		else
		{
			return FALSE;
		}
	}*/
		
	/*function ValidateFields(&$fields, &$errors)
	{
		$ok = true;
		foreach ($fields as &$field)
		{
			if ($field['required'] > 0)
			{
				$value = JRequest::getVar("custom_" . $field['id'],"");
				if ($value == "")
				{
					$errors["custom_" . $field['id']] = JText::sprintf("YOU_MUST_ENTER_A_VALUE_FOR",$field['description']);	
					$ok = false;
				}	
			}
		}
	
		return $ok;
	}*/

	/*function StoreFields(&$fields, $ticketid)
	{
		$allfields = FSJ_CustFields::GetAllCustomFields(false);
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$userid = $user->get('id');

		if (count($fields) > 0)
		{
			foreach ($fields as &$field)
			{
				// only place this is called is creating a new ticket, so dont overwrite any per user fields that have permissions > 0
				if (array_key_exists($field['id'],$allfields) && $allfields[$field['id']]['peruser'] && $allfields[$field['id']]['permissions'] > 0)
					continue;
					
				$value = JRequest::getVar("custom_" . $field['id'],"XX--XX--XX");
				
				if ($field['type'] == "plugin")
				{
					$aparams = FSJ_CustFields::GetValues($field);
					$plugin = FSJ_CustFields::get_plugin($aparams['plugin']);
					
					$value = $plugin->Save($field['id'], $aparams['plugindata']);
				}
				
				if ($value != "XX--XX--XX")
				{
					if (array_key_exists($field['id'],$allfields) && $allfields[$field['id']]['peruser'])
					{
						$qry = "REPLACE INTO #__fss_ticket_user_field (user_id, field_id, value) VALUES ('".FSJJ3Helper::getEscaped($db, $userid)."','";
						$qry .= FSJJ3Helper::getEscaped($db, $field['id']) . "','";
						$qry .= FSJJ3Helper::getEscaped($db, $value) . "')";
						$db->setQuery($qry);
						$db->Query();
					} else {
						$qry = "REPLACE INTO #__fss_ticket_field (ticket_id, field_id, value) VALUES ('".FSJJ3Helper::getEscaped($db, $ticketid)."','";
						$qry .= FSJJ3Helper::getEscaped($db, $field['id']) . "','";
						$qry .= FSJJ3Helper::getEscaped($db, $value) . "')";
						$db->setQuery($qry);
						$db->Query();
					}
				}	
			}
		}	
	}*/

	/*function StoreField($fieldid, $ticketid, $ticket)
	{
		$allfields = FSJ_CustFields::GetAllCustomFields(true);
		
		//print_p($allfields);
		$db = JFactory::getDBO();
		$value = JRequest::getVar("custom_" . $fieldid,"");
	
		$field = $allfields[$fieldid];
		
		if ($field['type'] == "plugin")
		{
			$aparams = FSJ_CustFields::GetValues($field);
			$plugin = FSJ_CustFields::get_plugin($aparams['plugin']);
									
			$value = $plugin->Save($field['id'], $aparams['plugindata']);
		}
				
				
		if (array_key_exists($fieldid, $allfields) && $allfields[$fieldid]['peruser'])
		{
			$userid = $ticket['user_id'];

			$qry = "SELECT value FROM #__fss_ticket_user_field WHERE user_id = '".FSJJ3Helper::getEscaped($db, $userid)."' AND field_id = '".FSJJ3Helper::getEscaped($db, $fieldid)."'";
			$db->setQuery($qry);
			$row = $db->loadObject();
			$qry = "REPLACE INTO #__fss_ticket_user_field (user_id, field_id, value) VALUES ('".FSJJ3Helper::getEscaped($db, $userid)."','";
			$qry .= FSJJ3Helper::getEscaped($db, $fieldid). "','";
			$qry .= FSJJ3Helper::getEscaped($db, $value) . "')";
			$db->setQuery($qry);
			$db->Query();
		} else{
						
			$qry = "SELECT value FROM #__fss_ticket_field WHERE ticket_id = '".FSJJ3Helper::getEscaped($db, $ticketid)."' AND field_id = '".FSJJ3Helper::getEscaped($db, $fieldid)."'";
			$db->setQuery($qry);
			$row = $db->loadObject();
			$qry = "REPLACE INTO #__fss_ticket_field (ticket_id, field_id, value) VALUES ('".FSJJ3Helper::getEscaped($db, $ticketid)."','";
			$qry .= FSJJ3Helper::getEscaped($db, $fieldid). "','";
			$qry .= FSJJ3Helper::getEscaped($db, $value) . "')";
			$db->setQuery($qry);
			$db->Query();
		}
		if (!$row)
			return array("",$value);
			
		return array($row->value,$value);
	}*/

	/*function &GetUserValues($userid = 0,$ticketid = 0)
	{
		if ($ticketid < 1)
		{
			$result = array();
			return $result;
		}
		
		if (empty($this->user_values))
		{
			$db = JFactory::getDBO();
			if ($userid < 1)
			{
				if (empty($this->ticket_user_id))
				{
					$qry = "SELECT user_id FROM #__fss_ticket_ticket WHERE id = '".FSJJ3Helper::getEscaped($db, $ticketid)."'";
					$db->setQuery($qry);
					$row = $db->loadObject();
					if ($row)
						$this->ticket_user_id = $row->user_id;	
				}
				
				$userid = $this->ticket_user_id;
			}
			
			$qry = "SELECT * FROM #__fss_ticket_user_field WHERE user_id ='".FSJJ3Helper::getEscaped($db, $userid)."'";
			$db->setQuery($qry);
			$this->user_values = $db->loadAssocList('field_id');
		}
		
		return $this->user_values;
	}*/

	/*function &GetTicketValues($ticketid,$ticket)
	{
		if (empty($this->_ticketvalues))
			$this->_ticketvalues = array();
			
		if (!array_key_exists($ticketid,$this->_ticketvalues))
		{
			$allfields = FSJ_CustFields::GetAllCustomFields(true);
			
			$db = JFactory::getDBO();
			$qry = "SELECT * FROM #__fss_ticket_field WHERE ticket_id ='".FSJJ3Helper::getEscaped($db, $ticketid)."'";
			$db->setQuery($qry);
			$values = $db->loadAssocList('field_id');
		
			$values2 = FSJ_CustFields::GetUserValues($ticket['user_id'], $ticket['id']);
			
			foreach ($values2 as $id => $value)
			{
				if (array_key_exists($id, $allfields) && $allfields[$id]['peruser'])
					$values[$id] = $value;
			}
			$this->_ticketvalues[$ticketid] = $values;
		}
		return $this->_ticketvalues[$ticketid];	
	}*/

	/*function FieldOutput(&$field,&$fieldvalues,$context)
	{
		$value = "";
		if (count($fieldvalues) > 0)
		{
			foreach ($fieldvalues as $fieldvalue)
			{
				if ($fieldvalue['field_id'] == $field['id'])
				{
					$value = $fieldvalue['value'];
					break;	
				}	
			}
		}
		
		if ($field['type'] == "plugin")
		{
			$aparams = FSJ_CustFields::GetValues($field);
			$plugin = FSJ_CustFields::get_plugin($aparams['plugin']);
			$value = $plugin->Display($value, $aparams['plugindata'], $context, $field['id']);
		}
		
		if ($field['type'] == "area")
		{
			$value = str_replace("\n","<br />",$value);	
		}
	
		if ($field['type'] == "checkbox")
		{
			if ($value == "on")
				return "Yes";
			return "No";
		}
	
		return $value;
	}*/
	
	// stuff below here is specific for comments
	/*function &Comm_GetCustomFields($ident)
	{
		$db = JFactory::getDBO();
	
		// get a list of all available fields
		if ($ident != -1)
		{
			$qry = "SELECT * FROM #__fss_field as f WHERE f.published = 1 AND (f.ident = 999 OR f.ident = '".FSJJ3Helper::getEscaped($db, $ident)."') ";
		} else {
			$qry = "SELECT * FROM #__fss_field as f WHERE f.published = 1 ";
		}
	
		$qry .= " ORDER BY f.ordering";
		$db->setQuery($qry);
		$rows = $db->loadAssocList("id");

		$indexes = array();

		if (count($rows) > 0)
		{
			foreach ($rows as $index => &$row)
			{
				$indexes[] = FSJJ3Helper::getEscaped($db, $index);
			} 
		}
	
		$indexlist = implode(",",$indexes);
		if (count($indexes) == 0)
			$indexlist = "0";
	
		$qry = "SELECT * FROM #__fss_field_values WHERE field_id IN ($indexlist)";
		$db->setQuery($qry);
		$values = $db->loadAssocList();

		if (count($values) > 0)
		{
			foreach($values as &$value)
			{
				$field_id = $value['field_id'];
				$rows[$field_id]['values'][] = $value['value'];
			}
		}

		return $rows;
	}*/
	
	/*function Comm_StoreFields(&$fields)
	{
		$result = array();
		
		if (count($fields) > 0)
		{
			foreach ($fields as &$field)
			{
				$value = JRequest::getVar("custom_" . $field['id'],"XX--XX--XX");
				if ($value != "XX--XX--XX")
				{
					$result[$field['id']] = $value;
				}	
			}
		}
		
		return $result;
	}*/

	/*function get_dests()
	{
		$db = JFactory::getDBO();
		$plugins = FSJ_Plugin_Handler::GetPlugins("cfdest");
		
		foreach ($plugins as $id => $plugin)
		{
			if (array_key_exists('type',$plugin->params) && $plugin->params['type']	== "sql")
			{
				$qry = $plugin->params['sql'];
				$db->setQuery($qry);
				$rows = $db->loadObjectList();
				
				foreach ($rows as &$row)
				{
					$newplugin = clone $plugin;
					$newplugin->name .= ":" . $row->id;
					$newplugin->params['desc'] = JText::_($newplugin->params['desc']);
					$newplugin->params['desc'] .= ": " . $row->title; 
					$plugins[$newplugin->name] = $newplugin;
				}
				unset($plugins[$id]);			
			}
		}
		return $plugins;
	}*/
	
	/*function get_dest($name)
	{
		$plugins = FSJ_CustFields::get_dests();
		return $plugins[$name];
	}*/
}


class __FSJ_CustField
{
	var $name = "-----";
	var $help = "-----";	
	var $group = "-----";
	
	function DisplaySettings($name, $params) // passed object with settings in
	{
		/*$params = json_decode($params, true);
		$class = get_class($this);
		$class = str_ireplace("FSJ_CustField_","", $class);
		$class = strtolower($class);
				
		JForm::addFormPath(JPATH_SITE.DS.'components'.DS.'com_fsj_main'.DS.'plugins'.DS.'custfield');
		$file = JPATH_SITE.DS.'components'.DS.'com_fsj_main'.DS.'plugins'.DS.'custfield'.DS.$class . '.xml';
		if (!file_exists($file))
			return "";
				
		$form = JForm::getInstance('cf_' . $name, $file, array('control' => 'cf_'.$name));
		$form->bind($params);
				
		if (!$form)
			return "";

		$html = "";

		foreach ($form->getFieldset() as $id => $field)
		{
			$html .= "<li>";
			$html .= $form->getLabel($field->fieldname) . $form->getInput($field->fieldname);
			$html .= "</li>";
		}
				
		return $html;*/
	}
	
	function SaveSettings($name) // return object with settings in
	{
		/*return json_encode(JRequest::getVar('cf_' . $name));*/
	}
	
	function Input($current, $params, $context, $id) // output the field for editing
	{
		/*return "";*/
	}
	
	function Save($id)
	{
		/*return "";*/
	}
	
	function Display($value, $params, $context, $id) // output the field for display
	{
		//return $value;
	}
	
	function CanEdit()
	{
		//return false;	
	}
	
	function CanSearch()
	{
		//return false;	
	}
}

class __FSJ_CustDest
{
	function __construct($plugin)
	{
		$this->plugin = $plugin;	
	}
	
	function DisplaySettings($name, $params) // passed object with settings in
	{
		$html = "";
		if (array_key_exists("xml",$this->plugin->params))
		{
			$xml = $this->plugin->params['xml'];	
			$xml_com = $this->plugin->params['xml_com'];
			
			$params = json_decode($params, true);
			
			$file = JPATH_SITE.DS.'components'.DS.'com_fsj_'.$xml_com.DS.'plugins'.DS.'cfdest'.DS.$xml . '.xml';
			if (!file_exists($file))
				return $file;
			
			$form = JForm::getInstance('cf_' . $name, $file, array('control' => 'cf_'.$name));
			$form->bind($params);
			
			if (!$form)
				return "";

			$html = "";

			foreach ($form->getFieldset() as $id => $field)
			{
				$html .= "<li>";
				$html .= $form->getLabel($field->fieldname) . $form->getInput($field->fieldname);
				$html .= "</li>";
			}	
		}
		

		return $html;
	}
	
	function SaveSettings($name) // return object with settings in
	{
		return json_encode(JRequest::getVar('cf_' . $name));
	}

}
