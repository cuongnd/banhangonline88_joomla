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

class JFBConnectModelNotification extends JModelLegacy
{
    var $_fbUserId = null;
    var $_requestIds = null;

    function setFbUserId($fbUserId)
    {
        $filter = JFilterInput::getInstance();
        $this->_fbUserId = $filter->clean($fbUserId, 'ALNUM');
    }

    // Clean and set the request IDs. Can be either comma separated (straight from URL) or already in a CLEANED! array
    function setFbRequestIds($fbRequestIds)
    {
        if (!is_array($fbRequestIds))
        {
            $filter = JFilterInput::getInstance();
            $fbRequestIds = explode(',', $fbRequestIds);
            $requestIds = array();
            foreach ($fbRequestIds as $fbRequestId)
                $requestIds[] = $filter->clean($fbRequestId, 'ALNUM'); // ALNum because int gets maxed out

            $fbRequestIds = array_unique($requestIds);
        }
        $this->_fbRequestIds = $fbRequestIds;
    }

    function getRequestsToDelete()
    {
        $del = array();
        // Can't delete requests if user hasn't approved the app, per bugs:
        // https://developers.facebook.com/bugs/239476836116522
        // https://developers.facebook.com/bugs/202883726463009
        if (!$this->_fbUserId)
            return $del;

        foreach ($this->_fbRequestIds as $req)
            $del[] = $req . "_" . $this->_fbUserId;

        return $del;
    }

    function markAsRead()
    {
        // If no user id, we can only mark requests as read that were sent to one user. If it was sent to multiple users, we don't know who is accepting
        // it, and therefore, can't mark any as read. Sucks.
        $now = JFactory::getDate()->toSql();
        if (!$this->_fbUserId)
        {
            // Check to see if each request went to just one user and mark those requests as read
            foreach ($this->_fbRequestIds as $rid)
            {
                $query = $this->_db->getQuery(true);
                $query->select("COUNT(*)")
                        ->from($this->_db->qn('#__jfbconnect_notification'))
                        ->where($this->_db->qn('fb_request_id') . "=" . $this->_db->q($rid));
                $this->_db->setQuery($query);
                $count = $this->_db->loadResult();
                if ($count == 1)
                {
                    $query = $this->_db->getQuery(true);
                    $query->update($this->_db->qn('#__jfbconnect_notification'))
                            ->set($this->_db->qn('modified') . "=" . $this->_db->q($now))
                            ->set($this->_db->qn('status') . "=" . $this->_db->q(1))
                            ->where($this->_db->qn('fb_request_id') . "=" . $this->_db->q($rid));
                    $this->_db->setQuery($query);
                    $this->_db->execute();
                }
            }
        } else
        {
            $query = $this->_db->getQuery(true);
            $query->update($this->_db->qn('#__jfbconnect_notification'))
                    ->set($this->_db->qn('modified') . "=" . $this->_db->q($now))
                    ->set($this->_db->qn('status') . "=" . $this->_db->q(1))
                    ->where($this->_db->qn('fb_user_to') . "=" . $this->_db->q($this->_fbUserId))
                    ->where($this->_db->qn('fb_request_id') . "='" . implode("' OR fb_request_id = '", $this->_fbRequestIds) . "'");
            $this->_db->setQuery($query);
            $this->_db->execute();
        }
    }

    function getRedirect()
    {
        $query = "SELECT r.destination_url rDestinationUrl, breakout_canvas FROM #__jfbconnect_request r INNER JOIN #__jfbconnect_notification n ON r.id = n.jfbc_request_id " .
                " WHERE n.fb_request_id IN (" . implode(', ', $this->_fbRequestIds) . ") ORDER BY n.created DESC LIMIT 1";
        $this->_db->setQuery($query);
        $data = $this->_db->loadObject();

        $redirectInfo = new stdClass();

        // Get Autotune settings to see if Canvas is enabled and if we should even check/use the breakout_canvas setting
        $autotune = JFBCFactory::config()->getSetting('autotune_app_config', null);
        if (empty($autotune))
            return null;

        $appConfig = new JRegistry();
        $appConfig->loadArray($autotune);
        $canvasEnabled = ($appConfig->get('canvas_url', null) != null && $appConfig->get('secure_canvas_url', null) != null) ? true : false;

        $redirectInfo->breakout_canvas = $canvasEnabled && $data->breakout_canvas;
        $redirectInfo->destination_url = $data->rDestinationUrl;

        return $redirectInfo;
    }
}