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

class JFBConnectProviderInstagramWidgetOembed extends JFBConnectProviderWidgetOembed
{
    public $examples = array (
        '{SCInstagramOEmbed url=http://instagr.am/p/BUG/ maxwidth=612 maxheight=612}'
    );

    function __construct($provider, $fields)
    {
        parent::__construct($provider, $fields, 'scInstagramOembedTag');

        $this->name = "Embedded Media";
        $this->className = 'sc_instagramoembed';
        $this->tagName = 'scinstagramoembed';

        $options = new JRegistry();
        $options->set('oembed_url', 'http://api.instagram.com/oembed');
        $options->set('url', $this->getParamValueEx('url', null, null, ''));
        $options->set('maxwidth', $this->getParamValueEx('maxwidth', null, null, ''));
        $options->set('maxheight', $this->getParamValueEx('maxheight', null, null, ''));

        $headers = array();
        $headers['Content-Type'] = 'application/json';
        $options->set('headers', $headers);

        $this->options = $options;
    }
}
