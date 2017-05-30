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

class JFBConnectControllerAjax extends JFBConnectController
{
    function display($cachable = false, $urlparams = false)
    {
        // Not to be called directly
        JFactory::getApplication()->exit(0);
    }

    public function friends()
    {
        JSession::checkToken('get') or die();
        $friends = JFBCFactory::provider('facebook')->api('/me/friends?fields=id,name,link,picture');

        if (empty($friends) || !isset($friends['data']) || empty($friends['data']))
        {
            JFactory::getApplication()->close(0);
        }

        $search = strtolower(JRequest::getString('q', 'GET'));
        $friends = $friends['data'];
        $matchedFriends = array();
        foreach ($friends as $friend)
        {
            if (!(isset($friend['id']) && isset($friend['name'])))
                continue;

            if (strpos(strtolower($friend['name']), $search) !== false)
            {
                $matchedFriend = array('label' => $friend['name'], 'value' => $friend['id']);
                if (isset($friend['picture']['data']['url']))
                    $matchedFriend['picture'] = $friend['picture']['data']['url'];
                if (isset($friend['link']))
                    $matchedFriend['link'] = $friend['link'];
                $matchedFriends[] = $matchedFriend;
                unset($matchedFriend);
            }
        }
        if (!empty($matchedFriends))
        {
            echo json_encode($matchedFriends);
        }
        JFactory::getApplication()->close(0);
    }

    public function places()
    {
        JSession::checkToken('get') or die();
        $search = JRequest::getString('q', '');
        $params = array(
            'type' => 'place',
            'fields' => 'id,name,location,picture,were_here_count,is_published,link',
            'q' => trim($search)
        );

        $center = JRequest::getVar('center');
        if ($center)
        {
            $params['center'] = trim($center);
            $params['distance'] = 1000;
        }

        $results = JFBCFactory::provider('facebook')->api('search', $params, true, 'GET');

        $results = $results['data'];
        $clean_results = array();
        foreach ($results as $result)
        {
            if (!(isset($result['is_published']) && $result['is_published'] && isset($result['id']) && $result['id'] && isset($result['name']) && $result['name']))
                continue;
            $clean_result = array('label' => trim($result['name']), 'value' => $result['id'], 'link' => $result['link']);

            // build location components for use in place summary
            if (isset($result['location']))
            {
                $location = array();

                if (isset($result['location']['street']) && $result['location']['street'])
                    $location['street'] = trim($result['location']['street']);
                if (isset($result['location']['city']) && $result['location']['city'] && isset($result['location']['state']) && $result['location']['state'])
                    $location['area'] = trim($result['location']['city'] . ', ' . $result['location']['state']);
                else if (isset($result['location']['state']) && $result['location']['state'] && isset($result['location']['country']) && $result['location']['country'])
                    $location['area'] = trim($result['location']['state'] . ', ' . $result['location']['country']);
                else if (isset($result['location']['country']) && $result['location']['country'])
                    $location['area'] = trim($result['location']['country']);

                if (!empty($location))
                    $clean_result['location'] = $location;
                unset($location);
            }

            if (isset($result['were_here_count']) && $result['were_here_count'])
                $clean_result['were_here_count'] = (int)$result['were_here_count'];

            if (isset($result['picture']['data']['url']) && $result['picture']['data']['url'])
                $clean_result['picture'] = $result['picture']['data']['url'];

            $clean_results[] = $clean_result;
            unset($clean_result);
        }
        echo json_encode($clean_results);
        JFactory::getApplication()->close(0);
    }

    public function fetch()
    {
        if (!JSession::checkToken('get'))
            exit;

        $input = JFactory::getApplication()->input;
        $library = $input->getCmd('library', null);
        $subtask = $input->getCmd('subtask', null);
        if ($library && $subtask)
        {
            $lib = JFBCFactory::library($library);
            $task = 'ajax' . ucfirst($subtask);
            $result = $lib->$task();
        }
        exit;
    }


}
