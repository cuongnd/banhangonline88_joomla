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

class JFBConnectModelUserMap extends JModelLegacy
{
    var $_pagination = null;
    static $instances;
    var $_id = null;
    var $_data = null;

    function __construct()
    {
        parent::__construct();
        if (JFactory::getApplication()->isAdmin())
        {
            $array = JRequest::getVar('cid', 0, '', 'array');
            if (is_array($array) && isset($array[0]))
            {
                $id = (int)$array[0];
                $this->setId($id);
            }
        }

        if (self::$instances === null)
            self::$instances = array();

    }

    static public function getUser($jUserId, $provider = 'facebook')
    {
        if (self::$instances == null)
            self::$instances = array();

        if (!array_key_exists($jUserId, self::$instances))
        {
            self::$instances[$jUserId] = array();
        }

        if (!array_key_exists($provider, self::$instances[$jUserId]))
        {
            $instance = new JFBConnectModelUserMap();
            $instance->getData($jUserId, $provider);
            self::$instances[$jUserId][$provider] = $instance;
        }

        return self::$instances[$jUserId][$provider];
    }

    function setId($id)
    {
        $this->_id = $id;
        $this->_data = null;
    }

    function &getData($j_user_id = null, $provider = 'facebook')
    {
        if (empty($this->_data) && ($j_user_id || $this->_id))
        {
            $query = $this->_db->getQuery(true);
            $query->select('um.*')
                    ->from($this->_db->qn('#__jfbconnect_user_map') . ' um');

            if (!$j_user_id)
                $query->where('id = ' . $this->_db->q($this->_id));
            else
            {
                $query->where('j_user_id = ' . $this->_db->q($j_user_id));
                $query->where('provider = ' . $this->_db->q($provider));
            }

            $this->_db->setQuery($query);
            $this->_data = $this->_db->loadObject();
        }
        if ($this->_data) // Setup the params variable with the data loaded above
        {
            if (!is_object($this->_data->params) || get_class($this->_data->params) != 'JRegistry' || get_class($this->_data->params) == 'Joomla\\Registry\\Registry')
            {
                $this->_data->params = new JRegistry($this->_data->params);
            }
        }
        else
        {
            $this->_data = new stdClass();
            $this->_data->id = 0;
            $this->_data->created_at = JFactory::getDate()->toSql();
            $this->_data->updated_at = JFactory::getDate()->toSql();
            $this->_data->j_user_id = $j_user_id;
            $this->_data->provider = null;
            $this->_data->provider_user_id = null;
            $this->_data->sent = null;
            $this->_data->access_token = null;
            $this->_data->params = new JRegistry();
            $this->_data->authorized = 1;
            $this->_data->received = null;
        }

        return $this->_data;
    }

    function &getPagination()
    {
        if ($this->_pagination == null)
        {
            $this->getList();
        }
        return $this->_pagination;
    }

