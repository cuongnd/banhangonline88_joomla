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

class JFormFieldFSJCFType extends JFormFieldGroupedList
{
	protected $type = 'FSJCFType';
	var $handlepost = 1;

	protected function getInput()
	{
		static $js = false;
		
		// need to build a list of field type available
		
		if (!$js)
		{
			$document = JFactory::getDocument();
			
			$js[] = "function fsj_cftype_load(set) {";
			$js[] = "   var value = jQuery('#jform_' + set).val()";
			$js[] = "	var url = '" . JRoute::_('index.php?option=' . JRequest::getVar('option') . '&view=' . JRequest::getVar('view') . '&cftask=change&field=' . $this->fieldname, false) . "';";
			$js[] = "   url = url + '&type=' + value;";
			$js[] = "   contid = 'field_' + set + '_extra';";
			$js[] = "	jQuery('#' + contid).html(\"".JText::_('FSJ_PLEASE_WAIT')."\");";
			$js[] = "   jQuery.get(url, function (data) {";
			$js[] = "		jQuery('#' + contid).html(data);";
			$js[] = "   });";
			$js[] = "}";
			$document->addScriptDeclaration("\n\n\n".implode("\n", $js)."\n\n\n");
			$js = true;
		}
	
		if ($this->value == "") $this->value = "fsjstring";
		
		$dest_field = $this->element['fsjcftype_paramfield'];
		$this->element['onchange'] = "fsj_cftype_load('{$this->fieldname}');";
		$this->onchange = "fsj_cftype_load('{$this->fieldname}');";
		if (isset($this->element['fsjcftype_class']))
		{
			$this->element['class'] = (string)$this->element['fsjcftype_class'];
			$this->class = $this->element['fsjcftype_class'];
		}
		
		$html = parent::getInput();
		
		return $html;
	}

	protected function getGroups()
	{
		$plugins = FSJ_CustFields::get_plugins();
		
		//$options[JText::_('FSJ_CF_GROUP_SIMPLE')] = array();
		foreach ($plugins as &$plugin)
		{
			$group = "extra";
			if (isset($plugin->params->group)) $group = $plugin->params->group;
			$group = "FSJ_CF_GROUP_" . $group;
			$options[JText::_($group)][$plugin->name] = JText::_($plugin->title);	
		}

		return $options;
	}
	
	function doSave($field, &$data)
	{
		// saving settings for the custom field. Need to work out the fields in the form, and call the relevant save functions if needed
		$type = $data[$field];
		
		$fieldobj = FSJ_Plugin_Handler::GetPluginInstance("custfield", $type);
		
		$xmlfile = JPATH_ROOT.DS.$fieldobj->plugin->path . DS . "custfield.{$type}.xml";
		
		$xml = simplexml_load_file($xmlfile);

		$form_xml_text = $xml->edit_form->asXML();
		$form_xml_text = str_replace("<edit_form>", "", $form_xml_text);
		$form_xml_text = str_replace("</edit_form>", "", $form_xml_text);
		
		$form_xml_text = trim($form_xml_text);
		
		if ($form_xml_text)
		{
			
			$form_xml = simplexml_load_string($form_xml_text);

			JForm::addFieldPath(JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field');
			$form = JForm::getInstance("custfield.$type", $form_xml_text, array('control' => 'cfparams.' . $field ));
			
			$params = JRequest::getVar('cfparams_' . $field);
			
			foreach ($form_xml->fields as $subfields)
			{
				foreach ($subfields->field as $subfield)
				{	
					// check field type against list and see if we need to do a save
					$subfield_obj = $form->getField($subfield->attributes()->name, '');
					if (method_exists($subfield_obj, "doSave"))
					{
						$subfield_obj->doSave((string)$subfield->attributes()->name, $params, 'cfparams_' . $field);
					}
				}	
			}

			$dest_field = $this->fsjcftype->paramfield;
			$data[$dest_field] = json_encode($params);	
		}
	}
	
	function Process()
	{
		$task = JRequest::getVar('cftask');
		if ($task == "change")
		{
			ob_clean();
			
			$fieldname = JRequest::getVar('field');
			$value = JRequest::getVar('type');
			$html = FSJ_CustFields::DisplaySettings($fieldname, $value, null);
			echo $html;
			exit;
		}
		
		return false;
	}
	
	function AdminDisplay($value, $name, $item)
	{
		static $plugins = false;
		
		$plugin = FSJ_Plugin_Handler::GetPlugin("custfield", $value);
		if ($plugin)
			return JText::_($plugin->title);

		return $value;
	}
	
	function getExtra()
	{
		// sub form data
		return FSJ_CustFields::DisplaySettings($this->fieldname, $this->value, $this->form->getValue($this->element['fsjcftype_paramfield']));	
	}
}
