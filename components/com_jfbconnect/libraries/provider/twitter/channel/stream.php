<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

class JFBConnectProviderTwitterChannelStream extends JFBConnectChannel
{
    public function setup()
    {
        $this->name = "Stream";
        $this->outbound = true;
        $this->inbound = true;
        $this->requiredScope[] = 'write';
        $this->postCharacterMax = "140";
        $this->urlLength = "23";
    }

    public function getStream($stream)
    {
        $user = $this->options->get('user_id');
        if (!$user)
            return;

        $feed = JFBCFactory::cache()->get('twitter.stream.' . $user);
        if ($feed === false)
        {
            $accessToken = JFBCFactory::usermap()->getUserAccessToken($user, 'twitter');
            if (!$accessToken)
                return;

            $params = array();
            $params['oauth_token'] = $accessToken->key;
            $this->provider->client->setToken((array)$accessToken);

            $path = $this->provider->options->get('api.url') . 'statuses/user_timeline.json';
            $feedResponse = $this->provider->client->oauthRequest($path, 'GET', $params);
            if ($feedResponse->code != 200)
                return array();

            $feed = json_decode($feedResponse->body);

            JFBCFactory::cache()->store($feed, 'twitter.stream.' . $user);
        }

        if($feed)
        {
            foreach ($feed as $data)
            {
                $post = new JFBConnectPost($this);

                $tweet = $data->retweeted ? $data->retweeted_status : $data;

                $post->message = (isset($tweet->text) ? $tweet->text : "");
                $post->authorScreenName = '@' . $tweet->user->screen_name;
                $post->authorName = $tweet->user->name;
                $post->authorImage = $tweet->user->profile_image_url_https;
                $post->updatedTime = (isset($data->created_at) ? $data->created_at : "");
                $post->thumbLink = (isset($tweet->entities->urls[0]) ? $tweet->entities->urls[0]->expanded_url : "");

                $stream->addPost($post);
            }
        }
    }

    public function post(JRegistry $data)
    {
        $path = $this->provider->options->get('api.url') . 'statuses/update.json';
        $message = $data->get('message', '');
        $link = $data->get('link', '');

        // t.co length. Should update this to use the help/configuration check to verify it hasn't grown.
        $urlLength = 23; // +1 for https
        $status = substr($message, 0, 140 - $urlLength - 1) . " " . $link;

        $user = $this->options->get('user_id');
        $accessToken = JFBCFactory::usermap()->getUserAccessToken($user, 'twitter');

        $params = array();
        $params['oauth_token'] = $accessToken->key;
        $this->provider->client->setToken((array)$accessToken);

        $data = array();
        $data['status'] = $status;

        $return = $this->provider->client->oauthRequest($path, 'POST', $params, $data);
        if ($return !== false)
            return JText::_('COM_JFBCONNECT_CHANNELS_TWITTER_STREAM_POST_SUCCESS');
        else
            return false;
    }
}