<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');

$jVersion = new JVersion();
if (version_compare($jVersion->getShortVersion(), '3.0.0', '>'))
    define('SC30', 1);
else
    define('SC16', 1);

require_once(JPATH_SITE . '/components/com_jfbconnect/libraries/factory.php');

$document = JFactory::getDocument();
$document->addStyleSheet("components/com_jfbconnect/assets/css/default.css");
$document->addStyleSheet(JURI::root() . "media/sourcecoast/css/sc_bootstrap.css");
$document->addStyleSheet(JURI::root() . "media/sourcecoast/css/common.css");
$document->addScript(JURI::root() . "media/sourcecoast/js/jq-bootstrap-1.8.3.js");
if (defined('SC16')) :
    $document->addScript("components/com_jfbconnect/assets/js/jfbcadmin-template.js");
endif;
$document->addScript("components/com_jfbconnect/assets/jfbconnect-admin.js");

jimport('sourcecoast.utilities');
SCStringUtilities::loadLanguage('com_jfbconnect', JPATH_ADMINISTRATOR);

require_once(JPATH_COMPONENT . '/controller.php');

$input = JFactory::getApplication()->input;
$task = $input->getCmd('task');

// Slowly update these 'old' admin views to the new style...
$oldStyle = array('ajax', 'autotune', 'canvas', 'config', 'notification', 'opengraph', 'profiles', 'request', 'social', 'usermap');
if (strpos($task, '.') === false &&
        (in_array(JRequest::getCmd('controller', ''), $oldStyle) ||
        in_array(JRequest::getCmd('view', ''), $oldStyle))
        )
{
    // Old beatup way. Don't do this anymore
    $view = JRequest::getCmd('controller', '');
    if ($view == "")
        $view = JRequest::getCmd('view', '');

    if ($view != '' && $view != "jfbconnect") // Don't do this for the main landing page. Fix this system
    {
        require_once(JPATH_COMPONENT . '/controllers/' . strtolower($view) . '.php');
        $controllerName = $view;
    }
    else
        $controllerName = "";

    $classname = 'JFBConnectController' . ucfirst($controllerName);
    $controller = new $classname();
}
else
    $controller = JControllerLegacy::getInstance('jfbconnect');

$controller->execute($input->getCmd('task'));

if (JRequest::getCmd('tmpl') != 'component')
    include_once(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/assets/footer/footer.php');

$controller->redirect();
