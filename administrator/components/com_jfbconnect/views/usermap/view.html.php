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

class JFBConnectViewUsermap extends JViewLegacy
{
    function display($tpl = null)
    {
        $userMapModel = $this->getModel('usermap');
        $pagination = &$userMapModel->getPagination();
        $lists = $this->get('ViewLists', 'usermap');

        $this->assignRef('pagination', $pagination);
        $this->assignRef('lists', $lists);

        $this->addToolbar();

        parent::display($tpl);
    }

    function addToolbar()
    {
        JToolBarHelper::title('JFBConnect', 'jfbconnect.png');
        SCAdminHelper::addAutotuneToolbarItem();
    }

    function selectRequest()
    {
        $requestModel = $this->getModel("request");
        $requests = $requestModel->getPublishedRequests();
        if (count($requests) == 0)
        {
            $app =JFactory::getApplication();
            $app->redirect('index.php?option=com_jfbconnect&controller=request', "No Requests are Published. Please create or publish the Request you wish to send.", 'error');
        }
        $requestList = JHTML::_('select.genericlist', $requests, 'id', null, 'id', 'title');
        $this->assignRef('requestList', $requestList);

        $userList = JRequest::getVar('cid', 0, '', 'array');
        $usermapModel = $this->getModel('usermap');
        $fbIds = $usermapModel->getFbIdsFromList($userList);
        $this->assignRef('fbIds', $fbIds);
        parent::display(null);
    }

}
