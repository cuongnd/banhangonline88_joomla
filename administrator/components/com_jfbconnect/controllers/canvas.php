<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */
 // Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class JFBConnectControllerCanvas extends JFBConnectController
{
    function apply()
    {
        $app = JFactory::getApplication();
        $configs = JRequest::get('POST', 4);
        $model = $this->getModel('config');
        $model->saveSettings($configs);
        $app->enqueueMessage(JText::_('COM_JFBCONNECT_MSG_SETTINGS_UPDATED'));
        $this->setRedirect('index.php?option=com_jfbconnect&view=canvas');
    }

    public static function setupCanvasProperties()
    {
        $jfbcLibrary = JFBCFactory::provider('facebook');

        $canvasProperties = new JObject();
        $appId = $jfbcLibrary->appId;
        if ($appId)
        {
            $params = "?fields=canvas_url,secure_canvas_url,page_tab_default_name,page_tab_url,secure_page_tab_url,namespace,website_url,canvas_fluid_height,canvas_fluid_width";
            $appProps = $jfbcLibrary->api($appId . $params, null, FALSE);

            $canvasProperties->setProperties($appProps);
        }
        return $canvasProperties;
    }

    public static function isCanvasSetupCorrect()
    {
        $canvasProperties = JFBConnectControllerCanvas::setupCanvasProperties();

        $canvasName = $canvasProperties->get('namespace', "");
        $canvasUrl = $canvasProperties->get('canvas_url', '');
        $secureCanvasUrl = $canvasProperties->get('secure_canvas_url', '');

        if (!$canvasName || $canvasUrl == "" || $secureCanvasUrl == "")
        {
            return false;
        }
        else
        {
            return true;
        }
    }

}