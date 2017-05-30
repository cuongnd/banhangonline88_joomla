<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

require_once(JPATH_SITE . '/components/com_jfbconnect/models/opengraphactivity.php');

class JFBConnectAdminModelOpenGraphActivity extends JFBConnectModelOpenGraphActivity
{
    function getRows()
    {
        $app =JFactory::getApplication();
        $option = JRequest::getCmd('option');
        $view = JRequest::getCmd('view');

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limitstart = $app->getUserStateFromRequest($option . $view . '.limitstart', 'limitstart', 0, 'int');
        $filter_order = $app->getUserStateFromRequest($option . $view . 'filter_order', 'filter_order', 'id', 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($option . $view . 'filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');

        $query = $this->_db->getQuery(true);
        $query->select('*')
            ->from($this->_db->qn('#__opengraph_activity'))
            ->order($this->_db->qn($filter_order). ' ' . $filter_order_Dir);
        $this->setFilters($query);
        $this->_db->setQuery($query, $limitstart, $limit);
        $rows = $this->_db->loadObjectList();
        return $rows;
    }

    function setFilters($query)
    {
        $app =JFactory::getApplication();
        $option = JRequest::getCmd('option');
        $view = JRequest::getCmd('view');

        $search = $app->getUserStateFromRequest($option . $view . 'search', 'search', '', 'string');
        $search = JString::strtolower($search);

        $filter_state = $app->getUserStateFromRequest($option . $view . 'filter_state', 'filter_state', -1, 'int');
        $filter_object = $app->getUserStateFromRequest($option . $view . 'filter_object', 'filter_object', -1, 'int');
        $filter_action = $app->getUserStateFromRequest($option . $view . 'filter_action', 'filter_action', -1, 'int');

        if ($search != '')
            $query->where($this->_db->qn('url') . "LIKE '%" . $search . "%'");

        if ($filter_state > -1)
            $query->where($this->_db->qn('status') . '=' . $filter_state);

        if ($filter_object > -1)
            $query->where($this->_db->qn('object_id') . '=' . $filter_object);

        if($filter_action > -1)
            $query->where($this->_db->qn('action_id') . '=' . $filter_action);

        return $query;
    }

    function getTotal()
    {
        $query = $this->_db->getQuery(true);
        $query->select('COUNT(*)')
            ->from($this->_db->qn('#__opengraph_activity'));
        $this->setFilters($query);
        $this->_db->setQuery($query);
        $total = $this->_db->loadResult();
        return $total;
    }

    function delete($id)
    {
        $query = $this->_db->getQuery(true);
        $query->delete($this->_db->qn("#__opengraph_activity"))
            ->where($this->_db->qn("status") . '<>' . OG_ACTIVITY_PUBLISHED)
            ->where($this->_db->qn("id") . '=' . $this->_db->q($id));
        $this->_db->setQuery($query);
        $this->_db->execute();
    }

    function getObjectList()
    {
        $query = $this->_db->getQuery(true);
        $query->select($this->_db->qn('id') .','. $this->_db->qn('display_name'))
            ->from($this->_db->qn('#__opengraph_object'));
        $this->_db->setQuery($query);
        return $this->_db->loadObjectList();
    }

    function getActionList()
    {
        $query = $this->_db->getQuery(true);
        $query->select($this->_db->qn('id') .','. $this->_db->qn('display_name'))
            ->from($this->_db->qn('#__opengraph_action'));
        $this->_db->setQuery($query);
        return $this->_db->loadObjectList();
    }
}