<?php
/**
 * @package JAdmin!
 * @version 1.5.4.3
 * @copyright (C) Copyright 2008-2010 CMS Fruit, CMSFruit.com. All rights reserved.
 * @license GNU/LGPL http://www.gnu.org/licenses/lgpl-3.0.txt

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU Lesser General Public License as published by
 the Free Software Foundation; either version 3 of the License, or (at your
 option) any later version.

 This program is distributed in the hope that it will be useful, but
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public
 License for more details.

 You should have received a copy of the GNU Lesser General Public License
 along with this program.  If not, see http://www.gnu.org/licenses/.
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Script file of JLive! Chat component
 */
class com_jadminInstallerScript
{
	function initDB()
	{
		$db =& JFactory::getDBO();

		//	Create required tables
		///////
		$sql = "CREATE TABLE IF NOT EXISTS #__cms_app (
			  `app_id` smallint(6) unsigned NOT NULL auto_increment,
			  `app_name` varchar(50) NOT NULL,
			  `app_data` text NULL,
			  `app_cdate` int(10) unsigned NOT NULL default '0',
			  `app_mdate` int(10) unsigned default '0',
			  PRIMARY KEY  (`app_id`),
			  UNIQUE KEY `idx_name` USING BTREE (`app_name`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();

		// Ensure InnoDB
		$sql = "ALTER TABLE #__cms_app ENGINE = InnoDB;";
		$db->setQuery($sql);
		$db->query();

		$sql = "UPDATE #__cms_app SET
			app_data = NULL
			WHERE app_name = 'JAdmin!'
			AND app_data LIKE 'a:%';";
		$db->setQuery($sql);
		$db->query();

		$sql = "ALTER TABLE #__cms_app
			MODIFY COLUMN app_data TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;";
		$db->setQuery($sql);
		$db->query();
		////////////////////////////////////////

		$sql = "CREATE TABLE IF NOT EXISTS #__jadm_ipblocker (
			  `rule_id` int(10) unsigned NOT NULL auto_increment,
			  `user_id` int(10) unsigned NOT NULL default '0',
			  `source_ip` varchar(15) character set latin1 NOT NULL,
			  `rule_desc` varchar(250) default NULL,
			  `rule_params` text,
			  `cdate` int(10) unsigned NOT NULL,
			  `mdate` int(10) unsigned NOT NULL,
			  PRIMARY KEY  (`rule_id`),
			  UNIQUE KEY `uniq_ip` (`user_id`,`source_ip`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();
		///////////////////////

		$sql = "CREATE TABLE IF NOT EXISTS #__jadm_user (
			  `user_id` int(10) unsigned NOT NULL auto_increment,
			  `fullname` varchar(150) default NULL,
			  `department` varchar(150) default NULL,
			  `sort_order` int(10) unsigned NOT NULL,
			  `params` text NOT NULL,
			  `is_enabled` tinyint(1) unsigned NOT NULL default '1',
			  `cdate` int(10) unsigned NOT NULL default '0',
			  `mdate` int(10) unsigned NOT NULL default '0',
			  `last_auth_date` int(11) default NULL,
			  `auth_key` char(64) NOT NULL,
			  `internal_user_id` int(10) unsigned NOT NULL default '0',
			  PRIMARY KEY  (`user_id`),
			  UNIQUE KEY `uniq_key` (`auth_key`),
			  KEY `department_index` (`department`),
			  KEY `online_index` (`is_enabled`,`last_auth_date`),
			  KEY `internal_user_index` (`internal_user_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();
		///////////////////////

		$sql = "CREATE TABLE IF NOT EXISTS #__jadm_user_keys (
			  `key_id` int(10) unsigned NOT NULL auto_increment,
			  `user_id` int(10) unsigned NOT NULL,
			  `auth_key` varchar(64) NOT NULL,
			  `is_expired` tinyint(1) unsigned NOT NULL default '0',
			  `cdate` int(10) unsigned NOT NULL,
			  `mdate` int(10) unsigned NOT NULL,
			  PRIMARY KEY  (`key_id`),
			  KEY `authkey_index` (`auth_key`),
			  KEY `authkey2_index` (`auth_key`,`is_expired`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
		$db->setQuery($sql);
		$db->query();
		///////////////////////
		// Drop it first if it already exists
		$sql = "DROP TABLE IF EXISTS #__jadm_user_sync;";
		$db->setQuery($sql);
		$db->query();

		$sql = "CREATE TABLE IF NOT EXISTS #__jadm_user_sync (
			  `sync_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `user_id` int(10) unsigned NOT NULL,
			  `sync_mode` enum('push','poll') NOT NULL DEFAULT 'push',
			  `user_ip` varchar(15) DEFAULT NULL,
			  `user_port` mediumint(8) unsigned DEFAULT NULL,
			  `system_uuid` varchar(36) NOT NULL,
			  `settings_checksum` int(10) unsigned DEFAULT NULL,
			  `visitors_checksum` bigint(20) unsigned DEFAULT NULL,
			  `ipblocker_checksum` bigint(20) unsigned DEFAULT NULL,
			  `menus_checksum` char(32) DEFAULT NULL,
			  `components_checksum` char(32) DEFAULT NULL,
			  `users_checksum` char(32) DEFAULT NULL,
			  `cdate` int(10) unsigned NOT NULL,
			  `mdate` int(10) unsigned NOT NULL,
			  PRIMARY KEY (`sync_id`),
			  UNIQUE KEY `uuid_uniq_index` (`user_id`,`system_uuid`),
			  KEY `sync_index` (`user_id`,`sync_mode`)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
		$db->setQuery($sql);
		$db->query();
		///////////////////////

		$sql = "CREATE TABLE IF NOT EXISTS #__jadm_visitor (
			  `visitor_id` char(32) NOT NULL,
			  `visitor_name` varchar(255) default NULL,
			  `visitor_username` varchar(150) default NULL,
			  `visitor_email` varchar(100) default NULL,
			  `visitor_ip_address` varchar(15) NOT NULL,
			  `visitor_browser` varchar(255) NOT NULL,
			  `visitor_city` varchar(150) NOT NULL,
			  `visitor_country` varchar(150) NOT NULL,
			  `visitor_country_code` char(2) NOT NULL,
			  `visitor_referrer` varchar(255) default NULL,
			  `visitor_cdate` int(10) unsigned NOT NULL,
			  `visitor_mdate` int(10) unsigned NOT NULL,
			  `visitor_params` text NOT NULL,
			  `user_id` int(10) unsigned NOT NULL default '0',
			  `visitor_operating_system` varchar(50) NOT NULL,
			  `visitor_last_uri` varchar(255) NOT NULL,
			  `is_spider` tinyint(1) unsigned NOT NULL default '0',
			  PRIMARY KEY (`visitor_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();
		///////////////////////
	}
	
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent)
	{
		// $parent is the class calling this method
		//$parent->getParent()->setRedirectURL('index.php?option=com_helloworld');
		self::initDB();
	}

	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent)
	{
		// $parent is the class calling this method
		//echo '<p>'.JText::_('COM_HELLOWORLD_UNINSTALL_TEXT').'</p>';
		self::initDB();
	}

	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent)
	{
		// $parent is the class calling this method
		//echo '<p>'.JText::sprintf('COM_HELLOWORLD_UPDATE_TEXT', $parent->get('manifest')->version).'</p>';
		self::initDB();
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent)
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		//echo '<p>'.JText::_('COM_HELLOWORLD_PREFLIGHT_'.$type.'_TEXT').'</p>';
		self::initDB();
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent)
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		//echo '<p>'.JText::_('COM_HELLOWORLD_POSTFLIGHT_'.$type.'_TEXT').'</p>';
		self::initDB();
	}
}