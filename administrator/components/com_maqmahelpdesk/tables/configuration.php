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

class MaQmaHelpdeskTableConfiguration extends JTable
{
	var $id = null;
	var $update_version = null;
	var $support_version = null;
	var $mail_assign = null;
	var $docspath = null;
	var $extensions = null;
	var $maxAllowed = null;
	var $public_attach = null;
	var $attachs_num = null;
	var $receive_mail = null;
	var $ac_active = null;
	var $ac_days = null;
	var $rating = null;
	var $kb_popinfo = 0;
	var $kb_rating = null;
	var $notify_rate = 0;
	var $less_rate = 2;
	var $unregister = 0;
	var $date_short = null;
	var $date_long = null;
	var $week_start = 1;
	var $kb_moderate = 0;
	var $faq_kb_hits = 0;
	var $faq_kb_nhits = 100;
	var $faq_kb_manual = 0;
	var $readmail_create_user = 0;
	var $license_agreement = 0;
	var $company_info = 0;
	var $create_client = 0;
	var $rf_phone = 0;
	var $rf_fax = 0;
	var $rf_mobile = 0;
	var $rf_address1 = 0;
	var $rf_address2 = 0;
	var $rf_zipcode = 0;
	var $rf_location = 0;
	var $rf_country = 0;
	var $rf_city = 0;
	var $users_registration = 0;
	var $default_source = null;
	var $rss = 0;
	var $minutes = 15;
	var $users_login = 0;
	var $use_uncategorized = 0;
	var $client_change_status = 0;
	var $support_change_status = 0;
	var $register_user_change_status = 0;
	var $support_only_show_assign = 0;
	var $support_workgroup_only = 0;
	var $duedate_algoritm = 0;
	var $duedate_holidays = 0;
	var $duedate_vacations = 0;
	var $duedate_firstday = 0;
	var $duedate_schedule = 0;
	var $duedate_firstday_minimum = 0;
	var $duedate_hoursday = 8;
	var $id_schedule_default = 0;
	var $mail_queue = 0;
	var $extra_email_notification = 0;
	var $show_dashboard_support = 0;
	var $show_dashboard_customer = 0;
	var $anonymous_tickets = 0;
	var $post_comments_in_wall = 0;
	var $use_jomsocial_avatars = 0;
	var $post_kb_creation_in_wall = 0;
	var $google_adwords = null;
	var $integrate_mtree = 0;
	var $integrate_sobi = 0;
	var $discussions_moderated = 0;
	var $integrate_artofuser = 0;
	var $use_cb_avatars = 0;
	var $mobile_interface = 0;
	var $offset = 0;
	var $js_post_question_wall = 0;
	var $js_post_answer_wall = 0;
	var $js_post_votes_wall = 0;
	var $js_answer_selected_wall = 0;
	var $js_post_bugtracker_wall = 0;
	var $profile_required = 0;
	var $stopspam = 0;
	var $hide_powered = 0;
	var $common_ticket_views = 0;
	var $currency = '&euro;';
	var $use_merge = 1;
	var $use_as_reply = 1;
	var $use_parent = 1;
	var $use_travel = 1;
	var $use_type = 1;
	var $integrate_jbolo = 0;
	var $integrate_digistore = 0;
	var $kb_approvement = 0;
	var $ticket_ignore_letter = null;
	var $bbb_url = null;
	var $bbb_apikey = null;
	var $bug_status = 'P';
	var $show_login_form = 1;
	var $show_login_details = 1;
	var $theme_icon = 'default';
	var $customfields_search = 0;
	var $tickets_numbers = 1;
	var $kbsocial = 1;
	var $system_log = 1;
	var $sms_assign = 0;
	var $sms_username = null;
	var $sms_password = null;
	var $sms_gateway = null;
	var $use_jomwall_avatars = 0;
	var $use_department_groups = 0;
	var $download_notification = 0;
	var $faq_single_page = 0;
	var $discussions_anonymous = 0;
	var $downloads_badges = 1;
	var $departments_template = null;
	var $screenr_account = null;
	var $screenr_api_id = null;
	var $jquery_source = 'google';
	var $kb_enable_rating = 1;
	var $kb_enable_comments = 1;
	var $digistore_domains = 1;
	var $github_username = null;
	var $github_password = null;
	var $show_kb_frontpage = 1;
	var $include_bootstrap = 1;
	var $dateonly_format = "d/m/Y";
	var $manual_times = 0;
	var $editor = 'builtin';
	var $autoclose_status = 0;
	var $use_eshop_suite_avatars = 0;
	var $date_country_code = '';
	var $tickets_per_department = 0;

	function __construct(&$_db)
	{
		parent::__construct('#__support_config', 'id', $_db);
	}
}