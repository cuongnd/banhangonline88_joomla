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

class JFBConnectProviderMeetupWidgetSponsoredGroups extends JFBConnectProviderMeetupWidget
{
    public $name = "Sponsored Meetup Groups";
    public $systemName = "sponsoredgroups";
    public $className = "sc_meetupsponsoredgroups";
    public $tagName = "scmeetupsponsoredgroups";
    public $examples = array (
        '{SCMeetupSponsorsedGroups campaign=moms}'
    );

    //private $events = array();
    private $topics = array();
    private $groups = array();

    protected function getTagHtml()
    {
        $width = $this->getParamValueEx('width', null, null, '660') - 10;
        $height = $this->getParamValueEx('height', null, null, '280') - 20;
        $campaign = $this->getParamValueEx('campaign', null, null, '');
        $parameters = array(
            'topic' => $campaign
        );

       // $this->events = $this->getData('/2/events', array_merge($parameters, array('page' => 3, 'after' => '0d')));
        $this->topics = $this->getData('/topics', $parameters);
        $this->groups = $this->getData('/2/groups', $parameters);

        if (!defined('MEETUPCOMMONCSS'))
        {
            define('MEETUPCOMMONCSS', true);
            $doc = JFactory::getDocument();
            $doc->addStyleSheet('http://static2.meetupstatic.com/style/widget.css');
        }

        if (!defined('MEETUPSPONSORSHIPCAROUSELCSS'))
        {
            define('MEETUPSPONSORSHIPCAROUSELCSS', true);

            if(!isset($doc)) $doc = JFactory::getDocument();
            $doc->addStyleSheet('http://static2.meetupstatic.com/style/widget.css');
            $doc->addStyleSheet(JURI::root(true).'/media/sourcecoast/css/widgets/meetup/sponsorshipcarousel.css');
            $doc->addScript(JURI::root(true).'/media/sourcecoast/js/widgets/meetup/sponsorshipcarousel.js');
        }

        $tag = "<div id='mup-wdgt-spnsr-carousel' class='mup-wdgt-spnsr-carousel' style='width: " . $width . "px; height: " . $height . "px;'>";

        if(count($this->topics))
        {
            $tag .= '<div class="mup-widget">';
            $tag .= $this->getTagHtmlTop();
            $tag .= $this->getTagHtmlBody();
            $tag .= $this->getTagHtmlFooter();
            $tag .= '</div>';
        }
        else
        {
            $tag .= '<div class="mup-widget error"><div class="errorMsg">'.sprintf(JText::_("COM_JFBCONNECT_WIDGET_MEETUP_ERROR_NO_RESULTS"), $campaign).'</div></div>';
        }

        $tag .= '</div><div class="clearfix"></div>';

        return $tag;
    }

    private function getTagHtmlTop()
    {
        $topic = $this->topics[0];
        $campaign = $this->getParamValueEx('campaign', null, null, '');
        $img_url = JURI::root(true).'/'.$this->getParamValueEx('image_url', null, null, '');
        $topHtml = '<div class="mup-hd">';
        $topHtml .= '<div class="mup-hd-img"><img src="'.$img_url.'" class="resize img-rounded"></div>';
        $topHtml .= '<div class="mup-hd-info"><h3><a href="'.$topic->link.'">'.ucfirst($campaign).'<span class="meetup"> Meetups</span></a></h3><h4>'.sprintf(JText::_("COM_JFBCONNECT_WIDGET_MEETUP_SPONSORED_GROUPS_NUMBER_MEMBERS"), $topic->members).'</h4></div>';
        $topHtml .= '</div>';

        return $topHtml;
    }

    private function getTagHtmlBody()
    {
        $groups = $this->groups;
        $bodyHtml = '<div class="mup-bd"><div id="slider-code"><div class="vp-wrap">';
        $bodyHtml .= '<a class="buttons prev" href="#"><i class="icon-backward"></i></a>';
        $bodyHtml .= '<div class="viewport">';
        if(count($groups))
        {
            $bodyHtml .= '<ul class="overview">';
            foreach($groups as $group)
            {
                $bodyHtml .= '<li class="text-center"><p><a href="'.$group->link.'">'.$group->name.'</a></p>';
                if(isset($group->group_photo->photo_link))
                    $bodyHtml .= '<img src="'.$group->group_photo->photo_link.'" />';
                $bodyHtml .= '</li>';
            }
            $bodyHtml .= '</ul>';
        }
        $bodyHtml .= '</div>';
        $bodyHtml .= '<a class="buttons next" href="#"><i class="icon-forward"></i></a>';
        $bodyHtml .= '</div></div></div>';

        return $bodyHtml;
    }

    private function getTagHtmlFooter()
    {
        $footerHtml = '<div class="mup-ft">';
        $footerHtml .= '<div class="mup-logo"><a href="http://www.meetup.com/everywhere/"><img src="http://img1.meetupstatic.com/84869143793177372874/img/birddog/everywhere_widget.png"></a></div>';
        $footerHtml .= '<div class="mup-getwdgt"><a href="http://www.meetup.com/meetup_api/foundry/#sponsored-meetup-groups">'.JText::_("COM_JFBCONNECT_WIDGET_MEETUP_ADD_THIS_TO_YOUR_SITE").'</a></div>';
        $footerHtml .= '</div>';
        $footerHtml .= '
        <script type="text/javascript">
            jfbcJQuery("#slider-code").tinycarousel({ display: 3 });
            jfbcJQuery(document).ready(function ()
            {
                var img = jfbcJQuery("img.resize");
                var width = img.width(),
                    height = img.height();
                if (width < height) {
                   img.css("width", 60);
                   img.css("height", "auto");
                }
                else {
                   img.css("height", 60);
                   img.css("width", "auto");
                }
            });
        </script>';

        return $footerHtml;
    }

}