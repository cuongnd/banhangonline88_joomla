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

class JFBConnectControllerRequest extends JFBConnectController
{

    function display($cachable = false, $urlparams = false)
    {
        exit;
    }

    function requestSent()
    {
        $jfbcRequestId = JRequest::getInt('jfbcId');
        $fbRequestId = JRequest::getString('requestId');
        $inToList = JRequest::getVar('to');

        // Get the from user id from the request
        $to = $inToList[0];
        $requestInfo = JFBCFactory::provider('facebook')->api('/' . $fbRequestId . "_" . $to);
        $fbFrom = $requestInfo['from']['id'];

        // Not using the model, as we're doing a simple store.
        JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/tables');
        $data = array();
        $data['fb_request_id'] = $fbRequestId;
        $data['fb_user_from'] = $fbFrom;
        $data['jfbc_request_id'] = $jfbcRequestId;
        $data['created'] = JFactory::getDate()->toSql();
        $data['modified'] = null;
        //        $data['destination_url'] = JRequest::getString('destinationUrl');

        $aupInstalled = false;
        if (JFBCFactory::config()->getSetting('social_alphauserpoints_enabled'))
        {
            $api_AUP = JPATH_SITE  . '/components/com_alphauserpoints/helper.php';
            if (file_exists($api_AUP))
            {
                require_once ($api_AUP);
                $aupInstalled = true;
            }
        }

        foreach ($inToList as $fbTo)
        {
            $row = & JTable::getInstance('JFBConnectNotification', 'Table');
            $to = JFilterInput::clean($fbTo, 'ALNUM');
            $data['fb_user_to'] = $to;
            $row->save($data);

            if ($aupInstalled)
                AlphaUserPointsHelper::newpoints('plgjfbconnect_request_sent', '', $fbTo);
        }

        $app = JFactory::getApplication();
        $app->close();
    }

}
