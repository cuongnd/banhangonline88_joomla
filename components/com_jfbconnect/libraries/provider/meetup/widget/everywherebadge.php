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

class JFBConnectProviderMeetupWidgetEverywhereBadge extends JFBConnectProviderMeetupWidget
{
    public $name = "Everywhere Badge";
    public $systemName = "everywherebadge";
    public $className = "sc_meetupeverywherebadge";
    public $tagName = "scmeetupeverywherebadge";
    public $examples = array (
        '{SCMeetupEverywhereBadge url_name=techcrunch}'
    );

    private $containers = array();

    protected function getTagHtml()
    {
        $width = $this->getParamValueEx('width', null, null, '200');
        $urlname = $this->getParamValueEx('url_name', null, null, '');
        $parameters = array(
            'urlname' => $urlname,
            'fields' => 'meetup_count,past_meetup_count,member_count,geo_ip'
        );

        $this->containers = $this->getData('/ew/containers', $parameters);

        if (!defined('MEETUPCOMMONCSS'))
        {
            define('MEETUPCOMMONCSS', true);
            $doc = JFactory::getDocument();
            $doc->addStyleSheet('http://static2.meetupstatic.com/style/widget.css');
        }

        if (!defined('MEETUPEVERYWHEREBADGECSS'))
        {
            define('MEETUPEVERYWHEREBADGECSS', true);

            if(!isset($doc)) $doc = JFactory::getDocument();

            $doc->addStyleSheet(JURI::root(true).'/media/sourcecoast/css/widgets/meetup/everywherebadge.css');
        }

        $tag = '<div class="mup-widget-2" id="mup-widget-2" style="width:'.$width.'px;">';

        if($this->containers)
        {
            $tag .= '<div class="mup-widget">';
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

    private function getTagHtmlBody()
    {
        $container = $this->containers[0];
        $total_meetup_count = $container->meetup_count + $container->past_meetup_count;
        $bodyHtml = '<div class="mup-bd">';
        $bodyHtml .= '<h3><a href="'.$container->meetup_url.'">'.$container->name.'</a></h3>';
        $bodyHtml .= '<span class="mup-stats">'.number_format($total_meetup_count).'<span class="mup-tlabel"> MEETUPS</span> <span class="mup-stats-divider">|</span> '.number_format($container->member_count).'<span class="mup-tlabel"> '.JText::_('COM_JFBCONNECT_WIDGET_MEETUP_EVERYWHERE_BADGE_PEOPLE').'</span></span>';
        $bodyHtml .= '<div class="mup-find-meetup"><span>'.JText::_('COM_JFBCONNECT_WIDGET_MEETUP_EVERYWHERE_BADGE_FIND_A_MEETUP_NEAR_YOU').' </span><span class="mup-button"><a href="'.$container->meetup_url.'">'.JText::_('COM_JFBCONNECT_WIDGET_MEETUP_EVERYWHERE_BADGE_GO').'</a></span></div>';
        $bodyHtml .= '</div>';

        return $bodyHtml;
    }

    private function getTagHtmlFooter()
    {
        $container = $this->containers[0];

        $footerHtml = '<div class="mup-ft">';
        $footerHtml .= '<div class="mup-logo"><a href="'.$container->meetup_url.'"><img src="http://img1.meetupstatic.com/84869143793177372874/img/birddog/everywhere_widget.png"></a></div>';
        $footerHtml .= '<div class="mup-getwdgt"><a href="http://www.meetup.com/meetup_api/foundry/#everywhere-badge">'.JText::_("COM_JFBCONNECT_WIDGET_MEETUP_ADD_THIS_TO_YOUR_SITE").'</a></div>';
        $footerHtml .= '</div>';

        return $footerHtml;
    }
}