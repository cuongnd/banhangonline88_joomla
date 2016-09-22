<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
// Include the menu functions only once
require_once __DIR__ . '/helper.php';

$helper=ModMenuHelper::getInstance();


$list       = $helper->getList($params);
$base       = $helper->getBase($params);
$active     = $helper->getActive($params);
$default    = $helper->getDefault();
$active_id  = $active->id;
$default_id = $default->id;
$path       = $base->tree;
$showAll    = $params->get('showAllChildren');
$class_sfx  = htmlspecialchars($params->get('class_sfx'), ENT_COMPAT, 'UTF-8');
require JModuleHelper::getLayoutPath('mod_menu', $params->get('layout', 'default'));