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

		JHtml::_('script', 'media/system/js/jquery.easing.1.3.js', false, true, false, false, $debug);
		JHtml::_('script', 'jui/jquery-1.9.1.js', false, true, false, false, $debug);
		// Check if we are loading in noConflict
		if ($noConflict)
		{
			JHtml::_('script', 'jui/jquery-noconflict.js', false, true, false, false, false);
		}

		// Check if we are loading Migrate
		if ($migrate)
		{
			JHtml::_('script', 'jui/jquery-migrate.min.js', false, true, false, false, $debug);
		}

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
	public static function zozo_tab()
	{
		$doc=JFactory::getDocument();
		$doc->addScript(JUri::root() . 'media/system/js/Zozo_Tabs_v.6.5/js/zozo.tabs.js');
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
}
