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
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/file.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/form.php');

$id = JRequest::getVar('id', 0, '', 'int');

// Activities logger
HelpdeskUtility::ActivityLog('site', 'users', $task, $id);

switch ($task) {
	case 'profile':
		HelpdeskValidation::ValidPermissions($task, 'U') ? editUser() : HelpdeskValidation::NoAccessQuit();
		break;
	case 'saveregistration':
		HelpdeskValidation::ValidPermissions($task, 'U') ? userSave() : HelpdeskValidation::NoAccessQuit();
		break;
	case 'saveuseredit':
		HelpdeskValidation::ValidPermissions($task, 'U') ? userSave() : HelpdeskValidation::NoAccessQuit();
		break;
	case "getuserdetails":
		GetUserDetails();
		break;
}

function GetUserDetails()
{
	$database = JFactory::getDBO();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$id = JRequest::getVar('id', '', 'GET', 'int');
	$id_workgroup = JRequest::getVar('id_workgroup', '', 'GET', 'int');
	$Itemid = JRequest::getVar('Itemid', '', 'GET', 'int');

	// Get tickets
	$database->setQuery("SELECT t.id as dbid, t.ticketmask as ticketid, t.subject, t.id_status, t.assign_to, t.id_priority, t.date, t.duedate, t.id_user, t.last_update, t.id_workgroup, s.description AS status FROM #__support_ticket as t INNER JOIN #__support_status AS s ON s.id=t.id_status WHERE t.id_user='" . $id . "' ORDER BY t.duedate DESC");
	$tickets = $database->loadObjectList();

	for ($z = 0; $z < count($tickets); $z++) {
		$ticket = $tickets[$z];
		$tickets_rows[$z]['dbid'] = $ticket->dbid;
		$tickets_rows[$z]['ticketid'] = $ticket->ticketid;
		$tickets_rows[$z]['subject'] = $ticket->subject;
		$tickets_rows[$z]['id_status'] = $ticket->id_status;
		$tickets_rows[$z]['assign_to'] = $ticket->assign_to;
		$tickets_rows[$z]['id_priority'] = $ticket->id_priority;
		$tickets_rows[$z]['date'] = $ticket->date;
		$tickets_rows[$z]['duedate'] = $ticket->duedate;
		$tickets_rows[$z]['id_user'] = $ticket->id_user;
		$tickets_rows[$z]['last_update'] = $ticket->last_update;
		$tickets_rows[$z]['id_workgroup'] = $ticket->id_workgroup;
		$tickets_rows[$z]['link'] = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_view&id=' . $ticket->dbid;
		$tickets_rows[$z]['status'] = $ticket->status;
		$tickets_rows[$z]['date_created'] = HelpdeskDate::DateOffset($supportConfig->date_short, strtotime($ticket->date));
		$tickets_rows[$z]['dbid'] = $ticket->dbid;
	}

	$database->setQuery("SELECT * FROM #__users AS u LEFT JOIN #__support_users AS su ON u.id = su.id_user WHERE u.id='" . $id . "' ");
	$userInfo = null;
	$userInfo = $database->loadObject();

	$html_cfields = '';
	ob_start();
	$database->setQuery("SELECT uf.id, uf.id_field, uf.ordering, uf.required, cf.caption, cf.ftype, cf.value, cf.size, cf.maxlength FROM #__support_user_fields uf INNER JOIN #__support_custom_fields cf ON cf.id=uf.id_field WHERE cf.cftype='U' ORDER BY uf.ordering");
	$cfields = $database->loadObjectList();
	if (count($cfields) > 0) {
		$imgpath = '../images';
		for ($x = 0; $x < count($cfields); $x++)
		{
			$cfield = $cfields[$x]; ?>
			<div>
				<label><?php echo $cfield->caption; ?>:</label>
				<b><?php echo HelpdeskForm::WriteField(0, $cfield->id_field, $cfield->ftype, $cfield->value, $cfield->size, $cfield->maxlength, $cfield->id, 0, 0, 0, 1); ?></b>
			</div>
			<div class="wrap">&nbsp;</div><?php
		}
	}
	$html_cfields = ob_get_contents();
	ob_end_clean();

	// Display toolbar
	HelpdeskToolbar::Create();

	$tmplfile = HelpdeskTemplate::GetFile('users/tickets');
	include $tmplfile;
}

