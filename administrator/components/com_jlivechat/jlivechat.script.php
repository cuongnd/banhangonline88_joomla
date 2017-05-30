<?php
/**
 * @package JLive! Chat
 * @version 4.3.3
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

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Script file of JLive! Chat component
 */
class com_jlivechatInstallerScript
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
				WHERE app_name = 'JLive! Chat'
				AND app_data LIKE 'a:%';";
		$db->setQuery($sql);
		$db->query();

		$sql = "ALTER TABLE #__cms_app MODIFY COLUMN app_data TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;";
		$db->setQuery($sql);
		$db->query();

		////////////////////////////////////////

		$sql = "CREATE TABLE IF NOT EXISTS #__jlc_chat (
			  `chat_session_id` int(10) unsigned NOT NULL auto_increment,
			  `chat_alt_id` varchar(7) NOT NULL,
			  `chat_session_content` text NOT NULL,
			  `chat_session_params` text,
			  `cdate` int(11) NOT NULL default '0',
			  `mdate` int(10) unsigned NOT NULL default '0',
			  `is_active` tinyint(1) unsigned NOT NULL default '0',
			  `is_multimember` tinyint(1) unsigned NOT NULL default '0',
			  PRIMARY KEY  (`chat_session_id`),
			  UNIQUE KEY `uniq_alt_id` (`chat_alt_id`),
			  KEY `chat_index` (`chat_session_id`,`is_active`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();
		///////////////////////

		$sql = "CREATE TABLE IF NOT EXISTS #__jlc_chat_member (
			  `member_id` int(10) unsigned NOT NULL auto_increment,
			  `chat_session_id` int(10) unsigned NOT NULL,
			  `operator_id` int(10) unsigned NOT NULL default '0',
			  `user_id` int(10) unsigned NOT NULL default '0',
			  `member_display_name` varchar(50) NOT NULL,
			  `member_ip_address` varchar(15) character set latin1 NOT NULL,
			  `member_web_browser` varchar(255) character set latin1 default NULL,
			  `member_city` varchar(150) default NULL,
			  `member_country` varchar(150) default NULL,
			  `member_country_code` char(2) default NULL,
			  `is_accepted` tinyint(1) unsigned default NULL,
			  `is_gone` tinyint(1) NOT NULL default '0',
			  `is_typing` tinyint(1) unsigned NOT NULL default '0',
			  `cdate` int(10) unsigned NOT NULL default '0',
			  `mdate` int(10) unsigned NOT NULL default '0',
			  `member_params` text,
			  `expire_time` int(10) unsigned default NULL,
			  PRIMARY KEY  (`member_id`),
			  UNIQUE KEY `uniq_operator` (`chat_session_id`,`operator_id`),
			  KEY `chat_index` (`chat_session_id`),
			  KEY `expire_time_index` (`expire_time`),
			  KEY `active_operators_index` USING BTREE (`operator_id`,`expire_time`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();
		///////////////////////////////////////////

		$sql = "CREATE TABLE IF NOT EXISTS #__jlc_chat_proactive (
			  `row_id` int(10) unsigned NOT NULL auto_increment,
			  `visitor_id` char(32) NOT NULL,
			  `chat_session_id` int(10) unsigned NOT NULL,
			  `cdate` int(10) unsigned NOT NULL,
			  `mdate` int(10) unsigned NOT NULL,
			  PRIMARY KEY  USING BTREE (`row_id`),
			  UNIQUE KEY `uniq_chat` (`visitor_id`,`chat_session_id`),
			  KEY `chat_index` (`chat_session_id`),
			  KEY `visitor_index` (`visitor_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
		$db->setQuery($sql);
		$db->query();
		///////////////////////////////////////////////

		$sql = "CREATE TABLE IF NOT EXISTS #__jlc_ipblocker (
			  `rule_id` int(10) unsigned NOT NULL auto_increment,
			  `operator_id` int(10) unsigned NOT NULL,
			  `source_ip` varchar(15) NOT NULL,
			  `rule_desc` varchar(250) default NULL,
			  `rule_action` enum('block_from_website','block_from_livechat','send_to_url') NOT NULL default 'block_from_website',
			  `rule_params` text,
			  `cdate` int(10) unsigned NOT NULL,
			  `mdate` int(10) unsigned NOT NULL,
			  PRIMARY KEY  (`rule_id`),
			  UNIQUE KEY `uniq_ip` (`operator_id`,`source_ip`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();
		///////////////////////////////////////////////

		$sql = "CREATE TABLE IF NOT EXISTS #__jlc_message (
			  `message_id` int(10) unsigned NOT NULL auto_increment,
			  `operator_id` int(10) unsigned NOT NULL default '0',
			  `user_id` int(10) unsigned NOT NULL default '0',
			  `registered_name` varchar(255) default NULL,
			  `registered_email` varchar(100) default NULL,
			  `message_name` varchar(50) NOT NULL,
			  `message_email` varchar(100) NOT NULL,
			  `message_phone` varchar(20) default NULL,
			  `message_txt` text NOT NULL,
			  `guest_city` varchar(150) default NULL,
			  `guest_country` varchar(150) default NULL,
			  `guest_country_code` char(2) default NULL,
			  `guest_web_browser` varchar(250) default NULL,
			  `guest_ip_address` varchar(15) NOT NULL,
			  `is_read` tinyint(1) unsigned NOT NULL default '0',
			  `params` text,
			  `cdate` int(10) unsigned NOT NULL,
			  `mdate` int(10) unsigned NOT NULL,
			  PRIMARY KEY  (`message_id`),
			  KEY `operator_index` (`operator_id`),
			  KEY `operator_message_index` (`message_id`,`operator_id`) 
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();

		// Drop and recreate column index to ensure it exists
		$sql = "ALTER TABLE #__jlc_message DROP INDEX `operator_message_index`;";
		$db->setQuery($sql);
		$db->query();

		$sql = "ALTER TABLE #__jlc_message ADD INDEX `operator_message_index`(`message_id`, `operator_id`);";
		$db->setQuery($sql);
		$db->query();
		///////////////////////////////////////////////

		$sql = "CREATE TABLE IF NOT EXISTS #__jlc_operator (
			  `operator_id` int(10) unsigned NOT NULL auto_increment,
			  `operator_name` varchar(50) default NULL,
			  `alt_name` varchar(150) default NULL,
			  `department` varchar(150) default NULL,
			  `accept_chat_timeout` mediumint(8) unsigned NOT NULL,
			  `sort_order` int(10) unsigned NOT NULL,
			  `operator_params` text character set latin1 NOT NULL,
			  `is_enabled` tinyint(1) unsigned NOT NULL default '1',
			  `cdate` int(10) unsigned NOT NULL default '0',
			  `mdate` int(10) unsigned NOT NULL default '0',
			  `last_auth_date` int(11) default NULL,
			  `auth_key` char(64) NOT NULL,
			  PRIMARY KEY  (`operator_id`),
			  UNIQUE KEY `uniq_key` (`auth_key`),
			  KEY `department_index` (`department`),
			  KEY `operators_online_index` (`is_enabled`,`last_auth_date`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();
		///////////////////////////////////////////////

		// Drop it first if it already exists
		$sql = "DROP TABLE IF EXISTS #__jlc_operator_sync;";
		$db->setQuery($sql);
		$db->query();

		$sql = "CREATE TABLE IF NOT EXISTS #__jlc_operator_sync (
			  `sync_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `operator_id` int(10) unsigned NOT NULL,
			  `sync_mode` enum('push','poll') NOT NULL DEFAULT 'push',
			  `operator_ip` varchar(15) DEFAULT NULL,
			  `operator_port` mediumint(8) unsigned DEFAULT NULL,
			  `system_uuid` varchar(36) NOT NULL,
			  `settings_checksum` int(10) unsigned DEFAULT NULL,
			  `chat_checksum` bigint(20) unsigned DEFAULT NULL,
			  `operators_checksum` bigint(20) unsigned DEFAULT NULL,
			  `responses_checksum` bigint(20) unsigned DEFAULT NULL,
			  `messages_checksum` bigint(20) unsigned DEFAULT NULL,
			  `visitors_checksum` bigint(20) unsigned DEFAULT NULL,
			  `ipblocker_checksum` bigint(20) unsigned DEFAULT NULL,
			  `cdate` int(10) unsigned NOT NULL,
			  `mdate` int(10) unsigned NOT NULL,
			  PRIMARY KEY (`sync_id`),
			  UNIQUE KEY `operator_uuid_uniq_index` (`operator_id`,`system_uuid`),
			  KEY `sync_index` (`operator_id`,`sync_mode`)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
		$db->setQuery($sql);
		$db->query();
		///////////////////////////////////////////////

		$sql = "CREATE TABLE IF NOT EXISTS #__jlc_response (
			  `response_id` int(10) unsigned NOT NULL auto_increment,
			  `response_category` varchar(150) default NULL,
			  `response_name` varchar(150) NOT NULL,
			  `response_txt` text NOT NULL,
			  `response_cdate` int(10) unsigned NOT NULL,
			  `response_mdate` int(10) unsigned NOT NULL default '0',
			  `response_enabled` tinyint(1) unsigned NOT NULL default '1',
			  `response_sort_order` mediumint(8) unsigned NOT NULL default '1',
			  `response_params` text NOT NULL,
			  PRIMARY KEY  (`response_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();
		///////////////////////////////////////////////

		$sql = "CREATE TABLE IF NOT EXISTS #__jlc_route (
			  `route_id` int(10) unsigned NOT NULL auto_increment,
			  `route_name` varchar(150) default NULL,
			  `route_source_data` text NOT NULL,
			  `route_action` varchar(50) NOT NULL default 'send_to_all_operators',
			  `route_enabled` tinyint(1) unsigned NOT NULL default '1',
			  `route_cdate` int(10) unsigned NOT NULL,
			  `route_mdate` int(10) unsigned default NULL,
			  `route_params` text,
			  `route_sort_order` int(10) unsigned NOT NULL,
			  PRIMARY KEY  (`route_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();
		///////////////////////////////////////////////

		$sql = "CREATE TABLE IF NOT EXISTS #__jlc_visitor (
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
			  PRIMARY KEY  (`visitor_id`),
			  KEY `mdate_index` (`visitor_mdate`) 
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();
		///////////////////////////////////////////////
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