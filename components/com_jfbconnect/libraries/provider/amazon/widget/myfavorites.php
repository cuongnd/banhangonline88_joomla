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

class JFBConnectProviderAmazonWidgetMyFavorites extends JFBConnectProviderAmazonWidget
{
    var $name = "My Favorites";
    var $systemName = "myfavorites";
    var $className = "sc_amznmyfavorites";
    var $tagName = "scamazonmyfavorites";
    var $examples = array (
        '{SCAmazonMyFavorites}',
        '{SCAmazonMyFavorites title=Joss Whedon: Great Shows ASIN=B0000AQS0F,B001M5UDGS,B0046XG48O theme=Orange tracking_id=jfbc-20}'
    );

    protected function getTagHtml()
    {
        $region = $this->getParamValueEx('region', null, null, 'US');
        $trackingId = $this->getTrackingId($region);
        $title = addslashes($this->getParamValueEx('title', null, null, ''));

        $columns = $this->getParamValueEx('columns', null, null, '1');
        $rows = $this->getParamValueEx('rows', null, null, '3');
        $width = $this->getParamValueEx('width', null, null, '250');
        $asin = str_replace(' ', '', $this->getParamValueEx('asin', null, null, ''));

        $shuffle_products = $this->getParamValueEx('show_image', null, null, 1);
        $show_image = $this->getParamValueEx('show_image', null, null, 1);
        $show_price = $this->getParamValueEx('show_price', null, null, 1);
        $show_rating = $this->getParamValueEx('show_rating', null, null, 1);

        $shuffle_products = $shuffle_products ? 'True' : 'False';
        $show_image = $show_image ? 'True' : 'False';
        $show_price = $show_price ? 'True' : 'False';
        $show_rating = $show_rating ? 'True' : 'False';

        $theme = $this->getParamValueEx('theme', null, null, '2.Default');
        list($design, $color_theme) = explode('.', $theme);

        $customized = $this->getParamValueEx('customized', null, null, '0');

        $tag = "
        <script type='text/javascript'>
        var amzn_wdgt={widget:'MyFavorites'};
        amzn_wdgt.tag='{$trackingId}';
        amzn_wdgt.columns='{$columns}';
        amzn_wdgt.rows='{$rows}';
        amzn_wdgt.title='{$title}';
        amzn_wdgt.width='{$width}';
        amzn_wdgt.ASIN='{$asin}';
        amzn_wdgt.shuffleProducts='{$shuffle_products}';
        amzn_wdgt.showImage='{$show_image}';
        amzn_wdgt.showPrice='{$show_price}';
        amzn_wdgt.showRating='{$show_rating}';
        amzn_wdgt.design='{$design}';
        amzn_wdgt.colorTheme='{$color_theme}';
        ";

        if($customized){
            $override = $this->getParamValueEx('override', null, null, '');

            if($design == 1 || $design == 2)
                $tag .= " amzn_wdgt.outerBackgroundColor='{$override->outer_bg}'; ";

            if($design == 1)
                $tag .= " amzn_wdgt.innerBackgroundColor='{$override->inner_bg}'; ";

            if($design == 2){
                $tag .= "
                amzn_wdgt.backgroundColor='{$override->bg}';
                amzn_wdgt.borderColor='{$override->border}';
                ";
            }

            $tag .= "
                amzn_wdgt.headerTextColor='{$override->header_txt}';
                amzn_wdgt.linkedTextColor='{$override->linked_txt}';
                amzn_wdgt.bodyTextColor='{$override->body_txt}';
            ";
        }

        $tag .= "
        amzn_wdgt.marketPlace='{$region}';
        </script>
        ";

        $tag .= $this->loadJavascript();

        return $tag;
    }


}