    function &getViewLists()
    {
        $app = JFactory::getApplication();

        //Search
        $search = $app->getUserStateFromRequest('com_jfbconnect.usermap.search', 'search', '', 'string');
        $search = JString::strtolower($search);
        $lists['search'] = $search;

        //Filter
        $filter_network = $app->getUserStateFromRequest('com_jfbconnect.usermap.provider', 'provider', -1, 'string');
        $filter_network_options[] = JHTML::_('select.option', -1, JText::_('COM_JFBCONNECT_USERMAP_SELECT_NETWORK'));
        $providers = JFBCFactory::getAllProviders();
        foreach ($providers as $provider)
            $filter_network_options[] = JHTML::_('select.option', $provider->systemName, $provider->name);
        $lists['provider'] = JHTML::_('select.genericlist', $filter_network_options, 'provider', 'onchange="this.form.submit()"', 'value', 'text', $filter_network);

        //Order
        $filter_order = $app->getUserStateFromRequest('com_jfbconnect.usermap.filter_order', 'filter_order', 'id', 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest('com_jfbconnect.usermap.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');

        if (!$filter_order)
        {
            $filter_order = 'id';
        }
        $lists['order_Dir'] = $filter_order_Dir;
        $lists['order'] = $filter_order;

        return $lists;
    }

    function getList()
    {
        // Lets load the data if it doesn't already exist
        if (empty($this->_listData))
        {
            $app = JFactory::getApplication();

            $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
            $limitstart = $app->getUserStateFromRequest('com_jfbconnect.usermap.limitstart', 'limitstart', 0, 'int');
            $filter_order = $app->getUserStateFromRequest('com_jfbconnect.usermap.filter_order', 'filter_order', 'id', 'cmd');
            $filter_order_Dir = $app->getUserStateFromRequest('com_jfbconnect.usermap.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');

            if (!$filter_order)
            {
                $filter_order = 'id';
            }

            //Search values
            $search = $app->getUserStateFromRequest("com_jfbconnect.usermap.search", 'search', '', 'string');
            $search = JString::strtolower($search);

            //Filter
            $filter_network = $app->getUserStateFromRequest('com_jfbconnect.usermap.provider', 'provider', -1, 'string');

            // Get our row count for pagination
            $query = $this->_db->getQuery(true);
            $query->select('COUNT(*) count')
                    ->from($this->_db->qn('#__jfbconnect_user_map') . ' um');

            if ($search != '') //Set up where clause using search
            {
                $query->innerJoin("#__users ju ON ju.id=um.j_user_id");
                $query->where("(um.j_user_id LIKE '%" . $search . "%'" .
                " OR um.provider_user_id LIKE '%" . $search . "%'" .
                " OR ju.name LIKE '%" . $search . "%'" .
                " OR ju.email LIKE '%" . $search . "%'" .
                " OR ju.username LIKE '%" . $search . "%')");
            }
            if ($filter_network != -1)
                $query->where("um.provider = " . $this->_db->q($filter_network));

            $this->_db->setQuery($query);
            $total = $this->_db->loadResult();

            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($total, $limitstart, $limit);

            // Get our rows
            $query->clear('select');
            $query->select('um.*');

            $cast = $this->_db->name == "postgresql" ? "BIGINT" : "UNSIGNED";
            $query->select('(SELECT COUNT(*) FROM #__jfbconnect_notification WHERE CAST(fb_user_from AS ' . $cast . ') = CAST(um.provider_user_id AS ' . $cast . ') ) sent')
                    ->select('(SELECT COUNT(*) FROM #__jfbconnect_notification WHERE CAST(fb_user_to AS ' . $cast . ') = CAST(um.provider_user_id AS ' . $cast . ') ) received');

            $query->order($filter_order . " " . $filter_order_Dir);
            $this->_listData = $this->_getList($query, $limitstart, $limit);
        }

        return $this->_listData;
    }

    function update($provider_user_id, $provider = 'facebook')
    {
        if (($this->_data->provider_user_id != $provider_user_id) && ($this->_data->provider != $provider))
        {
            $row = $this->getTable();
            $row->id = $this->_data->id;
            $row->created_at = $this->_data->created_at;
            $row->updated_at = JFactory::getDate()->toSql();
            $row->j_user_id = $this->_data->j_user_id;
            $row->provider_user_id = $provider_user_id;
            $row->provider = $provider;
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
        }
        return true;
    }

    function saveParameter($name, $value)
    {
        $params = $this->_data->params;
        $params->set($name, $value);

        $this->updateParams($params);
    }

    public function addScope($scope, $requester, $identifier)
    {
        if ($this->_data->j_user_id && $this->_data->provider && $this->_data->provider_user_id)
        {
            $params = $this->_data->params;
            $params->set('required_scope.' . $scope . '.' . $requester . '.' . $identifier, $identifier);

            $this->updateParams($params);
        }
    }

    public function removeScope($scope, $requester, $identifier)
    {
        if ($this->_data->j_user_id)
        {
            $params = $this->_data->params;
            $newParams = $params->toObject();
            if (isset($newParams->required_scope) &&
                    isset($newParams->required_scope->$scope) &&
                    isset($newParams->required_scope->$scope->$requester) &&
                    isset($newParams->required_scope->$scope->$requester->$identifier)
            )
            {
                unset($newParams->required_scope->$scope->$requester->$identifier);
                if (count((array)$newParams->required_scope->$scope->$requester) == 0)
                {
                    unset($newParams->required_scope->$scope->$requester);
                    if (count((array)$newParams->required_scope->$scope) == 0)
                        unset($newParams->required_scope->$scope);
                }

                $newParams = count((array)$newParams);
                $params->loadObject($newParams);
                $this->updateParams($params);
            }
        }
    }

    public function removeAllScope($requester, $identifier)
    {
        if ($this->_data->j_user_id)
        {
            $requiredScope = $this->_data->params->get('required_scope', null);
            if ($requiredScope)
            {
                foreach ($requiredScope as $key => $val)
                {
                    $this->removeScope($key, $requester, $identifier);
                }
            }
        }
    }

    private function updateParams(JRegistry $params)
    {
        $row = $this->getTable();
        $row->load(array('j_user_id' => $this->_data->j_user_id, 'provider' => $this->_data->provider));
        $row->params = $params->toString();

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
        $this->_data->params = $params;
    }

    function delete()
    {
        $cids = JRequest::getVar('cid', array(0), 'post', 'array');
        $row = $this->getTable();
        if (count($cids))
        {
            foreach ($cids as $cid)
            {
                if (!$row->delete($cid))
                {
                    $this->setError($row->getErrorMsg());
                    return false;
                }
            }
        }
        return true;
    }

    function deleteMapping($provider_user_id, $provider = 'facebook')
    {
        $query = $this->_db->getQuery(true);
        $query->delete($this->_db->qn('#__jfbconnect_user_map'))
                ->where($this->_db->qn('provider_user_id') . '=' . $this->_db->q($provider_user_id))
                ->where($this->_db->qn('provider') . '=' . $this->_db->q($provider));
        $this->_db->setQuery($query);
        $this->_db->execute();
    }

    // Default provider to Facebook for backward compatibility in v5.1
    function getJoomlaUserId($uid, $provider = 'facebook')
    {
        $query = $this->_db->getQuery(true);
        $query->select('j_user_id')
                ->from('#__jfbconnect_user_map')
                ->where($this->_db->qn('provider') . '=' . $this->_db->q($provider))
                ->where($this->_db->qn('provider_user_id') . '=' . $this->_db->q($uid));

        $this->_db->setQuery($query);
        $joomlaId = $this->_db->loadResult();
        return $joomlaId;
    }

    function getProviderUserId($jUserId, $provider)
    {
        $query = $this->_db->getQuery(true);
        $query->select('provider_user_id')
                ->from('#__jfbconnect_user_map')
                ->where($this->_db->qn('j_user_id') . '=' . $this->_db->q($jUserId))
                ->where($this->_db->qn('provider') . '=' . $this->_db->q($provider));
        $this->_db->setQuery($query);
        $providerId = $this->_db->loadResult();
        return $providerId;
    }

    // @deprecated in v5.1
    function getFacebookUserId($joomlaId)
    {
        JLog::add('JFBConnectUserMap getFacebookUserId is deprecated. Use getProviderUserId($jUserid, "facebook") instead', JLog::WARNING, 'deprecated');
        return $this->getProviderUserId($joomlaId, 'facebook');
    }

    function map($jUserId, $providerUserId, $provider, $token = null)
    {
        if ($jUserId && $providerUserId)
        {
            // Check for a previous mapping first. This could be a previous account the user had connected to their Joomla account.
            // Also, LinkedIn returns a different member ID for each API key. If the admin switches API keys, 'old' member IDs may
            // be mapped and need to be deleted here. This is a really bad scenario though as admins shouldn't be changing their API key.
            $oldProviderId = $this->getProviderUserId($jUserId, $provider);
            if ($oldProviderId && ($oldProviderId != $providerUserId))
                $this->deleteMapping($oldProviderId, $provider);

            JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/tables/');
            $row = $this->getTable();
            $row->load(array('provider_user_id' => $providerUserId, 'provider' => $provider));
            if (!$row->id)
                $row->created_at = JFactory::getDate()->toSql();
            if ($token)
                $row->access_token = json_encode($token);
            $row->updated_at = JFactory::getDate()->toSql();
            $row->j_user_id = $jUserId;
            $row->provider_user_id = $providerUserId;
            $row->authorized = true;
            $row->provider = $provider;
            if (!$row->check() || !$row->store())
            {
                $this->setError($this->_db->getErrorMsg());
                return false;
            }

            // Now, trigger the point rewards
            $point = new JFBConnectPoint();
            $point->set('name', 'account.map');
            $point->set('key', $provider);
            $point->set('userid', $jUserId);
            $point->award();

            return true;

        }
        else
            return false;
    }

    // @deprecated in v5.1
    function mapUser($fbUid, $jUserId = null)
    {
        if ($jUserId)
            $user = JUser::getInstance($jUserId);
        else
            $user = JFactory::getUser();
        JLog::add('JFBConnectUserMapModel mapUser is deprecated. Use map($jUserId, $providerUserId, $provider) instead', JLog::WARNING, 'deprecated');

        return $this->map($user->get('id'), $fbUid, 'facebook');

    }

    function getTotalMappings($provider = null, $includeBlocks = true)
    {
        $query = $this->_db->getQuery(true);
        $query->select("count(*)")
                ->from($this->_db->qn('#__jfbconnect_user_map') . " jfbc");
        if ($provider)
            $query->where($this->_db->qn('provider') . " = " . $this->_db->q($provider));
        if (!$includeBlocks)
        {
            $query->innerJoin($this->_db->qn("#__users") . ' ju ON ju.id=jfbc.j_user_id');
            $query->where($this->_db->qn('ju.block') . " = 0");
            $query->where($this->_db->qn('jfbc.authorized') . " = 1");
        }
        $this->_db->setQuery($query);
        return $this->_db->loadResult();
    }

    // Simple array of all active FB Ids. Used for sending requests to all users
    function getActiveUserFbIds($start = null, $length = null)
    {
        $query = $this->_db->getQuery(true);
        $query->select($this->_db->qn('provider_user_id'))
                ->from('#__jfbconnect_user_map jfbc')
                ->innerJoin('#__users ju ON ju.id=jfbc.j_user_id')
                ->where('ju.block = 0 AND jfbc.authorized = 1')
                ->where($this->_db->qn('jfbc.provider') . " = " . $this->_db->q('facebook'));
        $this->_db->setQuery($query, $start, $length);
        return $this->_db->loadColumn();
    }

    function getJoomlaUserIdFromEmail($email)
    {
        $query = $this->_db->getQuery(true);
        $query->select($this->_db->qn('id'))
                ->from('#__users')
                ->where($this->_db->qn('email') . '=' . $this->_db->q($email));
        $this->_db->setQuery($query);
        return $this->_db->loadResult();
    }

    function getFbIdsFromList($array)
    {
        $query = $this->_db->getQuery(true);
        $query->select($this->_db->qn('provider_user_id'))
                ->from('#__jfbconnect_user_map')
                ->where("id IN (" . implode(', ', $array) . ")");
        $this->_db->setQuery($query);
        return $this->_db->loadColumn();
    }

    function updateUserToken($jUserId, $provider, $token)
    {
        $token = json_encode($token);

        $query = $this->_db->getQuery(true);
        $query->update($this->_db->qn('#__jfbconnect_user_map'))
                ->set($this->_db->qn("authorized") . "=1")
                ->set($this->_db->qn("access_token") . "=" . $this->_db->q($token))
                ->set($this->_db->qn("updated_at") . "=" . $this->_db->q(JFactory::getDate()->toSql()))
                ->where($this->_db->qn("j_user_id") . "=" . $this->_db->q($jUserId))
                ->where($this->_db->qn("provider") . "=" . $this->_db->q($provider));
        $this->_db->setQuery($query);
        $this->_db->execute();
    }

    // Used for the callback from Facebook if the user has de-authorized the application
    function setAuthorized($fbUserId, $authorize)
    {
        $query = $this->_db->getQuery(true);
        $query->update('#__jfbconnect_user_map')
                ->set("authorized = " . $this->_db->q($authorize))
                ->set("updated_at = " . $this->_db->q(JFactory::getDate()->toSql()))
                ->where("provider_user_id = " . $this->_db->q($fbUserId))
                ->where("provider = " . $this->_db->q('facebook'));
        $this->_db->setQuery($query);
        $this->_db->execute();
    }

    function getUserAccessToken($jUserId, $provider)
    {
        $query = $this->_db->getQuery(true);
        $query->select($this->_db->qn('access_token'))
                ->from('#__jfbconnect_user_map')
                ->where($this->_db->qn('provider') . ' = ' . $this->_db->q($provider))
                ->where($this->_db->qn('j_user_id') . ' = ' . $this->_db->q($jUserId));
        $this->_db->setQuery($query);
        $token = $this->_db->loadResult();
        if ($token)
            $token = json_decode($token);
        return $token;
    }
}