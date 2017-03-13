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

class JFBConnectProviderAmazonWidgetMP3Clips extends JFBConnectProviderAmazonWidget
{
    var $name = "MP3 Clips";
    var $systemName = "mp3clips";
    var $className = "sc_amznmp3clips";
    var $tagName = "scamazonmp3clips";
    var $examples = array (
        '{SCAmazonMP3Clips}',
        '{SCAmazonMP3Clips title=My Picks size=250x250 ASIN=B000YN369A,B0016MJ2PU,B001LK1LA6 tracking_id=jfbc-20}'
    );

    protected function getTagHtml()
    {
        $region = $this->getParamValueEx('region', null, null, 'US');
        $trackingId = $this->getTrackingId($region);
        $title = addslashes($this->getParamValueEx('title', null, null, ''));
        $size = $this->getParamValueEx('size', null, null, '250x250');

        list($width, $height) = explode('x', $size);

        $shuffle_tracks = $this->getParamValueEx('shuffle_tracks', null, null, 1);
        $shuffle_tracks = $shuffle_tracks ? 'True' : 'False';

        $asin = str_replace(' ', '', $this->getParamValueEx('asin', null, null, ''));

        $widget_type = $this->getParamValueEx('widget_type', null, null, 'ASINList');
        $browse_node = $this->getParamValueEx('browse_node', null, null, '');
        $keywords = $this->getParamValueEx('keywords', null, null, '');
        $max_results = $this->getParamValueEx('max_results', null, null, '');

        $tag = "
        <script type='text/javascript'>
        var amzn_wdgt={widget:'MP3Clips'};
        amzn_wdgt.tag='{$trackingId}';
        amzn_wdgt.widgetType='{$widget_type}';
        amzn_wdgt.title='{$title}';
        amzn_wdgt.width='{$width}';
        amzn_wdgt.height='{$height}';
        amzn_wdgt.shuffleTracks='{$shuffle_tracks}';
        amzn_wdgt.marketPlace='{$region}';
        ";

        if($widget_type == 'ASINList')
            $tag .= " amzn_wdgt.ASIN='{$asin}';";

        if($widget_type != 'ASINList' && !empty($browse_node))
            $tag .= "amzn_wdgt.browseNode='{$browse_node}';";

        if($widget_type == 'SearchAndAdd')
            $tag .= "amzn_wdgt.keywords='{$keywords}';";

        if($max_results)
            $tag .= "amzn_wdgt.maxResults='{$max_results}';";

        $tag .="
        </script>
        ";

        $tag .= $this->loadJavascript();

        return $tag;
    }


}