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

class JFBConnectModelOpenGraphAction extends JModelLegacy
{
    var $instances;

    public function storeActivity($action, $object, $objectKey, $url, $status, $response)
    {
        $status = $status ? '1' : '0';
        $user = JFactory::getUser();
        $userId = $user->get('id');
        $query = "INSERT INTO #__opengraph_activity (`action_id`, `object_id`, `user_id`, `url`, `status`, `unique_key`, `response`, `created`, `modified`)
            VALUES (" . $action->id . ", " . $object->id . ", " . $userId . ", " . $this->_db->quote($url) . ", " . $status . ", " . $this->_db->quote($objectKey) . ", " . $this->_db->quote($response) . ", NOW(), NOW() )";
        $this->_db->setQuery($query);
        $this->_db->execute();

        // get the id of the last insert. Don't really like this approach. Needed for ability to delete just-posted request
        $query = "SELECT id FROM #__opengraph_activity WHERE `user_id` = " . $userId . " AND `action_id` = " . $action->id . " AND `object_id` = " . $object->id . " AND `status` = " . $status .
                " ORDER BY id DESC LIMIT 1";
        $this->_db->setQuery($query);
        return $this->_db->loadResult();
    }

    // Load the instance array with the row of data from the database.
    // If row has already been loaded, just return it (no db roundtrip)
    // If data is passed in (from another call), trust that data instead of doing another db query
    public function getAction($id, $data = null)
    {
        if (!isset($this->instances[$id]))
        {
            if (!$data)
            {
                $this->_db->setQuery("SELECT * FROM #__opengraph_action WHERE id = " . $this->_db->quote($id));
                $data = $this->_db->loadObject();
            }

            $action = new ogAction();
            $action->setProperties($data);
            $this->instances[$id] = $action;

        }
        return $this->instances[$id];
    }

    public function getActionsOfType($plugin, $name)
    {
        $this->_db->setQuery("SELECT * FROM #__opengraph_action WHERE
            plugin = " . $this->_db->quote($plugin) .
        " AND system_name = " . $this->_db->quote($name) .
        " AND published = 1");
        $rows = $this->_db->loadObjectList();
        $actions = array();
        if ($rows)
        {
            foreach ($rows as $row)
                $actions[] = $this->getAction($row->id, $row);
        }
        return $actions;
    }

    public function getActions($publishedOnly = false)
    {
        $query = $this->_db->getQuery(true);
        $query->select('*')
                ->from($this->_db->qn('#__opengraph_action'));
        if ($publishedOnly)
            $query->where($this->_db->qn('published') . '=' . $this->_db->q(1));
        $this->_db->setQuery($query);
        $rows = $this->_db->loadObjectList();
        $actions = array();
        if ($rows)
        {
            foreach ($rows as $row)
                $actions[] = $this->getAction($row->id, $row);
        }
        return $actions;
    }

