<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

/**
 * Script file of the jQuery Easy plugin
 */
class plgsystemjqueryeasyInstallerScript
{	
	static $version = '1.6.6';
				
	/**
	 * Called before an install/update method
	 *
	 * @return  boolean  True on success
	 */
	public function preflight($type, $parent) {
		
		return true;
	}
	
	/**
	 * Called after an install/update method
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($type, $parent) 
	{			
		echo '<p style="margin: 20px 0">';
		//echo '<img src="../plugins/system/jqueryeasy/images/logo.png" />';
		echo JText::_('PLG_SYSTEM_JQUERYEASY_VERSION_LABEL').' <span class="label">'.self::$version.'</span>';
		echo '<br /><br />Olivier Buisard @ <a href="http://www.simplifyyourweb.com" target="_blank">Simplify Your Web</a>';
		echo '</p>';
		
		if ($type == 'update') {
			
			// update warning
			
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_JQUERYEASY_WARNING_RELEASENOTES'), 'warning');
		}	
		
 		// language test
 			
 		$available_languages = array('en-GB', 'en-US', 'es-CO', 'es-ES', 'fr-FR', 'it-IT', 'nl-NL', 'pt-BR', 'ru-RU', 'sv-SE', 'tr-TR', 'uk-UA');
 		$current_language = JFactory::getLanguage()->getTag();
 		if (!in_array($current_language, $available_languages)) {
 			JFactory::getApplication()->enqueueMessage(JText::sprintf('PLG_SYSTEM_JQUERYEASY_INFO_LANGUAGETRANSLATE', JFactory::getLanguage()->getName()), 'notice');
 		}
		
		return true;
	}
	
	/**
	 * Called on installation
	 *
	 * @return  boolean  True on success
	 */
	public function install($parent) {}
	
	/**
	 * Called on update
	 *
	 * @return  boolean  True on success
	 */
	public function update($parent) {}
	
	/**
	 * Called on uninstallation
	 */
	public function uninstall($parent) {}
	
}
?>
