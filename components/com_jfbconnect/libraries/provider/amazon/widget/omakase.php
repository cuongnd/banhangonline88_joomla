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

class JFBConnectProviderAmazonWidgetOmakase extends JFBConnectProviderAmazonWidget
{
    var $name = "Omakase";
    var $systemName = "omakase";
    var $className = "sc_amznomakase";
    var $tagName = "scamazonomakase";
    var $examples = array (
        '{SCAmazonOmakase size=728x90 tracking_id=jfbc-20}'
    );

    protected function getTagHtml()
    {
        $region = $this->getParamValueEx('region', null, null, 'US');
        $trackingId = $this->getTrackingId($region);
        $size = $this->getParamValueEx('size', null, null, '728x90');

        list($width, $height) = explode('x', $size);

        $logo = $this->getParamValueEx('logo', null, null, 'show');
        $images = $this->getParamValueEx('images', null, null, 'show');
        $link = $this->getParamValueEx('link', null, null, 'same');
        $prices = $this->getParamValueEx('prices', null, null, 'all');
        $border = $this->getParamValueEx('border', null, null, 'show');
        $discount = $this->getParamValueEx('discount', null, null, 'add');

        $border_color = strtoupper($this->getParamValueEx('border_color', null, null, '#000000'));
        $background_color = strtoupper($this->getParamValueEx('background_color', null, null, '#FFFFFF'));
        $details_color = strtoupper($this->getParamValueEx('details_color', null, null, '#000000'));
        $link_color = strtoupper($this->getParamValueEx('link_color', null, null, '#3399FF'));
        $price_color = strtoupper($this->getParamValueEx('price_color', null, null, '#990000'));
        $amazon_color = strtoupper($this->getParamValueEx('amazon_color', null, null, '#CC6600'));

        $tag = "<script type='text/javascript'>";
        $tag .= " amazon_ad_tag = '{$trackingId}'; amazon_ad_width = '{$width}'; amazon_ad_height = '{$height}'; ";

        if($logo == 'hide')
            $tag .= "amazon_ad_logo = 'hide'; ";

        if($images == 'hide')
            $tag .= "amazon_ad_product_images = 'hide'; ";

        if($link == 'new')
            $tag .= "amazon_ad_link_target = 'new'; ";

        if($prices == 'retail')
            $tag .= "amazon_ad_price = 'retail'; ";

        if($border == 'hide')
            $tag .= "amazon_ad_border = 'hide'; ";

        if($discount == 'remove')
            $tag .= "amazon_ad_discount = 'remove'; ";

        if($border_color != '#000000')
            $tag .= "amazon_color_border = '".$this->remove($border_color)."'; ";

        if($background_color != '#FFFFFF')
            $tag .= "amazon_color_background = '".$this->remove($background_color)."'; ";

        if($details_color != '#000000')
            $tag .= "amazon_color_text = '".$this->remove($details_color)."'; ";

        if($link_color != '#3399FF')
            $tag .= "amazon_color_link = '".$this->remove($link_color)."'; ";

        if($price_color != '#990000')
            $tag .= "amazon_color_price = '".$this->remove($price_color)."'; ";

        if($amazon_color != '#CC6600')
            $tag .= "amazon_color_logo = '".$this->remove($amazon_color)."'; ";

        $tag .= "</script>";

        $tag .= $this->loadJavascript();

        return $tag;
    }

    private function remove($str)
    {
        return str_replace('#', '', $str);
    }
}