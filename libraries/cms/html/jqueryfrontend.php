<?php/** * @package     Joomla.Libraries * @subpackage  HTML * * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved. * @license     GNU General Public License version 2 or later; see LICENSE */defined('JPATH_PLATFORM') or die;/** * Utility class for jQuery JavaScript behaviors * * @since  3.0 */abstract class JHtmlJqueryFrontend{    /**     * @var    array  Array containing information for loaded files     * @since  3.0     */    protected static $loaded = array();    /**     * Method to load the jQuery JavaScript framework into the document head     *     * If debugging mode is on an uncompressed version of jQuery is included for easier debugging.     *     * @param   boolean $noConflict True to load jQuery in noConflict mode [optional]     * @param   mixed $debug Is debugging mode on? [optional]     * @param   boolean $migrate True to enable the jQuery Migrate plugin     *     * @return  void     *     * @since   3.0     */    public static function framework($noConflict = true, $debug = null, $migrate = true)    {        // Only load once        if (!empty(static::$loaded[__METHOD__])) {            return;        }        // If no debugging value is set, use the configuration setting        $doc = JFactory::getDocument();        $doc->addScript('/media/juifrontend/js/jquery.min-1.9.1.js');        $doc->addScript('/media/juifrontend/js/jquery-noconflict.min.js');        $doc->addScript('/media/juifrontend/js/jquery-migrate.min.js');        $doc->addScript('/media/system/js/frontend/jquery.easing.1.3.min.js');        // Check if we are loading in noConflict        static::$loaded[__METHOD__] = true;        return;    }    /**     * Method to load the jQuery UI JavaScript framework into the document head     *     * If debugging mode is on an uncompressed version of jQuery UI is included for easier debugging.     *     * @param   array $components The jQuery UI components to load [optional]     * @param   mixed $debug Is debugging mode on? [optional]     *     * @return  void     *     * @since   3.0     */    public static function ui(array $components = array('core'), $debug = null)    {        // Set an array containing the supported jQuery UI components handled by this method        $supported = array('core', 'sortable');        // Include jQuery        static::framework();        // If no debugging value is set, use the configuration setting        if ($debug === null) {            $config = JFactory::getConfig();            $debug = (boolean)$config->get('debug');        }        // Load each of the requested components        foreach ($components as $component) {            // Only attempt to load the component if it's supported in core and hasn't already been loaded            if (in_array($component, $supported) && empty(static::$loaded[__METHOD__][$component])) {                JHtml::_('script', 'jui/jquery.ui.' . $component . '.min.js', false, true, false, false, $debug);                static::$loaded[__METHOD__][$component] = true;            }        }        return;    }    public static function cookie($debug = null)    {        // Include jQuery        static::framework();        // If no debugging value is set, use the configuration setting        // Only attempt to load the component if it's supported in core and hasn't already been loaded        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript('/media/system/js/jquery-cookie-master/src/jquery.cookie.min.js');            static::$loaded[__METHOD__] = true;        }        return;    }    public static function dreymodal($debug = null)    {        // Include jQuery        static::framework();        // If no debugging value is set, use the configuration setting        // Only attempt to load the component if it's supported in core and hasn't already been loaded        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript('/media/system/js/Animated-jQuery-Modal/src/js/dreyanim.js');            $doc->addScript('/media/system/js/Animated-jQuery-Modal/src/js/dreymodal.js');            $doc->addStyleSheet('/media/system/js/Animated-jQuery-Modal/src/css/dreyanim.css');            $doc->addStyleSheet('/media/system/js/Animated-jQuery-Modal/src/css/dreymodal.css');            static::$loaded[__METHOD__] = true;        }        return;    }    public static function core($debug = null)    {        // Include jQuery        static::framework();        // If no debugging value is set, use the configuration setting        // Only attempt to load the component if it's supported in core and hasn't already been loaded        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript('/templates/core.min.js');            static::$loaded[__METHOD__] = true;        }        return;    }    public static function gmaps($debug = null)    {        // Include jQuery        static::framework();        // If no debugging value is set, use the configuration setting        // Only attempt to load the component if it's supported in core and hasn't already been loaded        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript('/media/system/js/frontend/jquery.picker_gmaps/js/google_map_api.js');            $doc->addScript('/media/system/js/frontend/gmaps-master/gmaps.js');            static::$loaded[__METHOD__] = true;        }        return;    }    public static function pickup_location($debug = null)    {        // Include jQuery        static::framework();        // If no debugging value is set, use the configuration setting        // Only attempt to load the component if it's supported in core and hasn't already been loaded        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript('/media/system/js/frontend/jquery.picker_gmaps/js/google_map_api.js');            $doc->addScript('/media/system/js/frontend/jquery.picker_gmaps/js/jquery-gmaps-latlon-picker.js');            $doc->addStyleSheet('/media/system/js/frontend/jquery.picker_gmaps/css/jquery-gmaps-latlon-picker.css');            static::$loaded[__METHOD__] = true;        }        return;    }    public static function template($debug = null)    {        // Include jQuery        static::framework();        // If no debugging value is set, use the configuration setting        // Only attempt to load the component if it's supported in core and hasn't already been loaded        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript('/templates/vina_bonnie/js/template.js');            static::$loaded[__METHOD__] = true;        }        return;    }    public static function utility($debug = null)    {        // Include jQuery        static::framework();        // If no debugging value is set, use the configuration setting        // Only attempt to load the component if it's supported in core and hasn't already been loaded        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript('/media/system/js/frontend/jquery.utility.min.js');            static::$loaded[__METHOD__] = true;        }        return;    }    public static function hikashop($debug = null)    {        // Include jQuery        static::framework();        // If no debugging value is set, use the configuration setting        if ($debug === null) {            $config = JFactory::getConfig();            $debug = (boolean)$config->get('debug');        }        // Only attempt to load the component if it's supported in core and hasn't already been loaded        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript('/media/com_hikashop/js/hikashop.min.js');            static::$loaded[__METHOD__] = true;        }        return;    }    public static function sly($debug = null)    {        // Include jQuery        static::framework();        // If no debugging value is set, use the configuration setting        // Only attempt to load the component if it's supported in core and hasn't already been loaded        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript('/media/system/js/sly-master/dist/sly.min.js');            static::$loaded[__METHOD__] = true;        }        return;    }    public static function long_text_truncating($debug = null)    {        // Include jQuery        static::framework();        // If no debugging value is set, use the configuration setting        // Only attempt to load the component if it's supported in core and hasn't already been loaded        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript('/media/system/js/long_text_truncating/src/jquery.dotdotdot.min.js');            static::$loaded[__METHOD__] = true;        }        return;    }    public static function lazyload($debug = null)    {        static::framework();        // If no debugging value is set, use the configuration setting        if ($debug === null) {            $config = JFactory::getConfig();            $debug = (boolean)$config->get('debug');        }        // Only attempt to load the component if it's supported in core and hasn't already been loaded        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript('/media/system/js/lazy-load-xt-master/dist/jquery.lazyloadxt.min.js');            $doc->addScript('/media/system/js/lazy-load-xt-master/dist/jquery.lazyloadxt.extra.min.js');            $doc->addStyleSheet(JUri::root() . 'media/system/js/lazy-load-xt-master/dist/jquery.lazyloadxt.fadein.css');            static::$loaded[__METHOD__] = true;        }        return;    }    public static function countdown($debug = null)    {        static::framework();        // If no debugging value is set, use the configuration setting        if ($debug === null) {            $config = JFactory::getConfig();            $debug = (boolean)$config->get('debug');        }        // Only attempt to load the component if it's supported in core and hasn't already been loaded        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript('/media/system/js/jquery.countdown/dest/jquery.countdown.js');            static::$loaded[__METHOD__] = true;        }        return;    }    public static function sprintf($debug = null)    {        static::framework();        // If no debugging value is set, use the configuration setting        if ($debug === null) {            $config = JFactory::getConfig();            $debug = (boolean)$config->get('debug');        }        // Only attempt to load the component if it's supported in core and hasn't already been loaded        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript('/media/system/js/jquery-sprintf/jquery.sprintf.js');            static::$loaded[__METHOD__] = true;        }        return;    }    public static function moment($debug = null)    {        static::framework();        // If no debugging value is set, use the configuration setting        if ($debug === null) {            $config = JFactory::getConfig();            $debug = (boolean)$config->get('debug');        }        // Only attempt to load the component if it's supported in core and hasn't already been loaded        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript('/media/system/js/moment-develop/moment.js');            static::$loaded[__METHOD__] = true;        }        return;    }    public static function bootstrap_notify($debug = null)    {        static::framework();        // If no debugging value is set, use the configuration setting        if ($debug === null) {            $config = JFactory::getConfig();            $debug = (boolean)$config->get('debug');        }        // Only attempt to load the component if it's supported in core and hasn't already been loaded        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript('/media/system/js/bootstrap-notify-master/bootstrap-notify.js');            static::$loaded[__METHOD__] = true;        }        return;    }    public static function serialize_object($debug = null)    {        static::framework();        // If no debugging value is set, use the configuration setting        if ($debug === null) {            $config = JFactory::getConfig();            $debug = (boolean)$config->get('debug');        }        // Only attempt to load the component if it's supported in core and hasn't already been loaded        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript('/media/system/js/jquery.serializeObject.js');            static::$loaded[__METHOD__] = true;        }        return;    }    public static function webui_popover($debug = null)    {        $jquery_webui_popover_compress = false;        $jquery_webui_popover_compress_css = true;        // Include jQuery        static::framework();        // If no debugging value is set, use the configuration setting        if ($debug === null) {            $config = JFactory::getConfig();            $debug = (boolean)$config->get('debug');        }        // Only attempt to load the component if it's supported in core and hasn't already been loaded        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript('/media/system/js/webui-popover-1.2.17/src/jquery.webui-popover.min.js');            $doc->addLessStyleSheet('/media/system/js/webui-popover-1.2.17/src/jquery.webui-popover.less');            static::$loaded[__METHOD__] = true;        }        return;    }    public static function hikamarket($debug = null)    {        $jquery_hikamarket_compress = true;        $jquery_hikamarket_compress_css = true;        // Include jQuery        static::framework();        // If no debugging value is set, use the configuration setting        if ($debug === null) {            $config = JFactory::getConfig();            $debug = (boolean)$config->get('debug');        }        // Only attempt to load the component if it's supported in core and hasn't already been loaded        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript('/media/com_hikamarket/js/hikamarket.min.js');            static::$loaded[__METHOD__] = true;        }        return;    }    public static function less($debug = null)    {        $jquery_less_compress = true;        $jquery_less_compress_css = true;        // Include jQuery        static::framework();        // If no debugging value is set, use the configuration setting        // Only attempt to load the component if it's supported in core and hasn't already been loaded        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript("/media/system/js/less-1.5.0.js");            static::$loaded[__METHOD__] = true;        }        return;    }    public static function sidr($debug = null)    {        $jquery_sidr_compress = false;        $jquery_sidr_compress_css = false;        // Include jQuery        static::framework();        // If no debugging value is set, use the configuration setting        if ($debug === null) {            $config = JFactory::getConfig();            $debug = (boolean)$config->get('debug');        }        // Only attempt to load the component if it's supported in core and hasn't already been loaded        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript("/media/system/js/sidr-master/dist/jquery.sidr.min.js");            $doc->addStyleSheet("/media/system/js/sidr-master/dist/stylesheets/jquery.sidr.light.css");            static::$loaded[__METHOD__] = true;        }        return;    }    public static function help_step($debug = null)    {        JHtml::_('jQuery.auo_typing_text');        JHtml::_('jQuery.texttospeak');        // Include jQuery        static::framework();        // If no debugging value is set, use the configuration setting        // Only attempt to load the component if it's supported in core and hasn't already been loaded        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript("/media/system/js/intro.js-2.3.0/intro.js");            $doc->addStyleSheet("/media/system/js/sidr-master/dist/stylesheets/jquery.sidr.light.css");            static::$loaded[__METHOD__] = true;        }        return;    }    public static function auo_typing_text($debug = null)    {        $jquery_auo_typing_text_compress = true;        $jquery_auo_typing_text_css = true;        // Include jQuery        static::framework();        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript("/media/system/js/auo_typing_text/jquery.teletype.js");            static::$loaded[__METHOD__] = true;        }        return;    }    public static function suggestion($debug = null)    {        $jquery_auo_typing_text_compress = true;        $jquery_auo_typing_text_css = true;        // Include jQuery        static::framework();        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript("/media/system/js/jquery-flexdatalist-1.9.2/jquery.flexdatalist.js");            $doc->addLessStyleSheet("/media/system/js/jquery-flexdatalist-1.9.2/jquery.flexdatalist.css");            static::$loaded[__METHOD__] = true;        }        return;    }    public static function uisortable($debug = null)    {        $jquery_auo_typing_text_compress = true;        $jquery_auo_typing_text_css = true;        // Include jQuery        static::framework();        static::ui();        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript("/media/system/js/backend/jquery.ui.sortable.js");            static::$loaded[__METHOD__] = true;        }        return;    }    public static function picker_gmaps($debug = null)    {        $jquery_auo_typing_text_compress = true;        $jquery_auo_typing_text_css = true;        // Include jQuery        static::framework();        static::ui();        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript('/media/system/js/backend/jquery.picker_gmaps/js/google_map_api.js');            $doc->addScript('/media/system/js/backend/jquery.picker_gmaps/js/jquery-gmaps-latlon-picker.js');            $doc->addStyleSheet('/media/system/js/backend/jquery.picker_gmaps/css/jquery-gmaps-latlon-picker.css');            static::$loaded[__METHOD__] = true;        }        return;    }    public static function feather($debug = null)    {        // Include jQuery        static::framework();        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript("/media/system/js/aviary/feather.js");            static::$loaded[__METHOD__] = true;        }        return;    }    public static function modal($debug = null)    {        // Include jQuery        static::framework();        // If no debugging value is set, use the configuration setting        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript("/media/system/js/jquery.avgrund.js-master/jquery.avgrund.js");            $doc->addStyleSheet(JUri::root() . 'media/system/js/jquery.avgrund.js-master/style/avgrund.css');            static::$loaded[__METHOD__] = true;        }        return;    }    public static function scrollto($debug = null)    {        $jquery_scrollto_compress = true;        $jquery_scrollto_css_compress = true;        // Include jQuery        static::framework();        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript("/media/system/js/jquery.scrollTo-master/jquery.scrollTo.js");            static::$loaded[__METHOD__] = true;        }        return;    }    public static function flip($debug = null)    {        // Include jQuery        static::framework();        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript("/media/system/js/jquery.flip/dist/jquery.flip.min.js");            static::$loaded[__METHOD__] = true;        }        return;    }    public static function texttospeak($debug = null)    {        // Include jQuery        static::framework();        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript("/media/system/js/texttospeak/responsivevoice.js");            static::$loaded[__METHOD__] = true;        }        return;    }    public static function notify($debug = null)    {        // Include jQuery        static::framework();        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript("/media/system/js/bootstrap-notify-master/bootstrap-notify.js");            static::$loaded[__METHOD__] = true;        }        return;    }    public static function jquery_load_file($debug = null)    {        $app = JFactory::getApplication();        $menu = $app->getMenu();        $active_menu = $menu->getActive();        if (!$active_menu) {            $active_menu = $menu->getDefault();        }        $params = $active_menu->params;        $jquery_load_file = $params->get('jquery_load_file', '');        if (!$jquery_load_file) {            return;        }        JHtml::_('jQuery.help_step');        $jquery_load_file_compress = true;        $jquery_load_file_css_compress = true;        // Include jQuery        static::framework();        // Only attempt to load the component if it's supported in core and hasn't already been loaded        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript('/media/system/js/jquery_load_file/' . $jquery_load_file);            $js_content = '';            ob_start();            ?>            <script type="text/javascript">                jQuery(document).ready(function ($) {                    $("body").load_page({                        show_help: false,                        enable_audio: true                    });                });            </script>            <?php            $js_content = ob_get_clean();            $js_content = JUtility::remove_string_javascript($js_content);            $doc->addScriptDeclaration($js_content);            static::$loaded[__METHOD__] = true;        }        return;    }    public static function select($debug = null)    {        $app = JFactory::getApplication();        // Include jQuery        static::framework();        // Only attempt to load the component if it's supported in core and hasn't already been loaded        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc = JFactory::getDocument();            $doc->addScript("/media/system/js/select2-4.0.0/dist/js/select2.full.js");            $doc->addStyleSheet('/media/system/js/select2-4.0.0/dist/css/select2.css');            $js_content = '';            ob_start();            ?>            <script type="text/javascript">                jQuery(document).ready(function ($) {                });            </script>            <?php            $js_content = ob_get_clean();            $js_content = JUtility::remove_string_javascript($js_content);            $doc->addScriptDeclaration($js_content);            static::$loaded[__METHOD__] = true;        }        return;    }    public static function checkbox($debug = null)    {        $app = JFactory::getApplication();        $jquery_checkbox_compress = true;        $jquery_checkbox_css_compress = true;        // Include jQuery        static::framework();        // If no debugging value is set, use the configuration setting        if ($debug === null) {            $config = JFactory::getConfig();            $debug = (boolean)$config->get('debug');        }        // Only attempt to load the component if it's supported in core and hasn't already been loaded        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript("/media/system/js/icheck-1.x/icheck.min.js");            $doc->addStyleSheet('/media/system/js/icheck-1.x/skins/flat/_all.css');            $js_content = '';            ob_start();            ?>            <script type="text/javascript">                jQuery(document).ready(function ($) {                });            </script>            <?php            $js_content = ob_get_clean();            $js_content = JUtility::remove_string_javascript($js_content);            $doc->addScriptDeclaration($js_content);            static::$loaded[__METHOD__] = true;        }        return;    }    public static function appear($debug = null)    {        $app = JFactory::getApplication();        // Include jQuery        static::framework();        // If no debugging value is set, use the configuration setting        if ($debug === null) {            $config = JFactory::getConfig();            $debug = (boolean)$config->get('debug');        }        // Only attempt to load the component if it's supported in core and hasn't already been loaded        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript("/media/system/js/jquery.appear-master/jquery.appear.js");            $js_content = '';            ob_start();            ?>            <script type="text/javascript">                jQuery(document).ready(function ($) {                });            </script>            <?php            $js_content = ob_get_clean();            $js_content = JUtility::remove_string_javascript($js_content);            $doc->addScriptDeclaration($js_content);            static::$loaded[__METHOD__] = true;        }        return;    }    public static function scrollbar($debug = null)    {        // Include jQuery        static::framework();        // If no debugging value is set, use the configuration setting        if ($debug === null) {            $config = JFactory::getConfig();            $debug = (boolean)$config->get('debug');        }        // Only attempt to load the component if it's supported in core and hasn't already been loaded        if (empty(static::$loaded[__METHOD__])) {            $doc = JFactory::getDocument();            $doc->addScript('/media/system/js/malihu-custom-scrollbar-plugin-master/js/uncompressed/jquery.mousewheel-3.0.6.js');            $doc->addScript('/media/system/js/malihu-custom-scrollbar-plugin-master/js/uncompressed/jquery.mCustomScrollbar.js');            $doc->addStyleSheet('/media/system/js/malihu-custom-scrollbar-plugin-master/jquery.mCustomScrollbar.css');            static::$loaded[__METHOD__] = true;        }        return;    }    public static function zozo_tab($debug = null)    {        // Include jQuery        static::framework();        $doc = JFactory::getDocument();        $doc->addscript("/media/system/js/zozo_tabs_v.6.5/js/zozo.tabs.min.js");        $list_file_css = array(            'media/system/js/zozo_tabs_v.6.5/css/zozo.tabs.css',            'media/system/js/zozo_tabs_v.6.5/source/zozo.tabs.core.css',            'media/system/js/zozo_tabs_v.6.5/source/zozo.tabs.responsive.css',            'media/system/js/zozo_tabs_v.6.5/source/zozo.tabs.clean.css',            'media/system/js/zozo_tabs_v.6.5/source/zozo.tabs.themes.css',            'media/system/js/zozo_tabs_v.6.5/source/zozo.tabs.underlined.css',            'media/system/js/zozo_tabs_v.6.5/source/zozo.tabs.vertical.css',            'media/system/js/zozo_tabs_v.6.5/source/zozo.tabs.grid.css',            'media/system/js/zozo_tabs_v.6.5/source/zozo.tabs.mobile.css',            'media/system/js/zozo_tabs_v.6.5/source/zozo.tabs.multiline.css',            'media/system/js/zozo_tabs_v.6.5/source-flat/zozo.tabs.flat.css',            'media/system/js/zozo_tabs_v.6.5/source-flat/zozo.tabs.flat.mobile.css',            'media/system/js/zozo_tabs_v.6.5/source-flat/zozo.tabs.flat.styles.css',            'media/system/js/zozo_tabs_v.6.5/source-flat/zozo.tabs.flat.themes.css',        );        foreach ($list_file_css as $file_css) {            $doc->addStyleSheet("/$file_css");        }        return;    }}