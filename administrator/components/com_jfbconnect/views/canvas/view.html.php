<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('sourcecoast.adminHelper');

class JFBConnectViewCanvas extends JViewLegacy
{
    function display($tpl = null)
    {
        $model = JFBCFactory::config();
        $jfbcLibrary = JFBCFactory::provider('facebook');

        require_once JPATH_ADMINISTRATOR . '/components/com_templates/helpers/templates.php';
        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_templates/models');
        $templatesModel = JModelLegacy::getInstance('Styles', 'TemplatesModel');
        $allTemplates = $templatesModel->getItems();
        $templates = array();
        foreach ($allTemplates as $template)
        {
            if ($template->client_id == 0)
            {
                // Make it the same as J15 so we can use the same selectlist
                $template->directory = $template->id;
                $template->name = $template->title;
                $templates[] = $template;
            }
        }

        // Add the "Don't Override" option to set no special template
        $defaultTemplate = new stdClass();
        $defaultTemplate->directory = -1;
        $defaultTemplate->name = JText::_('COM_JFBCONNECT_CANVAS_DISPLAY_TEMPLATE_DEFAULT');
        array_unshift($templates, $defaultTemplate);

        require_once(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/controllers/canvas.php');
        $canvasProperties = JFBConnectControllerCanvas::setupCanvasProperties();

        $canvasTabTemplate = $model->getSetting('canvas_tab_template', -1);
        $canvasCanvasTemplate = $model->getSetting('canvas_canvas_template', -1);

        $this->assignRef('canvasProperties', $canvasProperties);
        $this->assignRef('canvasTabTemplate', $canvasTabTemplate);
        $this->assignRef('canvasCanvasTemplate', $canvasCanvasTemplate);
        $this->assignRef('templates', $templates);
        $this->assignRef('model', $model);
        $this->assignRef('jfbcLibrary', $jfbcLibrary);

        $this->addToolbar();

        parent::display($tpl);
    }

    function addToolbar()
    {
        JToolBarHelper::title('JFBConnect', 'jfbconnect.png');
        JToolBarHelper::apply('apply', JText::_('COM_JFBCONNECT_BUTTON_APPLY_CHANGES'));
        SCAdminHelper::addAutotuneToolbarItem();
    }
}
