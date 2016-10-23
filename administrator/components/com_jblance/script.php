<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	23 April 2012
 * @file name	:	script.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Installation script file (jblance)
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Script file of JoomBri Freelance component
 */
class com_jblanceInstallerScript {
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent){
		// $parent is the class calling this method
		require_once(JPATH_ADMINISTRATOR.'/components/com_jblance/install.jbdefault.php');
		
		//0. Budget
		defaultBudgetRange();
		
		//1. Category
		defaultCategory();
		
		//2. insert default config values (Component Settings)
		defaultConfigs();
		
		//3. Custom Fields
		defaultCustomFields();
		
		//4. Project Duration
		defaultProjectDuration();
		
		//5. Email templates
		defaultEmailTemplates();
		
		//6. Location
		defaultLocation();
		
		//7. Payment Modes
		defaultPaymentModes();
		
		//8. Default Plans
		defaultPlans();
		
		//9. User Groups
		defaultUserGroups();
		
		//10. User Groups Fields
		defaultUserGroupFields();
	
		//Install modules and plugins
		$manifest = $parent->get("manifest");
		$parent = $parent->getParent();
		$source = $parent->getPath("source");
		$installer = new JInstaller();
		$db = JFactory::getDbo();
		
		// Install modules
		foreach($manifest->modules->module as $module){
			$attributes = $module->attributes();
			$mod = $source.'/'.$attributes['folder'].'/'.$attributes['module'];
			$result = $installer->install($mod);
		}
		// Install plugins
		foreach($manifest->plugins->plugin as $plugin){
			$installer = new JInstaller();		//has been added to avoid JInstaller: :Install: Cannot find XML setup file Failed deleting
			$attributes = $plugin->attributes();
			$plg = $source.'/'.$attributes['folder'].'/'.$attributes['plugin'];
			$installer->install($plg);
			
			// Enable plugins
			$query = "UPDATE #__extensions SET enabled=1 WHERE element=".$db->quote($attributes['plugin'])." AND type=".$db->quote('plugin');
			$db->setQuery($query);
			$db->execute();
		}
		
	}
 
	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent){
		// $parent is the class calling this method
		
		$this->install($parent);
		
		upgrade100_101();
		
		upgrade101_102();
		
		upgrade102_103();
		
		upgrade103_104();
		
		upgrade104_105();
		
		upgrade105_106();
		
		upgrade106_107();
		
		upgrade108_109();
		
		upgrade11B1_11B2();
		
		upgrade11B2_110();
		
		upgrade110_111();
		
		upgrade111_112();
		
		upgrade114_115();
		
		upgrade116_117();
		
		upgrade117_118();
		
		upgrade122_123();
		
		upgrade123_124();
		
		upgrade125_126();
		
		upgrade128_129();
		
		upgrade129_130();
		
		upgrade130_131();
		
		upgrade133_140();
		
		upgrade1500_1501();
		
		upgrade1502_151();
		
		upgrade151_1600();
		
		echo '<p>'.JText::sprintf('COM_JBLANCE_UPDATE_TEXT', $parent->get('manifest')->version) .'</p>';
		
	}
	
	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent){
		// $parent is the class calling this method
		
		//Uninstall modules and plugins
		$manifest = $parent->get("manifest");
		$parent = $parent->getParent();
		$db = JFactory::getDbo();
		
		// Uninstall modules
		foreach($manifest->modules->module as $module){
			$attributes = $module->attributes();
			$element = $attributes['module'];
			$query = "SELECT extension_id FROM #__extensions WHERE type='module' AND element=".$db->quote($element);
			$db->setQuery($query);
			$id = $db->loadResult();
		
			$installer = new JInstaller;
			$result = $installer->uninstall('module', $id);
		}
		
		// Uninstall plugins
		foreach($manifest->plugins->plugin as $plugin){
			$attributes = $plugin->attributes();
			$element = $attributes['plugin'];
			$query = "SELECT extension_id FROM #__extensions WHERE type='plugin' AND element=".$db->quote($element);
			$db->setQuery($query);
			$id = $db->loadResult();
		
			$installer = new JInstaller;
			$result = $installer->uninstall('plugin', $id);
		}
		
		echo '<p>' . JText::_('JoomBri Freelance uninstalled successfully') . '</p>';
	}
	
	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent){
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		//echo '<p>' . JText::_('COM_JBLANCE_PREFLIGHT_' . $type . '_TEXT') . '</p>';
		
		//check for Joomla version. JoomBri Freelance v1.4 and above will be compatible with Joomla 3.x and above
		if(version_compare(JVERSION, '3.0', '<')){
			$app 	= JFactory::getApplication();
			$msg 	= 'This version is compatible with Joomla 3.x and above only!';
			$link	= JRoute::_('index.php?option=com_installer&view=install');
			$app->enqueueMessage($msg, 'error');
			$app->redirect($link);
		}
		
	}
 
	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent){
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		require_once(JPATH_ADMINISTRATOR.'/components/com_jblance/install.jbdefault.php');
		
		//1. install/update menu
		$addMenus = addMenus();
		//echo '<p>' . JText::_('COM_JBLANCE_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
	}
}
?>