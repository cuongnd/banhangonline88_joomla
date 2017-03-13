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

class JFBConnectProviderMeetupWidget extends JFBConnectWidget
{
    const BASE = 'https://api.meetup.com';
    protected $parameters = array(
            'sign' => 'true',
    );

    public function getData($path, array $parameters = array())
    {
        $key = JFBCFactory::config()->get('meetup_widget_api_key');
        $parameters = array_merge($this->parameters, $parameters, array('key' => $key));

        if (preg_match_all('/:([a-z]+)/', $path, $matches))
        {

            foreach ($matches[0] as $i => $match)
            {

                if (isset($parameters[$matches[1][$i]]))
                {
                    $path = str_replace($match, $parameters[$matches[1][$i]], $path);
                    unset($parameters[$matches[1][$i]]);
                }
                else
                {
                    if (JFBCFactory::config()->get('facebook_display_errors'))
                        JFactory::getApplication()->enqueueMessage("Meetup Widget Error: Missing parameter '" . $matches[1][$i] . "' for path '" . $path . "'.", 'error');
                }
            }
        }

        $url = self::BASE . $path . '?' . http_build_query($parameters);

        $data = JFBCFactory::cache()->get('meetup.widget.' . $url);
        if ($data === false)
        {
            $response = $this->getURL($url);
            if ($response && isset($response->results))
            {
                $data = $response->results;
                JFBCFactory::cache()->store($data, 'meetup.widget.' . $url);
            }
        }
        return $data;
    }

    protected function getURL($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept-Charset: utf-8"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $content = curl_exec($ch);

        if (curl_errno($ch))
        {
            $error = curl_error($ch);
            curl_close($ch);
            if (JFBCFactory::config()->get('facebook_display_errors'))
                JFactory::getApplication()->enqueueMessage("Meetup Widget Error: Failed retrieving  '" . $url . "' because of ' " . $error . "'.", 'error');
        }

        $response = json_decode($content);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($status != 200)
        {
            $error = 'Meetup Widget Error ' . $status;

            if (isset($response->problem))
                $error .= ': ' . $response->problem;
            if (isset($response->details))
                $error .= '<br/>' . $response->details;
            if (isset($response->errors[0]->message))
                $error .= '<br/>' . $response->errors[0]->message;
            if (JFBCFactory::config()->get('facebook_display_errors'))
                JFactory::getApplication()->enqueueMessage($error);
        }

        if (isset($response) == false)
        {
            switch (json_last_error())
            {
                case JSON_ERROR_NONE:
                    $error = 'No errors';
                    break;
                case JSON_ERROR_DEPTH:
                    $error = 'Maximum stack depth exceeded';
                    break;
                case JSON_ERROR_STATE_MISMATCH:
                    $error = ' Underflow or the modes mismatch';
                    break;
                case JSON_ERROR_CTRL_CHAR:
                    $error = 'Unexpected control character found';
                    break;
                case JSON_ERROR_SYNTAX:
                    $error = 'Syntax error, malformed JSON';
                    break;
                case JSON_ERROR_UTF8:
                    $error = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                    break;
                default:
                    $error = 'Unknown error';
                    break;
            }
            if (JFBCFactory::config()->get('facebook_display_errors'))
                JFactory::getApplication()->enqueueMessage("Meetup Widget Error: Cannot read response by '" . $url . "' because of: '" . $error . "'.", 'error');
        }

        return $response;
    }

    public function getDateTime($milli, $format = 'M d, Y', $timezone = '')
    {
        if (empty($timezone))
        {
            $userTz = JFactory::getUser()->getParam('timezone');
            $timezone = JFactory::getConfig()->get('offset');
            if ($userTz)
                $timezone = $userTz;
        }

        date_default_timezone_set($timezone);
        $timestamp = $milli / 1000;

        return date($format, $timestamp);
    }

    public function getTimeAgo($datetime, $timezone = '')
    {
        if (empty($timezone))
        {
            $userTz = JFactory::getUser()->getParam('timezone');
            $timezone = JFactory::getConfig()->get('offset');
            if ($userTz)
                $timezone = $userTz;
        }

        date_default_timezone_set($timezone);

        $now = time();
        $post = $datetime / 1000;
        $diff = $now - $post;
        $oneHour = 3600;
        $daysAgo = $diff / $oneHour / 24;
        $hoursAgo = floor($diff / $oneHour);

        $txt = '';

        if ($daysAgo > 1)
        {
            $txt = $this->getDateTime($datetime, 'g:i A d M y');
        }
        else
        {
            // Hours ago
            if ($hoursAgo < 1)
            {
                $txt = JText::_('COM_JFBCONNECT_WIDGET_MEETUP_JUST_A_MOMENT_AGO');
            }
            else if ($hoursAgo == 1)
            {
                $txt = JText::_('COM_JFBCONNECT_WIDGET_MEETUP_ABOUT_AN_HOUR_AGO');
            }
            else
            {
                $txt = sprintf(JText::_('COM_JFBCONNECT_WIDGET_MEETUP_ABOUT_X_HOURS_AGO'), $hoursAgo);
            }
        }

        return $txt;
    }
}
