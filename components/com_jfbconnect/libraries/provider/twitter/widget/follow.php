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

class JFBConnectProviderTwitterWidgetFollow extends JFBConnectWidget
{
    var $name = "Follow";
    var $systemName = "follow";
    var $className = "sc_twitterfollow";
    var $tagName = "sctwitterfollow";
    var $examples = array (
        '{SCTwitterFollow}',
        '{SCTwitterFollow username=twitterapi lang=fr width=300px align=left show-screen-name=false size=medium dnt=false}'
    );

    protected function getTagHtml()
    {
        $username = $this->getParamValueEx('username', null, null, '');
        $tag = '<a href="https://twitter.com/' . $username . '" class="twitter-follow-button"';

        //NOTE: vertical count is not yet supported in twitter follow
        if($this->fields->exists('layout'))
            $tag .= SCEasyTags::getShareButtonLayout('twitter', $this->fields->get('layout'), '"');
        else
            $tag .= $this->getField('count', null, null, '', 'data-count');

        $tag .= $this->getField('lang', null, null, '', 'data-lang');
        $tag .= $this->getField('width', null, null, '', 'data-width');
        $tag .= $this->getField('align', null, null, '', 'data-align');
        $tag .= $this->getField('show-screen-name', null, 'boolean', '', 'data-show-screen-name');
        $tag .= $this->getField('size', null, null, '', 'data-size'); 
        $tag .= $this->getField('dnt', null, 'boolean', 'false', 'data-dnt');
        $tag .= '>Follow @' . $username . '</a>';

        return $tag;
       
    }
}
