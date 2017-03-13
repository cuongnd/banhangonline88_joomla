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

class JFBConnectProviderTwitterWidgetMention extends JFBConnectWidget
{
    var $name = "Mention";
    var $systemName = "mention";
    var $className = "sc_twittermention";
    var $tagName = "sctwittermention";
    var $examples = array (
        '{SCTwitterMention}',
        '{SCTwitterMention screen_name=twitterapi related=twitter lang=fr size=medium dnt=false}'
    );

    protected function getTagHtml()
    {      
        $screen_name = $this->getParamValueEx('screen_name', null, null, ''); 
        $text = $this->getParamValueEx('text', null, null, '');

        $query = 'screen_name='.$screen_name;       
        $query .=  empty( $text ) ? '' : '&text='.$text;

        $tag = '<a href="https://twitter.com/intent/tweet?'.$query.'" class="twitter-mention-button"';
        
        $tag .= $this->getField('related', null, null, '', 'data-related'); 
        $tag .= $this->getField('size', null, null, '', 'data-size'); 
        $tag .= $this->getField('dnt', null, 'boolean', 'false', 'data-dnt');
        $tag .= $this->getField('lang', null, null, '', 'data-lang'); 
        $tag .= '>Tweet to @' . $screen_name . '</a>';

        return $tag;
    }
}
//<a href="https://twitter.com/intent/tweet?screen_name=support&text=testste" class="twitter-mention-button" data-size="large">Tweet to @support</a>

//<a href="https://twitter.com/intent/tweet?screen_name=support" class="twitter-mention-button" data-size="large">Tweet to @support</a>