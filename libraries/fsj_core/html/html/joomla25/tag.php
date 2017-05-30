<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_BASE') or die;

/**
 * Utility class for tags
 *
 * @package     Joomla.Libraries
 * @subpackage  HTML
 * @since       3.1
 */
abstract class JHtmlTag
{
	/**
	 * Cached array of the tag items.
	 *
	 * @var    array
	 * @since  3.1
	 */
	protected static $items = array();

	public static function ajaxfield($selector='#jform_tags', $allowCustom = true)
	{
		// Tags field ajax

		JHtml::_('tag.chosen', $selector);

		// Allow custom values ?
		if ($allowCustom)
		{
			JFactory::getDocument()->addScriptDeclaration("
				(function(jQuery){
					jQuery(document).ready(function () {

						var customTagPrefix = '#new#';

						// Method to add tags pressing enter
						jQuery('" . $selector . "_chosen input').keyup(function(event) {

							// Tag is greater than 3 chars and enter pressed
							if (this.value.length >= 3 && (event.which === 13 || event.which === 188)) {

								// Search an highlighted result
								var highlighted = jQuery('" . $selector . "_chosen').find('li.active-result.highlighted').first();

								// Add the highlighted option
								if (event.which === 13 && highlighted.text() !== '')
								{
									// Extra check. If we have added a custom tag with this text remove it
									var customOptionValue = customTagPrefix + highlighted.text();
									jQuery('" . $selector . " option').filter(function () { return jQuery(this).val() == customOptionValue; }).remove();

									// Select the highlighted result
									var tagOption = jQuery('" . $selector . " option').filter(function () { return jQuery(this).html() == highlighted.text(); });
									tagOption.attr('selected', 'selected');
								}
								// Add the custom tag option
								else
								{
									var customTag = this.value;

									// Extra check. Search if the custom tag already exists (typed faster than AJAX ready)
									var tagOption = jQuery('" . $selector . " option').filter(function () { return jQuery(this).html() == customTag; });
									if (tagOption.text() !== '')
									{
										tagOption.attr('selected', 'selected');
									}
									else
									{
										var option = jQuery('<option>');
										option.text(this.value).val(customTagPrefix + this.value);
										option.attr('selected','selected');

										// Append the option an repopulate the chosen field
										jQuery('" . $selector . "').append(option);
									}
								}

								this.value = '';
								jQuery('" . $selector . "').trigger('chosen:updated');
								event.preventDefault();

							}
						});
					});
				})(jQuery);
				"
			);
		}
	}
	
	protected static $loaded = array();
	public static function chosen($selector = '.advancedSelect', $debug = null, $options = array())
	{
		if (isset(static::$loaded[__METHOD__][$selector]))
		{
			return;
		}

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = JFactory::getConfig();
			$debug  = (boolean) $config->get('debug');
		}

		// Default settings
		$options['disable_search_threshold']  = isset($options['disable_search_threshold']) ? $options['disable_search_threshold'] : 10;
		$options['allow_single_deselect']     = isset($options['allow_single_deselect']) ? $options['allow_single_deselect'] : true;
		$options['placeholder_text_multiple'] = isset($options['placeholder_text_multiple']) ? $options['placeholder_text_multiple']: JText::_('JGLOBAL_SELECT_SOME_OPTIONS');
		$options['placeholder_text_single']   = isset($options['placeholder_text_single']) ? $options['placeholder_text_single'] : JText::_('JGLOBAL_SELECT_AN_OPTION');
		$options['no_results_text']           = isset($options['no_results_text']) ? $options['no_results_text'] : JText::_('JGLOBAL_SELECT_NO_RESULTS_MATCH');
		$options['width']					  = isset($options['width']) ? $options['width'] : '600px';

		// Options array to json options string
		$options_str = json_encode($options, ($debug && defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : false));

		FSJ_Page::Chosen();
		JFactory::getDocument()->addScriptDeclaration("
				jQuery(document).ready(function (){
					jQuery('" . $selector . "').chosen(" . $options_str . ");
				});
			"
			);

		static::$loaded[__METHOD__][$selector] = true;

		return;
	}
}
