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

class JFBConnectProviderTwitterWidgetOembed extends JFBConnectProviderWidgetOembed
{
    public $examples = array (
        '{SCTwitterOEmbed url=https://twitter.com/BarackObama/statuses/266031293945503744 maxwidth=550}',
        '{SCTwitterOEmbed url=266031293945503744 maxwidth=550}'
    );

    function __construct($provider, $fields)
    {
        parent::__construct($provider, $fields, 'scTwitterOembedTag');

        $this->name = "Embedded Tweets";
        $this->className = 'sc_twitteroembed';
        $this->tagName = 'sctwitteroembed';

        $options = new JRegistry();
        $options->set('oembed_url', 'https://api.twitter.com/1/statuses/oembed.json');

        $url = $this->getParamValueEx('url', null, null, '');
        if(!is_numeric($url) && !empty($url))
            $options->set('url', $url);

        $options->set('maxwidth', $this->getParamValueEx('maxwidth', null, null, '550'));

        $headers = array();
        $headers['Content-Type'] = 'application/json';
        $options->set('headers', $headers);

        $this->options = $options;
    }

    protected function buildExtraQuery()
    {
        $url = '';

        $id = $this->getParamValueEx('url', null, null, '');
        if(is_numeric($id) && !empty($id))
            $url .= '&id='.urlencode($id);

        $url .= '&hide_media='.urlencode($this->getParamValueEx('hide_media', null, null, 'false'));
        $url .= '&hide_thread='.urlencode($this->getParamValueEx('hide_thread', null, null, 'false'));
        $url .= '&align='.urlencode($this->getParamValueEx('align', null, null, 'none'));
        $url .= '&omit_script=true';
        return $url;
    }
}
