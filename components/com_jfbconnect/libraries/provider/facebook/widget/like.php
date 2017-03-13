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

class JFBConnectProviderFacebookWidgetLike extends JFBConnectProviderFacebookWidget
{
    var $name = "Like Button";
    var $systemName = "like";
    var $className = "jfbclike";
    var $tagName = "jfbclike";
    var $examples = array (
        '{JFBCLike}',
        '{JFBCLike href=http://www.sourcecoast.com layout=standard show_faces=true share=true width=300 action=like colorscheme=light ref=homepage kid_directed_site=true}'
    );

    protected function getTagHtml()
    {
        JFBCFactory::addStylesheet('jfbconnect.css');
        $tag = '<div class="fb-like"';
        $tag .= $this->getField('href', 'url', null, SCSocialUtilities::getStrippedUrl(), 'data-href');
        $tag .= $this->getField('show_faces', null, 'boolean', 'true', 'data-show-faces');
        $tag .= $this->getField('share', 'show_send_button', 'boolean', 'true', 'data-share');
        $tag .= $this->getField('layout', null, null, '', 'data-layout');
        $tag .= $this->getField('width', null, null, '', 'data-width');
        $tag .= $this->getField('action', null, null, '', 'data-action');
        $tag .= $this->getField('colorscheme', null, null, '', 'data-colorscheme');
        $tag .= $this->getField('ref', null, null, '', 'data-ref');
        $tag .= $this->getField('kid_directed_site', null, 'boolean', 'false', 'data-kid-directed-site');
        $tag .= '></div>';
        return $tag;
    }
}
