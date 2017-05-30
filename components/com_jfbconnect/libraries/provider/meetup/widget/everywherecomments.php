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

class JFBConnectProviderMeetupWidgetEverywhereComments extends JFBConnectProviderMeetupWidget
{
    public $name = "Everywhere Comments";
    public $systemName = "everywherecomments";
    public $className = "sc_meetupeverywherecomments";
    public $tagName = "scmeetupeverywherecomments";
    public $examples = array (
        '{SCMeetupEverywhereComments url_name=Coursera}'
    );

    private $containers = array();
    private $comments = array();

    protected function getTagHtml()
    {
        $width = $this->getParamValueEx('width', null, null, '250');
        $urlname = $this->getParamValueEx('url_name', null, null, '');
        $parameters = array(
            'urlname' => $urlname
        );

        $this->containers = $this->getData('/ew/containers', array_merge($parameters, array('fields' => 'meetup_count,past_meetup_count,member_count,geo_ip')));
        $this->comments = $this->getData('/ew/comments', array_merge($parameters, array('page' => 50)));

        if (!defined('MEETUPCOMMONCSS'))
        {
            define('MEETUPCOMMONCSS', true);
            $doc = JFactory::getDocument();
            $doc->addStyleSheet('http://static2.meetupstatic.com/style/widget.css');
        }

        if (!defined('MEETUPEVERYWHERECOMMENTSCSS'))
        {
            define('MEETUPEVERYWHERECOMMENTSCSS', true);

            if(!isset($doc)) $doc = JFactory::getDocument();

            $doc->addStyleSheet(JURI::root(true).'/media/sourcecoast/css/widgets/meetup/everywherecomments.css');
        }

        $tag = '<div class="mup-widget-1" id="mup-widget-1">';

        if($this->containers)
        {
            $tag .= '<div class="mup-widget" style="width:'.$width.'px;">';
            $tag .= $this->getTagHtmlTop();
            $tag .= $this->getTagHtmlBody();
            $tag .= $this->getTagHtmlFooter();
            $tag .= '</div>';
        }
        else
        {
            $tag .= '<div class="mup-widget error" style="width:'.$width.'px;"><div class="errorMsg">'.sprintf(JText::_("COM_JFBCONNECT_WIDGET_MEETUP_ERROR_NO_RESULTS"), $urlname).'</div></div>';
        }

        $tag .= '</div>';

        return $tag;
    }

    private function getTagHtmlTop()
    {
        $container = $this->containers[0];
        $total_meetup_count = $container->meetup_count + $container->past_meetup_count;
        $topHtml = '<div class="mup-hd">';
        $topHtml .= '<h3><a href="'.$container->meetup_url.'">'.number_format($total_meetup_count).' '.$container->name.' Meetups</a></h3>';
        $topHtml .= '<h4><a href="'.$container->meetup_url.'">'.sprintf(JText::_('COM_JFBCONNECT_WIDGET_MEETUP_EVERYWHERE_COMMENTS_NUMBER_PEOPLE'), number_format($container->member_count)).'</a></h4>';
        $topHtml .= '</div>';

        return $topHtml;
    }

    private function getTagHtmlBody()
    {
        $bodyHtml = '<div class="mup-bd">';
        $bodyHtml .= '<ul class="mup-timeline">';
        if(count($this->comments))
        {

            foreach($this->comments as $comment)
            {
                $bodyHtml .= '<li>';
                $bodyHtml .= '<div class="mup-what">
                             <span class="mup-comment">&ldquo;' . $comment->comment . '&rdquo;</span>
                             <span class="mup-author"> -<em>' . $comment->member->name . '</em></span>
                             </div>';
                $bodyHtml .= '<div class="mup-when">
                             <span class="mup-time">' . $this->getTimeAgo( $comment->time ) . '</span>
                             </div>';
                $bodyHtml .= '</li>';
            }

        }
        else
        {
            $bodyHtml .= '<li>'.JText::_('COM_JFBCONNECT_WIDGET_MEETUP_EVERYWHERE_COMMENTS_NO_UPCOMING_EVENTS_W_COMMENTS').'</li>';
        }
        $bodyHtml .= '</ul>';
        $bodyHtml .= '</div>';

        return $bodyHtml;
    }

    private function getTagHtmlFooter()
    {
        $container = $this->containers[0];
        $footerHtml = '<div class="mup-ft">
                    <div class="mup-logo"><a href="'.$container->meetup_url.'"><img src="http://img1.meetupstatic.com/84869143793177372874/img/birddog/everywhere_widget.png"></div>
                    <div class="mup-getwdgt"><a href="http://www.meetup.com/meeup_api/foundry/#everywhere-comments">'.JText::_("COM_JFBCONNECT_WIDGET_MEETUP_ADD_THIS_TO_YOUR_SITE").'</a></div>
                    </div>';

        return $footerHtml;
    }
}