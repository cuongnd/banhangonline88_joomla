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

class JFBConnectProviderWidgetOembed extends JFBConnectWidget
{
    public $name = 'oEmbed';
    public $systemName = "oembed";

    protected $http = null;
    protected $response = null;
    protected $options = null;

    function __construct($provider, $fields, $className)
    {
        parent::__construct($provider, $fields);

        $this->className = $className;
    }

    protected function query()
    {
        try
        {
            $transport = new JHttpTransportCurl($this->options);
        }
        catch (Exception $e)
        {
            JFactory::getApplication()->enqueueMessage('JFBConnect requires the CURL PHP extension to be installed and callable.', 'error');
            $transport = null;
        }

        $this->http = new JHttp($this->options, $transport);

        $url = $this->buildQuery();
        $extraQuery = $this->buildExtraQuery();

        $response = $this->http->get($url.$extraQuery);

        if ($response->code < 200 || $response->code >= 400)
        {
            throw new RuntimeException('Error code ' . $response->code . ' received requesting data: ' . $response->body . '.');
        }
        return $response;
    }

    protected function buildQuery()
    {
        $url = $this->http->getOption('oembed_url').'?';

        if ($this->http->getOption('url'))
        {
            $url .= 'url=' . urlencode($this->http->getOption('url'));
            $url .= '&';
        }

        if ($this->http->getOption('maxwidth'))
        {
            $url .= 'maxwidth=' . urlencode($this->http->getOption('maxwidth'));
        }

        if ($this->http->getOption('maxheight'))
        {
            $url .= '&maxheight=' . urlencode($this->http->getOption('maxheight'));
        }

        $url .= '&format=';
        $url .= $this->http->getOption('format') ? urlencode($this->http->getOption('format')) : 'json';

        return $url;
    }

    protected function buildExtraQuery()
    {
        $extraurl = '';
        return $extraurl;
    }

    //override for other reponse format
    public function getData()
    {
        try
        {

            $key = strtolower($this->systemName) . '.' . strtolower($this->provider->systemName);
            $key .= '.' . strtolower(str_replace(' ', '_',$this->name)). '.' . md5($this->options->toString());

            //add caching capability
            $this->response = JFBCFactory::cache()->get($key);
            if ($this->response === false)
            {
                $data = $this->query();

                if ($data->code == 200)
                {
                    $this->response = json_decode($data->body, true);

                    if(is_array( $this->response ))
                    {
                        $this->response = (object) $this->response;
                    }

                    // Perform the curl Request and get $response
                    JFBCFactory::cache()->store($this->response, $key);
                }
            }
        }
        catch (Exception $e)
        {
            if (JFBCFactory::config()->get('facebook_display_errors'))
                JFactory::getApplication()->enqueueMessage($e->getMessage());
        }
    }

    protected function getTagHtml()
    {
        $type = '';
        $tag = '';
        $this->getData();

        //we get the type directly from the response
        if(isset($this->response->type))
            $type = $this->response->type;

        switch($type)
        {
            case 'photo':
                $tag .= $this->getTagHtmlPhoto();
                break;
            case 'video':
                $tag .= $this->getTagHtmlVideo();
                break;
            case 'link':
                $tag .= $this->getTagHtmlLink();
                break;
            case 'rich':
                $tag .= $this->getTagHtmlRich();
                break;
            default:
                $tag .= sprintf(JText::_('COM_JFBCONNECT_WIDGET_OEMBED_TYPE_SUPPORT_ERROR'), $type);
                break;
        }

        return $tag;
    }

    protected function getTagHtmlPhoto()
    {
        $tag = '';
        if(isset($this->response->url))
        {
            $tag = '<img width="'.$this->response->width.'" height="'.$this->response->height.'" src="'.$this->response->url.'" alt="'.$this->response->title.'" />';
        }

        return $tag;
    }

    protected function getTagHtmlVideo()
    {
        return isset($this->response->html) ? $this->response->html : '';
    }

    protected function getTagHtmlLink()
    {
        return '';
    }

    protected function getTagHtmlRich()
    {
        return isset($this->response->html) ? $this->response->html : '';
    }
}