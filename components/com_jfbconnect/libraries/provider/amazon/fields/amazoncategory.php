<?php

/**
 * @package		JFBConnect
 * @copyright (C) 2009-2014 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldAmazonCategory extends JFormFieldList
{
    public $type = 'AmazonCategory';

    protected function getOptions()
    {
        $options = array();
        $options[] = JHtml::_('select.option', "All", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_ALL") );
        $options[] = JHtml::_('select.option', "UnboxVideo", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_AMAZON_INSTANT_VIDEO") );
        $options[] = JHtml::_('select.option', "Apparel", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_APPAREL_ACCESSORIES") );
        $options[] = JHtml::_('select.option', "Appliances", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_APPLIANCES") );
        $options[] = JHtml::_('select.option', "ArtsAndCrafts", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_ARTS_CRAFTS_SEWING") );
        $options[] = JHtml::_('select.option', "Automotive", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_AUTOMOTIVE") );
        $options[] = JHtml::_('select.option', "Baby", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_BABY") );
        $options[] = JHtml::_('select.option', "Beauty", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_BEAUTY") );
        $options[] = JHtml::_('select.option', "Books", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_BOOKS") );
        $options[] = JHtml::_('select.option', "Photo", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_CAMERA_PHOTO") );
        $options[] = JHtml::_('select.option', "Wireless", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_CELL_PHONE_ACCESSORIES") );
        $options[] = JHtml::_('select.option', "Classical", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_CLASSICAL_MUSIC") );
        $options[] = JHtml::_('select.option', "PCHardware", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_COMPUTERS") );
        $options[] = JHtml::_('select.option', "DVD", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_DVD") );
        $options[] = JHtml::_('select.option', "Electronics", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_ELECTRONICS") );
        $options[] = JHtml::_('select.option', "Collectibles", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_ENTERTAINMENT_COLLECTIBLES") );
        $options[] = JHtml::_('select.option', "VideoGames", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_GAME_DOWNLOADS") );
        $options[] = JHtml::_('select.option', "GiftCards", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_GIFT_CARDS") );
        $options[] = JHtml::_('select.option', "Grocery", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_GROCERY_GOURMET_FOOD") );
        $options[] = JHtml::_('select.option', "HomeGarden", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_HOME_GARDEN") );
        $options[] = JHtml::_('select.option', "HealthPersonalCare", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_HEALTH_PERSONAL_CARE") );
        $options[] = JHtml::_('select.option', "Industrial", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_INDUSTRIAL_SCIENTIFIC") );
        $options[] = JHtml::_('select.option', "Jewelry", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_JEWELRY") );
        $options[] = JHtml::_('select.option', "KindleStore", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_KINDLE_STORE") );
        $options[] = JHtml::_('select.option', "Kitchen", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_KITCHEN_HOUSEWARE") );
        $options[] = JHtml::_('select.option', "Magazines", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_MAGAZINE_SUBSCRIPTION") );
        $options[] = JHtml::_('select.option', "Miscellaneous", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_MISSCELLANEOUS") );
        $options[] = JHtml::_('select.option', "MP3Downloads", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_MP3_DOWNLOADS") );
        $options[] = JHtml::_('select.option', "Music", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_MUSIC") );
        $options[] = JHtml::_('select.option', "MusicalInstruments", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_MUSICAL_INSTRUMENTS") );
        $options[] = JHtml::_('select.option', "OfficeProducts", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_OFFICE_PRODUCTS") );
        $options[] = JHtml::_('select.option', "PetSupplies", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_PET_SUPPLIES") );
        $options[] = JHtml::_('select.option', "LawnAndGarden", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_PATIO_LAWN_GARDEN") );
        $options[] = JHtml::_('select.option', "Shoes", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_SHOES") );
        $options[] = JHtml::_('select.option', "Software", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_SOFTWARE") );
        $options[] = JHtml::_('select.option', "Collectibles", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_SPORTS_COLLECTIBLES") );
        $options[] = JHtml::_('select.option', "SportingGoods", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_SPORTS_OUTDOORS") );
        $options[] = JHtml::_('select.option', "Tools", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_TOOLS_HARDWARE") );
        $options[] = JHtml::_('select.option', "Toys", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_TOYS_GAMES") );
        $options[] = JHtml::_('select.option', "VHS", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_VHS") );
        $options[] = JHtml::_('select.option', "VideoGames", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_VIDEO_GAMES") );
        $options[] = JHtml::_('select.option', "Watches", JText::_("COM_JFBCONNECT_WIDGET_AMAZON_CATEGORY_OPTION_WATCHES") );

        return $options;

    }
}
