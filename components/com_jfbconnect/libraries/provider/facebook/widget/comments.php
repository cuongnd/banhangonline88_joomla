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

class JFBConnectProviderFacebookWidgetComments extends JFBConnectProviderFacebookWidget
{
    var $name = "Comments";
    var $systemName = "comments";
    var $className = "jfbccomments";
    var $tagName = "jfbccomments";
    var $examples = array (
        '{JFBCComments}',
        '{JFBCComments href=http://www.sourcecoast.com width=550 num_posts=10 colorscheme=dark mobile=false order_by=time}'
    );

    protected function getTagHtml()
    {
        $tag = '<div class="fb-comments"';
        $tag .= $this->getField('href', 'url', null, SCSocialUtilities::getStrippedUrl(), 'data-href');
        $tag .= $this->getField('width', null, null, '', 'data-width');
        $tag .= $this->getField('num_posts', null, null, '', 'data-num-posts');
        $tag .= $this->getField('colorscheme', null, null, '', 'data-colorscheme');
        $tag .= $this->getField('mobile', null, 'boolean', 'false', 'data-mobile');
        $tag .= $this->getField('order_by', null, null, '', 'data-order-by');
        $tag .= '></div>';
        return $tag;
    }
}
