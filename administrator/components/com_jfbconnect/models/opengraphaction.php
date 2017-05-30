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
require_once(JPATH_SITE . '/components/com_jfbconnect/models/opengraphaction.php');

class JFBConnectAdminModelOpenGraphAction extends JFBConnectModelOpenGraphAction
{
    var $actionData;

    public function store($action = null)
    {
        $row = $this->getTable("JFBConnectOpenGraphAction", "Table");

        if (!$action)
        {
            $postData = JRequest::get('post');
            if ($postData['id'] != 0)
                $action = $this->getAction($postData['id']);
            else
                $action = new ogAction();
            $action->fb_built_in = '0'; // Using checkbox, so need to check if it's 1 each time.
            foreach (array_keys($postData) as $prop)
            {
                if (property_exists($action, $prop))
                {
                    if ($prop == "params")
                    {
                        $params = new JRegistry($postData['params']);
                        $action->params = $params->toString();
                    }
                    else
                        $action->$prop = $postData[$prop];
                }
            }
        }

        if ($action->id == 0 || $action->id == null)
            $action->created = JFactory::getDate()->toSql();

        $action->modified = JFactory::getDate()->toSql();

        if (!$row->bind($action))
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        if (!$row->check())
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        if (!$row->store())
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        $action->id = $row->id;
        // Store worked (should always..), lets update the action/object associations
        if (array_key_exists('objects', $postData))
        {
            $objects = $postData['objects'];
            $this->setObjectMappings($action->id, $objects);
        }
        return $row->id;
    }

    public function delete($id)
    {
        $query = $this->_db->getQuery(true);
        $query->delete($this->_db->qn("#__opengraph_action"))
            ->where($this->_db->qn("id") . '=' . $id);
        $this->_db->setQuery($query);
        $this->_db->execute();

        $query = $this->_db->getQuery(true);
        $query->delete($this->_db->qn("#__opengraph_action_object"))
            ->where($this->_db->qn("action_id") . '=' . $id);
        $this->_db->setQuery($query);
        $this->_db->execute();

        $query = $this->_db->getQuery(true);
        $query->delete($this->_db->qn("#__opengraph_activity"))
            ->where($this->_db->qn("action_id") . '=' . $id);
        $this->_db->setQuery($query);
        $this->_db->execute();
    }

    private function setObjectMappings($actionId, $objects)
    {
        $filter = JFilterInput::getInstance();
        // Delete all previous associations
        $query = $this->_db->getQuery(true);
        $query->delete($this->_db->qn("#__opengraph_action_object"))
            ->where($this->_db->qn("action_id") . '=' . $actionId);

        $this->_db->setQuery($query);
        $this->_db->execute();

        $columns = array('action_id', 'object_id');
        $query = $this->_db->getQuery(true);
        $query->insert($this->_db->qn("#__opengraph_action_object"))
            ->columns($this->_db->qn($columns));

        foreach ($objects as $objectId)
        {
            $objectId = $filter->clean($objectId, 'INT');
            if (is_int($objectId))
            {
                $query->clear('values');
                $query->values($actionId . ',' . $objectId);
                $this->_db->setQuery($query);
                $this->_db->query();
            }
        }
    }
}