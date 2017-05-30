<?php
/**
 * @package     Joomla.Platform
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Utility class for Tabs elements.
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.2
 */
abstract class JHtmlFSJTabs
{
	/**
	 * Creates a panes and creates the JavaScript object for it.
	 *
	 * @param   string  $group   The pane identifier.
	 * @param   array   $params  An array of option.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	
	static $tabs;
	static $current;
	static $group;
	static $params;
	
	public static function start($group = 'tabs', $params = array())
	{
		self::_loadBehavior($group, $params);

		self::$group = $group;
		self::$tabs = array();
		self::$current = null;
		self::$params = $params;
		
		ob_start();
		
		//return '<dl class="tabs" id="' . $group . '"><dt style="display:none;"></dt><dd style="display:none;">';
	}

	/**
	 * Close the current pane
	 *
	 * @return  string  HTML to close the pane
	 *
	 * @since   11.1
	 */
	public static function end()
	{
		if (self::$current)
		{
			self::$current->content = ob_get_contents();
			self::$tabs[] = self::$current;
			ob_clean();
		}
		
		ob_end_clean();

		$default = self::$tabs[0]->id;
		if (isset(self::$params['default']))
			$default = self::$params['default'];
		
		$output = array();
		$output[] = '<ul class="nav nav-tabs">';
	
		foreach (self::$tabs as $tab)
		{
			$key = self::$group . "-" . $tab->id;
			$liclass = "";
			if ($tab->id == $default)
				$liclass = "class='active'";
			$output[] = "<li {$liclass}><a href='#{$key}' data-toggle='tab'>{$tab->text}</a></li>";
		}
		
		$output[] = '</ul>';
		
		$output[] = '<div class="tab-content">';
		
		foreach (self::$tabs as $tab)
		{
			$key = self::$group . "-" . $tab->id;
			$actclass = "";
			if ($tab->id == $default)
				$actclass = "active";
			$output[] = "<div class='tab-pane {$actclass}' id='{$key}'>";
			$output[] = $tab->content;
			$output[] = "</div>";
		}
		
		$output[] = '</div>';
		
		return implode($output);
	}

	/**
	 * Begins the display of a new panel.
	 *
	 * @param   string  $text  Text to display.
	 * @param   string  $id    Identifier of the panel.
	 *
	 * @return  string  HTML to start a new panel
	 *
	 * @since   11.1
	 */
	public static function panel($text, $id)
	{
		if ($id == "")
			$id = substr(md5(mt_rand()),0,8);
		
		if (self::$current)
		{
			self::$current->content = ob_get_contents();
			self::$tabs[] = self::$current;
			ob_clean();
		}
		
		self::$current = new stdClass();
		self::$current->text = $text;
		self::$current->id = $id;
		
		//return '</dd><dt class="tabs ' . $id . '"><span><h3><a href="javascript:void(0);">' . $text . '</a></h3></span></dt><dd class="tabs">';
	}

	/**
	 * Load the JavaScript behavior.
	 *
	 * @param   string  $group   The pane identifier.
	 * @param   array   $params  Array of options.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	protected static function _loadBehavior($group, $params = array())
	{
		static $loaded = array();

		/*if (!array_key_exists((string) $group, $loaded))
		{
			// Include MooTools framework
			JHtml::_('behavior.framework', true);

			$options = '{';
			$opt['onActive'] = (isset($params['onActive'])) ? $params['onActive'] : "
				function(title, description) {
					description.setStyle('display', 'block');
					title.addClass('open').removeClass('closed');
					fsj_tab_change(title, description);
				}";
			$opt['onBackground'] = (isset($params['onBackground'])) ? $params['onBackground'] : null;
			$opt['display'] = (isset($params['startOffset'])) ? (int) $params['startOffset'] : null;
			$opt['useStorage'] = (isset($params['useCookie']) && $params['useCookie']) ? 'true' : 'false';
			$opt['titleSelector'] = "'dt.tabs'";
			$opt['descriptionSelector'] = "'dd.tabs'";
		
			foreach ($opt as $k => $v)
			{
				if ($v)
				{
					$options .= $k . ': ' . $v . ',';
				}
			}

			if (substr($options, -1) == ',')
			{
				$options = substr($options, 0, -1);
			}

			$options .= '}';

			$js = '
			
					var fsj_tabs = ( typeof fsj_tabs != "undefined" ) ? fsj_tabs : {};

					window.addEvent(\'domready\', function(){
					
						$$(\'dl#' . $group . '.tabs\').each(function(tabs){
							var tab_obj = new JTabs(tabs, ' . $options . ');
							fsj_tabs[tab_obj.storageName] = tab_obj;
						});
					});
					
					
					';

			$document = JFactory::getDocument();
			$document->addScriptDeclaration($js);
			JHtml::_('script', 'system/tabs.js', false, true);
			$document->addScript( JURI::root().'libraries/fsj_core/assets/js/misc.form.js' );

			$loaded[(string) $group] = true;
		}*/
	}
}
