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

class JFBConnectControllerUserMap extends JFBConnectController
{
    function __construct()
    {
        parent::__construct();
        $document = JFactory::getDocument();
        $viewType = $document->getType();
        $viewName = 'usermap';
        $this->view = $this->getView($viewName, $viewType);
    }

    function display($cachable = false, $urlparams = false)
    {
        $usermapModel = $this->getModel('usermap');
        $this->view->setModel($usermapModel, false);

        $viewLayout = JRequest::getCmd('layout', 'list');
        $this->view->setLayout($viewLayout);

        // Add Toolbar icons
        if (defined('SC16')):
            $icon = 'send';
        endif; //SC16
        if (defined('SC30')):
            $icon = 'envelope';
        endif; //SC30
        JToolBarHelper::custom('selectRequest', $icon, $icon, JText::_('COM_JFBCONNECT_BUTTON_SEND_REQUEST'), true);
        $app = JFactory::getApplication();
        JPluginHelper::importPlugin('socialprofiles');
        $profilePlugins = $app->triggerEvent('socialProfilesGetPlugins');
        $pluginNames = array();
        foreach ($profilePlugins as $plugin)
        {
            if ($plugin->canImportConnections())
                $pluginNames[] = $plugin->getName();
        }
        if (count($pluginNames) != 0)
        {
            $doc = JFactory::getDocument();
            $doc->addCustomTag('<script>var jfbcImportMsg = "' . JText::_('COM_JFBCONNECT_MSG_IMPORT_DESC') . '\n' . implode('\n', $pluginNames) . '\n\n' . JText::_('COM_JFBCONNECT_MSG_IMPORT_CONFIRMATION') . '";</script>');
            $html = "onclick=\"javascript:if(confirm(jfbcImportMsg)){submitbutton('importConnections');}\" class=\"toolbar\">\n";
            $html .= "<span class=\"icon-32-upload\" title=\"" . JText::_('COM_JFBCONNECT_MSG_IMPORT_TITLE') . "\">\n";
            $html .= "</span>\n";
            $html .= JText::_('COM_JFBCONNECT_MSG_IMPORT_TITLE') . "\n";

            if (defined('SC16')):
                $html = '<a href="#" ' . $html . "</a>\n";
            endif;
            if (defined('SC30')):
                $html = '<button ' . $html . "</button>\n";
            endif;
            $bar = JToolBar::getInstance('toolbar');
            //$bar->appendButton('Confirm', "Import connections from the enabled profile plugins? Ensure you've enabled only the plugins for 3rd party components that you'd want to import previous connections from.", 'upload', 'Import Connections', 'importConnections', false, false);
            $bar->appendButton('Custom', $html);
        }
        JToolBarHelper::deleteList(JText::_('COM_JFBCONNECT_MSG_USERMAP_DELETE_CONFIRMATION'));

        $this->view->display();
    }

    function remove()
    {
        $model = $this->getModel('UserMap');

        if (!$model->delete())
        {
            $msg = JText::_('COM_JFBCONNECT_MSG_USERMAP_DELETE_FAIL');
        }
        else
        {
            $msg = JText::_('COM_JFBCONNECT_MSG_USERMAP_DELETE_SUCCESS');
        }

        $this->display();
    }

    function importConnections()
    {
        $app = JFactory::getApplication();
        JPluginHelper::importPlugin('socialprofiles');
        $profilePlugins = $app->triggerEvent('jfbcImportConnections');
        $msg = JText::_('COM_JFBCONNECT_MSG_IMPORT_SUCCESS');
        $app->enqueueMessage($msg);
        $this->display();
    }

    function selectRequest()
    {
        if (defined('SC16')):
            $icon = 'forward';
        endif; //SC16
        if (defined('SC30')):
            $icon = 'arrow-right';
        endif; //SC30
        JToolBarHelper::custom("previewSend", $icon, $icon, "Preview Send", false);

        $usermapModel = $this->getModel('usermap');
        $this->view->setModel($usermapModel, false);

        $requestModel = $this->getModel('request');
        $this->view->setModel($requestModel, false);

        $this->view->setLayout('select_request');
        $this->view->selectRequest();
    }
}