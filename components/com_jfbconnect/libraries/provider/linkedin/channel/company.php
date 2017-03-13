<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

class JFBConnectProviderLinkedinChannelCompany extends JFBConnectChannel
{
    public function setup()
    {
        $this->name = "Company";
        $this->outbound = true;
        $this->inbound = true;
        $this->requiredScope[] = 'rw_company_admin';

        $this->postCharacterMax = '700';
    }

    public function getStream($stream)
    {
        $user = $this->options->get('user_id');
        if (!$user)
            return;

        $companyId = $this->options->get('company_id');

        $feed = JFBCFactory::cache()->get('linkedin.stream.' . $companyId);
        if ($feed === false)
        {
            $access_token = JFBCFactory::usermap()->getUserAccessToken($user, 'linkedin');
            if (!is_object($access_token) || ($access_token->created + $access_token->expires_in < time()))
                return;

            $url = 'https://api.linkedin.com/v1/companies/' . $companyId . '/updates';
            $this->provider->client->setToken((array)$access_token);
            try
            {
                $feedResponse = $this->provider->client->query($url, json_encode(array()), array(), 'get');
                if ($feedResponse->code != 200)
                    return;

                $feed = json_decode($feedResponse->body);
                JFBCFactory::cache()->store($feed, 'linkedin.stream.' . $companyId);
            }
            catch (Exception $e)
            {
                if (JFBCFactory::config()->get('facebook_display_errors'))
                    JFactory::getApplication()->enqueueMessage('LinkedIn Stream: ' . $e->getMessage(), 'error');

                return;
            }
        }

        if(isset($feed->values) && $feed->values)
        {
            foreach($feed->values as $data)
            {
                $post = new JFBConnectPost();

                $post->message = (isset($data->updateContent->companyStatusUpdate->share->comment)?$data->updateContent->companyStatusUpdate->share->comment:"");
                $post->authorScreenName = $data->updateContent->company->name;

                $post->thumbLink = (isset($data->updateContent->companyStatusUpdate->share->content->submittedUrl)?$data->updateContent->companyStatusUpdate->share->content->submittedUrl:"");
                $post->thumbPicture = (isset($data->updateContent->companyStatusUpdate->share->content->submittedImageUrl)?$data->updateContent->companyStatusUpdate->share->content->submittedImageUrl:"");
                $post->thumbDescription = (isset($data->updateContent->companyStatusUpdate->share->content->description)?$data->updateContent->companyStatusUpdate->share->content->description:"");
                $post->thumbCaption = (isset($data->updateContent->companyStatusUpdate->share->content->eyebrowUrl)?$data->updateContent->companyStatusUpdate->share->content->eyebrowUrl:"");
                $post->thumbTitle = (isset($data->updateContent->companyStatusUpdate->share->content->title)?$data->updateContent->companyStatusUpdate->share->content->title:"");

                if(isset($data->timestamp))
                {
                    $timestamp = intval($data->timestamp / 1000);
                    $post->updatedTime = gmdate($stream->options->get('datetime_format'), $timestamp);
                }
                else
                    $post->updatedTime = "";

                $stream->addPost($post);
            }
        }
    }

    public function post(JRegistry $data)
    {
        $user = $this->options->get('user_id');
        $access_token = JFBCFactory::usermap()->getUserAccessToken($user, 'linkedin');

        $this->provider->client->setToken((array)$access_token);

        $companyId = $this->options->get('company_id');
        $url = 'https://api.linkedin.com/v1/companies/' . $companyId . '/shares';

        $vals = array();
        $vals['visibility'] = array('code' => 'anyone');
        $vals['comment'] = $data->get('message', '');
        $vals['content'] = array('submitted-url' => $data->get('link', ''));

        $vals = json_encode($vals);
        $return = $this->provider->client->query($url, $vals, array(), 'post');

        if ($return !== false)
            return JText::_('COM_JFBCONNECT_CHANNELS_LINKEDIN_STREAM_POST_SUCCESS');
        else
            return false;
    }
}