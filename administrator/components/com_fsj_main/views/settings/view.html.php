<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
jimport( 'joomla.application.component.view' );
jimport('joomla.utilities.date');
jimport('joomla.html.pane');
jimport('fsj_core.lib.utils.xml');
jimport('fsj_core.admin.settings_edit');
class fsj_mainViewsettings extends JViewLegacy
{
	function display($tpl = null)
	{
		if (FSJ_Helper::IsJ3())
		{
			fsj_ToolbarsHelper::addSubmenu(JRequest::getCmd('view', 'fsj_main'));
			JHtml::_('formbehavior.chosen', 'select');
		}
		// init form and load data
		$this->Init();
		$com = "com_fsj_".$this->_com;
		if ($this->set == "global")
			$com = "com_fsj_main";
		if (!JFactory::getUser()->authorise('core.admin', $com)) {
			$this->Toolbar(false);
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		$this->Toolbar();
		// form actions
		if (JRequest::getVar('task') == "cancel")
			return $this->DoCancel();
		if (JRequest::getVar('task') == "resetsetting")
			return $this->DoResetSetting();
		if (JRequest::getVar('task') == "save" || JRequest::getVar('task') == "apply")
			return $this->DoSaveSettings();
		// display settings page
		parent::display();
	}
	function Init()
	{
		// load all data
		$mainframe = JFactory::getApplication();
		$this->_comname = JRequest::getVar('admin_com');
		$this->set = JRequest::getVar('settings');
		$this->_com = str_replace("com_fsj_","",$this->_comname);
		$this->_comname = "com_fsj_". $this->_com;
		$this->setting_set = $this->_comname;
		if ($this->set == "global")
			$this->setting_set = "com_fsj";
		JForm::addFieldPath(JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field');
		JForm::addFormPath(JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'forms');
		JForm::addFormPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_main'.DS.'models'.DS.'forms');
		if ($this->_com != "global" && $this->_com != "main")
			JForm::addFormPath(JPATH_ADMINISTRATOR.DS.'components'.DS.$this->_comname.DS.'models'.DS.'forms');
		if ($this->set == "global")
		{ 
			$this->settings = JForm::getInstance('fsj_main.settings', 'settings_global', array('control' => 'jform'), false);
			$file = JPath::find(JForm::addFormPath(), 'settings_global.xml');
			$this->xml = simplexml_load_file($file);
		} else {
			$this->settings = JForm::getInstance('fsj_main.settings', 'settings_' . $this->_com, array('control' => 'jform'), false);
			$file = JPath::find(JForm::addFormPath(), 'settings_'.$this->_com.'.xml');
			$this->xml = simplexml_load_file($file);
		}
		FSJ_Lang_Helper::Load_Component("com_fsj_".$this->_com);
		$file = JPath::find(JForm::addFormPath(), 'component_perms.xml');
		$perm_xml = @simplexml_load_file($file);
		$perm_xml->fieldset->field->attributes()->component = "com_fsj_".$this->_com;
		if ($this->set == "global")
			$perm_xml->fieldset->field->attributes()->component = "com_fsj_main";
		// form for permissions
		$this->perm_form = JForm::getInstance('fsj_main.component_perms', $perm_xml->asXML(), array('control' => 'jform'), false);
		$data = $this->LoadData();
		$this->settings->bind($data);
	}
	function LoadData()
	{
		JTable::addIncludePath(JPATH_LIBRARIES.DS.'fsj_core'.DS.'tables');
		$default = JTable::getInstance('FSJSettings', 'JTable');
		$default->loadByName("default.".$this->setting_set);
		$setting = JTable::getInstance('FSJSettings', 'JTable');
		$setting->loadByName($this->setting_set);
		$values = json_decode($default->value, true);
		$setting_value = json_decode($setting->value, true);
		//print_p($values);
		if ($setting_value)
		{
			foreach ($setting_value as $setid => $set)
			{
				foreach ($set as $key => $value)
				{
					$values[$setid][$key] = $value;	
				}	
			}
		}
		return $values;
	}
	function Toolbar($auth = true)
	{
		// display settings page
		$css = ".icon-48-componentsettings { background-image: url(../administrator/components/com_fsj_main/assets/images/componentsettings-48.png); }";
		FSJ_Page::StyleDec($css);
		FSJ_Page::Style("components/com_fsj_main/assets/css/settings.css");
		if ($this->set == "global")
		{
			$this->title = JText::_("COM_FSJ_".$this->_com) . " - " . JText::_('FSJ_ADMIN_GLOBAL_SETTINGS');
		} else {
			$this->title = JText::_("COM_FSJ_".$this->_com) . " - " . JText::_('FSJ_ADMIN_COMPONENT_SETTINGS');
		}
		JToolBarHelper::title($this->title , 'componentsettings.png' );
		if ($auth)
		{
			JToolBarHelper::save();
			JToolBarHelper::apply();
		}
		JToolBarHelper::cancel();
	}
	function DoResetSetting()
	{
		/*$setting = JRequest::getVar('setting');
		//echo "Resetting setting $setting<br>";
		$default = FSJ_Settings::GetComponentDefault($this->_com,$setting);
		//echo "Default : $default<br>";
		ob_clean();
		echo $default;*/
		exit;
	}
	function DoCancel()
	{
		$mainframe = JFactory::getApplication();
		$mainframe->redirect(JRoute::_("index.php?option={$this->_comname}", false));	
		return;
	}
	function DoSaveSettings()
	{
		$this->SaveSettings();
		//exit;
		$msg = JText::_( 'FSJ_SETTINGS_SAVED' );
		$mainframe = JFactory::getApplication();
		if (JRequest::getVar('task') == "apply")
		{
			$settings = "";
			if ($this->set == "global") $settings = "&settings=global";
			$mainframe->redirect(JRoute::_("index.php?option=com_fsj_main&view=settings&admin_com={$this->_com}$settings", false), $msg, 'message');		
		} else {
			$mainframe->redirect(JRoute::_("index.php?option={$this->_comname}", false), $msg, 'message');	
		}
	}
	function SaveSettings()
	{
		$db = JFactory::getDBO();
		// Save permissions!
		$data	= JRequest::getVar('jform', array(), 'post', 'array');
		$admin_com	= JRequest::getCmd('admin_com');
		$admin_com = "com_fsj_" . $admin_com;	
		if ($this->set == "global")
			$admin_com = "com_fsj_main";
		// save permissions
		$permdata['rules'] = $data['rules'];
		unset($data['rules']);
		JForm::addFormPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_main'.DS.'models'.DS.'forms');
		$permdata = $this->perm_form->filter($permdata);
		$this->perm_form->validate($permdata);
		// Save the rules.
		if (isset($permdata['rules'])) {
			$rules	= new JAccessRules($permdata['rules']);
			$asset	= JTable::getInstance('asset', 'JTable');
			if (!$asset->loadByName($admin_com)) {
				$root	= JTable::getInstance('asset', 'JTable');
				$root->loadByName('root.1');
				$asset->name = $option;
				$asset->title = $option;
				$asset->setLocation($root->id, 'last-child');
			}
			$asset->rules = (string) $rules;
			if (!$asset->check() || !$asset->store()) {
				$this->setError($asset->getError());
				return false;
			}
		}
		// need to store the settings in the fsj_main_settings table
		// need to go through all settings in xml file and clean any useglobal ones that 
		foreach ($this->xml->fields as $fields)
		{
			$fields_name = (string)$fields->attributes()->name;
			foreach ($fields->field as $field)
			{
				$field_name = (string)$field->attributes()->name;
				$ugvar = "jform_" . $fields_name	 . "_" . $field_name;
				$curval = $data[$fields_name][$field_name];
				$gval = "-";
				if (array_key_exists($ugvar, $_POST['use_global']))
					$gval = $_POST['use_global'][$ugvar];
				if ( ($curval == "" && $gval != 1) || $gval == -1)
				{
					unset($data[$fields_name][$field_name]);
				}
			}
			if (count($data[$fields_name]) == 0)
				unset($data[$fields_name]);
		}
		$value = json_encode($data);
		JTable::addIncludePath(JPATH_LIBRARIES.DS.'fsj_core'.DS.'tables');
		$setting = JTable::getInstance('FSJSettings', 'JTable');
		$setting->loadByName($this->setting_set);
		$setting->value = $value;
		$setting->store();
		//print_p($data);
		//exit;
	}
}
// stop K2 causing error messages
class fsj_mainModelSettings extends JModelLegacy {}
