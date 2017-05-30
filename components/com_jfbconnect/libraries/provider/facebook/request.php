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

/*
 * Canvas class for using site in a Facebook Canvas layout or Page tab
 */
class JFBConnectProviderFacebookRequest extends JObject
{
    /*
     * Get class instance for Canvas object.
     */
    public static function getInstance()
    {
        static $instance;
        if (!$instance)
        {
            $instance = new JFBConnectProviderFacebookRequest();
        }

        return $instance;
    }

    public function checkForNotification()
    {
        $fbRequestIds = JRequest::getVar('request_ids');
        if ($fbRequestIds == "" || $fbRequestIds == null)
            return;

        JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_jfbconnect/' . 'models');
        $notificationModel = JModelLegacy::getInstance('Notification', 'JFBConnectModel');

        // Check for a signed request. This gives us more information about who is coming from Facebook, but only exists on SSL connections
        $request = JRequest::getString('signed_request', null, 'POST');
        if ($request)
        {
            $request = JFBCFactory::provider('facebook')->client->parseSignedRequest($request);
            if (array_key_exists('user_id', $request))
                $notificationModel->setFbUserId($request['user_id']);
        }

        // Next, find the most recent (local) request, and take action on it.
        $notificationModel->setFbRequestIds($fbRequestIds);

        // First, delete all requests on Facebook, if possible. If no Signed Request or user hasn't approved app, we can't do this (Facebook won't allow it)
        foreach ($notificationModel->getRequestsToDelete() as $sig)
            JFBCFactory::provider('facebook')->api('/' . $sig, null, false, 'DELETE');

        // Mark all notifications as 'read'
        $notificationModel->markAsRead();

        // Get the request information from the last request sent
        $redirectInfo = $notificationModel->getRedirect();

        if ($redirectInfo && $redirectInfo->destination_url)
        {
            $app = JFactory::getApplication();
            if (!$redirectInfo->breakout_canvas)
            {
                $app->redirect($redirectInfo->destination_url);
            }
            else // Pop out and redirect
            {
                echo "<html><head></head><body><script type='text/javascript'>top.location.href='" . $redirectInfo->destination_url . "'</script></body></html>";
                $app->close();
            }
        }
    }

}
