<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package   MaQma_Helpdesk
 * @copyright (C) 2006-2012 Components Lab, Lda.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

/**
 * Script file of MaQma Helpdesk component
 */
class com_MaQmaHelpdeskInstallerScript
{

	/**
	 *
	 * method to run before an install/update/uninstall method
	 *
	 * return void
	 *
	 */
	function preflight($type, $parent)
	{

	}

	/**
	 *
	 * method to install the component
	 *
	 * return void
	 *
	 */
	function install($parent)
	{
		$CONFIG = new JConfig;
		$database = JFactory::getDBO();
		$docspath = JPATH_SITE . "/components/com_maqmahelpdesk/attachments/";

		// Workgroup email from address and email from name
		$sql = "UPDATE `#__support_workgroup`
				SET `wkmail_address` = '" . $CONFIG->mailfrom . "',
					`wkmail_address_name` = '" . $CONFIG->fromname . "';";
		$database->setQuery($sql);
		$database->query();

		// Set attachments path
		$sql = "UPDATE `#__support_config`
				SET `docspath` = '" . str_replace('\\', '/', $docspath) . "';";
		$database->setQuery($sql);
		$database->query();
	}

	/**
	 *
	 * method to uninstall the component
	 *
	 * return void
	 *
	 */
	function uninstall($parent)
	{
		$content = "MaQma Helpdesk Component Uninstalled!";
		echo $content;
	}

	/**
	 *
	 * method to update the component
	 *
	 * return void
	 *
	 */
	function update($parent)
	{
		$database = JFactory::getDBO();

		$sql = "UPDATE `#__support_config`
				SET `date_short`='%d/%m/%Y %R'
				WHERE `date_short`='j/n/Y H:i'";
		$database->setQuery($sql);
		$database->query();

		$sql = "UPDATE `#__support_config`
				SET `date_long`='%d of %B of %Y, %R'
				WHERE `date_long`='D jS M Y, H:i'";
		$database->setQuery($sql);
		$database->query();

		$sql = "UPDATE `#__support_config`
				SET `dateonly_format`='%d/%m/%Y'
				WHERE `dateonly_format`='d/m/Y'";
		$database->setQuery($sql);
		$database->query();

		$content = "MaQma Helpdesk Component Updated!";
		echo $content;
	}

