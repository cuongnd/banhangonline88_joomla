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

class JFBConnectProviderMeetupWidgetOembed extends JFBConnectProviderWidgetOembed
{
    public $examples = array (
        '{SCMeetupOEmbed url=http://www.meetup.com/ny-tech maxwidth=308}'
    );

    function __construct($provider, $fields)
    {
        parent::__construct($provider, $fields, 'scMeetupOembedTag');

        $this->name = "Embedded Resource";
        $this->className = 'sc_meetupoembed';
        $this->tagName = 'scmeetupoembed';

        $options = new JRegistry();
        $options->set('oembed_url', 'http://api.meetup.com/oembed');
        $options->set('url', $this->getParamValueEx('url', null, null, ''));
        $options->set('maxwidth', $this->getParamValueEx('maxwidth', null, null, '308'));

        $headers = array();
        $headers['Content-Type'] = 'application/json';
        $options->set('headers', $headers);

        $this->options = $options;
    }

    protected function getTagHtmlRich()
    {
        $tag = '';
        if(isset($this->response))
        {
            $tag = '<div class="oembed-preview" style="width: '.$this->response->width.'px; height: '.$this->response->height.'px;">'.$this->response->html.'</div>';
        }
        return $tag;
    }
}
