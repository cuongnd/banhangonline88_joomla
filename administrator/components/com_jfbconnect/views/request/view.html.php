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

class JFBConnectViewRequest extends JViewLegacy
{
    function display($tpl = null)
    {
        $app = JFactory::getApplication();
        $option = JRequest::getCmd('option');
        $view = JRequest::getCmd('view');

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limitstart = $app->getUserStateFromRequest($option . $view . '.limitstart', 'limitstart', 0, 'int');
        $filter_order = $app->getUserStateFromRequest($option . $view . 'filter_order', 'filter_order', 'id', 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($option . $view . 'filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');
        $filter_published = $app->getUserStateFromRequest($option . $view . 'filter_published', 'filter_published', -1, 'int');
        $search = $app->getUserStateFromRequest($option . $view . 'search', 'search', '', 'string');
        $search = JString::strtolower($search);

        $model = $this->getModel();
        $rows = $model->getRows();
        $this->assignRef('rows', $rows);

        $lists = array();
        $lists ['search'] = $search;

        if (!$filter_order)
        {
            $filter_order = 'id';
        }
        $lists['order_Dir'] = $filter_order_Dir;
        $lists['order'] = $filter_order;

        $filter_published_options[] = JHTML::_('select.option', -1, '-- Request State --');
        $filter_published_options[] = JHTML::_('select.option', 1, 'Published');
        $filter_published_options[] = JHTML::_('select.option', 0, 'Unpublished');
        $lists['published'] = JHTML::_('select.genericlist', $filter_published_options, 'filter_published', 'onchange="javascript:this.form.submit()"', 'value', 'text', $filter_published);

        $this->assignRef('lists', $lists);

        $dateFormat = "%m/%d/%Y";
        $this->assignRef('dateFormat', $dateFormat);
        $db = JFactory::getDBO();
        $nullDate = $db->getNullDate();
        $this->assignRef('nullDate', $nullDate);

        $total = $model->getTotal();
        jimport('joomla.html.pagination');

        $pageNav = new JPagination ($total, $limitstart, $limit);
        $this->assignRef('page', $pageNav);

        $canvasEnabled = $this->get('canvasEnabled');
        $this->assignRef('canvasEnabled', $canvasEnabled);

        //$ordering = (($this->lists['order'] == 'ordering' || $this->lists['order'] == 'category') && (!$this->filter_trash));
        //$this->assignRef('ordering', $ordering);

        $this->addToolbar();

        parent::display($tpl);
    }

    function addToolbar()
    {
        JToolBarHelper::title('JFBConnect', 'jfbconnect.png');
        SCAdminHelper::addAutotuneToolbarItem();
    }

    function edit()
    {
        $model = $this->getModel();
        $data = $model->getData();
        $this->assignRef('request', $data);

        $canvasEnabled = $this->get('canvasEnabled');
        $this->assignRef('canvasEnabled', $canvasEnabled);

        $total = 0;
        $pending = 0;
        $read = 0;
        $expired = 0;

        $model->getNotificationTotals($data->id, $total, $pending, $read, $expired);
        $this->assignRef('totalNotifications', $total);
        $this->assignRef('pendingNotifications', $pending);
        $this->assignRef('readNotifications', $read);
        $this->assignRef('expiredNotifications', $expired);

        $this->addToolbar();

        parent::display();
    }

    function previewSend()
    {
        JToolBarHelper::title('JFBConnect', 'jfbconnect.png');

        $model = $this->getModel();
        $requestId = JRequest::getVar('id', 'POST');
        $model->setId($requestId);
        $data = $model->getData();
        $this->assignRef('request', $data);

        $app = JFactory::getApplication();
        $app->setUserState('jfbconnect.request.requestId', $requestId);

        $fbIds = JRequest::getVar('fbIds', null, '', 'array');
        if (count($fbIds) > 0)
        {
            $totalUsers = count($fbIds);
            $sendToAll = false;
            $app->setUserState('jfbconnect.request.fbIds', $fbIds);
        }
        else // Send to ALL users
        {
            $usermapModel = $this->getModel('usermap');
            $totalUsers = $usermapModel->getTotalMappings('facebook', false);
            $this->assignRef('totalMappings', $totalUsers);
            $sendToAll = true;
        }
        $app->setUserState('jfbconnect.request.sendToAll', $sendToAll);

        $this->assignRef('totalUsers', $totalUsers);
        $this->assignRef('sendToAll', $sendToAll);
        $this->assignRef('fbIds', $fbIds);
        parent::display();
    }
}
