<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2016                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
 */

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2016
 * $Id$
 *
 */

/**
 * Settings metadata file
 */
return array(
  'contact_view_options' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'contact_view_options',
    'type' => 'String',
    'html_type' => 'checkboxes',
    'pseudoconstant' => array(
      'optionGroupName' => 'contact_view_options',
    ),
    'default' => '123456789101113',
    'add' => '4.1',
    'title' => 'Viewing Contacts',
    'is_domain' => '1',
    'is_contact' => 0,
    'description' => NULL,
    'help_text' => NULL,
  ),
  'contact_edit_options' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'contact_edit_options',
    'type' => 'String',
    'html_type' => 'checkboxes',
    'pseudoconstant' => array(
      'optionGroupName' => 'contact_edit_options',
    ),
    'default' => '123456789111214151617',
    'add' => '4.1',
    'title' => 'Editing Contacts',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => NULL,
    'help_text' => NULL,
  ),
  'advanced_search_options' => array(
    'group_name' => 'CiviCRM Preferences',
    'name' => 'advanced_search_options',
    'type' => 'String',
    'html_type' => 'checkboxes',
    'pseudoconstant' => array(
      'optionGroupName' => 'advanced_search_options',
    ),
    'default' => '123456789101112131516171819',
    'add' => '4.1',
    'title' => 'Contact Search',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => NULL,
    'help_text' => NULL,
  ),
  'user_dashboard_options' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'user_dashboard_options',
    'type' => 'String',
    'html_type' => 'checkboxes',
    'pseudoconstant' => array(
      'optionGroupName' => 'user_dashboard_options',
    ),
    'default' => '1234578',
    'add' => '4.1',
    'title' => 'Contact Dashboard',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => NULL,
    'help_text' => NULL,
  ),
  'address_options' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'address_options',
    'type' => 'String',
    'html_type' => 'Text',
    'pseudoconstant' => array(
      'optionGroupName' => 'address_options',
    ),
    'default' => '123456891011',
    'add' => '4.1',
    'title' => 'Addressing Options',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => NULL,
    'help_text' => NULL,
  ),
  'address_format' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'address_format',
    'type' => 'String',
    'html_type' => 'TextArea',
    'default' => "{contact.address_name}\n{contact.street_address}\n{contact.supplemental_address_1}\n{contact.supplemental_address_2}\n{contact.city}{, }{contact.state_province}{ }{contact.postal_code}\n{contact.country}",
    'add' => '4.1',
    'title' => 'Address Format',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => NULL,
    'help_text' => NULL,
  ),
  'mailing_format' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'mailing_format',
    'type' => 'String',
    'html_type' => 'Text',
    'default' => "{contact.addressee}\n{contact.street_address}\n{contact.supplemental_address_1}\n{contact.supplemental_address_2}\n{contact.city}{, }{contact.state_province}{ }{contact.postal_code}\n{contact.country}",
    'add' => '4.1',
    'title' => 'Mailing Format',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => NULL,
    'help_text' => NULL,
  ),
  'display_name_format' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'display_name_format',
    'type' => 'String',
    'html_type' => 'Text',
    'default' => '{contact.individual_prefix}{ }{contact.first_name}{ }{contact.last_name}{ }{contact.individual_suffix}',
    'add' => '4.1',
    'title' => 'Display Name Format',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => NULL,
    'help_text' => NULL,
  ),
  'sort_name_format' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'sort_name_format',
    'type' => 'String',
    'html_type' => 'Text',
    'default' => '{contact.last_name}{, }{contact.first_name}',
    'add' => '4.1',
    'title' => 'Sort Name Format',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => NULL,
    'help_text' => NULL,
  ),
  'remote_profile_submissions' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'remote_profile_submissions',
    'type' => 'Boolean',
    'quick_form_type' => 'YesNo',
    'default' => FALSE,
    'html_type' => 'radio',
    'add' => '4.7',
    'title' => 'Accept profile submissions from external sites',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'If enabled, CiviCRM will permit submissions from external sites to profiles. This is disabled by default to limit abuse.',
    'help_text' => NULL,
  ),
  'editor_id' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'editor_id',
    'type' => 'String',
    'html_type' => 'Select',
    'default' => 'CKEditor',
    'add' => '4.1',
    'title' => 'Wysiwig Editor',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => NULL,
    'help_text' => NULL,
  ),
  'contact_ajax_check_similar' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'contact_ajax_check_similar',
    'type' => 'String',
    'html_type' => 'Text',
    'default' => '1',
    'add' => '4.1',
    'title' => 'Ajax Check Similar',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => NULL,
    'help_text' => NULL,
  ),
  'ajaxPopupsEnabled' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'ajaxPopupsEnabled',
    'type' => 'Boolean',
    'quick_form_type' => 'YesNo',
    'default' => 1,
    'add' => '4.5',
    'title' => 'Ajax Popups Enabled',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => NULL,
    'help_text' => NULL,
  ),
  'activity_assignee_notification' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'activity_assignee_notification',
    'type' => 'String',
    'html_type' => 'Text',
    'default' => '1',
    'add' => '4.1',
    'title' => 'Notify Activity Assignees',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => NULL,
    'help_text' => NULL,
  ),
  'activity_assignee_notification_ics' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'activity_assignee_notification_ics',
    'type' => 'String',
    'html_type' => 'Text',
    'default' => '0',
    'add' => '4.3',
    'title' => 'Include ICal Invite to Activity Assignees',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => NULL,
    'help_text' => NULL,
  ),
  'contact_autocomplete_options' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'contact_autocomplete_options',
    'type' => 'String',
    'quick_form_type' => 'CheckBox',
    'pseudoconstant' => array(
      'callback' => 'CRM_Admin_Form_Setting_Search::getContactAutocompleteOptions',
    ),
    'default' => '12',
    'add' => '4.1',
    'title' => 'Autocomplete Contact Search',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => "Selected fields will be displayed in back-office autocomplete dropdown search results (Quick Search, etc.). Contact Name is always included.",
    'help_text' => NULL,
  ),
  'contact_reference_options' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'contact_reference_options',
    'type' => 'String',
    'quick_form_type' => 'CheckBox',
    'pseudoconstant' => array(
      'callback' => 'CRM_Admin_Form_Setting_Search::getContactReferenceOptions',
    ),
    'default' => '12',
    'add' => '4.1',
    'title' => 'Contact Reference Options',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => "Selected fields will be displayed in autocomplete dropdown search results for 'Contact Reference' custom fields. Contact Name is always included. NOTE: You must assign 'access contact reference fields' permission to the anonymous role if you want to use custom contact reference fields in profiles on public pages. For most situations, you should use the 'Limit List to Group' setting when configuring a contact reference field which will be used in public forms to prevent exposing your entire contact list.",
    'help_text' => NULL,
  ),
  'contact_smart_group_display' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'contact_smart_group_display',
    'type' => 'String',
    'html_type' => 'radio',
    'default' => '1',
    'add' => '4.7',
    'title' => ts('Viewing Smart Groups'),
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => NULL,
    'help_text' => NULL,
  ),
  'smart_group_cache_refresh_mode' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'smart_group_cache_refresh_mode',
    'type' => 'String',
    'html_type' => 'radio',
    'default' => 'opportunistic',
    'add' => '4.7',
    'title' => 'Smart Group Refresh Mode',
    'is_domain' => 1,
    'is_contact' => 0,
    'pseudoconstant' => array(
      'callback' => 'CRM_Contact_BAO_GroupContactCache::getModes',
    ),
    'description' => 'Should the smart groups be by cron jobs or user actions',
    'help_text' => 'In "Opportunistic Flush" mode, caches are flushed in response to user actions; this mode is broadly compatible but may add latency during form-submissions. In "Cron Flush" mode, you should schedule a cron job to flush caches; this can improve latency on form-submissions but requires more setup.',
  ),
  'installed' => array(
    'bootstrap_comment' => 'This is a boot setting which may be loaded during bootstrap. Defaults are loaded via SettingsBag::getSystemDefaults().',
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'installed',
    'type' => 'Boolean',
    'quick_form_type' => 'YesNo',
    'default' => FALSE,
    'add' => '4.7',
    'title' => 'System Installed',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'A flag indicating whether this system has run a post-installation routine',
    'help_text' => NULL,
  ),
  'max_attachments' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'max_attachments',
    'legacy_key' => 'maxAttachments',
    'type' => 'Integer',
    'quick_form_type' => 'Element',
    'html_type' => 'text',
    'html_attributes' => array(
      'size' => 2,
      'maxlength' => 8,
    ),
    'default' => 3,
    'add' => '4.3',
    'title' => 'Maximum Attachments',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Maximum number of files (documents, images, etc.) which can attached to emails or activities.',
    'help_text' => NULL,
  ),
  'maxFileSize' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'maxFileSize',
    'type' => 'Integer',
    'quick_form_type' => 'Element',
    'html_type' => 'text',
    'html_attributes' => array(
      'size' => 2,
      'maxlength' => 8,
    ),
    'default' => 3,
    'add' => '4.3',
    'title' => 'Maximum File Size (in MB)',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Maximum Size of file (documents, images, etc.) which can attached to emails or activities.<br />Note: php.ini should support this file size.',
    'help_text' => NULL,
  ),
  'contact_undelete' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'contact_undelete',
    'type' => 'Boolean',
    'quick_form_type' => 'YesNo',
    'default' => 1,
    'add' => '4.3',
    'title' => 'Contact Trash and Undelete',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'If enabled, deleted contacts will be moved to trash (instead of being destroyed). Users with the proper permission are able to search for the deleted contacts and restore them (or delete permanently).',
    'help_text' => NULL,
  ),
  'allowPermDeleteFinancial' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'allowPermDeleteFinancial',
    'type' => 'Boolean',
    'quick_form_type' => 'YesNo',
    'default' => FALSE,
    'add' => '4.3',
    'title' => 'Contact Permanent Delete',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Allow Permanent Delete for contacts who are linked to live financial transactions',
    'help_text' => NULL,
  ),
  'securityAlert' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'securityAlert',
    'type' => 'Boolean',
    'quick_form_type' => 'YesNo',
    'default' => 1,
    'add' => '4.4',
    'title' => 'Status Alerts',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => "If enabled, CiviCRM will display pop-up notifications (no more than once per day) for security and misconfiguration issues identified in the system check.",
    'help_text' => NULL,
  ),
  'doNotAttachPDFReceipt' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'doNotAttachPDFReceipt',
    'type' => 'Boolean',
    'quick_form_type' => 'YesNo',
    'default' => 0,
    'add' => '4.3',
    'title' => 'Attach PDF copy to receipts',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => "If enabled, CiviCRM sends PDF receipt as an attachment during event signup or online contribution.",
    'help_text' => NULL,
  ),
  'wkhtmltopdfPath' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'wkhtmltopdfPath',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_attributes' => array(
      'size' => 64,
      'maxlength' => 256,
    ),
    'html_type' => 'Text',
    'default' => NULL,
    'add' => '4.3',
    'title' => 'Path to wkhtmltopdf executable',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => NULL,
    'help_text' => NULL,
  ),
  'recaptchaOptions' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'recaptchaOptions',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_attributes' => array(
      'size' => 64,
      'maxlength' => 64,
    ),
    'html_type' => 'Text',
    'default' => NULL,
    'add' => '4.3',
    'title' => 'Recaptcha Options',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'You can specify the reCAPTCHA theme options as comma separated data.(eg: theme:\'blackglass\', lang : \'fr\' ). Check the available options at <a href="https://developers.google.com/recaptcha/docs/display#config">Customizing the Look and Feel of reCAPTCHA</a>.',
    'help_text' => NULL,
  ),
  'recaptchaPublicKey' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'recaptchaPublicKey',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_attributes' => array(
      'size' => 64,
      'maxlength' => 64,
    ),
    'html_type' => 'Text',
    'default' => NULL,
    'add' => '4.3',
    'title' => 'Recaptcha Site Key',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => NULL,
    'help_text' => NULL,
  ),
  'recaptchaPrivateKey' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'recaptchaPrivateKey',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_attributes' => array(
      'size' => 64,
      'maxlength' => 64,
    ),
    'html_type' => 'Text',
    'default' => NULL,
    'add' => '4.3',
    'title' => 'Recaptcha Secret Key',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => NULL,
    'help_text' => NULL,
  ),
  'checksum_timeout' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'checksum_timeout',
    'type' => 'Integer',
    'quick_form_type' => 'Element',
    'html_attributes' => array(
      'size' => 2,
      'maxlength' => 8,
    ),
    'html_type' => 'Text',
    'default' => 7,
    'add' => '4.3',
    'title' => 'Checksum Lifespan',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => NULL,
    'help_text' => NULL,
  ),
  'blogUrl' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'blogUrl',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_attributes' => array(
      'size' => 64,
      'maxlength' => 128,
    ),
    'html_type' => 'Text',
    'default' => '*default*',
    'add' => '4.3',
    'title' => 'Blog Feed URL',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Blog feed URL used by the blog dashlet',
    'help_text' => 'Use "*default*" for the system default or override with a custom URL',
  ),
  'communityMessagesUrl' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'communityMessagesUrl',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_attributes' => array(
      'size' => 64,
      'maxlength' => 128,
    ),
    'html_type' => 'Text',
    'default' => '*default*',
    'add' => '4.3',
    'title' => 'Community Messages URL',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Service providing CiviCRM community messages',
    'help_text' => 'Use "*default*" for the system default or override with a custom URL',
  ),
  'gettingStartedUrl' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'gettingStartedUrl',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_attributes' => array(
      'size' => 64,
      'maxlength' => 128,
    ),
    'html_type' => 'Text',
    'default' => '*default*',
    'add' => '4.3',
    'title' => 'Getting Started URL',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Service providing the Getting Started data',
    'help_text' => 'Use "*default*" for the system default or override with a custom URL',
  ),
  'resCacheCode' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'resCacheCode',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_type' => 'text',
    'default' => NULL,
    'add' => '4.3',
    'title' => 'resCacheCode',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Code appended to resource URLs (JS/CSS) to coerce HTTP caching',
    'help_text' => NULL,
  ),
  'verifySSL' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'verifySSL',
    'type' => 'Boolean',
    'quick_form_type' => 'YesNo',
    'default' => 1,
    'add' => '4.3',
    'title' => 'Verify SSL?',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'If disabled, outbound web-service requests will allow unverified, insecure HTTPS connections',
    'help_text' => 'Unless you are absolutely unable to configure your server to check the SSL certificate of the remote server you should leave this set to Yes',
  ),
  'enableSSL' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'enableSSL',
    'type' => 'Boolean',
    'quick_form_type' => 'YesNo',
    'default' => 0,
    'add' => '4.5',
    'title' => 'Force SSL?',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'If enabled, inbound HTTP requests for sensitive pages will be redirected to HTTPS.',
    'help_text' => 'If enabled, inbound HTTP requests for sensitive pages will be redirected to HTTPS.',
  ),
  'wpBasePage' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'wpBasePage',
    'type' => 'String',
    'html_type' => 'text',
    'quick_form_type' => 'Element',
    'default' => '',
    'add' => '4.3',
    'title' => 'WordPress Base Page',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'If set, CiviCRM will use this setting as the base url.',
    'help_text' => 'By default, CiviCRM will generate front-facing pages using the home page at http://wp/ as its base. If you want to use a different template for CiviCRM pages, set the path here.',
  ),
  'secondDegRelPermissions' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'secondDegRelPermissions',
    'type' => 'Boolean',
    'quick_form_type' => 'YesNo',
    'default' => 0,
    'add' => '4.3',
    'title' => 'Allow second-degree relationship permissions',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => "If enabled, contacts with the permission to edit a related contact will inherit that contact's permission to edit other related contacts",
    'help_text' => NULL,
  ),
  'enable_components' => array(
    'bootstrap_comment' => 'This is a boot setting which may be loaded during bootstrap. Defaults are loaded via SettingsBag::getSystemDefaults().',
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'enable_components',
    'type' => 'Array',
    'quick_form_type' => 'Element',
    'html_type' => 'advmultiselect',
    'html_attributes' => array(
      'size' => 5,
      'style' => 'width:150px',
      'class' => 'advmultiselect',
    ),
    'default' => NULL,
    'add' => '4.4',
    'title' => 'Enable Components',
    'is_domain' => '1',
    'is_contact' => 0,
    'description' => NULL,
    'help_text' => NULL,
    'on_change' => array(
      'CRM_Case_Info::onToggleComponents',
      'CRM_Core_Component::flushEnabledComponents',
      'call://resources/resetCacheCode',
    ),
  ),
  'disable_core_css' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'disable_core_css',
    'type' => 'Boolean',
    'quick_form_type' => 'YesNo',
    'default' => '0',
    'add' => '4.4',
    'title' => 'Disable CiviCRM css',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Prevent the stylesheet "civicrm.css" from being loaded.',
    'help_text' => NULL,
  ),
  'empoweredBy' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'empoweredBy',
    'type' => 'Boolean',
    'quick_form_type' => 'YesNo',
    'default' => 1,
    'add' => '4.5',
    'title' => 'Display "empowered by CiviCRM"',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'When enabled, "empowered by CiviCRM" is displayed at the bottom of public forms.',
    'help_text' => NULL,
  ),
  'logging_no_trigger_permission' => array(
    'add' => '4.7',
    'is_domain' => 1,
    'is_contact' => 0,
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'help_text' => ts('(EXPERIMENTAL) If the MySQL user does not have permission to administer triggers, then you must create the triggers outside CiviCRM. No support is provided for this configuration.'),
    'name' => 'logging_no_trigger_permission',
    'type' => 'Boolean',
    'quick_form_type' => 'YesNo',
    'html_type' => '',
    'default' => 0,
    'title' => '(EXPERIMENTAL) MySQL user does not have trigger permissions',
    'description' => 'Set this when you intend to manage trigger creation outside of CiviCRM',
  ),
  'logging' => array(
    'add' => '4.7',
    'help_text' => NULL,
    'is_domain' => 1,
    'is_contact' => 0,
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'logging',
    'type' => 'Boolean',
    'quick_form_type' => 'YesNo',
    'html_type' => '',
    'default' => '0',
    'title' => 'Logging',
    'description' => 'If enabled, all actions will be logged with a complete record of changes.',
    'validate_callback' => 'CRM_Logging_Schema::checkLoggingSupport',
    'on_change' => array(
      'CRM_Logging_Schema::onToggle',
    ),
  ),
  'logging_uniqueid_date' => array(
    'add' => '4.7',
    'help_text' => ts('This is the date when CRM-18193 was implemented'),
    'is_domain' => 1,
    'is_contact' => 0,
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'logging_uniqueid_date',
    'type' => 'Date',
    'quick_form_type' => 'DateTime',
    'html_type' => '',
    'default' => NULL,
    'title' => 'Logging Unique ID not recorded before',
    'description' => 'This is the date when CRM-18193 was implemented',
  ),
  'logging_all_tables_uniquid' => array(
    'add' => '4.7',
    'help_text' => ts('This indicates there are no tables holdng pre-uniqid log_conn_id values (CRM-18193)'),
    'is_domain' => 1,
    'is_contact' => 0,
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'logging_all_tables_uniquid',
    'type' => 'Boolean',
    'quick_form_type' => 'YesNo',
    'html_type' => '',
    'default' => 0,
    'title' => 'All tables use Unique Connection ID',
    'description' => 'Do some tables pre-date CRM-18193?',
  ),
  'userFrameworkUsersTableName' => array(
    'add' => '4.7',
    'help_text' => NULL,
    'is_domain' => 1,
    'is_contact' => 0,
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'userFrameworkUsersTableName',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_type' => 'text',
    'html_attributes' => array(
      'size' => '32',
      'maxlength' => '64',
    ),
    'default' => NULL,
    'title' => 'Drupal Users Table Name',
    'description' => '',
  ),
  'wpLoadPhp' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'wpLoadPhp',
    'type' => 'String',
    'html_type' => 'text',
    'quick_form_type' => 'Element',
    'default' => '',
    'add' => '4.6',
    'title' => 'WordPress Path to wp-load.php',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'CiviCRM will use this setting as path to bootstrap WP.',
    'help_text' => NULL,
  ),
  'secure_cache_timeout_minutes' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'secure_cache_timeout_minutes',
    'type' => 'Integer',
    'quick_form_type' => 'Element',
    'html_type' => 'text',
    'html_attributes' => array(
      'size' => 2,
      'maxlength' => 8,
    ),
    'default' => 20,
    'add' => '4.7',
    'title' => 'Secure Cache Timeout',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Maximum number of minutes that secure form data should linger',
    'help_text' => NULL,
  ),
  'site_id' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'site_id',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_type' => 'text',
    'default' => '',
    'add' => '4.6',
    'title' => 'Unique Site ID',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => NULL,
    'help_text' => NULL,
  ),
  'systemStatusCheckResult' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'systemStatusCheckResult',
    'type' => 'Integer',
    'quick_form_type' => 'Element',
    'html_type' => 'text',
    'default' => 0,
    'add' => '4.7',
    'title' => 'systemStatusCheckResult',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => NULL,
    'help_text' => NULL,
  ),
  'recentItemsMaxCount' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'recentItemsMaxCount',
    'type' => 'Integer',
    'quick_form_type' => 'Element',
    'html_type' => 'text',
    'html_attributes' => array(
      'size' => 2,
      'maxlength' => 3,
    ),
    'default' => 20,
    'add' => '4.7',
    'title' => 'Size of "Recent Items" stack',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'How many items should CiviCRM store in it\'s "Recently viewed" list.',
    'help_text' => NULL,
  ),
  'recentItemsProviders' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'recentItemsProviders',
    'type' => 'Array',
    'html_type' => 'Select',
    'quick_form_type' => 'Select',
    'html_attributes' => array(
      'multiple' => 1,
      'class' => 'crm-select2',
    ),
    'default' => '',
    'add' => '4.7',
    'title' => 'Recent Items Providers',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'What providers may save views in CiviCRM\'s "Recently viewed" list. If empty, all are in.',
    'help_text' => NULL,
    'pseudoconstant' => array(
      'callback' => 'CRM_Utils_Recent::getProviders',
    ),
  ),
  'dedupe_default_limit' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'dedupe_default_limit',
    'type' => 'Integer',
    'default' => 0,
    'quick_form_type' => 'Element',
    'html_type' => 'text',
    'add' => '4.7',
    'title' => 'Default limit for dedupe screen',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => ts('Default to only loading matches against this number of contacts'),
    'help_text' => ts('Deduping larger databases can crash the server. By configuring a limit other than 0 here the dedupe query will only search for matches against a limited number of contacts.'),
  ),
);