	/**
	 *
	 * method to run after an install/update/uninstall method
	 *
	 * return void
	 *
	 */
	function postflight($type, $parent)
	{
		$database = JFactory::getDBO();

		// If it's already installed needs to check if there are support users without views
		$sql = "SELECT DISTINCT `id_user`, `manager`
				FROM `#__support_permission`
				WHERE `id_user` NOT IN (SELECT `id_user` FROM `#__support_views`)";
		$database->setQuery($sql);
		$rows = $database->loadObjectList();
		for ($i = 0; $i < count($rows); $i++)
		{
			$row = $rows[$i];
			$operator = 'AND' . ($row->manager > 5 ? '|OR' : '');
			$field = 't.assign_to' . ($row->manager > 5 ? '|t.assign_to' : '');
			$arithmetic = '=' . ($row->manager > 5 ? '|=' : '');
			$value = $row->id_user . '' . ($row->manager > 5 ? '|0' : '');
			$sql = "INSERT INTO `#__support_views`(`id_user`, `name`, `viewtype`, `ordering`, `operator`, `field`, `arithmetic`, `value`, `default`)
					VALUES(" . $row->id_user . ", 'Tickets assigned to me" . ($row->manager > 5 ? ' and not assigned' : '') . "', 'table', 't.duedate', '$operator', '$field', '$arithmetic', '$value', 1)";
			$database->setQuery($sql);
			$database->query();
		}

		// Maintain users syncronization
		$database->setQuery("select `id` from `#__users` where `id` not in (select `id_user` from `#__support_users`)");
		$rows = $database->loadObjectList();
		for ($i = 0; $i < count($rows); $i++)
		{
			$row = $rows[$i];
			$sql = "INSERT INTO `#__support_users`(id_user,avatar)
					VALUES('" . $row->id . "', '" . JURI::root() . "media/com_maqmahelpdesk/images/avatars/anonymous.png')";
			$database->setQuery($sql);
			$database->query();
		} ?>

		<style>
		.detailmsg{font-size:16px;width:100%;color:#666;min-height:250px;}
		.detailmsg h1{font-size:22px;color:#333;}
		#maqma_links{list-style-type:none;padding:0;font-size:10px;font-weight:normal;}
		</style>
		<div class="detailmsg">
			<table width="100%">
			<tr>
				<td width="30%" align="center" valign="top">
					<img src="components/com_maqmahelpdesk/images/logo_helpdesk.png" alt=""/>
				</td>
				<td width="70%">
					<h3>MaQma Helpdesk</h3>
					<p style="font-size:16px;"><?php echo ($type == 'update' ? 'The component was <b>updated</b>!' : 'The component was <b>installed</b>!');?></p>
					<p style="font-size:16px;">Visit us at <a href="http://www.imaqma.com" target="_blank">www.imaqma.com</a> for news, updates and more products.</p>
					<ul id="maqma_links">
						<li>
							<a href="http://www.facebook.com/imaqma" target="_blank"><img
								src="../media/com_maqmahelpdesk/images/ui/facebook.png" alt="" width="16" border="0"
								style="padding-right:3px;padding-top:3px;"/><span
                                    style="padding-bottom:5px;">Facebook</span>
							</a>
						</li>
						<li>
							<a href="http://www.twitter.com/imaqma" target="_blank">
								<img
								src="../media/com_maqmahelpdesk/images/ui/twitter.png" alt="" width="16" border="0"
								style="padding-right:3px;padding-top:3px;"/><span
                                    style="padding-bottom:5px;">Twitter</span>
							</a>
						</li>
						<li>
							<a href="javascript:;">
								<img src="../media/com_maqmahelpdesk/images/ui/skype.png" alt="" width="16" border="0"
									 style="padding-right:3px;padding-top:3px;"/>
                                <span style="padding-bottom:5px;">Skype: <b>pdaniel</b></span>
							</a>
						</li>
					</ul>
				</td>
			</tr>
			</table>
		</div><?php

		self::olderInstall();
	}

	function checkField($table, $field)
	{
		$db = JFactory::getDBO();

		if ($field != '') {
			$sql = "SHOW COLUMNS FROM " . $table;
			$db->setQuery($sql);
			$rows = $db->loadObjectList();

			for ($i = 0; $i < count($rows); $i++) {
				$row = $rows[$i];
				if ($row->Field == $field) {
					return true;
				}
			}
		} else {
			$sql = "SHOW TABLES";
			$db->setQuery($sql);
			$rows = $db->loadAssocList();

			for ($i = 0; $i < count($rows); $i++) {
				$row = $rows[$i];
				if ($row[0] == $table) {
					return true;
				}
			}
		}

		return false;
	}

	function olderInstall()
	{
		$database = JFactory::getDBO();

		if (!self::checkField('#__support_config', 'kb_enable_rating')) {
			$database->setQuery("ALTER TABLE `#__support_config` ADD `kb_enable_rating` tinyint(1) default '1';");
			$database->query();
		}
		if (!self::checkField('#__support_config', 'kb_enable_comments')) {
			$database->setQuery("ALTER TABLE `#__support_config` ADD `kb_enable_comments` tinyint(1) default '1';");
			$database->query();
		}
		if (!self::checkField('#__support_config', 'digistore_domains')) {
			$database->setQuery("ALTER TABLE `#__support_config` ADD `digistore_domains` tinyint(1) default '1';");
			$database->query();
		}
		if (!self::checkField('#__support_ticket', 'github_repository')) {
			$database->setQuery("ALTER TABLE `#__support_ticket` ADD `github_repository` varchar(50) NOT NULL DEFAULT '';");
			$database->query();
		}
		if (!self::checkField('#__support_ticket', 'github_issue')) {
			$database->setQuery("ALTER TABLE `#__support_ticket` ADD `github_issue` varchar(50) NOT NULL DEFAULT '';");
			$database->query();
		}
		if (!self::checkField('#__support_config', 'github_username')) {
			$database->setQuery("ALTER TABLE `#__support_config` ADD `github_username` VARCHAR(50) default '';");
			$database->query();
		}
		if (!self::checkField('#__support_config', 'github_password'))
		{
			$database->setQuery("ALTER TABLE `#__support_config` ADD `github_password` VARCHAR(50) default '';");
			$database->query();
		}
		if (!self::checkField('#__support_config', 'show_kb_frontpage'))
		{
			$database->setQuery("ALTER TABLE `#__support_config` ADD `show_kb_frontpage` tinyint(1) default '1';");
			$database->query();
		}
		if (!self::checkField('#__support_config', 'include_bootstrap'))
		{
			$database->setQuery("ALTER TABLE `#__support_config` ADD `include_bootstrap` tinyint(1) default '1';");
			$database->query();
		}
		if (!self::checkField('#__support_config', 'dateonly_format'))
		{
			$database->setQuery("ALTER TABLE `#__support_config` ADD `dateonly_format` varchar(50) default '%d/%m/%Y';");
			$database->query();
		}
		if (!self::checkField('#__support_config', 'manual_times'))
		{
			$database->setQuery("ALTER TABLE `#__support_config` ADD `manual_times` tinyint(1) default '0';");
			$database->query();
		}
		if (!self::checkField('#__support_client', 'autoassign'))
		{
			$database->setQuery("ALTER TABLE `#__support_client` ADD `autoassign` int(11) NOT NULL default '0';");
			$database->query();
		}
		$database->setQuery("CREATE TABLE IF NOT EXISTS `#__support_timesheet` (`id` int(11) NOT NULL auto_increment,`id_client` int(11) NOT NULL default '0',`id_user` int(11) NOT NULL default '0',`year` varchar(4) NOT NULL default '',`month` varchar(2) NOT NULL default '',`day` varchar(2) NOT NULL default '',`time` TIME NOT NULL,PRIMARY KEY  (`id`));");
		$database->query();
		if (!self::checkField('#__support_config', 'editor'))
		{
			$database->setQuery("ALTER TABLE `#__support_config` ADD `editor` varchar(15) NOT NULL default 'builtin';");
			$database->query();
		}
		if (!self::checkField('#__support_permission', 'id_schedule'))
		{
			$database->setQuery("ALTER TABLE `#__support_permission` ADD `id_schedule` int(11) NOT NULL default '0';");
			$database->query();
		}
		if (!self::checkField('#__support_config', 'kb_number_chars'))
		{
			$database->setQuery("ALTER TABLE `#__support_config` ADD `kb_number_chars` varchar(3) NOT NULL default '30';");
			$database->query();
		}
		if (!self::checkField('#__support_config', 'kb_number_columns'))
		{
			$database->setQuery("ALTER TABLE `#__support_config` ADD `kb_number_columns` varchar(2) NOT NULL default '3';");
			$database->query();
		}
		if (!self::checkField('#__support_config', 'autoclose_status'))
		{
			$database->setQuery("ALTER TABLE `#__support_config` ADD `autoclose_status` INT(11) NOT NULL DEFAULT '0';");
			$database->query();
		}
		$database->setQuery("CREATE TABLE IF NOT EXISTS `#__support_country` (`startip` varchar(10) NOT NULL default '',`endip` varchar(10) NOT NULL default '',`countrycode` varchar(2) NOT NULL DEFAULT '',`countryname` varchar(100) NOT NULL DEFAULT '',UNIQUE KEY `idxBoth` (`startip`,`endip`),KEY `idxStart` (`startip`),KEY `idxEnd` (`endip`));");
		$database->query();
		$database->setQuery("ALTER TABLE `#__support_custom_fields` CHANGE  `ftype`  `ftype` ENUM('text', 'select', 'radio', 'checkbox', 'textarea', 'htmleditor', 'dbselect', 'country', 'state', 'note', 'date');");
		$database->query();
		if (!self::checkField('#__support_client_wk', 'app_announcements'))
		{
			$database->setQuery("ALTER TABLE `#__support_client_wk` ADD `app_announcements` TINYINT( 1 ) NOT NULL DEFAULT '1';");
			$database->query();
		}
		if (!self::checkField('#__support_client_wk', 'app_bugtracker'))
		{
			$database->setQuery("ALTER TABLE `#__support_client_wk` ADD `app_bugtracker` TINYINT( 1 ) NOT NULL DEFAULT '1';");
			$database->query();
		}
		if (!self::checkField('#__support_client_wk', 'app_discussions'))
		{
			$database->setQuery("ALTER TABLE `#__support_client_wk` ADD `app_discussions` TINYINT( 1 ) NOT NULL DEFAULT '1';");
			$database->query();
		}
		if (!self::checkField('#__support_client_wk', 'app_glossary'))
		{
			$database->setQuery("ALTER TABLE `#__support_client_wk` ADD `app_glossary` TINYINT( 1 ) NOT NULL DEFAULT '1';");
			$database->query();
		}
		if (!self::checkField('#__support_client_wk', 'app_trouble'))
		{
			$database->setQuery("ALTER TABLE `#__support_client_wk` ADD `app_trouble` TINYINT( 1 ) NOT NULL DEFAULT '1';");
			$database->query();
		}
		if (!self::checkField('#__support_client_wk', 'app_downloads'))
		{
			$database->setQuery("ALTER TABLE `#__support_client_wk` ADD `app_downloads` TINYINT( 1 ) NOT NULL DEFAULT '1';");
			$database->query();
		}
		if (!self::checkField('#__support_client_wk', 'app_kb'))
		{
			$database->setQuery("ALTER TABLE `#__support_client_wk` ADD `app_kb` TINYINT( 1 ) NOT NULL DEFAULT '1';");
			$database->query();
		}
		if (!self::checkField('#__support_client_wk', 'app_faq'))
		{
			$database->setQuery("ALTER TABLE `#__support_client_wk` ADD `app_faq` TINYINT( 1 ) NOT NULL DEFAULT '1';");
			$database->query();
		}
		if (!self::checkField('#__support_client_wk', 'app_ticket'))
		{
			$database->setQuery("ALTER TABLE `#__support_client_wk` ADD `app_ticket` TINYINT( 1 ) NOT NULL DEFAULT '1';");
			$database->query();
		}
		if (!self::checkField('#__support_users', 'vacances'))
		{
			$database->setQuery("ALTER TABLE `#__support_users` ADD `vacances` text NOT NULL default '';");
			$database->query();
		}
		if (!self::checkField('#__support_users', 'id_schedule'))
		{
			$database->setQuery("ALTER TABLE `#__support_users` ADD `id_schedule` int(11) NOT NULL default '0';");
			$database->query();
		}
		if (!self::checkField('#__support_ticket', 'internal'))
		{
			$database->setQuery("ALTER TABLE `#__support_ticket` ADD `internal` TINYINT( 1 ) NOT NULL DEFAULT '0';");
			$database->query();
		}
		if (!self::checkField('#__support_config', 'use_eshop_suite_avatars'))
		{
			$database->setQuery("ALTER TABLE `#__support_config` ADD `use_eshop_suite_avatars` TINYINT( 1 ) NOT NULL DEFAULT '0';");
			$database->query();
		}
		if (!self::checkField('#__support_config', 'date_country_code'))
		{
			$database->setQuery("ALTER TABLE `#__support_config` ADD `date_country_code` varchar(25) NOT NULL default '';");
			$database->query();
		}
		if (!self::checkField('#__support_config', 'tickets_per_department'))
		{
			$database->setQuery("ALTER TABLE `#__support_config` ADD `tickets_per_department` TINYINT(1) NOT NULL DEFAULT '0';");
			$database->query();
		}
		if (!self::checkField('#__support_reports', 'groupby2'))
		{
			$database->setQuery("ALTER TABLE `#__support_reports` ADD `groupby2` varchar(10) NOT NULL;");
			$database->query();
		}
		if (!self::checkField('#__support_client', 'overtime'))
		{
			$database->setQuery("ALTER TABLE `#__support_client` ADD `overtime` DECIMAL(14, 2) NOT NULL DEFAULT '0'");
			$database->query();
		}
		$database->setQuery("ALTER TABLE `#__support_mail_fetch` CHANGE `extra_info` `extra_info` VARCHAR( 100 ) NOT NULL DEFAULT '';");
		$database->query();
		$database->setQuery("ALTER TABLE `#__support_reports` CHANGE `f_year` `f_year` varchar(4) NOT NULL;");
		$database->query();
		$database->setQuery("CREATE TABLE IF NOT EXISTS `#__support_department_groups` (`id_department` INT( 11 ) NOT NULL , `id_group` INT( 11 ) NOT NULL ,PRIMARY KEY (  `id_department` ,  `id_group` ));");
		$database->query();
		$database->setQuery("ALTER TABLE `#__support_form` CHANGE `description` `description` MEDIUMTEXT NULL DEFAULT ''");
		$database->query();
		$database->setQuery("CREATE TABLE IF NOT EXISTS `#__support_download_field_value` (`id_field` int(11) NOT NULL default '0',`id_download` int(11) NOT NULL default '0',`value` text default NULL,PRIMARY KEY  (`id_field`,`id_download`),INDEX `idx_id_download` (`id_download`));");
		$database->query();
		$database->setQuery("CREATE TABLE IF NOT EXISTS `#__support_client_field_value` (`id_field` int(11) NOT NULL default '0',`id_client` int(11) NOT NULL default '0',`value` text default NULL,PRIMARY KEY  (`id_field`,`id_client`),INDEX `idx_id_client` (`id_client`));");
		$database->query();
	}

}