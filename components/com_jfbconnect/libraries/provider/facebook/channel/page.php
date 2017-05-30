<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

class JFBConnectProviderFacebookChannelPage extends JFBConnectChannel
{
    public function setup()
    {
        $this->name = "Page";
        $this->outbound = true;
        $this->inbound = true;
        $this->requiredScope[] = 'manage_pages';
        $this->requiredScope[] = 'publish_actions';
    }

    public function getStream($stream)
    {
        $pageId = $this->options->get('page_id');
        if (!$pageId || $pageId == '--')
            return;

        $feed = JFBCFactory::cache()->get('facebook.page.stream.' . $pageId);
        if ($feed === false)
        {
            $params = array();
            $params['access_token'] = $this->options->get('access_token');
            $feed = $this->provider->api($pageId . '/feed', $params, true, 'GET');
            JFBCFactory::cache()->store($feed, 'facebook.page.stream.' . $pageId);
        }

        if($feed['data'])
        {
            foreach($feed['data'] as $data)
            {
                if(array_key_exists('from', $data) && $data['from']['id'] == $pageId)
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
    }

    public function post(JRegistry $data)
    {
        $pageId = $this->options->get('page_id');
        $message = $data->get('message', '');
        $link = $data->get('link', '');

        $params = array();
        $params['access_token'] = $this->options->get('access_token');
        $params['message'] = $message;
        $params['link'] = $link;

        $return = $this->provider->api($pageId . '/feed', $params);
        if ($return !== false)
            return JText::_('COM_JFBCONNECT_CHANNELS_FACEBOOK_PAGE_POST_SUCCESS');
        else
            return false;
    }


    public function onBeforeSave($data)
    {
        if (isset($data['attribs']) && isset($data['attribs']['user_id']) &&
            isset($data['attribs']['page_id'])
        )
        {
            $pageId = $data['attribs']['page_id'];
            $userId = $data['attribs']['user_id'];
            $providerId = JFBCFactory::usermap()->getProviderUserId($userId, 'facebook');
            if ($providerId)
            {
                $access_token = JFBCFactory::usermap()->getUserAccessToken($userId, 'facebook');
                $params['access_token'] = $access_token;

                $pages = JFBCFactory::provider('facebook')->api('/' . $providerId . '/accounts/', $params, true, 'GET');
                if (isset($pages['data']) && count($pages['data']) > 0)
                {
                    foreach ($pages['data'] as $p)
                    {
                        if ($p['id'] == $pageId)
                        {
                            $data['attribs']['access_token'] = $p['access_token'];
                        }
                    }
                }
            }
        }
        return $data;
    }
}