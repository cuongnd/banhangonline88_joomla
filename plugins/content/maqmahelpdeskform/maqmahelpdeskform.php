<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package   MaQma_Helpdesk
 * @copyright (C) 2006-2012 Components Lab, Lda.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

// Include helpers
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/form.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/user.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/utility.php');

$lang = JFactory::getLanguage();
$language_tag = $lang->getTag();
$lang->load('com_maqmahelpdesk', JPATH_SITE, $language_tag, true);

$option = JRequest::getVar('option', '', 'REQUEST', 'string');

class plgContentMaQmaHelpdeskForm extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	public function onContentPrepare($context, &$row, &$params, $page = 0)
	{
		$database = JFactory::getDBO();

		// next line is a fix for /m option not working as it should??
		$row->text = str_replace("\n", "__CRLF__", $row->text);
		preg_match_all("/{scform}(.*?){\/scform}/im", $row->text, $bots, PREG_SET_ORDER);

		// split the text around the mambot
		$text = preg_split("/{scform}(.*?){\/scform}/im", $row->text);

		// count the number of forms present
		$n = count($text);
		if ($n > 0) {
			$row->text = '';
			for ($i = 0; $i < $n; $i++) {
				$row->text .= str_replace("__CRLF__", "\n", $text[$i]);
				if (trim(@$bots[$i][1])) {
					$code = @$bots[$i][1];
					$code = str_replace("\r", "", $code);
					$code = str_replace("__CRLF__", "\n", $code);

					//Get the component Itemid ********************************************************
					$sql = "SELECT `id`
							FROM `#__menu`
							WHERE `link`='index.php?option=com_maqmahelpdesk'
							  AND `published`=1";
					$database->setQuery($sql);
					$comItemid = $database->loadResult();

					//Get the form info ***************************************************************
					$database->setQuery("SELECT * FROM #__support_form WHERE id='" . $code . "'");
					$rowForm = null;
					$rowForm = $database->loadObject();

					//Get the form actions ************************************************************
					$database->setQuery("SELECT * FROM #__support_form_action WHERE id_form='" . $code . "'");
					$rowActions = $database->loadObjectList();

					//Get the form fields *************************************************************
					$database->setQuery("SELECT * FROM #__support_form_field WHERE id_form='" . $code . "' ORDER BY `order`");
					$rowFields = $database->loadObjectList();

					//Builds the validation javascript ************************************************
					$row->text .= "<script type='text/javascript'>\n";
					$row->text .= "function ValidateFields() {\n";

					for ($x = 0; $x < count($rowFields); $x++) {
						$rowField = $rowFields[$x];
						if ($rowField->required == "1") {
							$row->text .= "if(document." . str_replace(" ", "", $rowForm->name) . ".custom" . $rowField->id . ".value==\"\") {\n";
							$row->text .= "     alert(\"" . str_replace('%1', $rowField->caption, JText::_('field_required')) . "\")\n";
							$row->text .= "     document." . str_replace(" ", "", $rowForm->name) . ".custom" . $rowField->id . ".focus();\n";
							$row->text .= "     return false\n";
							$row->text .= "}\n";
						}
					}

					$row->text .= "return true\n";
					$row->text .= "}\n";
					$row->text .= "</script>\n";

					//Builds the form *****************************************************************
					$row->text .= $rowForm->description;
					$row->text .= "<form name='" . str_replace(" ", "", $rowForm->name) . "' action='index.php' method='POST' onSubmit='return ValidateFields();'>\n";
					$row->text .= "<table width='100%' cellpadding='5' cellspacing='0'>\n";

					for ($x = 0; $x < count($rowFields); $x++) {
						$rowField = $rowFields[$x];
						$row->text .= '<tr>';
						$row->text .= '<td width="100">' . utf8_decode($rowField->caption) . '</td>';
						$row->text .= '<td>' . HelpdeskForm::WriteField(0, $rowField->id, $rowField->type, $rowField->value, $rowField->size, $rowField->maxlength, 0, $code) . ($rowField->required ? ' <span style="color:#ff0000;">*</span>' : '') . '</td>';
						$row->text .= '</tr>';
					}

					$row->text .= "</table>\n";
					$row->text .= '<p align="right"><small><span class="required">*</span> <b>' . JText::_('field_required_desc') . "</b></small>&nbsp;&nbsp;&nbsp;";
					$row->text .= "<input type='submit' name='submit' value='" . JText::_('save') . "' class='button' /></p>\n";
					$row->text .= "<input type='hidden' name='redirect' value='" . $rowForm->redirect . "' />\n";
					$row->text .= "<input type='hidden' name='id' value='" . $rowForm->id . "' />\n";
					$row->text .= "<input type='hidden' name='pageurl' value='" . $_SERVER["PHP_SELF"] . "' />\n";
					$row->text .= "<input type='hidden' name='option' value='com_maqmahelpdesk' />\n";
					$row->text .= "<input type='hidden' name='task' value='forms_save' />\n";
					$row->text .= "<input type='hidden' name='Itemid' value='" . $comItemid . "' />\n";
					$row->text .= JHtml::_('form.token');
					$row->text .= "</form>\n";
				}
			}
		} else {
			$row->text = str_replace("__CRLF__", "\n", $row->text);
		}

		return $row;
	}

	public function onContentBeforeDisplay($context, &$row, &$params, $page = 0)
	{
		$database = JFactory::getDBO();

		// next line is a fix for /m option not working as it should??
		$row->text = str_replace("\n", "__CRLF__", $row->text);
		preg_match_all("/{scform}(.*?){\/scform}/im", $row->text, $bots, PREG_SET_ORDER);

		// split the text around the mambot
		$text = preg_split("/{scform}(.*?){\/scform}/im", $row->text);

		// count the number of forms present
		$n = count($text);
		if ($n > 0) {
			$row->text = '';
			for ($i = 0; $i < $n; $i++) {
				$row->text .= str_replace("__CRLF__", "\n", $text[$i]);
				if (trim(@$bots[$i][1])) {
					$code = @$bots[$i][1];
					$code = str_replace("\r", "", $code);
					$code = str_replace("__CRLF__", "\n", $code);

					//Get the component Itemid ********************************************************
					$sql = "SELECT `id`
							FROM `#__menu`
							WHERE `link`='index.php?option=com_maqmahelpdesk'
							  AND `published`=1";
					$database->setQuery($sql);
					$comItemid = $database->loadResult();

					//Get the form info ***************************************************************
					$database->setQuery("SELECT * FROM #__support_form WHERE id='" . $code . "'");
					$rowForm = null;
					$rowForm = $database->loadObject();

					//Get the form actions ************************************************************
					$database->setQuery("SELECT * FROM #__support_form_action WHERE id_form='" . $code . "'");
					$rowActions = $database->loadObjectList();

					//Get the form fields *************************************************************
					$database->setQuery("SELECT * FROM #__support_form_field WHERE id_form='" . $code . "' ORDER BY `order`");
					$rowFields = $database->loadObjectList();

					//Builds the validation javascript ************************************************
					$row->text .= "<script type='text/javascript'>\n";
					$row->text .= "function ValidateFields() {\n";

					for ($x = 0; $x < count($rowFields); $x++) {
						$rowField = $rowFields[$x];
						if ($rowField->required == "1") {
							$row->text .= "if(document." . str_replace(" ", "", $rowForm->name) . ".custom" . $rowField->id . ".value==\"\") {\n";
							$row->text .= "     alert(\"" . str_replace('%1', $rowField->caption, JText::_('field_required')) . "\")\n";
							$row->text .= "     document." . str_replace(" ", "", $rowForm->name) . ".custom" . $rowField->id . ".focus();\n";
							$row->text .= "     return false\n";
							$row->text .= "}\n";
						}
					}

					$row->text .= "return true\n";
					$row->text .= "}\n";
					$row->text .= "</script>\n";

					//Builds the form *****************************************************************
					$row->text .= $rowForm->description;
					$row->text .= "<form name='" . str_replace(" ", "", $rowForm->name) . "' action='index.php' method='POST' onSubmit='return ValidateFields();'>\n";
					$row->text .= "<table width='100%' cellpadding='5' cellspacing='0'>\n";

					for ($x = 0; $x < count($rowFields); $x++) {
						$rowField = $rowFields[$x];
						$row->text .= '<tr>';
						$row->text .= '<td width="100">' . utf8_decode($rowField->caption) . '</td>';
						$row->text .= '<td>' . HelpdeskForm::WriteField(0, $rowField->id, $rowField->type, $rowField->value, $rowField->size, $rowField->maxlength, 0, $code) . ($rowField->required ? ' <span style="color:#ff0000;">*</span>' : '') . '</td>';
						$row->text .= '</tr>';
					}

					$row->text .= "</table>\n";
					$row->text .= '<p align="right"><small><span class="required">*</span> <b>' . JText::_('field_required_desc') . "</b></small>&nbsp;&nbsp;&nbsp;";
					$row->text .= "<input type='submit' name='submit' value='" . JText::_('save') . "' class='button' /></p>\n";
					$row->text .= "<input type='hidden' name='redirect' value='" . $rowForm->redirect . "' />\n";
					$row->text .= "<input type='hidden' name='id' value='" . $rowForm->id . "' />\n";
					$row->text .= "<input type='hidden' name='pageurl' value='" . $_SERVER["PHP_SELF"] . "' />\n";
					$row->text .= "<input type='hidden' name='option' value='com_maqmahelpdesk' />\n";
					$row->text .= "<input type='hidden' name='task' value='forms_save' />\n";
					$row->text .= "<input type='hidden' name='Itemid' value='" . $comItemid . "' />\n";
					$row->text .= JHtml::_('form.token');
					$row->text .= "</form>\n";
				}
			}
		} else {
			$row->text = str_replace("__CRLF__", "\n", $row->text);
		}
	}
}
