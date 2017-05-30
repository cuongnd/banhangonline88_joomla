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

$enterOptions = array(
	$settings->makeOption('Enter Key Submit Comment', 'submit'),
	$settings->makeOption('Enter Key Insert New Line', 'newline')
);

echo $settings->renderPage(
	$settings->renderColumn(
		$settings->renderSection(
			$settings->renderHeader('General'),
			$settings->renderSetting('Show Submit Button', 'comments.submit', 'boolean', array('help' => true)),
			$settings->renderSetting('Enter Key', 'comments.enter', 'list', array('options' => $enterOptions, 'help' => true))
		),
		$settings->renderSection(
			$settings->renderHeader('Pagination'),
			$settings->renderSetting('Pagination Limit', 'comments.limit', 'input', array('unit' => true, 'help' => true, 'class' => 'form-control input-sm input-short text-center'))
		)
	)
	// @ 1.3 reply comments
	/*,
	$settings->renderColumn(
		$settings->renderSection(
			$settings->renderHeader('Replies'),
			$settings->renderSetting('Enable Replies', 'comments.reply', 'boolean', array('help' => true)),
			$settings->renderSetting('Replies Max Level', 'comments.maxlevel', 'input', array('help' => true, 'class' => 'form-control input-sm input-short text-center'))
		)
	)
	*/
);
