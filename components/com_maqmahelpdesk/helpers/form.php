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

class HelpdeskForm
{
	public static function checkForm($id, $action)
	{
		$database = JFactory::getDBO();

		if ($action == 1)
		{
			$sql_create = '';
			$sql = "SELECT *
					FROM #__support_form_field
					WHERE id_form='" . $id . "'";
			$database->setQuery($sql);
			$rows = $database->loadObjectList();

			for ($i = 0; $i < count($rows); $i++) {
				$row = $rows[$i];
				$field = JString::strtolower(str_replace(' ', '_', $row->caption));
				$sql_create .= '`' . $field . '` VARCHAR( 250 ) NOT NULL ,';
			}

			$sql_create = 'CREATE TABLE IF NOT EXISTS `#__support_form_' . $id . '` (
								`id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
								' . $sql_create . '
								`sc_ipaddress` VARCHAR( 25 ) NOT NULL ,
								`sc_recorddate` datetime NOT NULL default \'0000-00-00\' ,
								`sc_userid` INT NOT NULL default \'0\' ,
								PRIMARY KEY ( `id` ) 
							);';
			$database->setQuery($sql_create);
			$database->query();

			return true;
		}
	}

	public static function alterForm($id_form, $id_field)
	{
		$database = JFactory::getDBO();

		$sql = "SELECT caption FROM #__support_form_field WHERE id='" . $id_field . "'";
		$database->setQuery($sql);
		$row = $database->loadObject();
		$field = JString::strtolower(str_replace(' ', '_', $row->caption));

		$sql_create = 'ALTER TABLE `#__support_form_' . $id_form . '` ADD `' . $field . '` VARCHAR( 250 ) NOT NULL;';
		$database->setQuery($sql_create);
		$database->query();
	}

	public static function saveForm($id_form, $row, $save_db, $output, $emails, $includes, $user)
	{
		$database = JFactory::getDBO();
		$CONFIG = new JConfig();
		$luser = JFactory::getUser();

		/*print "<p>save_db: $save_db";
		  print "<br>output: $output";
		  print "<br>emails: $emails";
		  print "<br>includes: $includes";
		  print "<br>user: $user";*/

		// Get the form fields
		$sql = "SELECT * FROM `#__support_form_field` WHERE `id_form`='" . $id_form . "' ORDER BY `order`";
		$database->setQuery($sql);
		$rowFields = $database->loadObjectList();

		$insert_sql_label = '';
		$insert_sql_value = '';
		$output_html = '';

		for ($x = 0; $x < count($rowFields); $x++) {
			$rowField = $rowFields[$x];

			$insert_sql_label .= '`' . JString::strtolower(str_replace(' ', '_', $rowField->caption)) . '`,';
			$insert_sql_value .= "'" . $_POST['custom' . $rowField->id] . "',";
			$form_result[JString::strtolower(str_replace(' ', '_', $rowField->caption))] = $rowField->id;
			$output_html .= "<tr><td valign='top'><b>" . $rowField->caption . "</b></td><td>" . nl2br($_POST['custom' . $rowField->id]) . "</td></tr>";
		}

		// Save the form result into DB
		if ($save_db) {
			$insert_sql_label = $insert_sql_label . '`sc_ipaddress`,`sc_recorddate`,`sc_userid`';
			$insert_sql_value = $insert_sql_value . "'" . HelpdeskUser::GetIP() . "','" . date("Y-m-d H:i:s") . "'," . $luser->id;
			$sql = "INSERT INTO `#__support_form_" . $id_form . "` (" . $insert_sql_label . ") VALUES (" . $insert_sql_value . ");";
			$database->setQuery($sql);
			$database->query();
		}

		$output_html = JText::_('form_thankyou') . '<table width="100%">' . $output_html . '</table>';

		// Outputs the results to the screen
		if ($output) {
			print $output_html;
		}

		// Sends the e-mail's
		if ($emails != '')
		{
			$emails = explode(',', $emails);

			for ($i = 0; $i < count($emails); $i++)
			{
				jimport('joomla.mail.helper');
				unset($mailer);
				$mailer = JFactory::getMailer();
				$mailer->ClearAllRecipients();
				$mailer->setSender(array($CONFIG->mailfrom, $CONFIG->fromname));
				$mailer->addRecipient($emails[$i]);
				$mailer->setSubject($row->name);
				$mailer->setBody($output_html);
				$mailer->IsHTML(true);
				$mailer->Send();
			}
		}

		// Sends the e-mail's to specified fields
		if ($user != '')
		{
			$user = explode(',', $user);

			for ($i = 0; $i < count($user); $i++)
			{
				$send_user = $_POST['custom' . $form_result[JString::strtolower(str_replace(' ', '_', $user[$i]))]];
				jimport('joomla.mail.helper');
				unset($mailer);
				$mailer = JFactory::getMailer();
				$mailer->ClearAllRecipients();
				$mailer->setSender(array($CONFIG->mailfrom, $CONFIG->fromname));
				$mailer->addRecipient($send_user);
				$mailer->setSubject($row->name);
				$mailer->setBody($output_html);
				$mailer->IsHTML(true);
				$mailer->Send();
			}
		}

		// Includes external files
		if ($includes != '')
		{
			$includes = explode(',', $includes);

			for ($i = 0; $i < count($includes); $i++)
			{
				if (file_exists($includes[$i]))
				{
					include($includes[$i]);
				}
			}
		}
	}

	public static function SwitchCheckbox($type, $name, $captions, $values, $checked = 0, $class = null, $change = null)
	{
		$htmlcode = '';
		for ($i = 0; $i < count($values); $i++)
		{
			if (substr($name, strlen($name)-2, 2) == '[]')
			{
				$id = rand(1000, 9999).rand(10, 99);
			}
			else
			{
				$id = $name;
			}
			$htmlcode .= '<label class="checkbox inline" for="' . $name . $values[$i] . '">';
			$htmlcode .= '<input type="' . $type . '"
								 id="' . $name . $values[$i] . '"
								 name="' . $name . '"
								 value="' . $values[$i] . '" ' .
								 ($checked == $values[$i] ? 'checked ' : '') .
								 ($change != null ? 'onchange="' . $change . '" ' : '') . ' /> ';
			$htmlcode .= $captions[$i];
			$htmlcode .= '</label> &nbsp;';
		}
		return $htmlcode;
	}

	public static function BuildCategories($id_category, $filter = false, $width = false, $multiple = false, $kb = false, $bugtracker = false, $glossary = false, $name = null, $discussions = false, $onchange = null)
	{
		$mainframe = JFactory::getApplication();
		$database = JFactory::getDBO();
		$user = JFactory::getUser();
		$supportConfig = HelpdeskUtility::GetConfig();
		$id_workgroup = JRequest::getInt('id_workgroup', 0);
		$task = JRequest::getVar('task', '', 'REQUEST', 'string');
		$items = '';

		// Check categories permissions
		$categories = '';
		if (!$kb && !$bugtracker && !$glossary && !$discussions && $task != 'category_new' && $task != 'category_edit' && $task != 'category_categories' && $task != 'kb_search') {
			$sql = "SELECT `id_category`
					FROM `#__support_permission_category`
					WHERE `id_workgroup`=$id_workgroup
					  AND `id_user`=" . $user->id;
			$database->setQuery($sql);
			$categories = $database->loadObjectList();
			$catpermissions = null;
			foreach ($categories as $rowcat) {
				$catpermissions[] = $rowcat->id_category;
			}
			$categories = (count($catpermissions) ? 'AND c.id IN (' . implode(',', $catpermissions) . ')' : '');
		}

		// To show in the ticket manager
		if ($task == 'ticket_my' && $supportConfig->support_workgroup_only == 1)
		{
			// if can search all workgroups, list them all
			$sql = "SELECT c.id, c.name, c.parent, c.level, c.id_workgroup
					FROM #__support_category AS c
						 INNER JOIN #__support_workgroup AS w ON w.`id`=c.`id_workgroup`
					WHERE c.show=1
					  AND c.parent=%s
					  AND c.level=%s
					  AND c.id_workgroup=$id_workgroup
					  AND c.tickets=1 " . $categories . "
					ORDER BY c.ordering, c.name";

			$items .= '<option value="">' . JText::_('all') . '</option>';

			if ($supportConfig->use_uncategorized)
			{
				$items .= '<option value="0">' . JText::_('uncategorized') . '</option>';
			}

			$items .= '<option value="">' . JText::_('this_workgroup') . '</option>';
			$items .= HelpdeskForm::BuildCategoryHierarchy($sql, $id_category);

			$sql = "SELECT c.id, c.name, c.parent, c.level, c.id_workgroup
					FROM #__support_category as c
						 INNER JOIN #__support_workgroup AS w ON w.`id`=c.`id_workgroup`
					WHERE c.show=1
					  AND c.parent=%s
					  AND c.level=%s
					  AND c.id_workgroup<>$id_workgroup $categories
					ORDER BY c.name";
			$items .= '<option value="">' . JText::_('other_workgroup') . '</option>';
			$items .= HelpdeskForm::BuildCategoryHierarchy($sql, $id_category);

			$parent = '<select id="' . ($filter ? 'filter_category' : 'id_category') . '" name="' . ($filter ? 'filter_category' : 'id_category') . '" class="' . ($filter ? '' : '') . '" size="1">' . $items . '</select>';

		// Other places
		} else {
			$sql = "SELECT c.id, c.name, c.show, c.parent, c.level, w.wkdesc, c.id_workgroup
					FROM #__support_category as c
						 INNER JOIN #__support_workgroup AS w ON w.`id`=c.`id_workgroup`
					WHERE c.show=1
					  AND c.parent=%s
					  AND c.level=%s " .
					  ($id_workgroup != 0 ? "AND c.id_workgroup='" . $id_workgroup . "' " : '');
			if ($task != 'category_new' && $task != 'category_edit' && $task != 'category_categories' && $task != 'kb_search')
			{
				if ($kb)
				{
					$sql .= "AND c.kb=1 ";
				}
				elseif ($bugtracker)
				{
					$sql .= "AND c.bugtracker=1 ";
				}
				elseif ($glossary)
				{
					$sql .= "AND c.glossary=1 ";
				}
				elseif ($discussions)
				{
					$sql .= "AND c.discussions=1 ";
				}
				else
				{
					$sql .= "AND c.tickets=1 " . $categories . " ";
				}
			}
			$sql .= "ORDER BY w.wkdesc, c.ordering, c.name";

			if (($task != 'ticket_new' && $task != 'ticket_view' && !$kb && !$bugtracker && !$glossary && !$discussions && $task != 'category_new' && $task != 'category_edit' && $task != 'category_categories' && $task != 'kb_search') || ($task == 'kb_search'))
			{
				$items .= '<option value="">' . JText::_('all') . '</option>';
			}
			elseif (!$kb)
			{
				$items .= '<option value=""></option>';
			}
			elseif ($task == 'category_new' || $task == 'category_edit' || $task == 'category_categories' || $task == 'kb_search')
			{
				$items .= '<option value="0">' . JText::_('top') . '</option>';
			}

			if ($supportConfig->use_uncategorized && !$kb && !$bugtracker && !$glossary && !$discussions && $task != 'category_new' && $task != 'category_edit' && $task != 'category_categories' && $task != 'kb_search')
			{
				$items .= '<option value="0" ' . (!$id_category ? 'selected="selected"' : '') . '>' . JText::_('uncategorized') . '</option>';
			}

			$items .= HelpdeskForm::BuildCategoryHierarchy($sql, $id_category);

			if ($items != '')
			{
				$class = (!$mainframe->isSite() ? '' : ($filter == '' ? '' : ''));
				$parent = '<select id="' . ($name != '' ? $name : ($filter ? 'filter_category' : 'id_category')) . '" name="' . ($name != '' ? $name : ($filter ? 'filter_category' : 'id_category')) . '" class="' . $class . '" ' . ($multiple ? 'size="10"' : '') . ' ' . ($multiple ? ' multiple="multiple"' : '') . ($onchange != '' ? 'onchange="' . $onchange . '"' : '') . '>' . $items . '</select>';
			}
			else
			{
				$parent = "<input type='hidden' name='" . ($name != '' ? $name : 'id_category') . "' value='0' /> " . JText::_('workgroup_without_categories');
			}
		}

		return $parent;
	}

	public static function BuildCategoryHierarchy($query, $selected = 0, $id = 0, $level = 1)
	{
		$database = JFactory::getDBO();
		$sql = sprintf($query, $id, $level);
		$database->setQuery($sql);
		$rows = $database->loadObjectList();
		$items = '';
		$department = '';
		$task = JRequest::getVar('task', '', 'REQUEST', 'string');

		foreach ($rows as $row)
		{
			if ($level == 1 && $department != $row->id_workgroup && ($task == 'kb_search' || $task == 'kb_new' || $task == 'kb_edit' || $task == 'glossary_new' || $task == 'glossary_edit'))
			{
				if ($department != '')
				{
					$items .= '</optgroup>';
				}
				$department = $row->id_workgroup;
				$items .= '<optgroup label="' . $row->wkdesc . '">';
			}
			$items .= '<option value="' . $row->id . '" ' . ($selected == $row->id ? 'selected' : '') . '>' .
				str_repeat('--', ($level - 1)) . ' ' . $row->name . '</option>';
			$items .= self::BuildCategoryHierarchy($query, $selected, $row->id, ($level + 1));
		}

		if ($department != '')
		{
			$items .= '</optgroup>';
		}

		return $items;
	}

	public static function GetRate($id, $table, $image)
	{
		$database = JFactory::getDBO();
		$supportConfig = HelpdeskUtility::GetConfig();

		$database->setQuery("SELECT (SUM(rate)/COUNT(id)) AS rating FROM #__support_rate WHERE id_table='" . $id . "' AND source=" . $database->quote($table));

		// Image
		if ($image == 1) {
			$rate = '<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/rating/' . number_format($database->loadResult(), 0) . 'star.png" border="0" align="absmiddle" />';

			// PDF Image
		} elseif ($image == 2) {
			$rate = number_format($database->loadResult(), 0);

			// Text
		} else {
			$rate = number_format($database->loadResult(), 0);
		}

		return $rate;
	}

	static function WriteField($ticketId, $fid, $ftype, $fvalue, $fsize, $fmax, $user = 0, $form = 0, $exclude = 0, $contract = 0, $showonly = 0, $ftooltip = '', $download = 0, $client = 0)
	{
		global $id_workgroup, $sysmsgs_user_location;

		$database = JFactory::getDBO();
		$logged = JFactory::getUser();
		$supportConfig = HelpdeskUtility::GetConfig();
		$editor = JFactory::getEditor();
		$is_support = HelpdeskUser::IsSupport();
		$is_client = HelpdeskUser::IsClient();

		$field = '';
		$fieldSel = '';

		$filter = $user ? $user : ($client ? $client : ($download ? $download : ($form ? $form : ($contract ? $contract : $ticketId))));

		if ($user) {
			$sql = "SELECT `value` FROM #__support_user_values WHERE id_field='" . $fid . "' AND id_user='" . $filter . "'";
		} elseif ($form) {
			$sql = "SELECT `value` FROM #__support_form_field WHERE id='" . $fid . "' AND id_form='" . $form . "'";
		} elseif ($contract) {
			$sql = "SELECT `value` FROM #__support_contract_fields_values WHERE id_field='" . $fid . "' AND id_contract='" . $contract . "'";
		} elseif ($download) {
			$sql = "SELECT `value` FROM #__support_download_field_value WHERE id_field='" . $fid . "' AND id_download='" . $download . "'";
		} elseif ($client) {
			$sql = "SELECT `value` FROM #__support_client_field_value WHERE id_field='" . $fid . "' AND id_client='" . $client . "'";
		} else {
			$sql = "SELECT newfield FROM #__support_field_value WHERE id_field='" . $fid . "' AND id_ticket='" . $filter . "'";
		}
		$database->setQuery($sql);
		$record_value = $database->loadResult();

		if ($showonly) {
			return $record_value;
		}

		if (!$exclude || $sysmsgs_user_location == 'a' || $is_support || $form > 0 || $download > 0 || $client > 0)
		{
			// Textbox
			if ($ftype == "text") {
				$field .= '<input type="text" id="custom' . $fid . '" name="custom' . $fid . '" value="' . ($filter > 0 ? $record_value : $fvalue) . '" maxlength="' . $fmax . '" '.(!$contract ? '' : '').' />';

				// Readonly
			} elseif ($ftype == "readonly") {
				$field .= '<input type="hidden" id="custom' . $fid . '" name="custom' . $fid . '" value="' . ($filter > 0 ? $record_value : $fvalue) . '" maxlength="' . $fmax . '" '.(!$contract ? '' : '').' readonly />' . ($filter > 0 ? $record_value : $fvalue);

				// Hidden
			} elseif ($ftype == "hidden") {
				$field .= '<input type="hidden" id="custom' . $fid . '" name="custom' . $fid . '" value="' . ($filter > 0 ? $record_value : $fvalue) . '" />';

				// Radio Button
			} elseif ($ftype == "radio") {
				$selectOptions = explode(",", $fvalue);
				$field .= '<div class="controlset-pad">';
				for ($z = 0; $z < count($selectOptions); $z++) {
					$field .= "<label for='custom" . $fid . "_" . preg_replace('/[^A-Za-z0-9_]/', '_', trim($selectOptions[$z])) . "' class='checkbox inline'><input type='radio' name='custom" . $fid . "' id='custom" . $fid . "_" . preg_replace('/[^A-Za-z0-9_]/', '_', trim($selectOptions[$z])) . "' value='" . $selectOptions[$z] . "'" . ($record_value == $selectOptions[$z] ? " checked" : "") . " /> " . $selectOptions[$z]." </label>";
				}
				$field .= '</div>';

				// Checkbox
			} elseif ($ftype == "checkbox") {
				$selectOptions = explode(",", $fvalue);
				array_walk($selectOptions, 'trim');
				$record_value2 = explode(",", $record_value);
				$field .= '<div class="controlset-pad">';
				for ($z = 0; $z < count($selectOptions); $z++) {
					$field .= "<label for='custom" . $fid . "_" . preg_replace('/[^A-Za-z0-9_]/', '_', trim($selectOptions[$z])) . "' class='checkbox inline'><input type='checkbox' id='custom" . $fid . "_" . preg_replace('/[^A-Za-z0-9_]/', '_', trim($selectOptions[$z])) . "' name='custom" . $fid . "[]' value='" . $selectOptions[$z] . "'" . (in_array($selectOptions[$z], $record_value2) ? " checked" : "") . " /> " . $selectOptions[$z]." </label>";
				}
				$field .= '</div>';

				// Dropdown
			} elseif ($ftype == "select") {
				$field .= '<select id="custom' . $fid . '" name="custom' . $fid . '" class="inputbox">';
				$selectOptions = array();
				$selectOptions = explode(",", $fvalue);
				for ($z = 0; $z < count($selectOptions); $z++) {
					$selectOptions[$z] = trim($selectOptions[$z]);
					if ($selectOptions[$z] == $record_value) {
						$fieldSel = ' selected';
					} else {
						$fieldSel = '';
					}
					$field .= '<option value="' . $selectOptions[$z] . '"' . $fieldSel . '>' . $selectOptions[$z] . '</option>';
				}
				$field .= '</select>';

				// Database Dropdown
			} elseif ($ftype == "dbselect") {
				$class = '';

				if (strpos($fvalue, '|') === false)
				{
					if ($ticketId)
					{
						$sql = "SELECT id_client, id_user
								FROM #__support_ticket
								WHERE id='" . $filter . "'";
						$database->setQuery($sql);
						$ticket_details = $database->loadObject();
						$ticket_client = $ticket_details->id_client;
						$ticket_user = $ticket_details->id_user;
					}
					else
					{
						if ($is_support)
						{
							$class = 'relclient';
							$ticket_client = JRequest::getInt('id_client',0);
							$ticket_user = JRequest::getInt('id_user',0);
						}
						else
						{
							$ticket_client = $is_client;
							$ticket_user = $logged->id;
						}
					}
					$sql = $fvalue;
					$sql = str_replace('%user%', $ticket_user, $sql);
					$sql = str_replace('%client%', $ticket_client, $sql);
				}
				else
				{
					$fvalue = explode('|', $fvalue);
					$where = '';
					if (isset($fvalue[2]) && $fvalue[2] != '')
					{
						$where = 'WHERE ' . $fvalue[2];
						if ($ticketId)
						{
							$sql = "SELECT id_client, id_user
									FROM #__support_field_value 
									WHERE id_field='" . $fid . "' AND id_ticket='" . $filter . "'";
							$database->setQuery($sql);
							$ticket_details = $database->loadObject();
							$ticket_client = $ticket_details->id_client;
							$ticket_user = $ticket_details->id_user;
						}
						else
						{
							$ticket_client = $is_client;
							$ticket_user = $logged->id;
						}
						$where = str_replace('%user%', $ticket_user, $where);
						$where = str_replace('%client%', $ticket_client, $where);
					}
					$sql = "select trim(`" . $fvalue[0] . "`) as value
							from `" . $fvalue[1] . "` " .
						$where . "
							order by `" . $fvalue[0] . "`";
				}
				$sql = str_replace('\"', '"', $sql);
				$sql = str_replace("\'", "'", $sql);
				$database->setQuery($sql);
				$rows = $database->loadObjectList();

				$field .= '<select id="custom' . $fid . '" name="custom' . $fid . '" class="' . $class . '">';
				for ($i = 0; $i < count($rows); $i++)
				{
					$row = $rows[$i];
					if ($row->value == $record_value)
					{
						$fieldSel = ' selected';
					}
					else
					{
						$fieldSel = '';
					}
					$field .= '<option value="' . $row->value . '"' . $fieldSel . '>' . $row->value . '</option>';
				}
				$field .= '</select>';

				// Date
			} elseif ($ftype == "date") {
				ob_start();
				echo JHTML::Calendar(($filter > 0 ? $record_value : $fvalue), 'custom' . $fid, 'custom' . $fid, '%Y-%m-%d', array('class' => 'inputbox', 'size' => '12', 'maxlength' => '10'));
				$field .= ob_get_contents();
				ob_end_clean();

				// Textarea
			} elseif ($ftype == "textarea") {
				$field .= '<textarea id="custom' . $fid . '" name="custom' . $fid . '" rows="7">' . ($filter > 0 ? $record_value : $fvalue) . "</textarea>";

				// HTML Editor
			} elseif ($ftype == "htmleditor") {
				ob_start();
				echo $editor->display("custom" . $fid, ($filter > 0 ? $record_value : $fvalue), $fsize, '100', '5', '50', array('pagebreak', 'readmore'));
				$htmlcode = ob_get_contents();
				$field .= $htmlcode;
				ob_end_clean();

				// Country
			} elseif ($ftype == "country") {
				ob_start();
				include(JPATH_SITE . '/components/com_maqmahelpdesk/includes/countries.php');
				$field .= ob_get_contents();
				ob_end_clean();

				// State
			} elseif ($ftype == "state") {
				$field .= "state field here...";

				// Note
			} elseif ($ftype == "note") {
				$field .= $fvalue;

			}
		} else {
			$field .= ($filter > 0 ? $record_value : ($exclude ? '<i>' . JText::_('field_not_editable') . '</i>' : $fvalue)) . "<input type='hidden' name='custom" . $fid . "' value='" . ($filter > 0 ? $record_value : ($exclude ? '' : $fvalue)) . "' />";
		}

		return $field;
	}
}
