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

class JFBConnectProviderFacebookWidgetRecommendations extends JFBConnectProviderFacebookWidget
{
    var $name = "Recommendations";
    var $systemName = "recommendations";
    var $className = "jfbcrecommendations";
    var $tagName = "jfbcrecommendations";
    var $examples = array (
        '{JFBCRecommendations}',
        '{JFBCRecommendations site=http://www.sourcecoast.com width=350 height=350 colorscheme=light header=false link_target=_top}'
    );

    protected function getTagHtml()
    {
        $tag = '<div class="fb-recommendations"';
        $tag .= $this->getField('header', null, 'boolean', 'true', 'data-header');
        $tag .= $this->getField('site', null, null, '', 'data-site');
        $tag .= $this->getField('width', null, null, '', 'data-width');
        $tag .= $this->getField('height', null, null, '', 'data-height');
        $tag .= $this->getField('colorscheme', null, null, '', 'data-colorscheme');
        $tag .= $this->getField('link_target', null, null, '', 'data-linktarget');
        $tag .= $this->getField('action', null, null, '', 'data-action');
        $tag .= $this->getField('ref', null, null, '', 'data-ref');
        $tag .= $this->getField('max_age', null, null, '', 'data-max-age');
        $tag .= '></div>';
        return $tag;
    }
}
