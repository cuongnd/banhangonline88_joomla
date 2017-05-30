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

// Required helpers
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/configuration.php';

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/configuration.php';

// Include the HTML file
require_once "components/com_maqmahelpdesk/views/config/tmpl/default.php";

// Set toolbar and page title
HelpdeskConfigurationAdminHelper::addToolbar();
HelpdeskConfigurationAdminHelper::setDocument();

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'config', $task);

switch ($task) {
	case "apply":
		SaveConfig(true);
		break;

	case "save":
		SaveConfig(false);
		break;

	case "ajax":
		ConfigInclude();
		break;

	default:
		ShowConfig();
		break;
}

function ConfigInclude()
{
	$page = JRequest::getCmd('page', '', '', 'string');
	include_once JPATH_SITE . '/administrator/components/com_maqmahelpdesk/views/ajax/ajax_' . $page . '.php';
}

function SaveConfig($apply = false)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$supportConfig = HelpdeskUtility::GetConfig();
	$row = new MaQmaHelpdeskTableConfiguration($database);
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (!$row->bind($_POST)) {
		echo "<script type='text/javascript'> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->check()) {
		echo "<script type='text/javascript'> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->store()) {
		echo "<script type='text/javascript'> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$row->checkin();

	// Verifies if icon theme changed
	if ($supportConfig->theme_icon != $row->theme_icon) {
		$sql = "UPDATE `#__support_links`
				SET `image`=REPLACE(`image`, '/" . $supportConfig->theme_icon . "/', '/" . $row->theme_icon . "/');";
		$database->setQuery($sql);
		$database->query();
	}

	if ($apply) {
		$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=config", JText::_('config_saved'));
	} else {
		$mainframe->redirect("index.php?option=com_maqmahelpdesk", JText::_('config_saved'));
	}
}

