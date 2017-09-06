<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$input=JFactory::getApplication()->input;
$os=$input->getString('os','');

if($os){
    $controller = JControllerLegacy::getInstance('Users');
    $controller->execute($input->get('task', 'display'));
    $controller->redirect();
    return;

}
require_once JPATH_COMPONENT . '/helpers/route.php';
$controller = JControllerLegacy::getInstance('Users');
$controller->execute($input->get('task', 'display'));
$controller->redirect();
