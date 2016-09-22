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
		$dstOptions[] = array('text' => $i . ' ' . JText::_('COM_EASYSOCIAL_SYSTEM_SETTINGS_DAYLIGHT_SAVING_OFFSET_HOURS'), 'value' => $i);
	}

	ob_start(); ?>
	<p class="mt-20"><b><?php echo JText::_('COM_EASYSOCIAL_SYSTEM_SETTINGS_ENVIRONMENT_PRODUCTION'); ?></b> - <?php echo JText::_('COM_EASYSOCIAL_SYSTEM_SETTINGS_ENVIRONMENT_PRODUCTION_DESC'); ?></p>
	<p><b><?php echo JText::_('COM_EASYSOCIAL_SYSTEM_SETTINGS_ENVIRONMENT_DEVELOPMENT'); ?></b> - <?php echo JText::_('COM_EASYSOCIAL_SYSTEM_SETTINGS_ENVIRONMENT_DEVELOPMENT_DESC'); ?></p>
	<?php
	$environmentInfo = ob_get_contents();
	ob_end_clean();

	$envOptions = array(
		$settings->makeOption('Environment Development', 'development'),
		$settings->makeoption('Environment Production', 'static'),
		'help' => true,
		'info' => $environmentInfo,
		'class' => 'form-control input-sm'
	);

	ob_start(); ?>
	<p class="mt-20"><b><?php echo JText::_('COM_EASYSOCIAL_SYSTEM_SETTINGS_COMPRESSION_COMPRESSED'); ?></b> - <?php echo JText::_('COM_EASYSOCIAL_SYSTEM_SETTINGS_COMPRESSION_COMPRESSED_DESC'); ?></p>
	<p><b><?php echo JText::_('COM_EASYSOCIAL_SYSTEM_SETTINGS_COMPRESSION_UNCOMPRESSED'); ?></b> - <?php echo JText::_('COM_EASYSOCIAL_SYSTEM_SETTINGS_COMPRESSION_UNCOMPRESSED_DESC'); ?></p>
	<p><small><?php echo JText::_('COM_EASYSOCIAL_SYSTEM_SETTINGS_COMPRESSION_UNCOMPRESSED_INFO'); ?></small></p>
	<?php
	$compressInfo = ob_get_contents();
	ob_end_clean();

	$compressOptions = array(
		$settings->makeOption('Compression Compressed', 'compressed'),
		$settings->makeoption('Compression Uncompressed', 'uncompressed'),
		'help' => true,
		'info' => $compressInfo,
		'class' => 'form-control input-sm'
	);

	$processEmailText = $settings->renderSettingText('Send Email on page load', 'info') . ' <a href="#">' . $settings->renderSettingText('Send Email on page load', 'learn more') . '</a>';

	echo $settings->renderPage(
		$settings->renderColumn(
			$settings->renderSection(
				$settings->renderHeader('System Settings'),
				$settings->renderSetting('API Key', 'general.key', 'input', array('help' => true, 'class' => 'input-sm form-control')),
				$settings->renderSetting('Environment', 'general.environment', 'list', $envOptions),
				$settings->renderSetting('Javascript Compression', 'general.mode', 'list', $compressOptions),
				$settings->renderSetting('Inline Configuration', 'general.inline', 'boolean', array(
					'help' => true,
					'info' => '<p class="mt-20">' . JText::_('COM_EASYBLOG_SETTINGS_SYSTEM_INLINE_CONFIGURATION_INFO') . '</p>'
				)),
				$settings->renderSetting('Profiler', 'general.profiler', 'boolean', array('help' => true)),
				$settings->renderSetting('Logger', 'general.logger', 'boolean', array('help' => true))
			)
		),
		$settings->renderColumn(
			$settings->renderSection(
				$settings->renderHeader('CDN Settings'),
				$settings->renderSettingText('CDN Info'),
				$settings->renderSetting('Enable CDN', 'general.cdn.enabled', 'boolean', array('help' => true)),
				$settings->renderSetting('CDN Url', 'general.cdn.url', 'input', array('help' => true, 'class' => 'input-sm form-control')),
				$settings->renderSetting('Passive CDN', 'general.cdn.passive', 'boolean', array(
					'help' => true,
					'info' => '<p class="mt-20">' . JText::_('COM_EASYBLOG_SETTINGS_CDN_PASSIVE_CDN_INFO') . '</p>'
				))
			)
		)
	);
