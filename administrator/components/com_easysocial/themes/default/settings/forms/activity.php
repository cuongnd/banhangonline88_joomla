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
echo $settings->renderPage(

	$settings->renderColumn(
		$settings->renderSection(
			$settings->renderHeader('Pagination'),
			$settings->renderSetting('Backend user activities limit', 'activity.pagination.max', 'input', array('unit'=> true, 'help' => true, 'class' => 'form-control input-sm input-short text-center')),
			$settings->renderSetting('Frontend data fetch limit', 'activity.pagination.limit', 'input', array('unit'=> true, 'help' => true, 'class' => 'form-control input-sm input-short text-center'))
		)
	)

);
