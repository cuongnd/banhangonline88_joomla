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

class JFBConnectProviderAmazonWidget extends JFBConnectWidget
{
    private function getCountryCode($region)
    {
        if ($region == "CA")
            return "-20";
        if ($region == "US")
            return "-20";
        if ($region == "GB")
            return "-21";
        if ($region == "DE")
            return "-21";
        if ($region == "ES")
            return "-21";
        if ($region == "FR")
            return "-21";
        if ($region == "IT")
            return "-21";
        if ($region == "JP")
            return "-22";
        if ($region == "BR") // Brazil - Not currently used
            return "-20";
        if ($region == "CN") // China - Not currently used
            return "-23";

        return "123";
    }

    protected function loadJavascript()
    {
        $region = $this->getParamValueEx('region', null, null, 'US');
        switch ($region)
        {
            case 'GB':
                $omakaseRegion = "uk";
                $zPath = 'eu';
                break;
            case 'DE':
                $omakaseRegion = "de";
                $zPath = "eu";
                break;
            case 'FR':
                $zPath = 'eu';
                break;
            case 'IT':
                $omakaseRegion = "na";
                $zPath = 'eu';
                break;
            case 'JP':
                $zPath = 'fe';
                $omakaseRegion = "jp";
                break;
            case 'CA':
                $zPath = 'na';
                break;
            case 'US':
            default:
                $zPath = 'na';
                $omakaseRegion = 'na';
        }

        $script = "";
        switch ($this->systemName)
        {
            case "search":
                // Search
                // https://affiliate-program.amazon.com/gp/associates/promo/search-widget.html
                $script = '//z-' . $zPath . '.amazon-adsystem.com/widgets/q?ServiceVersion=20070822&Operation=GetScript&ID=OneJS&WS=1&MarketPlace=' . $region;
                break;
            case "carousel":
            case "mp3clips" :
                // https://widgets.amazon.com/Widget-Source/?store=cookallefree-20&tag=jfbc-20
                // Carousel
                $script = '//wms-' . $zPath . '.amazon-adsystem.com/20070822/' . $region . '//js/swfobject_1_5.js'; // US - tags
                break;
            case "myfavorites":
                // My Favorites
                $script = '//wms-' . $zPath . '.amazon-adsystem.com/20070822/' . $region . '//js/AmazonWidgets.js';
                break;
            case "omakase":
                // Omakase
                $script = '//ir-' . $omakaseRegion . '.amazon-adsystem.com/s/ads.js';
                break;
        }
        return '<script src="' . $script . '"></script>';
    }

    protected function getTrackingId($region)
    {
        $trackingId = $this->getParamValueEx('tracking_id', null, null, '');

        $countryCode = substr($trackingId, -3);
        if ($countryCode == $this->getCountryCode($region))
            return $trackingId;

        return "jfbc" . strtolower($region) . $this->getCountryCode($region);
    }

}
