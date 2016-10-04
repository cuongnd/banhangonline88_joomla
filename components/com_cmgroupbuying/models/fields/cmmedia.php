<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for the Joomla Platform.
 * Provides a modal media selector including upload mechanism
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldCMMedia extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'CMMedia';

	/**
	 * The initialised state of the document object.
	 *
	 * @var    boolean
	 * @since  11.1
	 */
	protected static $initialised = false;

	/**
	 * Method to get the field input markup for a media selector.
	 * Use attributes to identify specific created_by and asset_id fields
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		$assetField = $this->element['asset_field'] ? (string) $this->element['asset_field'] : 'asset_id';
		$authorField = $this->element['created_by_field'] ? (string) $this->element['created_by_field'] : 'created_by';
		$asset = $this->form->getValue($assetField) ? $this->form->getValue($assetField) : (string) $this->element['asset_id'];

		if ($asset == '')
		{
			$asset = JFactory::getApplication()->input->get('option', '', 'word');
		}

		$link = (string) $this->element['link'];

		if (!self::$initialised)
		{
			// Load the modal behavior script.
			JHtml::_('behavior.modal');

			// Build the script.
			$script = array();
			$script[] = '	function jInsertFieldValue(value, id) {';
			$script[] = '		var old_value = document.id(id).value;';
			$script[] = '		if (old_value != value) {';
			$script[] = '			var elem = document.id(id);';
			$script[] = '			elem.value = value;';
			$script[] = '			elem.fireEvent("change");';
			$script[] = '			if (typeof(elem.onchange) === "function") {';
			$script[] = '				elem.onchange();';
			$script[] = '			}';
			$script[] = '			jMediaRefreshPreview(id);';
			$script[] = '		}';
			$script[] = '	}';

			$script[] = '	function jMediaRefreshPreview(id) {';
			$script[] = '		var value = document.id(id).value;';
			$script[] = '		var img = document.id(id + "_preview");';
			$script[] = '		if (img) {';
			$script[] = '			if (value) {';
			$script[] = '				img.src = "' . JURI::root() . '" + value;';
			$script[] = '				document.id(id + "_preview_empty").setStyle("display", "none");';
			$script[] = '				document.id(id + "_preview_img").setStyle("display", "");';
			$script[] = '			} else { ';
			$script[] = '				img.src = ""';
			$script[] = '				document.id(id + "_preview_empty").setStyle("display", "");';
			$script[] = '				document.id(id + "_preview_img").setStyle("display", "none");';
			$script[] = '			} ';
			$script[] = '		} ';
			$script[] = '	}';

			$script[] = '	function jMediaRefreshPreviewTip(tip)';
			$script[] = '	{';
			$script[] = '		tip.setStyle("display", "block");';
			$script[] = '		var img = tip.getElement("img.media-preview");';
			$script[] = '		var id = img.getProperty("id");';
			$script[] = '		id = id.substring(0, id.length - "_preview".length);';
			$script[] = '		jMediaRefreshPreview(id);';
			$script[] = '	}';

			// Add the script to the document head.
			JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

			self::$initialised = true;
		}

		// Initialize variables.
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

		// The text field.
		if(version_compare(JVERSION, '3.0.0', 'ge'))
			$previewHTML = '<div class="input-prepend input-append">';
		else
			$previewHTML = '';

		// The Preview.
		$preview = (string) $this->element['preview'];
		$showPreview = true;
		$showAsTooltip = false;

		switch ($preview)
		{
			case 'no': // Deprecated parameter value
			case 'false':
			case 'none':
				$showPreview = false;
				break;

			case 'yes': // Deprecated parameter value
			case 'true':
			case 'show':
				break;

			case 'tooltip':
			default:
				$showAsTooltip = true;
				$options = array(
						'onShow' => 'jMediaRefreshPreviewTip',
				);
				JHtml::_('behavior.tooltip', '.hasTipPreview', $options);
				break;
		}

		if($showPreview)
		{
				if ($this->value && file_exists(JPATH_ROOT . '/' . $this->value))
				{
					$src = JURI::root() . $this->value;
				}
				else
				{
					$src = '';
				}

				$width = isset($this->element['preview_width']) ? (int) $this->element['preview_width'] : 300;
				$height = isset($this->element['preview_height']) ? (int) $this->element['preview_height'] : 200;
				$style = '';
				$style .= ($width > 0) ? 'max-width:' . $width . 'px;' : '';
				$style .= ($height > 0) ? 'max-height:' . $height . 'px;' : '';

				$imgattr = array(
						'id' => $this->id . '_preview',
						'class' => 'media-preview',
						'style' => $style,
				);

				$img = JHtml::image($src, JText::_('JLIB_FORM_MEDIA_PREVIEW_ALT'), $imgattr);
				$previewImg = '<div id="' . $this->id . '_preview_img"' . ($src ? '' : ' style="display:none"') . '>' . $img . '</div>';
				$previewImgEmpty = '<div id="' . $this->id . '_preview_empty"' . ($src ? ' style="display:none"' : '') . '>'
						. JText::_('JLIB_FORM_MEDIA_PREVIEW_EMPTY') . '</div>';

				$previewHTML .= '<div class="media-preview add-on">';

				if($showAsTooltip)
				{
					if(version_compare(JVERSION, '3.0.0', 'ge'))
						$text = '<i class="icon-eye-open"></i>';
					else
						$text = JText::_('JLIB_FORM_MEDIA_PREVIEW_TIP_TITLE');
						$tooltip = $previewImgEmpty . $previewImg;
						$options = array(
							'title' => JText::_('JLIB_FORM_MEDIA_PREVIEW_SELECTED_IMAGE'),
							'text' => $text,
							'class' => 'hasTipPreview'
						);
					$previewHTML .= JHtml::tooltip($tooltip, $options);
				}
				else
				{
					$previewHTML .= ' ' . $previewImgEmpty;
					$previewHTML .= ' ' . $previewImg;
				}

				$previewHTML .= '</div>';
		}

		if(version_compare(JVERSION, '3.0.0', 'ge'))
			$previewHTML .= '</div>';

		if(version_compare(JVERSION, '3.0.0', 'ge'))
			$html[] = $previewHTML;

		$html[] = '	<input type="text" class="input-small" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
				. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' . ' readonly="readonly"' . $attr . ' />';

		$directory = (string) $this->element['directory'];

		if($this->value && file_exists(JPATH_ROOT . '/' . $this->value))
		{
			$folder = explode('/', $this->value);
			array_shift($folder);
			array_pop($folder);
			$folder = implode('/', $folder);
		}
		elseif (file_exists(JPATH_ROOT . '/' . JComponentHelper::getParams('com_media')->get('image_path', 'images') . '/' . $directory))
		{
			$folder = $directory;
		}
		else
		{
			$folder = '';
		}

		// CMGroupBuying - Start
		$user = JFactory::getUser();
		$folder = $folder . '/' . $user->username;
		// CMGroupBuying - End

		// The button.
		$html[] = '	<a class="modal btn" title="' . JText::_('JLIB_FORM_BUTTON_SELECT') . '"' . ' href="'
			. ($this->element['readonly'] ? ''
			: ($link ? $link
					: './index.php?option=com_cmgroupbuying&amp;view=images&amp;tmpl=component&amp;asset=' . $asset . '&amp;author='
					. $this->form->getValue($authorField)) . '&amp;fieldid=' . $this->id) . '"'
			. ' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
		$html[] = JText::_('JLIB_FORM_BUTTON_SELECT') . '</a>';

		$html[] = '	<a class="btn" title="' . JText::_('JLIB_FORM_BUTTON_CLEAR') . '"' . ' href="#" onclick="';
		$html[] = 'jInsertFieldValue(\'\', \'' . $this->id . '\');';
		$html[] = 'return false;';
		$html[] = '$">';
		$html[] = JText::_('JLIB_FORM_BUTTON_CLEAR') . '</a>';

		if(version_compare(JVERSION, '3.0.0', 'lt'))
			$html[] = $previewHTML;

		return implode("\n", $html);
	}
}
