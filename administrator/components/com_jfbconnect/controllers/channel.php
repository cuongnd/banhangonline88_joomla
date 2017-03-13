<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');
class JFBConnectControllerChannel extends JControllerForm
{
    private function checkAutotune()
    {
        // Saving an object or action
        $appConfig = JFBCFactory::config()->getSetting('autotune_app_config', array());
        $namespace = $appConfig['namespace'];
        if ($namespace == '')
            return false;
        else
            return true;
    }
}