    /*
     * triggerAction - Call Facebook API to add an Open Graph Action for the user
     * $action = action object already setup
     * $url = Full URL of the object that the action is being performed on
     * $params = Array of any extra parameters for the action (message, friends, places, etc)
     * $uniqueKey = JFBConnect assigned 'key' for any URL. Used for ensuring that the action isn't performed too often. Will be generated if not specified.
     */
    function triggerAction($action, $url, $params = null, $uniqueKey = null)
    {
        if (!$params)
            $params = array();

        $return = new stdClass();
        $return->status = false;
        $return->message = "";

        SCStringUtilities::loadLanguage('com_jfbconnect');

        if (JFBCFactory::provider('facebook')->userIsConnected() && $action->enabledForUser())
        {
            // Find the object definition for the passed in URL
            $app = JFactory::getApplication();
            JPluginHelper::importPlugin('opengraph');
            $pluginArgs = array($url);
            $objects = $app->triggerEvent('onOpenGraphFindObjectType', $pluginArgs);
            // Remove all null elements and get the first real object returned
            $objects = array_filter($objects);
            $object = array_shift($objects);
            if ($object && $action->isAssociatedTo($object))
            {
                if (!$uniqueKey)
                    $uniqueKey = $this->getUniqueKey($url);
                if ($action->actionReady($uniqueKey))
                {
                    // Always include the 'object' argument as some actions want the generic, others want the specific.
                    if (array_key_exists('explicitly_shared', $params))
                    {
                        if ($params['explicitly_shared'] == 'true')
                            $params['fb:explicitly_shared'] = 'true';
                        unset($params['explicitly_shared']);
                    }

                    $args = $params;
                    $args['object'] = $url;
                    $args[strtolower($object->type)] = $url;
                    $actionPath = $action->getActionPath();
                    $response = JFBCFactory::provider('facebook')->api('/me/' . $actionPath, $args);
                    //$response['id'] = '12345';

                    $error = JFBCFactory::provider('facebook')->getLastError();
                    //$error = "";
                    if ($error)
                    {
                        $return->status = false; // Error / not posted
                        $return->response = $error;
                    }
                    else
                    {
                        $return->status = true; // Posted, response is the action ID
                        $return->response = $response['id'];
                    }
                    $activityId = $this->storeActivity($action, $object, $uniqueKey, $url, $return->status, $return->response);

                    // Setup the return message, used for AJAX calls
                    if ($error)
                        $return->message = $error;
                    else
                    {
                        $return->message = '<span class="ogMessage">' . JText::_("COM_JFBCONNECT_OPENGRAPH_ACTION_ADDED") . '</span>' .
                                '<span class="ogOptions">' .
                                '<a href="' . JRoute::_('index.php?option=com_jfbconnect&task=opengraph.undoAndDisableAction&action=' . $action->id . '&activity=' . $activityId . '&' . JSession::getFormToken() . '=1') . '" target="_blank">' . JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_DELETE_AND_DISABLE') . '</a>' .
                                ' | <a href="' . JRoute::_('index.php?option=com_jfbconnect&view=opengraph') . '" target="_blank">' . JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_SEE_ALL') . '</a>' .
                                '</span>';
                    }
                }
                else
                    $return->message = JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_NOT_READY');
            }
            else
                $return->message = JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_NOT_ASSOCIATED');
        }
        else
            $return->message = JText::_('COM_JFBCONNECT_OPENGRAPH_ACTION_NOT_ENABLED');

        return $return;
    }

    // getUniqueKey
    // Used for generating a unique key per URL or 'item'. Used to ensure that certain actions aren't repeated within the
    // set action intervale. For example, reading an article should only be triggered once (or at most every day).
    // By default, will decompose the URL to non-SEF and look for an id query string param.
    // If none exists, will use the MD5 hash of the full path/query string of URL.
    public function getUniqueKey($url)
    {
        $queryVars = $this->getUrlVars($url);

        if (array_key_exists('id', $queryVars))
        {
            $filter = JFilterInput::getInstance();
            $key = $filter->clean($queryVars['id'], 'INT');
        }
        else
        {
            $juri = JURI::getInstance($url);
            $key = md5($juri->toString(array('path', 'query')));
        }

        return $key;
    }

    // Get the URL query variables for the passed in URL.
    // The caller *MUST* filter any variables it uses from the return
    public function getUrlVars($url)
    {
        $router = JRouter::getInstance('site');
        $origVars = $router->getVars();
        $router->setVars(array(), false);
        // DO NOT use JURI::getInstance! Re-routing on the same instance causes big issues
        $juri = new JURI($url);
        // Odd hack to prevent the parsing of the URL to redirect to the https version in certain circumstances
        $jConfig = JFactory::getConfig();
        $forceSSL = $jConfig->get('force_ssl');
        $jConfig->set('force_ssl', 0);

        $queryVars = $router->parse($juri);

        $jConfig->set('force_ssl', $forceSSL);

        // Reset the router back to it's original state
        $router->setVars($origVars);
        return $queryVars;
    }

}

// Should make these objects derive from the same class to share some of the common functionality.
// getNamespace, setProperties, etc.
class ogAction extends JObject
{
    var $id;
    var $plugin;
    var $system_name;
    var $display_name;
    var $action;
    var $uniqueKey;
    var $fb_built_in;
    var $params;
    var $published;
    var $created;
    var $modified;
    var $db;

    private $associatedObjects = null;

    public function __construct()
    {
        $this->db = JFactory::getDbo();
    }