function ShowConfig()
{
	$database = JFactory::getDBO();
	$document = JFactory::getDocument();
	$supportConfig = HelpdeskUtility::GetConfig();
	$document->addScriptDeclaration('var IMQM_WARNING = "' . addslashes(JText::_('integrate_warning')) . '";');
	HelpdeskUtility::AppendResource('configuration.js', JURI::root() . 'media/com_maqmahelpdesk/js/administrator/', 'js', true);
	HelpdeskUtility::AppendResource('draganddrop.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
	HelpdeskUtility::AppendResource('equalheights.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	$captions = array(JText::_('MQ_NO'), JText::_('MQ_YES'));
	$values = array('0', '1');

	$lists = array();
	$lists['post_kb_creation_in_wall'] = HelpdeskForm::SwitchCheckbox('radio', 'post_kb_creation_in_wall', $captions, $values, $supportConfig->post_kb_creation_in_wall, 'switch');
	$lists['post_comments_in_wall'] = HelpdeskForm::SwitchCheckbox('radio', 'post_comments_in_wall', $captions, $values, $supportConfig->post_comments_in_wall, 'switch');
	$lists['use_jomsocial_avatars'] = HelpdeskForm::SwitchCheckbox('radio', 'use_jomsocial_avatars', $captions, $values, $supportConfig->use_jomsocial_avatars, 'switch');
	$lists['client_change_status'] = HelpdeskForm::SwitchCheckbox('radio', 'client_change_status', $captions, $values, $supportConfig->client_change_status, 'switch');
	$lists['public_attach'] = HelpdeskForm::SwitchCheckbox('radio', 'public_attach', $captions, $values, $supportConfig->public_attach, 'switch');
	$lists['receive_mail'] = HelpdeskForm::SwitchCheckbox('radio', 'receive_mail', $captions, $values, $supportConfig->receive_mail, 'switch');
	$lists['ac_active'] = HelpdeskForm::SwitchCheckbox('radio', 'ac_active', $captions, $values, $supportConfig->ac_active, 'switch');
	$lists['kb_popinfo'] = HelpdeskForm::SwitchCheckbox('radio', 'kb_popinfo', $captions, $values, $supportConfig->kb_popinfo, 'switch');
	$lists['notify_rate'] = HelpdeskForm::SwitchCheckbox('radio', 'notify_rate', $captions, $values, $supportConfig->notify_rate, 'switch');
	$lists['unregister'] = HelpdeskForm::SwitchCheckbox('radio', 'unregister', $captions, $values, $supportConfig->unregister, 'switch');
	$lists['kb_moderate'] = HelpdeskForm::SwitchCheckbox('radio', 'kb_moderate', $captions, $values, $supportConfig->kb_moderate, 'switch');
	$lists['faq_kb_manual'] = HelpdeskForm::SwitchCheckbox('radio', 'faq_kb_manual', $captions, $values, $supportConfig->faq_kb_manual, 'switch');
	$lists['faq_kb_hits'] = HelpdeskForm::SwitchCheckbox('radio', 'faq_kb_hits', $captions, $values, $supportConfig->faq_kb_hits, 'switch');
	$lists['readmail_create_user'] = HelpdeskForm::SwitchCheckbox('radio', 'readmail_create_user', $captions, $values, $supportConfig->readmail_create_user, 'switch');
	$lists['license_agreement'] = HelpdeskForm::SwitchCheckbox('radio', 'license_agreement', $captions, $values, $supportConfig->license_agreement, 'switch');
	$lists['company_info'] = HelpdeskForm::SwitchCheckbox('radio', 'company_info', $captions, $values, $supportConfig->company_info, 'switch');
	$lists['create_client'] = HelpdeskForm::SwitchCheckbox('radio', 'create_client', $captions, $values, $supportConfig->create_client, 'switch');
	$lists['rf_phone'] = HelpdeskForm::SwitchCheckbox('radio', 'rf_phone', $captions, $values, $supportConfig->rf_phone, 'switch');
	$lists['rf_fax'] = HelpdeskForm::SwitchCheckbox('radio', 'rf_fax', $captions, $values, $supportConfig->rf_fax, 'switch');
	$lists['rf_mobile'] = HelpdeskForm::SwitchCheckbox('radio', 'rf_mobile', $captions, $values, $supportConfig->rf_mobile, 'switch');
	$lists['rf_address1'] = HelpdeskForm::SwitchCheckbox('radio', 'rf_address1', $captions, $values, $supportConfig->rf_address1, 'switch');
	$lists['rf_address2'] = HelpdeskForm::SwitchCheckbox('radio', 'rf_address2', $captions, $values, $supportConfig->rf_address2, 'switch');
	$lists['rf_zipcode'] = HelpdeskForm::SwitchCheckbox('radio', 'rf_zipcode', $captions, $values, $supportConfig->rf_zipcode, 'switch');
	$lists['rf_location'] = HelpdeskForm::SwitchCheckbox('radio', 'rf_location', $captions, $values, $supportConfig->rf_location, 'switch');
	$lists['rf_country'] = HelpdeskForm::SwitchCheckbox('radio', 'rf_country', $captions, $values, $supportConfig->rf_country, 'switch');
	$lists['rf_city'] = HelpdeskForm::SwitchCheckbox('radio', 'rf_city', $captions, $values, $supportConfig->rf_city, 'switch');
	$lists['users_registration'] = HelpdeskForm::SwitchCheckbox('radio', 'users_registration', $captions, $values, $supportConfig->users_registration, 'switch');
	$lists['users_login'] = HelpdeskForm::SwitchCheckbox('radio', 'users_login', $captions, $values, $supportConfig->users_login, 'switch');
	$lists['use_uncategorized'] = HelpdeskForm::SwitchCheckbox('radio', 'use_uncategorized', $captions, $values, $supportConfig->use_uncategorized, 'switch');
	$lists['show_dashboard_support'] = HelpdeskForm::SwitchCheckbox('radio', 'show_dashboard_support', $captions, $values, $supportConfig->show_dashboard_support, 'switch');
	$lists['show_dashboard_customer'] = HelpdeskForm::SwitchCheckbox('radio', 'show_dashboard_customer', $captions, $values, $supportConfig->show_dashboard_customer, 'switch');
	$lists['anonymous_tickets'] = HelpdeskForm::SwitchCheckbox('radio', 'anonymous_tickets', $captions, $values, $supportConfig->anonymous_tickets, 'switch');
	$lists['register_user_change_status'] = HelpdeskForm::SwitchCheckbox('radio', 'register_user_change_status', $captions, $values, $supportConfig->register_user_change_status, 'switch');
	$lists['support_only_show_assign'] = HelpdeskForm::SwitchCheckbox('radio', 'support_only_show_assign', $captions, $values, $supportConfig->support_only_show_assign, 'switch');
	$lists['support_workgroup_only'] = HelpdeskForm::SwitchCheckbox('radio', 'support_workgroup_only', $captions, $values, $supportConfig->support_workgroup_only, 'switch');
	$lists['mail_queue'] = HelpdeskForm::SwitchCheckbox('radio', 'mail_queue', $captions, $values, $supportConfig->mail_queue, 'switch');
	$lists['duedate_algoritm'] = HelpdeskForm::SwitchCheckbox('radio', 'duedate_algoritm', $captions, $values, $supportConfig->duedate_algoritm, 'switch', 'duedate_cfg();');
	$lists['duedate_holidays'] = HelpdeskForm::SwitchCheckbox('radio', 'duedate_holidays', $captions, $values, $supportConfig->duedate_holidays, 'switch');
	$lists['duedate_vacations'] = HelpdeskForm::SwitchCheckbox('radio', 'duedate_vacations', $captions, $values, $supportConfig->duedate_vacations, 'switch');
	$lists['duedate_firstday'] = HelpdeskForm::SwitchCheckbox('radio', 'duedate_firstday', $captions, $values, $supportConfig->duedate_firstday, 'switch');
	$lists['duedate_schedule'] = HelpdeskForm::SwitchCheckbox('radio', 'duedate_schedule', $captions, $values, $supportConfig->duedate_schedule, 'switch');
	$lists['discussions_moderated'] = HelpdeskForm::SwitchCheckbox('radio', 'discussions_moderated', $captions, $values, $supportConfig->discussions_moderated, 'switch');
	$lists['integrate_mtree'] = HelpdeskForm::SwitchCheckbox('radio', 'integrate_mtree', $captions, $values, $supportConfig->integrate_mtree, 'switch');
	$lists['integrate_sobi'] = HelpdeskForm::SwitchCheckbox('radio', 'integrate_sobi', $captions, $values, $supportConfig->integrate_sobi, 'switch');
	$lists['integrate_artofuser'] = HelpdeskForm::SwitchCheckbox('radio', 'integrate_artofuser', $captions, $values, $supportConfig->integrate_artofuser, 'switch');
	$lists['use_cb_avatars'] = HelpdeskForm::SwitchCheckbox('radio', 'use_cb_avatars', $captions, $values, $supportConfig->use_cb_avatars, 'switch');
	$lists['js_post_question_wall'] = HelpdeskForm::SwitchCheckbox('radio', 'js_post_question_wall', $captions, $values, $supportConfig->js_post_question_wall, 'switch');
	$lists['js_post_answer_wall'] = HelpdeskForm::SwitchCheckbox('radio', 'js_post_answer_wall', $captions, $values, $supportConfig->js_post_answer_wall, 'switch');
	$lists['js_post_votes_wall'] = HelpdeskForm::SwitchCheckbox('radio', 'js_post_votes_wall', $captions, $values, $supportConfig->js_post_votes_wall, 'switch');
	$lists['js_answer_selected_wall'] = HelpdeskForm::SwitchCheckbox('radio', 'js_answer_selected_wall', $captions, $values, $supportConfig->js_answer_selected_wall, 'switch');
	$lists['js_post_bugtracker_wall'] = HelpdeskForm::SwitchCheckbox('radio', 'js_post_bugtracker_wall', $captions, $values, $supportConfig->js_post_bugtracker_wall, 'switch');
	$lists['profile_required'] = HelpdeskForm::SwitchCheckbox('radio', 'profile_required', $captions, $values, $supportConfig->profile_required, 'switch');
	$lists['stopspam'] = HelpdeskForm::SwitchCheckbox('radio', 'stopspam', $captions, $values, $supportConfig->stopspam, 'switch');
	$lists['hide_powered'] = HelpdeskForm::SwitchCheckbox('radio', 'hide_powered', $captions, $values, $supportConfig->hide_powered, 'switch');
	$lists['common_ticket_views'] = HelpdeskForm::SwitchCheckbox('radio', 'common_ticket_views', $captions, $values, $supportConfig->common_ticket_views, 'switch');
	$lists['use_merge'] = HelpdeskForm::SwitchCheckbox('radio', 'use_merge', $captions, $values, $supportConfig->use_merge, 'switch');
	$lists['use_as_reply'] = HelpdeskForm::SwitchCheckbox('radio', 'use_as_reply', $captions, $values, $supportConfig->use_as_reply, 'switch');
	$lists['use_parent'] = HelpdeskForm::SwitchCheckbox('radio', 'use_parent', $captions, $values, $supportConfig->use_parent, 'switch');
	$lists['use_travel'] = HelpdeskForm::SwitchCheckbox('radio', 'use_travel', $captions, $values, $supportConfig->use_travel, 'switch');
	$lists['use_type'] = HelpdeskForm::SwitchCheckbox('radio', 'use_type', $captions, $values, $supportConfig->use_type, 'switch');
	$lists['integrate_jbolo'] = HelpdeskForm::SwitchCheckbox('radio', 'integrate_jbolo', $captions, $values, $supportConfig->integrate_jbolo, 'switch');
	$lists['integrate_digistore'] = HelpdeskForm::SwitchCheckbox('radio', 'integrate_digistore', $captions, $values, $supportConfig->integrate_digistore, 'switch');
	$lists['kb_approvement'] = HelpdeskForm::SwitchCheckbox('radio', 'kb_approvement', $captions, $values, $supportConfig->kb_approvement, 'switch');
	$lists['show_login_form'] = HelpdeskForm::SwitchCheckbox('radio', 'show_login_form', $captions, $values, $supportConfig->show_login_form, 'switch');
	$lists['show_login_details'] = HelpdeskForm::SwitchCheckbox('radio', 'show_login_details', $captions, $values, $supportConfig->show_login_details, 'switch');
	$lists['customfields_search'] = HelpdeskForm::SwitchCheckbox('radio', 'customfields_search', $captions, $values, $supportConfig->customfields_search, 'switch');
	$lists['tickets_numbers'] = HelpdeskForm::SwitchCheckbox('radio', 'tickets_numbers', $captions, $values, $supportConfig->tickets_numbers, 'switch');
	$lists['system_log'] = HelpdeskForm::SwitchCheckbox('radio', 'system_log', $captions, $values, $supportConfig->system_log, 'switch');
	$lists['sms_assign'] = HelpdeskForm::SwitchCheckbox('radio', 'sms_assign', $captions, $values, $supportConfig->sms_assign, 'switch');
	$lists['kbsocial'] = HelpdeskForm::SwitchCheckbox('radio', 'kbsocial', $captions, $values, $supportConfig->kbsocial, 'switch');
	$lists['use_jomwall_avatars'] = HelpdeskForm::SwitchCheckbox('radio', 'use_jomwall_avatars', $captions, $values, $supportConfig->use_jomwall_avatars, 'switch');
	$lists['use_department_groups'] = HelpdeskForm::SwitchCheckbox('radio', 'use_department_groups', $captions, $values, $supportConfig->use_department_groups, 'switch');
	$lists['download_notification'] = HelpdeskForm::SwitchCheckbox('radio', 'download_notification', $captions, $values, $supportConfig->download_notification, 'switch');
	$lists['faq_single_page'] = HelpdeskForm::SwitchCheckbox('radio', 'faq_single_page', $captions, $values, $supportConfig->faq_single_page, 'switch');
	$lists['discussions_anonymous'] = HelpdeskForm::SwitchCheckbox('radio', 'discussions_anonymous', $captions, $values, $supportConfig->discussions_anonymous, 'switch');
	$lists['downloads_badges'] = HelpdeskForm::SwitchCheckbox('radio', 'downloads_badges', $captions, $values, $supportConfig->downloads_badges, 'switch');
	$lists['kb_enable_rating'] = HelpdeskForm::SwitchCheckbox('radio', 'kb_enable_rating', $captions, $values, $supportConfig->kb_enable_rating, 'switch');
	$lists['kb_enable_comments'] = HelpdeskForm::SwitchCheckbox('radio', 'kb_enable_comments', $captions, $values, $supportConfig->kb_enable_comments, 'switch');
	$lists['digistore_domains'] = HelpdeskForm::SwitchCheckbox('radio', 'digistore_domains', $captions, $values, $supportConfig->digistore_domains, 'switch');
	$lists['show_kb_frontpage'] = HelpdeskForm::SwitchCheckbox('radio', 'show_kb_frontpage', $captions, $values, $supportConfig->show_kb_frontpage, 'switch');
	$lists['include_bootstrap'] = HelpdeskForm::SwitchCheckbox('radio', 'include_bootstrap', $captions, $values, $supportConfig->include_bootstrap, 'switch');
	$lists['manual_times'] = HelpdeskForm::SwitchCheckbox('radio', 'manual_times', $captions, $values, $supportConfig->manual_times, 'switch');
	$lists['use_eshop_suite_avatars'] = HelpdeskForm::SwitchCheckbox('radio', 'use_eshop_suite_avatars', $captions, $values, $supportConfig->use_eshop_suite_avatars, 'switch');
	$lists['tickets_per_department'] = HelpdeskForm::SwitchCheckbox('radio', 'tickets_per_department', $captions, $values, $supportConfig->tickets_per_department, 'switch');

	// Build the number of offset list
	$offsetlist[] = JHTML::_('select.option', '-18', '-18:00');
	$offsetlist[] = JHTML::_('select.option', '-17', '-17:00');
	$offsetlist[] = JHTML::_('select.option', '-16', '-16:00');
	$offsetlist[] = JHTML::_('select.option', '-15', '-15:00');
	$offsetlist[] = JHTML::_('select.option', '-14', '-14:00');
	$offsetlist[] = JHTML::_('select.option', '-13', '-13:00');
	$offsetlist[] = JHTML::_('select.option', '-12.75', '-12:45');
	$offsetlist[] = JHTML::_('select.option', '-12', '-12:00');
	$offsetlist[] = JHTML::_('select.option', '-11.5', '-11:30');
	$offsetlist[] = JHTML::_('select.option', '-11', '-11:00');
	$offsetlist[] = JHTML::_('select.option', '-10.5', '-10:30');
	$offsetlist[] = JHTML::_('select.option', '-10', '-10:00');
	$offsetlist[] = JHTML::_('select.option', '-9.5', '-09:30');
	$offsetlist[] = JHTML::_('select.option', '-9', '-09:00');
	$offsetlist[] = JHTML::_('select.option', '-8.75', '-08:45');
	$offsetlist[] = JHTML::_('select.option', '-8', '-08:00');
	$offsetlist[] = JHTML::_('select.option', '-7', '-07:00');
	$offsetlist[] = JHTML::_('select.option', '-6.5', '-06:30');
	$offsetlist[] = JHTML::_('select.option', '-6', '-06:00');
	$offsetlist[] = JHTML::_('select.option', '-5.75', '-05:45');
	$offsetlist[] = JHTML::_('select.option', '-5.5', '-05:30');
	$offsetlist[] = JHTML::_('select.option', '-5', '-05:00');
	$offsetlist[] = JHTML::_('select.option', '-4.5', '-04:30');
	$offsetlist[] = JHTML::_('select.option', '-4', '-04:00');
	$offsetlist[] = JHTML::_('select.option', '-3.5', '-03:30');
	$offsetlist[] = JHTML::_('select.option', '-3', '-03:00');
	$offsetlist[] = JHTML::_('select.option', '-2', '-02:00');
	$offsetlist[] = JHTML::_('select.option', '-1', '-01:00');
	$offsetlist[] = JHTML::_('select.option', '0', 'n/a');
	$offsetlist[] = JHTML::_('select.option', '1', '+01:00');
	$offsetlist[] = JHTML::_('select.option', '2', '+02:00');
	$offsetlist[] = JHTML::_('select.option', '3', '+03:00');
	$offsetlist[] = JHTML::_('select.option', '3.5', '+03:30');
	$offsetlist[] = JHTML::_('select.option', '4', '+04:00');
	$offsetlist[] = JHTML::_('select.option', '4.5', '+04:30');
	$offsetlist[] = JHTML::_('select.option', '5', '+05:00');
	$offsetlist[] = JHTML::_('select.option', '5.5', '+05:30');
	$offsetlist[] = JHTML::_('select.option', '5.75', '+05:45');
	$offsetlist[] = JHTML::_('select.option', '6', '+06:00');
	$offsetlist[] = JHTML::_('select.option', '6.5', '+06:30');
	$offsetlist[] = JHTML::_('select.option', '7', '+07:00');
	$offsetlist[] = JHTML::_('select.option', '8', '+08:00');
	$offsetlist[] = JHTML::_('select.option', '8.75', '+08:45');
	$offsetlist[] = JHTML::_('select.option', '9', '+09:00');
	$offsetlist[] = JHTML::_('select.option', '9.5', '+09:30');
	$offsetlist[] = JHTML::_('select.option', '10', '+10:00');
	$offsetlist[] = JHTML::_('select.option', '10.5', '+10:30');
	$offsetlist[] = JHTML::_('select.option', '11', '+11:00');
	$offsetlist[] = JHTML::_('select.option', '11.5', '+11:30');
	$offsetlist[] = JHTML::_('select.option', '12', '+12:00');
	$offsetlist[] = JHTML::_('select.option', '12.75', '+12:45');
	$offsetlist[] = JHTML::_('select.option', '13', '+13:00');
	$offsetlist[] = JHTML::_('select.option', '14', '+14:00');
	$offsetlist[] = JHTML::_('select.option', '15', '+15:00');
	$offsetlist[] = JHTML::_('select.option', '16', '+16:00');
	$offsetlist[] = JHTML::_('select.option', '17', '+17:00');
	$offsetlist[] = JHTML::_('select.option', '18', '+18:00');
	$lists['offset'] = JHTML::_('select.genericlist', $offsetlist, 'offset', 'class="inputbox" size="1"', 'value', 'text', $supportConfig->offset);

	// Build the number of attachs list
	$attachslist[] = JHTML::_('select.option', '1', '1');
	$attachslist[] = JHTML::_('select.option', '2', '2');
	$attachslist[] = JHTML::_('select.option', '3', '3');
	$attachslist[] = JHTML::_('select.option', '4', '4');
	$lists['attachs_num'] = JHTML::_('select.genericlist', $attachslist, 'attachs_num', 'class="inputbox" size="1"', 'value', 'text', $supportConfig->attachs_num);

	// Build the less rate list
	$lessrate[] = JHTML::_('select.option', '1', '1');
	$lessrate[] = JHTML::_('select.option', '2', '2');
	$lessrate[] = JHTML::_('select.option', '3', '3');
	$lessrate[] = JHTML::_('select.option', '4', '4');
	$lessrate[] = JHTML::_('select.option', '5', '5');
	$lists['less_rate'] = JHTML::_('select.genericlist', $lessrate, 'less_rate', 'class="inputbox" size="1"',
		'value', 'text', $supportConfig->less_rate);

	// Build the weekday list
	$weekday[] = JHTML::_('select.option', '0', JText::_('week_full_sunday'));
	$weekday[] = JHTML::_('select.option', '1', JText::_('week_full_monday'));
	$weekday[] = JHTML::_('select.option', '2', JText::_('week_full_tuesday'));
	$weekday[] = JHTML::_('select.option', '3', JText::_('week_full_wednesday'));
	$weekday[] = JHTML::_('select.option', '4', JText::_('week_full_thursday'));
	$weekday[] = JHTML::_('select.option', '5', JText::_('week_full_friday'));
	$weekday[] = JHTML::_('select.option', '6', JText::_('week_full_saturday'));
	$lists['week_start'] = JHTML::_('select.genericlist', $weekday, 'week_start', 'class="inputbox" size="1"',
		'value', 'text', $supportConfig->week_start);

	// Build the Source select list
	$sourcelist[] = JHTML::_('select.option', '', JText::_('selectlist'));
	$sourcelist[] = JHTML::_('select.option', 'F', JText::_('fax'));
	$sourcelist[] = JHTML::_('select.option', 'P', JText::_('phone'));
	$sourcelist[] = JHTML::_('select.option', 'M', JText::_('email'));
	$sourcelist[] = JHTML::_('select.option', 'W', JText::_('website'));
	$sourcelist[] = JHTML::_('select.option', 'O', JText::_('other'));
	$lists['source'] = JHTML::_('select.genericlist', $sourcelist, 'default_source', 'class="inputbox" size="1"', 'value', 'text', $supportConfig->default_source);

	// Support user types select list
	$sup_usertype[] = JHTML::_('select.option', '7', JText::_('manager'));
	$sup_usertype[] = JHTML::_('select.option', '6', JText::_('team_leader'));
	$sup_usertype[] = JHTML::_('select.option', '5', JText::_('support_user'));
	$lists['sup_usertype'] = JHTML::_('select.genericlist', $sup_usertype, 'support_change_status', 'class="inputbox" size="1"', 'value', 'text', $supportConfig->support_change_status);

	// jQuery source select list
	$jquery[] = JHTML::_('select.option', '', JText::_('jquery_none'));
	$jquery[] = JHTML::_('select.option', 'local', JText::_('jquery_local'));
	$jquery[] = JHTML::_('select.option', 'google', 'Google Ajax API CDN (' . JText::_('jquery_ssl') . ')');
	$jquery[] = JHTML::_('select.option', 'microsoft', 'Microsoft CDN (' . JText::_('jquery_ssl') . ')');
	$jquery[] = JHTML::_('select.option', 'jquery', 'jQuery CDN');
	$lists['jquery_source'] = JHTML::_('select.genericlist', $jquery, 'jquery_source', 'class="inputbox" size="1"', 'value', 'text', $supportConfig->jquery_source);

	// Get user schedules
	$database->setQuery("SELECT id, profile FROM #__support_schedule ORDER BY id, profile ");
	$schedules_list = $database->loadObjectList();
	$numb_schedules = sizeof($schedules_list);

	$arr = array();
	for ($i = 0; $i < $numb_schedules; $i++) {
		$arr[] = get_object_vars($schedules_list[$i]);
	}
	$schedules[] = JHTML::_('select.option', '0', JText::_('schedule_select'));
	for ($i = 0; $i < $numb_schedules; $i++) {
		$schedules[] = JHTML::_('select.option', $arr[$i]['id'], $arr[$i]['profile']);
	}
	$lists['schedules'] = JHTML::_('select.genericlist', $schedules, 'id_schedule_default', 'class="inputbox" size="1"', 'value', 'text', $supportConfig->id_schedule_default);

	// Bug tracker statuses
	$bug_status[] = JHTML::_('select.option', 'P', JText::_('BUG_STATUS_P'));
	$bug_status[] = JHTML::_('select.option', 'O', JText::_('BUG_STATUS_O'));
	$bug_status[] = JHTML::_('select.option', 'I', JText::_('BUG_STATUS_I'));
	$bug_status[] = JHTML::_('select.option', 'R', JText::_('BUG_STATUS_R'));
	$bug_status[] = JHTML::_('select.option', 'C', JText::_('BUG_STATUS_C'));
	$bug_status[] = JHTML::_('select.option', 'D', JText::_('BUG_STATUS_D'));
	$lists['bug_status'] = JHTML::_('select.genericlist', $bug_status, 'bug_status', 'class="inputbox" size="1"', 'value', 'text', $supportConfig->bug_status);

	// Extra email notifications
	$extra_email_notification[] = JHTML::_('select.option', '0', JText::_('EXTRA_NOT_DISABLED'));
	$extra_email_notification[] = JHTML::_('select.option', '1', JText::_('EXTRA_NOT_FILTERED'));
	$extra_email_notification[] = JHTML::_('select.option', '2', JText::_('EXTRA_NOT_FULL'));
	$lists['extra_email_notification'] = JHTML::_('select.genericlist', $extra_email_notification, 'extra_email_notification', 'class="inputbox" size="1"', 'value', 'text', $supportConfig->extra_email_notification);

	// Build Themes select list
	$directory = JPATH_SITE . '/media/com_maqmahelpdesk/images/themes/';
	$handle = opendir($directory);
	while ($file = readdir($handle)) {
		$dir = JPath::clean($directory . '/' . $file);
		if (is_dir($dir) && $file != '.' && $file != '..') {
			$themes[] = JHTML::_('select.option', $file);
		}
	}
	closedir($handle);
	$lists['theme_icon'] = JHTML::_('select.genericlist', $themes, 'theme_icon', 'class="inputbox" size="1"', 'value', 'text', $supportConfig->theme_icon);

	// Build the SMS gateway list
	$smslist[] = JHTML::_('select.option', '', '');
	$smslist[] = JHTML::_('select.option', 'pswin', 'PSWin');
	$lists['sms_gateway'] = JHTML::_('select.genericlist', $smslist, 'sms_gateway', 'class="inputbox" size="1"', 'value', 'text', $supportConfig->sms_gateway);

	// HTML editor
	$editorlist[] = JHTML::_('select.option', '', '');
	$editorlist[] = JHTML::_('select.option', 'builtin', JText::_('HTML_EDITOR_BUILTIN'));
	$editorlist[] = JHTML::_('select.option', 'joomla', JText::_('HTML_EDITOR_JOOMLA'));
	$lists['editor'] = JHTML::_('select.genericlist', $editorlist, 'editor', 'class="inputbox" size="1"', 'value', 'text', $supportConfig->editor);

	// Status for the auto-close
	$sql = "SELECT `id` AS value, `description` AS text
			FROM `#__support_status`
			WHERE `show`=1 AND `status_group`='C'
			ORDER BY `description`";
	$database->setQuery($sql);
	$rows_status = $database->loadObjectList();
	$rows_status = array_merge(array(JHTML::_('select.option', '0', JText::_("SELECTLIST"))), $rows_status);
	$lists['autoclose_status'] = JHTML::_('select.genericlist', $rows_status, 'autoclose_status', 'class="inputbox" size="1"', 'value', 'text', $supportConfig->autoclose_status);

	HTML_config::configForm($lists);
}
