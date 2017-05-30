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

class JFBConnectProviderFacebookWidgetLikebox extends JFBConnectProviderFacebookWidget
{
    var $name = "Like Box";
    var $systemName = "likebox";
    var $className = "jfbcfan";
    var $tagName = "jfbcfan";
    var $examples = array (
        '{JFBCFan}',
        '{JFBCFan height=200 width=200 colorscheme=light href=http://www.facebook.com/SourceCoast show_faces=true stream=false header=true show_border=true force_wall=false}'
    );

    protected function getTagHtml()
    {
        $tag = '<div class="fb-like-box"';
        $tag .= $this->getField('show_faces', null, 'boolean', 'true', 'data-show-faces');
        $tag .= $this->getField('header', null, 'boolean', 'true', 'data-header');
        $tag .= $this->getField('stream', null, 'boolean', 'true', 'data-stream');
        $tag .= $this->getField('force_wall', null, 'boolean', 'true', 'data-force-wall');
        $tag .= $this->getField('width', null, null, '', 'data-width');
        $tag .= $this->getField('height', null, null, '', 'data-height');
        $tag .= $this->getField('href', 'url', null, '', 'data-href');
        $tag .= $this->getField('colorscheme', null, null, '', 'data-colorscheme');
        $tag .= $this->getField('show_border', null, 'boolean', 'true', 'data-show-border');
        $tag .= '></div>';
        return $tag;
    }
}
