<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

	// Prepare all the options here
	$dstOptions = array();
	for($i = -4 ; $i <= 4; $i++) {
		$dstOptions[] = array('text' => $i . ' ' . JText::_('COM_EASYSOCIAL_GENERAL_SETTINGS_DAYLIGHT_SAVING_OFFSET_HOURS'), 'value' => $i);
	}

	// limit options. in minute
	$ProximityUnitOptions = array(
		$settings->makeOption('In Miles', 'mile'),
		$settings->makeoption('In Kilometers', 'km'),
		'help' => true,
		'class' => 'form-control input-sm input-medium'
	);

	$processEmailText = $settings->renderSettingText('Send Email on page load', 'info') . ' <a href="http://docs.stackideas.com/administrators/cronjobs/cronjobs">' . $settings->renderSettingText('Send Email on page load', 'learn more') . '</a>';

	$cronUrl	= '';

	if($this->config->get('general.cron.key') )
	{
		$url 		= JURI::root() . 'index.php?option=com_easysocial&cron=true&phrase=' . $this->config->get('general.cron.key');

		$cronUrl	= '<br />' . JText::_('COM_EASYSOCIAL_GENERAL_SETTINGS_CRON_URL') . ':';

		$cronUrl 	.= '<input type="text" class="form-control input-sm" value="' . $url . '" />';
	}

	echo $settings->renderPage(
		$settings->renderColumn(
			$settings->renderSection(
				$settings->renderHeader('Lockdown Mode'),
				$settings->renderSetting('Enable Lockdown Mode', 'general.site.lockdown.enabled', 'boolean', array('help' => true)),
				$settings->renderSetting('Allow Registrations in Lockdown Mode', 'general.site.lockdown.registration', 'boolean', array('help' => true))
			),
			$settings->renderSection(
				$settings->renderHeader('Transporter Behaviour'),
				$settings->renderSetting('Send Email on page load', 'email.pageload', 'boolean', array('help' => true, 'info' => $processEmailText)),
				$settings->renderSetting('Sender name', 'email.sender.name', 'input', array('help' => true, 'class' => 'form-control input-sm', 'default' => $this->jConfig->getValue('fromname'))),
				$settings->renderSetting('Sender Email address', 'email.sender.email', 'input', array('help' => true,  'class' => 'form-control input-sm','default' => $this->jConfig->getValue('mailfrom'))),
				$settings->renderSetting('Reply to Email address', 'email.replyto', 'input', array('help' => true,  'class' => 'form-control input-sm','default' => $this->jConfig->getValue('mailfrom')))
			),
			$settings->renderSection(
				$settings->renderHeader('Location'),
				$settings->renderSetting('Language Code', 'general.location.language', 'input', array('help' => true, 'info' => true, 'default' => 'en', 'class' => 'input-sm')),
				$settings->renderSetting('Proximity Search Unit', 'general.location.proximity.unit', 'list', $ProximityUnitOptions)
			)
		),
		$settings->renderColumn(
			$settings->renderSection(
				$settings->renderHeader('Cronjob Settings'),
				$settings->renderSetting('Enable Secure Cron Url', 'general.cron.secure', 'boolean', array('help' => true, 'info' => true)),
				$settings->renderSetting('Secure Cron Key', 'general.cron.key', 'input', array('help' => true, 'info' => true, 'custom' => $cronUrl, 'class' => 'input-sm')),
				$settings->renderSetting('Number Of Emails', 'general.cron.limit', 'input', array('help' => true, 'info' => true, 'class' => 'form-control input-sm input-short text-center', 'unit' => true))
			),
			$settings->renderSection(
				$settings->renderHeader('URL Caching'),
				$settings->renderSetting('Automatically purge cached urls', 'general.url.purge', 'boolean', array('help' => true)),
				$settings->renderSetting('Purge interval', 'general.url.interval', 'input', array('help' => true, 'class' => 'form-control input-sm input-short text-center', 'unit' => true))
			)
		)
	);
