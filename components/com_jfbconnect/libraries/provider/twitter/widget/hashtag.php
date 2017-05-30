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

class JFBConnectProviderTwitterWidgetHashtag extends JFBConnectWidget
{
    var $name = "Hashtag";
    var $systemName = "hashtag";
    var $className = "sc_twitterhashtag";
    var $tagName = "sctwitterhashtag";
    var $examples = array (
        '{SCTwitterHashtag}',
        '{SCTwitterHashtag hashtag=TwitterStories related=twitterapi,twitter lang=fr size=medium dnt=false}'
    );

    protected function getTagHtml()
    {        
        $hashtag = $this->getParamValueEx('hashtag', null, null, ''); 
        $text = $this->getParamValueEx('text', null, null, '');

        $query = 'button_hashtag='.$hashtag;       
        $query .=  empty( $text ) ? '' : '&text='.$text;

        $tag = '<a href="https://twitter.com/intent/tweet?'.$query.'" class="twitter-hashtag-button"';

        $tag .= $this->getField('lang', null, null, '', 'data-lang'); 
        $tag .= $this->getField('related', null, null, '', 'data-related');
        $tag .= $this->getField('url', null, null, '', 'data-url');
        $tag .= $this->getField('size', null, null, '', 'data-size'); 
        $tag .= $this->getField('dnt', null, 'boolean', 'false', 'data-dnt');
        $tag .= '>Tweet #' . $hashtag . '</a>';

        return $tag;
    }
}