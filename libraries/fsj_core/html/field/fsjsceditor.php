<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('textarea');
require_once (JPATH_SITE.DS.'components'.DS.'com_fss'.DS.'helper'.DS.'helper.php');

class JFormFieldFSJSCEditor extends JFormFieldTextarea
{
	protected $type = 'FSJSCEditor';

	protected function getInput()
	{
		// Translate placeholder text
		$hint = $this->translateHint ? JText::_($this->hint) : $this->hint;

		// Initialize some field attributes.
		$class        = ' class="sceditor ' . $this->class . '"';
		$disabled     = $this->disabled ? ' disabled' : '';
		$readonly     = $this->readonly ? ' readonly' : '';
		$columns      = $this->columns ? ' cols="' . $this->columns . '"' : '';
		$rows         = $this->rows ? ' rows="' . $this->rows . '"' : '';
		$required     = $this->required ? ' required aria-required="true"' : '';
		$hint         = $hint ? ' placeholder="' . $hint . '"' : '';
		$autocomplete = !$this->autocomplete ? ' autocomplete="off"' : ' autocomplete="' . $this->autocomplete . '"';
		$autocomplete = $autocomplete == ' autocomplete="on"' ? '' : $autocomplete;
		$autofocus    = $this->autofocus ? ' autofocus' : '';
		$spellcheck   = $this->spellcheck ? '' : ' spellcheck="false"';
		$style		  = $this->element['fsjsceditor_style'] ? ' style="' . $this->element['fsjsceditor_style'] . '"' : 'style="width: 100%;"';

		// Initialize JavaScript field attributes.
		$onchange = $this->onchange ? ' onchange="' . $this->onchange . '"' : '';
		$onclick = $this->onclick ? ' onclick="' . $this->onclick . '"' : '';

		// Including fallback code for HTML5 non supported browsers.
		FSJ_Page::StylesAndJS();

		$document = JFactory::getDocument();
		$document->addScript(JURI::root(true).'/components/com_fss/assets/js/sceditor/jquery.sceditor.bbcode.js'); 
		$document->addScript(JURI::root(true).'/components/com_fss/assets/js/sceditor/include.sceditor.js'); 
		$document->addScriptDeclaration("var sceditor_emoticons_root = '" . JURI::root( true ) . "/components/com_fss/assets/';");
		$document->addScriptDeclaration("var sceditor_style_root = '" . JURI::root( true ) . "/components/com_fss/assets/js/sceditor/';");
		$document->addScriptDeclaration("var sceditor_style_type = '" . FSS_Settings::get('sceditor_content') . "';");
		$document->addScriptDeclaration("var sceditor_toolbar_exclude = '';");
		$document->addStyleSheet(JURI::root(true).'/components/com_fss/assets/js/sceditor/themes/default.css'); 


		$js = "function fsj_sceditor_presave() {
			try {
				var elems = jQuery('textarea');
				for (var i = 0 ; i < elems.length ; i++)
				{
					try {
						var elem = jQuery(elems[i]);
						var editor = elem.sceditor('instance');
						editor.updateOriginal();	
					} catch (e) {

					}
				}
			} catch (e) {
			}

			return true;
		}";

		JFactory::getDocument()->addScriptDeclaration($js);

		return '<textarea name="' . $this->name . '" id="' . $this->id . '"' . $columns . $rows . $class
			. $hint . $disabled . $readonly . $onchange . $onclick . $required . $autocomplete . $autofocus . $spellcheck . $style . ' >'
			. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '</textarea>';
	}
	
	
	function AdminDisplay($value, $name, $item)
	{
		return $value;	
	}
}
