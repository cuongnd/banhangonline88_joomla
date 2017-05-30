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

class JFBConnectProviderAmazonWidgetCarousel extends JFBConnectProviderAmazonWidget
{
    var $name = "Carousel";
    var $systemName = "carousel";
    var $className = "sc_amzncarousel";
    var $tagName = "scamazoncarousel";
    var $examples = array (
        '{SCAmazonCarousel}',
        '{SCAmazonCarousel title=My Picks size=600x200 widget_type=Bestsellers title=New Video Game titles from Amazon tracking_id=jfbc-20}'
    );

    protected function getTagHtml()
    {
        $region = $this->getParamValueEx('region', null, null, 'US');

        $tracking_id = $this->getTrackingId($region);
        $title = addslashes($this->getParamValueEx('title', null, null, ''));
        $widget_type = $this->getParamValueEx('widget_type', null, null, 'Bestsellers');
        $size = $this->getParamValueEx('size', null, null, '250x250');

        list($width, $height) = explode('x', $size);

        $asin = str_replace(' ', '', $this->getParamValueEx('asin', null, null, ''));
        $browse_node = $this->getParamValueEx('browse_node', null, null, '');
        $keywords = $this->getParamValueEx('keywords', null, null, '');
        $search_index = $this->getParamValueEx('search_index', null, null, '');

        $shuffle_products = $this->getParamValueEx('shuffle_products', null, null, 1);
        $shuffle_products = $shuffle_products ? 'True' : 'False';
        $show_border = $this->getParamValueEx('show_border', null, null, 1);
        $show_border = $show_border ? 'True' : 'False';

        $tag = "
        <script type='text/javascript'>
            var amzn_wdgt={widget:'Carousel'};
            amzn_wdgt.tag='{$tracking_id}';
            amzn_wdgt.widgetType='{$widget_type}';
            amzn_wdgt.title='{$title}';
            amzn_wdgt.width='{$width}';
            amzn_wdgt.height='{$height}';
            amzn_wdgt.marketPlace='{$region}';
            amzn_wdgt.shuffleProducts='{$shuffle_products}';
            amzn_wdgt.showBorder='{$show_border}';
        ";

        if($widget_type == 'ASINList')
            $tag .= "amzn_wdgt.ASIN='{$asin}';";

        if($widget_type != 'ASINList' && !empty($search_index))
            $tag .= "amzn_wdgt.searchIndex='{$search_index}';";

        if($widget_type != 'ASINList' && !empty($browse_node))
            $tag .= "amzn_wdgt.browseNode='{$browse_node}';";

        if($widget_type == 'SearchAndAdd')
            $tag .= "amzn_wdgt.keywords='{$keywords}';";

        $tag .="
        </script>
        ";

        $tag .= $this->loadJavascript();
        return $tag;
    }


}