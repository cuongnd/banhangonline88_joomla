DELETE FROM `#__menu` WHERE `link` LIKE '%com_maqmahelpdesk%';
DELETE FROM `#__extensions` WHERE `element`='com_maqmahelpdesk';
DROP TABLE IF EXISTS `#__support_activities`;
DROP TABLE IF EXISTS `#__support_activity_rate`;
DROP TABLE IF EXISTS `#__support_activity_type`;
DROP TABLE IF EXISTS `#__support_addon`;
DROP TABLE IF EXISTS `#__support_addon_contract`;
DROP TABLE IF EXISTS `#__support_announce`;
DROP TABLE IF EXISTS `#__support_announce_mail`;
DROP TABLE IF EXISTS `#__support_bbb`;
DROP TABLE IF EXISTS `#__support_bbb_invites`;
DROP TABLE IF EXISTS `#__support_bbb_links`;
DROP TABLE IF EXISTS `#__support_bookmark`;
DROP TABLE IF EXISTS `#__support_bugtracker`;
DROP TABLE IF EXISTS `#__support_bugtracker_messages`;
DROP TABLE IF EXISTS `#__support_category`;
DROP TABLE IF EXISTS `#__support_client`;
DROP TABLE IF EXISTS `#__support_client_docs`;
DROP TABLE IF EXISTS `#__support_client_info`;
DROP TABLE IF EXISTS `#__support_client_users`;
DROP TABLE IF EXISTS `#__support_client_wk`;
DROP TABLE IF EXISTS `#__support_components`;
DROP TABLE IF EXISTS `#__support_config`;
DROP TABLE IF EXISTS `#__support_contract`;
DROP TABLE IF EXISTS `#__support_contract_comp`;
DROP TABLE IF EXISTS `#__support_contract_fields`;
DROP TABLE IF EXISTS `#__support_contract_fields_values`;
DROP TABLE IF EXISTS `#__support_contract_files`;
DROP TABLE IF EXISTS `#__support_contract_template`;
DROP TABLE IF EXISTS `#__support_country`;
DROP TABLE IF EXISTS `#__support_custom_fields`;
DROP TABLE IF EXISTS `#__support_department_group`;
DROP TABLE IF EXISTS `#__support_discussions`;
DROP TABLE IF EXISTS `#__support_discussions_messages`;
DROP TABLE IF EXISTS `#__support_discussions_subscribe`;
DROP TABLE IF EXISTS `#__support_discussions_votes`;
DROP TABLE IF EXISTS `#__support_dl`;
DROP TABLE IF EXISTS `#__support_dl_access`;
DROP TABLE IF EXISTS `#__support_dl_category`;
DROP TABLE IF EXISTS `#__support_dl_group`;
DROP TABLE IF EXISTS `#__support_dl_license`;
DROP TABLE IF EXISTS `#__support_dl_notify`;
DROP TABLE IF EXISTS `#__support_dl_stats`;
DROP TABLE IF EXISTS `#__support_dl_users`;
DROP TABLE IF EXISTS `#__support_dl_version`;
DROP TABLE IF EXISTS `#__support_escalation_config`;
DROP TABLE IF EXISTS `#__support_export`;
DROP TABLE IF EXISTS `#__support_export_profile`;
DROP TABLE IF EXISTS `#__support_field_value`;
DROP TABLE IF EXISTS `#__support_file`;
DROP TABLE IF EXISTS `#__support_file_notify`;
DROP TABLE IF EXISTS `#__support_form`;
DROP TABLE IF EXISTS `#__support_form_action`;
DROP TABLE IF EXISTS `#__support_form_field`;
DROP TABLE IF EXISTS `#__support_glossary`;
DROP TABLE IF EXISTS `#__support_holidays`;
DROP TABLE IF EXISTS `#__support_kb`;
DROP TABLE IF EXISTS `#__support_kb_category`;
DROP TABLE IF EXISTS `#__support_kb_comment`;
DROP TABLE IF EXISTS `#__support_license`;
DROP TABLE IF EXISTS `#__support_links`;
DROP TABLE IF EXISTS `#__support_log`;
DROP TABLE IF EXISTS `#__support_mail_fetch`;
DROP TABLE IF EXISTS `#__support_mail_fetch_ignore`;
DROP TABLE IF EXISTS `#__support_mail_log`;
DROP TABLE IF EXISTS `#__support_mail_queue`;
DROP TABLE IF EXISTS `#__support_note`;
DROP TABLE IF EXISTS `#__support_options`;
DROP TABLE IF EXISTS `#__support_permission`;
DROP TABLE IF EXISTS `#__support_permission_category`;
DROP TABLE IF EXISTS `#__support_priority`;
DROP TABLE IF EXISTS `#__support_rate`;
DROP TABLE IF EXISTS `#__support_reply`;
DROP TABLE IF EXISTS `#__support_reports`;
DROP TABLE IF EXISTS `#__support_schedule`;
DROP TABLE IF EXISTS `#__support_schedule_weekday`;
DROP TABLE IF EXISTS `#__support_status`;
DROP TABLE IF EXISTS `#__support_store_integration`;
DROP TABLE IF EXISTS `#__support_store_integration_log`;
DROP TABLE IF EXISTS `#__support_sysmsgs`;
DROP TABLE IF EXISTS `#__support_task`;
DROP TABLE IF EXISTS `#__support_ticket`;
DROP TABLE IF EXISTS `#__support_ticket_resp`;
DROP TABLE IF EXISTS `#__support_ticket_screenr`;
DROP TABLE IF EXISTS `#__support_troubleshooter`;
DROP TABLE IF EXISTS `#__support_twitter`;
DROP TABLE IF EXISTS `#__support_twitter_log`;
DROP TABLE IF EXISTS `#__support_updates`;
DROP TABLE IF EXISTS `#__support_users`;
DROP TABLE IF EXISTS `#__support_user_fields`;
DROP TABLE IF EXISTS `#__support_user_values`;
DROP TABLE IF EXISTS `#__support_views`;
DROP TABLE IF EXISTS `#__support_wk_fields`;
DROP TABLE IF EXISTS `#__support_workgroup`;
DROP TABLE IF EXISTS `#__support_workgroup_category_assign`;