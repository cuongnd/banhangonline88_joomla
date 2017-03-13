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

jimport('joomla.application.component.view');
jimport('sourcecoast.adminHelper');

class JFBConnectViewOpengraph extends JViewLegacy
{
    function display($tpl = null)
    {
        $title = "JFBConnect: Open Graph";
        $app = JFactory::getApplication();

        $layout = $this->getLayout();
        if ($layout != 'display' && $layout != 'default')
        {
            JToolBarHelper::custom('display', 'opengraph.png', 'index.php?option=com_jfbconnect&view=opengraph', 'Open Graph Home', false);
            JToolBarHelper::divider();
        }

        switch ($this->getLayout())
        {
            case 'actions':
                $title .= " - Actions";
                $bar = JToolBar::getInstance('toolbar');
                $bar->appendButton('Popup', 'new', 'New', 'index.php?option=com_jfbconnect&view=opengraph&task=actioncreate&tmpl=component', '550', '400', '0', '0', '');
                JToolBarHelper::publishList();
                JToolBarHelper::unpublishList();
                JToolBarHelper::deleteList('Deleting these actions will delete all activity associated with them. Are you sure?');

                $model = $this->getModel('OpenGraphAction', 'JFBConnectAdminModel');
                $actionsCustom = $model->getActions();

                $this->assignRef('actions', $actionsCustom);
                break;

            case 'actionedit':
                $title .= " - Edit Action";
                JToolBarHelper::apply('apply', 'Save');
                JToolBarHelper::save('save', 'Save & Close');
                JToolBarHelper::cancel('cancel', 'Cancel');

                $model = $this->getModel('OpenGraphAction', 'JFBConnectAdminModel');
                $id = JRequest::getInt('id', null);
                if ($id != 0)
                    $action = $model->getAction($id);
                else
                {
                    $plugin = JRequest::getCmd('plugin');
                    $name = JRequest::getCmd('name');

                    $action = new ogAction();
                    $action->loadDefaultAction($plugin, $name);
                }

                $this->assignRef('action', $action);

                $objectModel = $this->getModel('OpenGraphObject', 'JFBConnectAdminModel');
                $objects = $objectModel->getObjects(true);

                $this->assignRef('objects', $objects);
                break;

            case 'objects':
                $title .= " - Objects";
                $bar = JToolBar::getInstance('toolbar');
                $bar->appendButton('Popup', 'new', 'New', 'index.php?option=com_jfbconnect&view=opengraph&task=objectcreate&tmpl=component', '550', '400', '0', '0', '');

                JToolBarHelper::publishList();
                JToolBarHelper::unpublishList();
                JToolBarHelper::deleteList('Deleting these objects will delete all activity associated with them. Are you sure?');

                $objectModel = $this->getModel('OpenGraphObject', 'JFBConnectAdminModel');
                $objects = $objectModel->getObjects();
                $this->assignRef('objects', $objects);
                break;

            // Modal popups for selecting the action/object to create
            case 'actioncreate':
            case 'objectcreate':
                JPluginHelper::importPlugin('opengraph');
                $plugins = $app->triggerEvent('onOpenGraphGetPlugins');
                $this->assignRef('plugins', $plugins);
                break;
            case 'objectedit':
                $title .= " - Edit Object";
                JToolBarHelper::apply('apply', 'Save');
                JToolBarHelper::save('save', 'Save & Close');
                JToolBarHelper::cancel('cancel', 'Cancel');

                $model = $this->getModel('OpenGraphObject', 'JFBConnectAdminModel');
                $id = JRequest::getInt('id', 0);
                if ($id != 0)
                    $object = $model->getObject($id);
                else
                {
                    $plugin = JRequest::getString('plugin');
                    $name = JRequest::getString('name');

                    $object = new ogObject();
                    $object->loadDefaultObject($plugin, $name);
                }

                $this->assignRef('object', $object);

                // Load the params for this specific object
                jimport('joomla.filesystem.file');
                JFormHelper::addFieldPath(JPATH_SITE . '/plugins/opengraph/' . $object->plugin . '/objects');
                $xml = JPATH_SITE . '/plugins/opengraph/' . $object->plugin . '/objects/' . $object->system_name . '.xml';
                if (JFile::exists($xml))
                {
                    $form = JForm::getInstance('opengraph.' . $object->plugin . '.' . $object->system_name, $xml);
                    $form->bind(array('params' => $object->params->toArray()));
                }
                else
                    $form = null;
                $this->assignRef('params', $form);
                break;

            case 'activitylist':
                $title .= " - Activity Log";
                $model = $this->getModel('OpenGraphActivity', 'JFBConnectAdminModel');
                $option = JRequest::getCmd('option');
                $view = JRequest::getCmd('view');
                $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
                $limitstart = $app->getUserStateFromRequest($option . $view . '.limitstart', 'limitstart', 0, 'int');
                $search = $app->getUserStateFromRequest($option . $view . 'search', 'search', '', 'string');
                $search = JString::strtolower($search);
                $filter_state = $app->getUserStateFromRequest($option . $view . 'filter_state', 'filter_state', -1, 'int');
                $filter_object = $app->getUserStateFromRequest($option . $view . 'filter_object', 'filter_object', -1, 'int');
                $filter_action = $app->getUserStateFromRequest($option . $view . 'filter_action', 'filter_action', -1, 'int');
                $filter_order = $app->getUserStateFromRequest($option . $view . 'filter_order', 'filter_order', 'id', 'cmd');
                $filter_order_Dir = $app->getUserStateFromRequest($option . $view . 'filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');

                $lists = array();
                if (!$filter_order) {
                    $filter_order = 'id';
                }
                $lists['order_Dir'] = $filter_order_Dir;
                $lists['order'] = $filter_order;
                $lists ['search'] = $search;

                $filter_state_options[] = JHTML::_('select.option', -1, JText::_('COM_JFBCONNECT_OPENGRAPH_SELECT_PUBLISHING_STATE'));
                $filter_state_options[] = JHTML::_('select.option', OG_ACTIVITY_PUBLISHED, JText::_('JPUBLISHED'));
                $filter_state_options[] = JHTML::_('select.option', OG_ACTIVITY_DELETED, JText::_('COM_JFBCONNECT_OPENGRAPH_DELETED'));
                $filter_state_options[] = JHTML::_('select.option', OG_ACTIVITY_ERROR, JText::_('COM_JFBCONNECT_OPENGRAPH_ERROR'));
                $lists['state'] = JHTML::_('select.genericlist', $filter_state_options, 'filter_state', 'onchange="this.form.submit()"', 'value', 'text', $filter_state);

                $objectOptions = $model->getObjectList();
                $filter_object_options[] = JHTML::_('select.option', -1, JText::_('COM_JFBCONNECT_OPENGRAPH_SELECT_OBJECT_TYPE'));
                foreach($objectOptions as $newOption)
                    $filter_object_options[] = JHTML::_('select.option', $newOption->id, $newOption->display_name);
                $lists['object'] = JHTML::_('select.genericlist', $filter_object_options, 'filter_object', 'onchange="this.form.submit()"', 'value', 'text', $filter_object);

                $actionOptions = $model->getActionList();
                $filter_action_options[] = JHTML::_('select.option', -1, JText::_('COM_JFBCONNECT_OPENGRAPH_SELECT_ACTION_TYPE'));
                foreach($actionOptions as $newOption)
                    $filter_action_options[] = JHTML::_('select.option', $newOption->id, $newOption->display_name);
                $lists['action'] = JHTML::_('select.genericlist', $filter_action_options, 'filter_action', 'onchange="this.form.submit()"', 'value', 'text', $filter_action);

                $this->assignRef('lists', $lists);

                JToolBarHelper::deleteList();

                $rows = $model->getRows();
                $this->assignRef('rows', $rows);

                $total = $model->getTotal();
                jimport('joomla.html.pagination');
                $pageNav = new JPagination ($total, $limitstart, $limit);
                $this->assignRef('page', $pageNav);

                $objectModel = $this->getModel('OpenGraphObject', 'JFBConnectAdminModel');
                $this->assignRef('objectModel', $objectModel);
                $actionModel = $this->getModel('OpenGraphAction', 'JFBConnectAdminModel');
                $this->assignRef('actionModel', $actionModel);

                break;
            case 'settings':
                $title .= " - Settings";
                JToolBarHelper::apply('apply', 'Save');
                JToolBarHelper::save('save', 'Save & Close');
                JToolBarHelper::cancel('cancel', 'Cancel');
                $model = $this->getModel('config');
                $this->assignRef('model', $model);

                break;
            default:
                require_once(JPATH_COMPONENT_ADMINISTRATOR . '/assets/sourcecoast.php');
                $versionChecker = new sourceCoastConnect('jfbconnect_j16', 'components/com_jfbconnect/assets/images/');
                $this->assignRef('versionChecker', $versionChecker);
                break;
        }
        JToolBarHelper::title($title, 'jfbconnect.png');

        SCAdminHelper::addAutotuneToolbarItem();

        parent::display($tpl);
    }
}
