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

// current logged in user
$my = ES::user();

if ($my->guest) {
    // if user is a guest, do not proceed further.
    return;
}

// Determine if admins should be included in the user's listings.
$config = ES::config();

$accessModel = ES::model('AccessLogs');
$access = $my->getAccess();
$stat = array();

// conversation
if ($params->get('show_conversation')) {
    $obj = new stdClass();
    $obj->limit = $access->get('conversations.send.daily');
    $model = ES::model('Conversations');
    $obj->usage = $model->getTotalSentDaily($my->id);
    $obj->icon = 'fa-envelope';
    $stat['conversation'] = $obj;
}

// events
if ($params->get('show_event')) {
    $obj = new stdClass();
    $tmp = $access->get('events.limit');
    $obj->limit = $tmp->value;
    $obj->usage = $accessModel->getUsage('events.limit', $my->id, $tmp->interval);
    $obj->interval = ($tmp->interval) ? JText::_('MOD_EASYSOCIAL_PROFILE_STATS_INTERVAL_' . $tmp->interval) : '';
    $obj->icon = 'fa-calendar';
    $stat['event'] = $obj;
}

// groups.limit // create
if ($params->get('show_group')) {
    $obj = new stdClass();
    $tmp = $access->get('groups.limit');
    $obj->limit = $tmp->value;
    $obj->usage = $accessModel->getUsage('groups.limit', $my->id, $tmp->interval);
    $obj->interval = ($tmp->interval) ? JText::_('MOD_EASYSOCIAL_PROFILE_STATS_INTERVAL_' . $tmp->interval) : '';
    $obj->icon = "fa-group";
    $stat['group'] = $obj;
}

// friends.limit // make friend
if ($params->get('show_friend') && $config->get('friends.enabled')) {
    $obj = new stdClass();
    $obj->limit = $access->get('friends.limit');
    $obj->usage = $my->getTotalFriends() + $my->getTotalFriendRequestsSent();
    $obj->icon = "fa-users";
    $stat['friend'] = $obj;
}

// albums.total
if ($params->get('show_album')) {
    $obj = new stdClass();
    $obj->limit = $access->get('albums.total');
    $obj->usage = $my->getTotalAlbums(true);
    $obj->icon = "fa-photo";
    $stat['album'] = $obj;
}

// photos.uploader.maxdaily
if ($params->get('show_photo')) {
    $obj = new stdClass();
    $obj->icon = 'fa-photo';
    $obj->limit = $access->get('photos.uploader.maxdaily');
    $obj->usage = $my->getTotalPhotos(true, true);
    $obj->interval = JText::_('MOD_EASYSOCIAL_PROFILE_STATS_DAILY');
    $obj->icon = "fa-photo";
    $stat['photo'] = $obj;
}

// videos.daily
if ($params->get('show_video')) {
    $obj = new stdClass();
    $obj->limit = $access->get('videos.daily');
    $obj->usage = $my->getTotalVideos(true, true);
    $obj->interval = JText::_('MOD_EASYSOCIAL_PROFILE_STATS_DAILY');
    $obj->icon = "fa-film";
    $stat['video'] = $obj;
}

// pages.limit
if ($params->get('show_page')) {
    $obj = new stdClass();
    $tmp = $access->get('pages.limit');
    $obj->limit = $tmp->value;
    $obj->usage = $accessModel->getUsage('pages.limit', $my->id, $tmp->interval);
    $obj->interval = ($tmp->interval) ? JText::_('MOD_EASYSOCIAL_PROFILE_STATS_INTERVAL_' . $tmp->interval) : '';
    $obj->icon = 'fa-cube';
    $stat['page'] = $obj;
}

require($lib->getLayout());
