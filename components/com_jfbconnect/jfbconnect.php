<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$input = JFactory::getApplication()->input;
$task = $input->getCmd('task', '');
require_once (__DIR__ . '/controller.php');

if (!$task)
{
    $view = $input->getCmd('view', '');
    // Build up the controller / task dynamically
    if ($view == "loginregister" || $view == 'opengraph')
        $input->set('task', $view . '.' . 'display');
}


$controller = JControllerLegacy::getInstance('jfbconnect');

$controller->execute($input->get('task'));
$controller->redirect();