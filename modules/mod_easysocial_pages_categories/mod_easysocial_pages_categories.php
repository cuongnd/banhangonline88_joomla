<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');

// Include main engine
$engine = JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/easysocial.php';
$exists = JFile::exists($engine);

if (!$exists) {
	return;
}

// Include the engine file.
require_once($engine);

// Get the current logged in user
$my = ES::user();

$lib = ES::modules($module);

// Get the categories
$model = ES::model('PageCategories');

$options = array();
$options['limit'] = $params->get('display_limit', 1);
$options['ordering'] = $params->get('ordering', 'ordering');
$options['state'] = SOCIAL_STATE_PUBLISHED;

$categories = $model->getCategories($options);

require($lib->getLayout());