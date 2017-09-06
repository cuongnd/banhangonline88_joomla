<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
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

$lib = ES::modules($module);
$options = array();
$my = ES::user();

if ($params->get('total')) {
    $options['limit'] = $params->get('total');
}

// Check filter type
$filter = $params->get('filter', 'followedBy');

$model = ES::model('Followers');
$users = array();

if ($filter == 'following') {
	$users = $model->getFollowing($my->id, $options);
} else {
    $users = $model->getFollowers($my->id, $options);
}

// When there are no users, we shouldn't display anything
if (!$users) {
	return;
}

require($lib->getLayout());