function editUser()
{
	$mainframe = JFactory::getApplication();
	$document = JFactory::getDocument();
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	
	$document->addScriptDeclaration( 'var MQM_USER_SHOW_LOGIN = '.$supportConfig->show_login_details.';' );
	$document->addScriptDeclaration( 'var MQM_USER_RF_PHONE = '.$supportConfig->rf_phone.';' );
	$document->addScriptDeclaration( 'var MQM_USER_RF_FAX = '.$supportConfig->rf_fax.';' );
	$document->addScriptDeclaration( 'var MQM_USER_RF_MOBILE = '.$supportConfig->rf_mobile.';' );
	$document->addScriptDeclaration( 'var MQM_USER_RF_ADDRESS1 = '.$supportConfig->rf_address1.';' );
	$document->addScriptDeclaration( 'var MQM_USER_RF_ADDRESS2 = '.$supportConfig->rf_address2.';' );
	$document->addScriptDeclaration( 'var MQM_USER_RF_ZIP = '.$supportConfig->rf_zipcode.';' );
	$document->addScriptDeclaration( 'var MQM_USER_RF_LOCATION = '.$supportConfig->rf_location.';' );
	$document->addScriptDeclaration( 'var MQM_USER_RF_CITY = '.$supportConfig->rf_city.';' );
	$document->addScriptDeclaration( 'var MQM_USER_RF_COUNTRY = '.$supportConfig->rf_country.';' );
	$document->addScriptDeclaration( 'var MQM_USER_WARNING = "'.addslashes(JText::_('warning')).'";' );
	$document->addScriptDeclaration( 'var MQM_USER_SAVE = "'.addslashes(JText::_('user_must_be_saved')).'";' );
	$document->addScriptDeclaration( 'var MQM_USER_NAME = "'.addslashes(JText::_('name_required')).'";' );
	$document->addScriptDeclaration( 'var MQM_USER_EMAIL = "'.addslashes(JText::_('email_required')).'";' );
	$document->addScriptDeclaration( 'var MQM_USER_PASS = "'.addslashes(JText::_('password_match')).'";' );
	$document->addScriptDeclaration( 'var MQM_USER_PHONE = "'.addslashes(JText::_('phone_required')).'";' );
	$document->addScriptDeclaration( 'var MQM_USER_FAX = "'.addslashes(JText::_('fax_required')).'";' );
	$document->addScriptDeclaration( 'var MQM_USER_MOBILE = "'.addslashes(JText::_('mobile_required')).'";' );
	$document->addScriptDeclaration( 'var MQM_USER_ADDRESS1 = "'.addslashes(JText::_('address1_required')).'";' );
	$document->addScriptDeclaration( 'var MQM_USER_ADDRESS2 = "'.addslashes(JText::_('address2_required')).'";' );
	$document->addScriptDeclaration( 'var MQM_USER_ZIP = "'.addslashes(JText::_('zipcode_required')).'";' );
	$document->addScriptDeclaration( 'var MQM_USER_LOCATION = "'.addslashes(JText::_('location_required')).'";' );
	$document->addScriptDeclaration( 'var MQM_USER_CITY = "'.addslashes(JText::_('city_required')).'";' );
	$document->addScriptDeclaration( 'var MQM_USER_COUNTRY = "'.addslashes(JText::_('country_required')).'";' );
	HelpdeskUtility::AppendResource('helpdesk.profile.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	HelpdeskUtility::PageTitle('showUserForm');
	$document->title = JText::_('wk_profile');

	JHTML::_('behavior.modal', 'a.modal');

	// Get the user info
	$sql = "SELECT us.phone, us.fax, us.mobile, us.address1, us.address2, us.zipcode, us.location, us.city, us.country, u.name, u.email, u.username, us.avatar
			FROM #__support_users AS us 
				 INNER JOIN #__users AS u ON u.id=us.id_user 
			WHERE us.id_user=" . $user->id;
	$database->setQuery($sql);
	$userInfo = $database->loadObject();

	if (!isset($userInfo)) {
		$userInfo->avatar = HelpdeskUser::GetAvatar($user->id);
		$userInfo->phone = '';
		$userInfo->fax = '';
		$userInfo->mobile = '';
		$userInfo->address1 = '';
		$userInfo->address2 = '';
		$userInfo->zipcode = '';
		$userInfo->location = '';
		$userInfo->city = '';
		$userInfo->country = '';
		$userInfo->name = $user->name;
		$userInfo->email = $user->email;
		$userInfo->username = '';
	} else {
		$userInfo->avatar = HelpdeskUser::GetAvatar($user->id);
	}

	// Get Users Custom Fields
	$sql = "SELECT uf.id, uf.id_field, uf.ordering, uf.required, cf.caption, cf.ftype, cf.value, cf.size, cf.maxlength
			FROM #__support_user_fields uf 
				 INNER JOIN #__support_custom_fields cf ON cf.id=uf.id_field 
			WHERE cf.cftype='U' 
			ORDER BY uf.ordering";
	$database->setQuery($sql);
	$cfields = $database->loadObjectList();

	// Custom fields
	$required_fields = null;
	$javascript = null;
	for ($x = 0; $x < count($cfields); $x++) {
		$cfield = $cfields[$x];

		if ($cfield->required && $cfield->ftype != 'radio') {
			$javascript.= 'if (form.custom' . $cfield->id_field . '.value == "") { ';
			$javascript.= 'alert( "You must provide a ' . $cfield->caption . '." ); ';
			$javascript.= 'return false; ';
			$javascript.= '} ';
		}
		elseif ($cfield->required && $cfield->ftype == 'radio')
		{
			$fieldval = '';
			$fieldoptions = explode(',', $cfield->value);
			for ($y = 0; $y < count($fieldoptions); $y++)
			{
				$fieldval .= '$jMaQma("#custom' . $cfield->id_field . '_' . preg_replace('/[^A-Za-z0-9_]/', '_', trim($fieldoptions[$y])) . '").is(":checked")==false && ';
			}
			$fieldval = JString::substr($fieldval, 0, strlen($fieldval) - 4);
			$javascript.= "if( " . $fieldval . " ) {\n";
			$javascript.= "    alert('" . $cfield->caption . JText::_('tmpl_msg07') . "');\n";
			$javascript.= "    return false;\n";
			$javascript.= "}\n";
		}
	}
	
	$document->addScriptDeclaration( 'function CheckCustomFields() { var form = document.profileForm; '.$javascript.' return true; }' );
	$document->addScriptDeclaration( 'var MQM_USER_IS_COUNTRY = "'.$userInfo->country.'";' );

	$i = 1;
	$custom_fields = null;
	foreach ($cfields as $key2 => $value2) {
		if (is_object($value2)) {
			foreach ($value2 as $key3 => $value3) {
				$custom_fields[$i][$key3] = $value3;

				if ($key3 == 'caption') {
					$custom_fields[$i]['caption'] = $value3;
				}
				if ($key3 == 'id_field') {
					$custom_fields[$i]['id_field'] = $value3;
				}
				if ($key3 == 'ftype') {
					$custom_fields[$i]['ftype'] = $value3;
				}
				if ($key3 == 'value') {
					$custom_fields[$i]['value'] = $value3;
				}
				if ($key3 == 'size') {
					$custom_fields[$i]['size'] = $value3;
				}
				if ($key3 == 'maxlength') {
					$custom_fields[$i]['maxlength'] = $value3;
				}

				if ($key3 == 'required') {
					$custom_fields[$i]['required'] = $value3 ? '<span class="required">*</span>' : '';
				}
			}

			$custom_fields[$i]['field'] = HelpdeskForm::WriteField(0, $custom_fields[$i]['id_field'], $custom_fields[$i]['ftype'], $custom_fields[$i]['value'], $custom_fields[$i]['size'], $custom_fields[$i]['maxlength'], $user->id);
		}

		$i++;
	}

	// Countries
	ob_start();
	include_once(JPATH_SITE . '/components/com_maqmahelpdesk/includes/countries.php');
	$countries = ob_get_contents();
	ob_end_clean();

	$req_img = '<span class="required">*</span>';

	// Display toolbar
	HelpdeskToolbar::Create();

	$tmplfile = HelpdeskTemplate::GetFile('users/profile');
	include $tmplfile;
}

function userSave()
{
	jimport('joomla.user.helper');

	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$CONFIG = new JConfig();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	// check if password was changed
	$newpass = '';
	$password = JRequest::getVar('password', '', 'POST', 'string');
	$password2 = JRequest::getVar('password2', '', 'POST', 'string');
	if ($password != '' && $password2 != '' && $password == $password2)
	{
		$salt = JUserHelper::genRandomPassword(32);
		$crypt = JUserHelper::getCryptedPassword($password, $salt);
		$newpass = $crypt . ':' . $salt;
	}

	$sql = "UPDATE #__users
			SET `name`=" . $database->quote(JRequest::getVar('name', '', 'POST', 'string')) . ",
				`email`=" . $database->quote(JRequest::getVar('email', '', 'POST', 'string')) . "" .
				($newpass != '' ? ", `password`=" . $database->quote($newpass) : '') . "
			WHERE id='" . $user->id . "'";
	$database->setQuery($sql);
	$database->query();

	// support center normal fields
	$sql = "DELETE FROM `#__support_users`
			WHERE `id_user`=" . $user->id;
	$database->setQuery($sql);
	$database->query();

	$sql = "INSERT INTO #__support_users(`id_user`, `phone`, `fax`, `mobile`, `address1`, `address2`, `zipcode`, `location`, `city`, `country`, `avatar`)
			VALUES('" . $user->id . "', " . $database->quote(JRequest::getVar('phone', '', 'POST', 'string')) . ", " . $database->quote(JRequest::getVar('fax', '', 'POST', 'string')) . ", " . $database->quote(JRequest::getVar('mobile', '', 'POST', 'string')) . ", " . $database->quote(JRequest::getVar('address1', '', 'POST', 'string')) . ", " . $database->quote(JRequest::getVar('address2', '', 'POST', 'string')) . ", " . $database->quote(JRequest::getVar('zipcode', '', 'POST', 'string')) . ", " . $database->quote(JRequest::getVar('location', '', 'POST', 'string')) . ", " . $database->quote(JRequest::getVar('city', '', 'POST', 'string')) . ", " . $database->quote(JRequest::getVar('country', '', 'POST', 'string')) . ", " . $database->quote(JRequest::getVar('avatar', '', 'POST', 'string')) . ")";
	$database->setQuery($sql);
	$database->query();

	// support center custom fields
	$database->setQuery("DELETE FROM `#__support_user_values` WHERE `id_user`=" . $user->id);
	$database->query();

	$database->setQuery("SELECT uf.`id`, uf.`id_field` FROM `#__support_user_fields` uf INNER JOIN `#__support_custom_fields` cf ON cf.`id`=uf.`id_field` WHERE cf.`cftype`='U'");
	$cfields = $database->loadObjectList();

	for ($x = 0; $x < count($cfields); $x++)
	{
		$cfield = $cfields[$x];
		$database->setQuery("INSERT INTO `#__support_user_values`(`id_user`, `id_field`, `value`)
							 VALUES('" . $user->id . "', '" . $cfield->id_field . "', '" . JRequest::getVar('custom' . $cfield->id_field, '', 'POST', 'string') . "')");
		$database->query();
	}
	
	if (isset($_FILES['avatar_file']['name']) && $_FILES['avatar_file']['name']!='') {
		HelpdeskFile::Upload($user->id, 'U', "avatar_file", JPATH_SITE . '/media/com_maqmahelpdesk/images/avatars/', null);
	}

	$mainframe->redirect(JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'), JText::_('user_save_changes'));
}
