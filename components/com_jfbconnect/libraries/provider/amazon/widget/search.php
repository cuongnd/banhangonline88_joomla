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

class JFBConnectProviderAmazonWidgetSearch extends JFBConnectProviderAmazonWidget
{
    var $name = "Search";
    var $systemName = "search";
    var $className = "sc_amznsearch";
    var $tagName = "scamazonsearch";
    var $examples = array(
            '{SCAmazonSearch}',
            '{SCAmazonSearch default_search_key=Joomla default_search_category=Books theme=dark tracking_id=jfbc-20}'
    );

    protected function getTagHtml()
    {
        $region = $this->getParamValueEx('region', null, null, 'US');
        $trackingId = $this->getTrackingId($region);

        $defaultSearchKey = $this->getParamValueEx('default_search_key', null, null, '');
        $defaultSearchCategory = $this->getParamValueEx('default_search_category', null, null, '');
        $searchType = $this->getParamValueEx('search_type', null, null, 'search_widget');

        $size = $this->getParamValueEx('size', null, null, 'auto'); // Auto or custom?
        if ($size == "custom")
        {
            $width = $this->getParamValueEx('width', null, null, 'auto');
            $height = $this->getParamValueEx('height', null, null, 'auto');
        }
        else
            $width = $height = 'auto';

        $theme = $this->getParamValueEx('theme', null, null, 'light');
        $backgroundhue = $this->getParamValueEx('backgroundhue', null, null, '#FFFFFF');
        $bgColor = str_replace('#', '', $backgroundhue);

        $tag = '<script charset="utf-8" type="text/javascript">
            amzn_assoc_ad_type = "responsive_search_widget";
            amzn_assoc_tracking_id = "' . $trackingId . '";
            amzn_assoc_marketplace = "amazon";
            amzn_assoc_region = "' . $region . '";
            amzn_assoc_placement = "";
            amzn_assoc_search_type = "' . $searchType . '";
            amzn_assoc_width = "' . $width . '";
            amzn_assoc_height = "' . $height . '";
            amzn_assoc_default_search_category = "' . $defaultSearchCategory . '";
            amzn_assoc_default_search_key = "' . $defaultSearchKey . '";
            amzn_assoc_theme = "' . $theme . '";
            amzn_assoc_bg_color = "' . $bgColor . '";
        </script>';


        $tag .= $this->loadJavascript();
        return $tag;
    }
}