    public function setProperties($data)
    {
        parent::setProperties($data);

        $this->params = new JRegistry($this->params);
    }

    function loadDefaultAction($plugin, $name)
    {
        $this->id = 0;
        $this->plugin = $plugin;
        $this->system_name = $name;
        $this->display_name = $name;
        $this->action = "";
        $this->fb_built_in = false;
        $this->can_disable = 0;
        $this->params = new JRegistry();
        $this->published = 0;
        $this->created = null;
        $this->modified = null;

        $this->params->set('og_unique_action', 1);
        $this->params->set('og_interval_duration', 1);
        $this->params->set('og_interval_type', "DAY");
        $this->params->set('og_auto_timer', 15);
        $this->params->set('og_auto_type', "none");
        $this->params->set('og_user_disable', '1');
    }

    function getAssociatedObjects()
    {
        if (!$this->associatedObjects)
        {
            $this->associatedObjects = array();
            $this->db->setQuery('SELECT o.* FROM #__opengraph_action_object ao ' .
            ' INNER JOIN #__opengraph_object o ON ao.object_id = o.id ' .
            ' WHERE ao.action_id = ' . $this->id .
            ' AND o.published = 1');

            $objects = $this->db->loadObjectList();

            if (count($objects) > 0)
            {
                $objectModel = JModelLegacy::getInstance('opengraphobject', 'JFBConnectModel');
                foreach ($objects as $object)
                    $this->associatedObjects[] = $objectModel->getObject($object->id, $object);
            }
        }
        return $this->associatedObjects;
    }

    function isAssociatedTo($object)
    {
        $objects = $this->getAssociatedObjects();
        foreach ($objects as $testObj)
        {
            if ($object->id == $testObj->id)
                return true;
        }
        return false;
    }

    // Check to see if the action is published *and* hasn't been triggered for the user over the set interval
    public function actionReady($uniqueKey)
    {
        if (!$this->published || $this->getNamespace() == '')
            return false;

        $user = JFactory::getUser();
        $userId = $user->get('id');

        $duration = "INTERVAL " . $this->params->get('og_interval_duration') . " " . $this->params->get('og_interval_type');
        $query = "SELECT COUNT(*) FROM #__opengraph_activity WHERE
            `action_id` = " . $this->id . " AND
            `user_id` = " . $userId . " AND
            `unique_key` = " . $this->db->quote($uniqueKey) . " AND
            `status` = 1 AND
                (`created` > (NOW() - " . $duration . ") OR " . $this->params->get('og_unique_action') . ")";
        $this->db->setQuery($query);
        $count = $this->db->loadResult();
        return $count == 0 ? true : false;
    }

    public function getLastPublished($uniqueKey)
    {
        if (!$this->published || $this->getNamespace() == '')
            return false;

        $user = JFactory::getUser();
        $userId = $user->get('id');

        $query = "SELECT id, created FROM #__opengraph_activity WHERE
            `action_id` = " . $this->id . " AND
            `user_id` = " . $userId . " AND
            `unique_key` = " . $this->db->quote($uniqueKey) . " AND
            `status` = 1
            ORDER BY created DESC LIMIT 1";
        $this->db->setQuery($query);
        $date = $this->db->loadObject();
        return $date;
    }

    public function enabledForUser()
    {
        if ($this->can_disable)
        {
            $user = JFactory::getUser();
            $userModel = JFBConnectModelUserMap::getUser($user->get('id'), 'facebook');
            $userData = $userModel->getData();
            $actionsDisabled = $userData->params->get('og_actions_disabled');
            $actId = $this->id;
            if (is_object($actionsDisabled) && property_exists($actionsDisabled, $actId) && ($actionsDisabled->$actId == 1))
                return false;
        }
        return true;
    }

    public function getActionPath()
    {
        if ($this->fb_built_in == 0)
        {
            $namespace = $this->getNamespace();
            $actionPath = $namespace . ":" . $this->action;
        }
        else
            $actionPath = $this->action;
        return $actionPath;
    }

    private function getNamespace()
    {
        $appConfig = JFBCFactory::config()->getSetting('autotune_app_config');
        $namespace = $appConfig['namespace'];
        return $namespace;
    }
}
