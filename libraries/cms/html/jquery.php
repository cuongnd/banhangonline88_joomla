<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Utility class for jQuery JavaScript behaviors
 *
 * @since  3.0
 */
abstract class JHtmlJquery
{
	/**
	 * @var    array  Array containing information for loaded files
	 * @since  3.0
	 */
	protected static $loaded = array();

	/**
	 * Method to load the jQuery JavaScript framework into the document head
	 *
	 * If debugging mode is on an uncompressed version of jQuery is included for easier debugging.
	 *
	 * @param   boolean  $noConflict  True to load jQuery in noConflict mode [optional]
	 * @param   mixed    $debug       Is debugging mode on? [optional]
	 * @param   boolean  $migrate     True to enable the jQuery Migrate plugin
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function framework($noConflict = true, $debug = null, $migrate = true)
	{
		$jquery_compress=true;
		// Only load once
		if (!empty(static::$loaded[__METHOD__]))
		{
			return;
		}

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = JFactory::getConfig();
			$debug  = (boolean) $config->get('debug');
		}
		$doc=JFactory::getDocument();
		if($jquery_compress){
			$doc->addScript(JUri::root().'media/jui/js/jquery.min-1.9.1.js');
			$doc->addScript(JUri::root().'media/system/js/jquery.easing.min.1.3.js');
			if ($noConflict)
			{
				$doc->addScript(JUri::root().'media/jui/js/jquery-noconflict.min.js');
			}

			// Check if we are loading Migrate
			if ($migrate)
			{
				$doc->addScript(JUri::root().'media/jui/js/jquery-migrate.min.js');
			}


		}else {
			$doc->addScript(JUri::root().'media/jui/js/jquery-1.9.1.js');
			$doc->addScript(JUri::root().'media/system/js/jquery.easing.1.3.js');
			if ($noConflict)
			{
				$doc->addScript(JUri::root().'media/jui/js/jquery-noconflict.js');
			}

			// Check if we are loading Migrate
			if ($migrate)
			{
				$doc->addScript(JUri::root().'media/jui/js/jquery-migrate.js');
			}

		}
		// Check if we are loading in noConflict

		static::$loaded[__METHOD__] = true;

		return;
	}

	/**
	 * Method to load the jQuery UI JavaScript framework into the document head
	 *
	 * If debugging mode is on an uncompressed version of jQuery UI is included for easier debugging.
	 *
	 * @param   array  $components  The jQuery UI components to load [optional]
	 * @param   mixed  $debug       Is debugging mode on? [optional]
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function ui(array $components = array('core'), $debug = null)
	{
		// Set an array containing the supported jQuery UI components handled by this method
		$supported = array('core', 'sortable');

		// Include jQuery
		static::framework();

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = JFactory::getConfig();
			$debug  = (boolean) $config->get('debug');
		}

		// Load each of the requested components
		foreach ($components as $component)
		{
			// Only attempt to load the component if it's supported in core and hasn't already been loaded
			if (in_array($component, $supported) && empty(static::$loaded[__METHOD__][$component]))
			{
				JHtml::_('script', 'jui/jquery.ui.' . $component . '.min.js', false, true, false, false, $debug);
				static::$loaded[__METHOD__][$component] = true;
			}
		}

		return;
	}
	public static function cookie( $debug = null)
	{

		// Include jQuery
		static::framework();

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = JFactory::getConfig();
			$debug  = (boolean) $config->get('debug');
		}
		// Only attempt to load the component if it's supported in core and hasn't already been loaded
		if ( empty(static::$loaded[__METHOD__]))
		{
			$doc=JFactory::getDocument();
			$doc->addScript(JUri::root().'media/system/js/jquery-cookie-master/src/jquery.cookie.min.js');
			static::$loaded[__METHOD__]= true;
		}

		return;
	}
	public static function template( $debug = null)
	{
		$jquery_template_compress=true;
		// Include jQuery
		static::framework();

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = JFactory::getConfig();
			$debug  = (boolean) $config->get('debug');
		}
		// Only attempt to load the component if it's supported in core and hasn't already been loaded
		if ( empty(static::$loaded[__METHOD__]))
		{
			$doc=JFactory::getDocument();
			if($jquery_template_compress)
			{
				$doc->addScript(JUri::root().'templates/vina_bonnie/js/template.min.js');
			}else{
				$doc->addScript(JUri::root().'templates/vina_bonnie/js/template.js');
			}
			static::$loaded[__METHOD__]= true;
		}

		return;
	}
	public static function utility( $debug = null)
	{
		$jquery_utility_compress=true;
		// Include jQuery
		static::framework();

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = JFactory::getConfig();
			$debug  = (boolean) $config->get('debug');
		}
		// Only attempt to load the component if it's supported in core and hasn't already been loaded
		if ( empty(static::$loaded[__METHOD__]))
		{
			$doc=JFactory::getDocument();
			if($jquery_utility_compress)
			{
				$doc->addScript(JUri::root().'media/system/js/jquery.utility.min.js');
			}else{
				$doc->addScript(JUri::root().'media/system/js/jquery.utility.js');
			}
			static::$loaded[__METHOD__]= true;
		}

		return;
	}
	public static function hikashop( $debug = null)
	{
		$jquery_hikashop_compress=true;
		// Include jQuery
		static::framework();

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = JFactory::getConfig();
			$debug  = (boolean) $config->get('debug');
		}
		// Only attempt to load the component if it's supported in core and hasn't already been loaded
		if ( empty(static::$loaded[__METHOD__]))
		{
			$doc=JFactory::getDocument();
			if($jquery_hikashop_compress)
			{
				$doc->addScript(JUri::root().'media/com_hikashop/js/hikashop.min.js');
			}else{
				$doc->addScript(JUri::root().'media/com_hikashop/js/hikashop.js');
			}
			static::$loaded[__METHOD__]= true;
		}

		return;
	}
	public static function lazyload( $debug = null)
	{
		$jquery_lazyload_compress=true;
		$jquery_lazyload_compress_css=true;
		// Include jQuery
		static::framework();

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = JFactory::getConfig();
			$debug  = (boolean) $config->get('debug');
		}
		// Only attempt to load the component if it's supported in core and hasn't already been loaded
		if ( empty(static::$loaded[__METHOD__]))
		{
			$doc=JFactory::getDocument();
			if($jquery_lazyload_compress)
			{
				$doc->addScript(JUri::root().'media/system/js/lazy-load-xt-master/dist/jquery.lazyloadxt.min.js');
				$doc->addScript(JUri::root().'media/system/js/lazy-load-xt-master/dist/jquery.lazyloadxt.extra.min.js');
			}else{
				$doc->addScript(JUri::root().'media/system/js/lazy-load-xt-master/dist/jquery.lazyloadxt.js');
				$doc->addScript(JUri::root().'media/system/js/lazy-load-xt-master/dist/jquery.lazyloadxt.extra.js');
			}

			if($jquery_lazyload_compress_css)
			{
				$doc->addStyleSheet(JUri::root().'media/system/js/lazy-load-xt-master/dist/jquery.lazyloadxt.fadein.min.css');
			}else{
				$doc->addStyleSheet(JUri::root().'media/system/js/lazy-load-xt-master/dist/jquery.lazyloadxt.fadein.css');
			}
			static::$loaded[__METHOD__]= true;
		}

		return;
	}
	public static function hikamarket( $debug = null)
	{
		$jquery_hikamarket_compress=true;
		$jquery_hikamarket_compress_css=true;
		// Include jQuery
		static::framework();

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = JFactory::getConfig();
			$debug  = (boolean) $config->get('debug');
		}
		// Only attempt to load the component if it's supported in core and hasn't already been loaded
		if ( empty(static::$loaded[__METHOD__]))
		{
			$doc=JFactory::getDocument();
			if($jquery_hikamarket_compress)
			{
				$doc->addScript(JUri::root().'media/com_hikamarket/js/hikamarket.min.js');
			}else{
				$doc->addScript(JUri::root().'media/com_hikamarket/js/hikamarket.js');
			}

			if($jquery_hikamarket_compress_css)
			{
			}else{
			}
			static::$loaded[__METHOD__]= true;
		}

		return;
	}
	public static function less( $debug = null)
	{
		$jquery_less_compress=true;
		$jquery_less_compress_css=true;
		// Include jQuery
		static::framework();

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = JFactory::getConfig();
			$debug  = (boolean) $config->get('debug');
		}
		// Only attempt to load the component if it's supported in core and hasn't already been loaded
		if ( empty(static::$loaded[__METHOD__]))
		{
			$doc=JFactory::getDocument();
			if($jquery_less_compress)
			{
				$doc->addScript(JUri::root().'media/system/js/less.min-1.5.0.js');
			}else{
				$doc->addScript(JUri::root().'media/system/js/less-1.5.0.js');
			}

			if($jquery_less_compress_css)
			{
			}else{
			}
			static::$loaded[__METHOD__]= true;
		}

		return;
	}
	public static function help_step( $debug = null)
	{
		JHtml::_('jQuery.auo_typing_text');
		JHtml::_('jQuery.texttospeak');
		$jquery_help_step_compress=false;
		$jquery_help_step_compress_css=true;
		// Include jQuery
		static::framework();

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = JFactory::getConfig();
			$debug  = (boolean) $config->get('debug');
		}
		// Only attempt to load the component if it's supported in core and hasn't already been loaded
		if ( empty(static::$loaded[__METHOD__]))
		{
			$doc=JFactory::getDocument();
			if($jquery_help_step_compress)
			{
				$doc->addScript(JUri::root().'media/system/js/intro.js-2.3.0/intro.min.js');
			}else{
				$doc->addScript(JUri::root().'media/system/js/intro.js-2.3.0/intro.js');
			}

			if($jquery_help_step_compress_css)
			{
				$doc->addStyleSheet(JUri::root().'media/system/js/intro.js-2.3.0/introjs.min.css');
			}else{
				$doc->addStyleSheet(JUri::root().'media/system/js/intro.js-2.3.0/introjs.css');
			}
			static::$loaded[__METHOD__]= true;
		}

		return;
	}
	public static function auo_typing_text( $debug = null)
	{
		$jquery_auo_typing_text_compress=true;
		$jquery_auo_typing_text_css=true;
		// Include jQuery
		static::framework();

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = JFactory::getConfig();
			$debug  = (boolean) $config->get('debug');
		}
		// Only attempt to load the component if it's supported in core and hasn't already been loaded
		if ( empty(static::$loaded[__METHOD__]))
		{
			$doc=JFactory::getDocument();
			if($jquery_auo_typing_text_compress)
			{
				$doc->addScript(JUri::root().'media/system/js/auo_typing_text/jquery.teletype.min.js');
			}else{
				$doc->addScript(JUri::root().'media/system/js/auo_typing_text/jquery.teletype.js');
			}

			if($jquery_auo_typing_text_css)
			{
			}else{
			}
			static::$loaded[__METHOD__]= true;
		}

		return;
	}
	public static function feather( $debug = null)
	{
		$jquery_feather_compress=true;
		$jquery_feather_css=true;
		// Include jQuery
		static::framework();

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = JFactory::getConfig();
			$debug  = (boolean) $config->get('debug');
		}
		// Only attempt to load the component if it's supported in core and hasn't already been loaded
		if ( empty(static::$loaded[__METHOD__]))
		{
			$doc=JFactory::getDocument();
			if($jquery_feather_compress)
			{
				$doc->addScript(JUri::root().'media/system/js/aviary/feather.min.js');
			}else{
				$doc->addScript(JUri::root().'media/system/js/aviary/feather.js');
			}

			if($jquery_feather_css)
			{
			}else{
			}
			static::$loaded[__METHOD__]= true;
		}

		return;
	}
	public static function texttospeak( $debug = null)
	{
		$jquery_texttospeak_compress=true;
		$jquery_texttospeak_css_compress=true;
		// Include jQuery
		static::framework();

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = JFactory::getConfig();
			$debug  = (boolean) $config->get('debug');
		}
		// Only attempt to load the component if it's supported in core and hasn't already been loaded
		if ( empty(static::$loaded[__METHOD__]))
		{
			$doc=JFactory::getDocument();
			if($jquery_texttospeak_compress)
			{
				$doc->addScript(JUri::root().'media/system/js/texttospeak/responsivevoice.js');
			}else{
				$doc->addScript(JUri::root().'media/system/js/texttospeak/responsivevoice.js');
			}

			if($jquery_texttospeak_css_compress)
			{
			}else{
			}
			static::$loaded[__METHOD__]= true;
		}

		return;
	}
	public static function jquery_load_file( $debug = null)
	{
		$app=JFactory::getApplication();
		$menu=$app->getMenu();
		$active_menu=$menu->getActive();
		if(!$active_menu)
		{
			$active_menu=$menu->getDefault();
		}
		$params=$active_menu->params;
		$jquery_load_file=$params->get('jquery_load_file','');
		if(!$jquery_load_file)
		{
			return;
		}
		JHtml::_('jQuery.help_step');
		$jquery_load_file_compress=true;
		$jquery_load_file_css_compress=true;
		// Include jQuery
		static::framework();

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = JFactory::getConfig();
			$debug  = (boolean) $config->get('debug');
		}
		// Only attempt to load the component if it's supported in core and hasn't already been loaded
		if ( empty(static::$loaded[__METHOD__]))
		{
			$doc=JFactory::getDocument();
			if($jquery_load_file_compress)
			{
				$doc->addScript(JUri::root().'media/system/js/jquery_load_file/'.$jquery_load_file);
			}else{
				$doc->addScript(JUri::root().'media/system/js/jquery_load_file/'.$jquery_load_file);
			}

			if($jquery_load_file_css_compress)
			{
			}else{
			}
			$js_content = '';
			ob_start();
			?>
			<script type="text/javascript">
				jQuery(document).ready(function ($) {
					$("body").load_page({
						show_help:false,
						enable_audio:true
					});
				});
			</script>
			<?php
			$js_content = ob_get_clean();
			$js_content = JUtility::remove_string_javascript($js_content);
			$doc->addScriptDeclaration($js_content);

			static::$loaded[__METHOD__]= true;
		}

		return;
	}
	public static function select( $debug = null)
	{
		$app=JFactory::getApplication();
		$jquery_select_compress=true;
		$jquery_select_css_compress=true;
		// Include jQuery
		static::framework();

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = JFactory::getConfig();
			$debug  = (boolean) $config->get('debug');
		}
		// Only attempt to load the component if it's supported in core and hasn't already been loaded
		if ( empty(static::$loaded[__METHOD__]))
		{
			$doc=JFactory::getDocument();
			if($jquery_select_compress)
			{
				$doc->addScript(JUri::root().'media/system/js/select2-4.0.0/dist/js/select2.full.js');
			}else{
				$doc->addScript(JUri::root().'media/system/js/select2-4.0.0/dist/js/select2.full.min.js');
			}

			if($jquery_select_css_compress)
			{
				$doc->addStyleSheet(JUri::root().'media/system/js/select2-4.0.0/dist/css/select2.css');
			}else{
				$doc->addStyleSheet(JUri::root().'media/system/js/select2-4.0.0/dist/css/select2.min.css');
			}
			$js_content = '';
			ob_start();
			?>
			<script type="text/javascript">
				jQuery(document).ready(function ($) {

				});
			</script>
			<?php
			$js_content = ob_get_clean();
			$js_content = JUtility::remove_string_javascript($js_content);
			$doc->addScriptDeclaration($js_content);

			static::$loaded[__METHOD__]= true;
		}

		return;
	}
	public static function checkbox( $debug = null)
	{
		$app=JFactory::getApplication();
		$jquery_checkbox_compress=true;
		$jquery_checkbox_css_compress=true;
		// Include jQuery
		static::framework();

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = JFactory::getConfig();
			$debug  = (boolean) $config->get('debug');
		}
		// Only attempt to load the component if it's supported in core and hasn't already been loaded
		if ( empty(static::$loaded[__METHOD__]))
		{
			$doc=JFactory::getDocument();
			if($jquery_checkbox_compress)
			{
				$doc->addScript(JUri::root().'media/system/js/icheck-1.x/icheck.min.js');
			}else{
				$doc->addScript(JUri::root().'media/system/js/icheck-1.x/icheck.js');
			}

			if($jquery_checkbox_css_compress)
			{
				$doc->addStyleSheet(JUri::root().'media/system/js/icheck-1.x/skins/flat/_all.min.css');
			}else{
				$doc->addStyleSheet(JUri::root().'media/system/js/icheck-1.x/skins/flat/_all.css');
			}
			$js_content = '';
			ob_start();
			?>
			<script type="text/javascript">
				jQuery(document).ready(function ($) {

				});
			</script>
			<?php
			$js_content = ob_get_clean();
			$js_content = JUtility::remove_string_javascript($js_content);
			$doc->addScriptDeclaration($js_content);

			static::$loaded[__METHOD__]= true;
		}

		return;
	}
	public static function appear( $debug = null)
	{
		$app=JFactory::getApplication();
		$jquery_checkbox_compress=true;
		$jquery_checkbox_css_compress=true;
		// Include jQuery
		static::framework();

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = JFactory::getConfig();
			$debug  = (boolean) $config->get('debug');
		}
		// Only attempt to load the component if it's supported in core and hasn't already been loaded
		if ( empty(static::$loaded[__METHOD__]))
		{
			$doc=JFactory::getDocument();
			if($jquery_checkbox_compress)
			{
				$doc->addScript(JUri::root().'media/system/js/jquery.appear-master/jquery.appear.min.js');
			}else{
				$doc->addScript(JUri::root().'media/system/js/jquery.appear-master/jquery.appear.js');
			}

			if($jquery_checkbox_css_compress)
			{
			}else{
			}
			$js_content = '';
			ob_start();
			?>
			<script type="text/javascript">
				jQuery(document).ready(function ($) {

				});
			</script>
			<?php
			$js_content = ob_get_clean();
			$js_content = JUtility::remove_string_javascript($js_content);
			$doc->addScriptDeclaration($js_content);

			static::$loaded[__METHOD__]= true;
		}

		return;
	}
	public static function scrollbar( $debug = null)
	{
		$jquery_scrollbar_compress=true;
		$jquery_scrollbar_compress_css=true;
		// Include jQuery
		static::framework();

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = JFactory::getConfig();
			$debug  = (boolean) $config->get('debug');
		}
		// Only attempt to load the component if it's supported in core and hasn't already been loaded
		if ( empty(static::$loaded[__METHOD__]))
		{
			$doc=JFactory::getDocument();
			if($jquery_scrollbar_compress)
			{
				$doc->addScript(JUri::root().'media/system/js/malihu-custom-scrollbar-plugin-master/js/uncompressed/jquery.mousewheel.min-3.0.6.js');
				$doc->addScript(JUri::root().'media/system/js/malihu-custom-scrollbar-plugin-master/js/uncompressed/jquery.mCustomScrollbar.min.js');


			}else{
				$doc->addScript(JUri::root().'media/system/js/malihu-custom-scrollbar-plugin-master/js/uncompressed/jquery.mousewheel-3.0.6.js');
				$doc->addScript(JUri::root().'media/system/js/malihu-custom-scrollbar-plugin-master/js/uncompressed/jquery.mCustomScrollbar.js');

			}

			if($jquery_scrollbar_compress_css)
			{
				$doc->addStyleSheet(JUri::root().'media/system/js/malihu-custom-scrollbar-plugin-master/jquery.mCustomScrollbar.min.css');
			}else{
				$doc->addStyleSheet(JUri::root().'media/system/js/malihu-custom-scrollbar-plugin-master/jquery.mCustomScrollbar.css');
			}
			static::$loaded[__METHOD__]= true;
		}

		return;
	}
	public static function zozo_tab( $debug = null)
	{
		$jquery_zozo_tab_compress=true;
		$jquery_zozo_tab_compress_css=true;
		// Include jQuery
		static::framework();

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = JFactory::getConfig();
			$debug  = (boolean) $config->get('debug');
		}
		// Only attempt to load the component if it's supported in core and hasn't already been loaded
		if ( empty(static::$loaded[__METHOD__]))
		{
			$doc=JFactory::getDocument();
			if($jquery_zozo_tab_compress)
			{
				$doc->addScript(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/js/zozo.tabs.js');
			}else{
				$doc->addScript(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/js/zozo.tabs.min.js');			}

			if($jquery_zozo_tab_compress_css)
			{
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/css/zozo.tabs.min.css');
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/source/zozo.tabs.core.css');
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/source/zozo.tabs.responsive.css');
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/source/zozo.tabs.clean.css');
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/source/zozo.tabs.themes.css');
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/source/zozo.tabs.underlined.css');
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/source/zozo.tabs.vertical.css');
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/source/zozo.tabs.grid.css');
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/source/zozo.tabs.mobile.css');
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/source/zozo.tabs.multiline.css');
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/source-flat/zozo.tabs.flat.core.css');
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/source-flat/zozo.tabs.flat.css');
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/source-flat/zozo.tabs.flat.mobile.css');
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/source-flat/zozo.tabs.flat.styles.css');
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/source-flat/zozo.tabs.flat.themes.css');
			}else{
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/css/zozo.tabs.min.css');
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/source/zozo.tabs.core.css');
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/source/zozo.tabs.responsive.css');
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/source/zozo.tabs.clean.css');
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/source/zozo.tabs.themes.css');
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/source/zozo.tabs.underlined.css');
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/source/zozo.tabs.vertical.css');
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/source/zozo.tabs.grid.css');
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/source/zozo.tabs.mobile.css');
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/source/zozo.tabs.multiline.css');
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/source-flat/zozo.tabs.flat.core.css');
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/source-flat/zozo.tabs.flat.css');
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/source-flat/zozo.tabs.flat.mobile.css');
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/source-flat/zozo.tabs.flat.styles.css');
				$doc->addStyleSheet(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/source-flat/zozo.tabs.flat.themes.css');
			}
			static::$loaded[__METHOD__]= true;
		}

		return;
	}
}
