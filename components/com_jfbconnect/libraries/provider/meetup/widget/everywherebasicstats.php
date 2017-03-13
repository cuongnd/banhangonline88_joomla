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

class JFBConnectProviderMeetupWidgetEverywhereBasicStats extends JFBConnectProviderMeetupWidget
{
    public $name = "Everywhere Basic Stats";
    public $systemName = "everywherebasicstats";
    public $className = "sc_meetupeverywherebasicstats";
    public $tagName = "scmeetupeverywherebasicstats";
    public $examples = array (
        '{SCMeetupEverywhereBasicStats url_name=mashable}'
    );

    private $containers = array();

    protected function getTagHtml()
    {
        $width = $this->getParamValueEx('width', null, null, '200');
        $urlname = $this->getParamValueEx('url_name', null, null, '');
        $parameters = array(
            'urlname' => $urlname
        );

        $this->containers = $this->getData('/ew/containers', array_merge($parameters, array('fields' => 'meetup_count,past_meetup_count,member_count')));

        if (!defined('MEETUPCOMMONCSS'))
        {
            define('MEETUPCOMMONCSS', true);
            $doc = JFactory::getDocument();
            $doc->addStyleSheet('http://static2.meetupstatic.com/style/widget.css');
        }

        if (!defined('MEETUPEVERYWHEREBASICSTATSCSS'))
        {
            define('MEETUPEVERYWHEREBASICSTATSCSS', true);

            if(!isset($doc)) $doc = JFactory::getDocument();

            $doc->addStyleSheet(JURI::root(true).'/media/sourcecoast/css/widgets/meetup/everywherebasicstats.css');
        }

        $tag = '<div class="mup-widget-3" id="mup-widget-3" style="width:'.$width.'px;">';

        if($this->containers)
        {
            $tag .= '<div class="mup-widget">';
            $tag .= $this->getTagHtmlTop();
            $tag .= $this->getTagHtmlBody();
            $tag .= $this->getTagHtmlFooter();
            $tag .= '</div>';
        }
        else
        {
            $tag .= '<div class="mup-widget error"><div class="errorMsg">'.sprintf(JText::_("COM_JFBCONNECT_WIDGET_MEETUP_ERROR_NO_RESULTS"), $urlname).'</div></div>';
        }

        $tag .= '</div>';

        return $tag;
    }

    private function getTagHtmlTop()
    {
        $container = $this->containers[0];
        $total_meetup_count = $container->meetup_count + $container->past_meetup_count;
        $topHtml = '<div class="mup-hd">';
        $topHtml .= '<h3><a href="'.$container->meetup_url.'">'.number_format($total_meetup_count).' <span>'.$container->name.' Meetups</span></a></h3>';
        $topHtml .= '<h4>'.sprintf(JText::_('COM_JFBCONNECT_WIDGET_MEETUP_EVERYWHERE_COMMENTS_NUMBER_PEOPLE'), number_format($container->member_count)).'</h4>';
        $topHtml .= '</div>';

        return $topHtml;
    }

