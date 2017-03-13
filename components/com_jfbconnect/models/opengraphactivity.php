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

define('OG_ACTIVITY_PUBLISHED', 1);
define('OG_ACTIVITY_ERROR', 0);
define('OG_ACTIVITY_DELETED', 2);
class JFBConnectModelOpenGraphActivity extends JModelLegacy
{
    private $userId;
    public function __construct($configArray = array())
    {
        parent::__construct($configArray);
        $app = JFactory::getApplication();
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getActivityForUser()
    {
        $this->_db->setQuery("SELECT * FROM #__opengraph_activity WHERE user_id = " . $this->userId .
                    " AND status = " . OG_ACTIVITY_PUBLISHED .
                    " ORDER BY created DESC",
            $this->getState('limitstart'),
            $this->getState('limit')
        );
        return $this->_db->loadObjectList();
    }

    function getTotal()
    {
        $this->_db->setQuery("SELECT COUNT(*) FROM #__opengraph_activity WHERE user_id = " . $this->userId .
                    " AND status = " . OG_ACTIVITY_PUBLISHED);
        return $this->_db->loadResult();
    }

    public function getActivity($id)
    {
        $this->_db->setQuery("SELECT * FROM #__opengraph_activity WHERE id = ".$this->_db->quote($id));
        return $this->_db->loadObject();
    }

    public function userdelete($id)
    {
        $this->_db->setQuery("UPDATE #__opengraph_activity SET status = " . OG_ACTIVITY_DELETED . " WHERE id = ".$this->_db->quote($id));
        return $this->_db->execute();
    }

    function getPagination()
    {
        if (empty($this->_pagination))
        {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal()
                , $this->getState('limitstart'),
                $this->getState('limit'));
        }
        return $this->_pagination;
    }
}
