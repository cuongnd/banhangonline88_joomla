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

class JFBConnectProviderMeetupWidgetGroupStats extends JFBConnectProviderMeetupWidget
{
    public $name = "Meetup Group Stats";
    public $systemName = "groupstats";
    public $className = "sc_meetupgroupstats";
    public $tagName = "scmeetupgroupstats";
    public $examples = array (
        '{SCMeetupGroupStats urlname=nystartrek}'
    );

    private $urlname = '';
    private $rating = 0;
    private $width = 0;
    private $height = 0;
    private $groups = array();

    protected function getTagHtml()
    {
        $this->urlname = $this->getParamValueEx('url_name', null, null, 'nystartrek');
        $this->width = $this->getParamValueEx('width', null, null, '225');
        $this->height = $this->getParamValueEx('height', null, null, '570');

        $parameters = array(
            'group_urlname' => $this->urlname
        );

        $this->groups = $this->getData('/2/groups', $parameters);

        if (!defined('MEETUPGROUSTATSCSS'))
        {
            define('MEETUPGROUSTATSCSS', true);
            $doc = JFactory::getDocument();
            $doc->addStyleSheet(JURI::root(true).'/media/sourcecoast/css/widgets/meetup/groupstats.css');
         }

        $tag = '<div id="mug-badge" class="mug-badge" style="width:'.$this->width.'px;height:'.$this->height.'px;">';

        if(empty($this->groups))
        {
            $tag .= '<div class="mup-widget error"><div class="errorMsg">'.sprintf( JText::_('COM_JFBCONNECT_WIDGET_MEETUP_ERROR_NO_RESULTS'), $this->urlname ).'</div></div>';
        }
        else
        {
            $tag .= '<div class="mup-widget">';
            $tag .= $this->getTagHtmlBody();
            $tag .= $this->getTagHtmlFooter();
            $tag .= '</div>';
        }

        $tag .= '</div>';

        return $tag;
    }

    private function getTagHtmlBody()
    {
        //get the first group
        $group = $this->groups[0];
        $this->rating = $group->rating;

        $bodyHtml = '<div class="mup-bd">';
        $bodyHtml .= '<h3 class="mup-widget-group-name"><a href="'.$group->link.'" target="_top">'.$group->name.'</a></h3>';
        $bodyHtml .= '<h4 class="mup-widget-est"><span class="mup-tlabel">'.sprintf(JText::_("COM_JFBCONNECT_WIDGET_MEETUP_GROUP_STATS_CREATED_EST"), $this->getDateTime($group->created, 'M d, Y', $group->timezone)).'</span></h4>';

        $img = '';
        if($group->group_photo){
            $width = $this->width - 50;
            $img = "<img width='{$width}' class='mup-img' alt='{$group->name}' src='{$group->group_photo->photo_link}' />";
        }

        $bodyHtml .= '<span class="mup-stats"><div class="mup-img-wrap">'.$img.'</div>'.$group->members.' <span class="mup-tlabel">'.$group->who.'</span></span>';
        $bodyHtml .= '<span class="mup-stats"><div class="next-event">'.$this->getNextEventHtml().'</div></span>';
        $bodyHtml .= '<h4><span class="mup-button"><a href="'.$group->link.'" target="_top">'.JText::_("COM_JFBCONNECT_WIDGET_MEETUP_GROUP_STATS_JOIN").'</a></span></h4>';
        $bodyHtml .= '</div>';

        return $bodyHtml;
    }

    private function getTagHtmlFooter()
    {
        $footerHtml = '<div class="mup-ft">';
        $footerHtml .= '<div class="mup-logo"><div class="pull-left"><a href="http://www.meetup.com" target="_top"><img src="http://img1.meetupstatic.com/84869143793177372874/img/birddog/everywhere_widget.png" /></a></div><div class="pull-right"><div class="pull-right">'.$this->addStarRating().'</div><br><div class="pull-right"><span class="mup-tlabel">'.JText::_("COM_JFBCONNECT_WIDGET_MEETUP_GROUP_STATS_GROUP_RATING").'</span></div></div></div>';
        $footerHtml .= '<div class="mup-getwdgt"><a href="http://www.meetup.com/meetup_api/foundry/#'.str_replace(' ', '-', strtolower($this->name)).'" target="_top">'.JText::_("COM_JFBCONNECT_WIDGET_MEETUP_ADD_THIS_TO_YOUR_SITE").'</a></div>';
        $footerHtml .= '</div>';

        return $footerHtml;
    }

    private function getNextEventHtml()
    {
        $parameters = array(
            'group_urlname' => $this->urlname,
            'page' => 1
        );

        $events = $this->getData('/2/events', $parameters);
        $group = $this->groups[0];

        $event = null;
        if(!empty($events) && is_array($events)) $event = $events[0];

        $nextEventHtml = "";
        if ($event)
            $nextEventHtml = '<h4><div class="mup-tlabel">'.$this->getDateTime($event->time, 'M d, Y', $group->timezone).' &nbsp; | &nbsp; '.$this->getDateTime($event->time, 'g:i A', $group->timezone).'</div><a href="'.$event->event_url.'" target="_top">'.$event->name.'</a><div class="mup-tlabel">'.$this->getLocation($event).'</div></h4>';

        return $nextEventHtml;
    }

    private function getLocation($event)
    {
        $venue = isset($event->venue) ? $event->venue : '';
        $group = $this->groups[0];

        $venue_addr = '';
        if ($venue) {
            if ($venue->name) {
                $venue_addr = $venue->name . " - ";
            } else if ($venue->address_1) {
                $venue_addr = $venue->address_1 . " - ";
            }
        }

        if (empty($venue) || empty($venue->state)) {
            if ($group->state == "") {
                $state_country = strtoupper($group->country);
            } else {
                $state_country = $group->state;
            }
        } else {
            $state_country = $venue->state;
        }

        $city = empty($venue) || empty($venue->city) ? $group->city : $venue->city;

        return $venue_addr . $city . ", " . $state_country;
    }

    private function addStarRating()
    {
        $url = 'http://img1.meetupstatic.com/9260736631256287675/img/star_';
        $starlink = '';

        if ($this->rating == 0) {
            return 'Not Yet Rated';
        }else if ($this->rating < 1.25) {
            $url .= "100.png";
        }else if ($this->rating < 1.75) {
            $url .= "150.png";
        }else if ($this->rating < 2.25) {
            $url .= "200.png";
        }else if ($this->rating < 2.75) {
            $url .= "250.png";
        }else if ($this->rating < 3.25) {
            $url .= "300.png";
        }else if ($this->rating < 3.75) {
            $url .= "350.png";
        }else if ($this->rating < 4.25) {
            $url .= "400.png";
        }else if ($this->rating < 4.75) {
            $url .= "450.png";
        }else {
            $url .= "500.png";
        }

        return "<img src='{$url}' alt='{$this->rating}' />";
    }
}