<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

include_once(JPATH_SITE . '/components/com_jfbconnect/libraries/stream.php');

abstract class JFBConnectChannel
{
    var $options;
    var $provider;
    var $name;
    var $inbound = false;
    var $outbound = false;
    var $requiredScope = array();

    var $postCharacterMax = 0;
    var $urlLength = 0;

    public function __construct(JFBConnectProvider $provider, JRegistry $options)
    {
        $this->provider = $provider;
        $this->options = $options;
        $this->setup();
    }

    public function getStream($stream)
    {
        return null;
    }

    public function post(JRegistry $data)
    {
        return false;
    }

    // manipulate the input data in some way (retrieve an access token, etc)
    public function onBeforeSave($data)
    {
        return $data;
    }

    // Called whenever a channel is saved to check the settings and make any required changes
    public function onAfterSave($newData, $oldData)
    {
        if (count($this->requiredScope) > 0)
        {
            if (isset($oldData['attribs']) && isset($oldData['attribs']['user_id']) && $oldData['attribs']['user_id'])
            {
                $userModel = JFBConnectModelUserMap::getUser($oldData['attribs']['user_id'], strtolower($this->provider->name));
                $userModel->removeAllScope('channel', $oldData['id']);
            }
            // Save the manage_pages permission to the required_scope settings for the selected user
            if (isset($newData['attribs']) && isset($newData['attribs']['user_id']) && $newData['attribs']['user_id'])
            {
                $userModel = JFBConnectModelUserMap::getUser($newData['attribs']['user_id'], strtolower($this->provider->name));
                foreach ($this->requiredScope as $scope)
                    $userModel->addScope($scope, 'channel', $newData['id']);
            }
        }
        return true;
    }
}