    private function getTagHtmlBody()
    {
        $key = JFBCFactory::config()->get('meetup_widget_api_key');

        $bodyHtml = '<div class="mup-bd">';
        $bodyHtml .= '<ul class="mup-list">';

        $doc = JFactory::getDocument();
        $urlname = $this->getParamValueEx('url_name', null, null, '');
        $num_events = $this->getParamValueEx('num_events', null, null, '3');
        $path = "http://api.meetup.com/ew/events?callback=?&key={$key}&urlname={$urlname}&status=upcoming&fields=rsvp_count,geo_ip";

        $script = "
        jfbcJQuery( document ).ready(function() {
            jfbcJQuery.getJSON('{$path}', function(data){
                var resultCount = 0;
                if (data.status && data.status.match(/^200/) == null) {
                    alert(data.status + \": \" + data.details);
                }else{
                    if( (resultCount = parseInt(data.meta.total_count)) ) {
                        var meetupArray = [],
                            R = 6371, // km
                            dLat = '',
                            dLon = '',
                            a = '',
                            c = '',
                            mylon = data.meta.geo_ip.lon,
                            mylat = data.meta.geo_ip.lat;
                        Number.prototype.toRad = function() {  // convert degrees to radians
                          return this * Math.PI / 180;
                        };
                        var months = [".JText::_('COM_JFBCONNECT_WIDGET_MEETUP_EVERYWHERE_BASIC_STATS_MONTHS')."],
                        addLeadingZero = function( num ) {
                            return (num < 10) ? ('0' + num) : num;
                        },
                        getTwoDigitYear = function( yr ) {
                            return yr.substring(2,4);
                        },
                        getFormattedDate = function( millis ) {
                            var date = new Date( millis );
                            return addLeadingZero( date.getDate() ) + ' ' + months[date.getMonth()] + ' ' + getTwoDigitYear( date.getFullYear().toString() );
                        },
                        getFormattedTime = function( millis ) {
                            var	time = new Date( millis ),
                                    hours = time.getHours(),
                                    min = time.getMinutes(),
                                    ampm = (hours > 11) ? 'PM' : 'AM';
                            min = (min < 10) ? ('0' + min) : min;
                            hours = (hours == 0) ? 1 : hours;
                            hours = (hours > 12) ? hours-12 : hours;
                            return hours + ':' + min + ' ' + ampm;
                        },
                        getFormattedCity = function( city, state ) {
                                return city + (state == undefined ? \"\" : \", \" + state);
                        },
                        getFormattedVenue = function( venue ) {
                            return (venue != null) ? venue : '".JText::_('COM_JFBCONNECT_WIDGET_MEETUP_EVERYWHERE_BASIC_STATS_VENUE_TBD')."';
                        },
                        addLink = function( content, link ) {
                            return '<a href=\"' + link + '\">' + content + '</a>';
                        };

                        jfbcJQuery.each(data.results, function(i, ev) {
                            if ( i < resultCount ) {
                                dLat = (ev.lat-mylat).toRad();  // Javascript functions in radians
                                dLon = (ev.lon-mylon).toRad();
                                a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                                        Math.cos(mylat.toRad()) * Math.cos(ev.lat.toRad()) *
                                        Math.sin(dLon/2) * Math.sin(dLon/2);
                                c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                                ev.dist = R * c; // Distance in km
                                meetupArray.push(ev);
                            }
                        });

                        meetupArray.sort( function(a, b) {return a.dist-b.dist;});

                        for (var i=0; i < {$num_events}; i++ ) {
                            jfbcJQuery('.mup-list').append(
                                '<li>\
                                    <div class=\"mup-when\">\
                                        <span class=\"mup-time\">' + getFormattedDate( meetupArray[i].time ) + '</span>\
                                        <span class=\"mup-city\">' + addLink( getFormattedCity( meetupArray[i].city, meetupArray[i].state ), meetupArray[i].meetup_url ) + '</span>\
                                    </div>\
                                    <div class=\"mup-where\">\
                                        <span class=\"mup-time\">' + getFormattedTime( meetupArray[i].time ) + '</span>\
                                        <span class=\"mup-venue\">' + getFormattedVenue( meetupArray[i].venue_name ) + '</span>\
                                    </div>\
                                    <span class=\"mup-badge\"><span class=\"mup-badge-label\">".JText::_('COM_JFBCONNECT_WIDGET_MEETUP_EVERYWHERE_BADGE_PEOPLE')." </span><span class=\"' +
                                    (i == 0 ? 'mup-rsvpcount-1' : 'mup-rsvpcount') + '\">' + meetupArray[i].rsvp_count + '</span></span>\
                                </li>'
                            );
                        }
                    }else{
                        jfbcJQuery('.mup-list').append('<li style=\"padding-left: 6px;\"><div class=\"mup-where\">".JText::_("COM_JFBCONNECT_WIDGET_MEETUP_EVERYWHERE_BASIC_STATS_NO_UPCOMING_MEETUPS")."</div></li>');
                    }
                }
            });
        });
        ";
        $doc->addScriptDeclaration($script);

        $bodyHtml .= '</ul>';
        $bodyHtml .= '</div>';

        return $bodyHtml;
    }

    private function getTagHtmlFooter()
    {
        $footerHtml = '<div class="mup-ft">';
        $footerHtml .= '<div class="mup-logo"><a href="http://www.meetup.com/everywhere/"><img src="http://img1.meetupstatic.com/84869143793177372874/img/birddog/everywhere_widget.png"></a></div>';
        $footerHtml .= '<div class="mup-getwdgt"><a href="http://www.meetup.com/meetup_api/foundry/#everywhere-basic-stats">'.JText::_("COM_JFBCONNECT_WIDGET_MEETUP_ADD_THIS_TO_YOUR_SITE").'</a></div>';
        $footerHtml .= '</div>';

        return $footerHtml;
    }
}