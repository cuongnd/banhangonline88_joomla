<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

class JFBConnectProviderFacebookChannelGroup extends JFBConnectChannel
{
    var $name = "Group";

    public function setup()
    {
        $this->name = "Group";
        $this->outbound = true;
        $this->inbound = true;
        $this->requiredScope[] = 'user_groups';
        $this->requiredScope[] = 'publish_actions';
    }

    public function getStream($stream)
    {
        $groupId = $this->options->get('group_id');
        if (!$groupId || $groupId == '--')
            return;

        $feed = JFBCFactory::cache()->get('facebook.page.group.' . $groupId);
        if ($feed === false)
        {
            $params = array();
            $params['access_token'] = JFBCFactory::usermap()->getUserAccessToken($this->options->get('user_id'), 'facebook');
            $feed = $this->provider->api($groupId . '/feed', $params, true, 'GET');
            JFBCFactory::cache()->store($feed, 'facebook.page.group.' . $groupId);
        }

        if($feed['data'])
        {
            foreach($feed['data'] as $data)
            {
                $post = new JFBConnectPost();
                $post->message = (array_key_exists('message', $data)?$data['message']:"");
                $post->authorScreenName = $data['from']['name'];
                $post->updatedTime = (array_key_exists('updated_time', $data)?$data['updated_time']:"");
                $post->thumbTitle = (array_key_exists('name', $data)?$data['name']:"");
                $post->thumbLink = (array_key_exists('link', $data)?$data['link']:"");
                $post->thumbPicture = (array_key_exists('picture', $data)?$data['picture']:"");
                $post->thumbCaption = (array_key_exists('caption', $data)?$data['caption']:"");
                $post->thumbDescription = (array_key_exists('description', $data)?$data['description']:"");
                $post->comments = (array_key_exists('comments', $data)?$data['comments']:"");

                $stream->addPost($post);
            }
        }
    }

    public function post(JRegistry $data)
    {
        $groupId = $this->options->get('group_id');
        $message = $data->get('message', '');
        $link = $data->get('link', '');

        $params = array();
        $params['access_token'] = JFBCFactory::usermap()->getUserAccessToken($this->options->get('user_id'), 'facebook');
        $params['message'] = $message;
        $params['link'] = $link;

        $return = $this->provider->api($groupId . '/feed', $params);
        if ($return !== false)
            return JText::_('COM_JFBCONNECT_CHANNELS_FACEBOOK_GROUP_POST_SUCCESS');
        else
            return false;
    }}