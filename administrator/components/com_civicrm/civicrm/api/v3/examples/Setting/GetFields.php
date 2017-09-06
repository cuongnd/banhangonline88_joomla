<?php
/**
 * Test Generated example demonstrating the Setting.getfields API.
 *
 * Demonstrate return from getfields - see subfolder for variants
 *
 * @return array
 *   API result array
 */
function setting_getfields_example() {
  $params = array();

  try{
    $result = civicrm_api3('Setting', 'getfields', $params);
  }
  catch (CiviCRM_API3_Exception $e) {
    // Handle error here.
    $errorMessage = $e->getMessage();
    $errorCode = $e->getErrorCode();
    $errorData = $e->getExtraParams();
    return array(
      'error' => $errorMessage,
      'error_code' => $errorCode,
      'error_data' => $errorData,
    );
  }

  return $result;
}

/**
 * Function returns array of result expected from previous function.
 *
 * @return array
 *   API result array
 */
function setting_getfields_expectedresult() {

  $expectedResult = array(
    'is_error' => 0,
    'version' => 3,
    'count' => 91,
    'values' => array(
      'address_standardization_provider' => array(
        'group_name' => 'Address Preferences',
        'group' => 'address',
        'name' => 'address_standardization_provider',
        'type' => 'String',
        'html_type' => 'Select',
        'default' => '',
        'add' => '4.1',
        'title' => 'Address Standardization Provider.',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => 'CiviCRM includes an optional plugin for interfacing with the United States Postal Services (USPS) Address Standardization web service. You must register to use the USPS service at https://www.usps.com/business/web-tools-apis/address-information.htm. If you are approved, they will provide you with a User ID and the URL for the service. Plugins for other address standardization services may be available from 3rd party developers. If installed, they will be included in the drop-down below. ',
      ),
      'address_standardization_userid' => array(
        'group_name' => 'Address Preferences',
        'group' => 'address',
        'name' => 'address_standardization_userid',
        'type' => 'String',
        'html_type' => 'Text',
        'default' => '',
        'add' => '4.1',
        'title' => 'Web service user ID',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'address_standardization_url' => array(
        'group_name' => 'Address Preferences',
        'group' => 'address',
        'name' => 'address_standardization_url',
        'type' => 'Text',
        'html_type' => 'Text',
        'default' => '',
        'add' => '4.1',
        'title' => 'Web Service URL',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => 'Web Service URL',
        'validate_callback' => 'CRM_Utils_Rule::url',
      ),
      'tag_unconfirmed' => array(
        'group_name' => 'Campaign Preferences',
        'group' => 'campaign',
        'name' => 'tag_unconfirmed',
        'type' => 'String',
        'html_type' => 'Text',
        'default' => 'Unconfirmed',
        'add' => '4.1',
        'title' => 'Tag for Unconfirmed Petition Signers',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => 'If set, new contacts that are created when signing a petition are assigned a tag of this name.',
      ),
      'petition_contacts' => array(
        'group_name' => 'Campaign Preferences',
        'group' => 'campaign',
        'name' => 'petition_contacts',
        'type' => 'String',
        'html_type' => 'Text',
        'default' => 'Petition Contacts',
        'add' => '4.1',
        'title' => 'Petition Signers Group',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => 'If set, new contacts that are created when signing a petition are assigned a tag of this name.',
      ),
      'cvv_backoffice_required' => array(
        'group_name' => 'Contribute Preferences',
        'group' => 'contribute',
        'name' => 'cvv_backoffice_required',
        'type' => 'Boolean',
        'quick_form_type' => 'YesNo',
        'default' => '1',
        'add' => '4.1',
        'title' => 'CVV required for backoffice?',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'Is the CVV code required for back office credit card transactions',
        'help_text' => 'If set it back-office credit card transactions will required a cvv code. Leave as required unless you have a very strong reason to change',
      ),
      'contact_view_options' => array(
        'group_name' => 'CiviCRM Preferences',
        'group' => 'core',
        'name' => 'contact_view_options',
        'type' => 'String',
        'html_type' => 'checkboxes',
        'pseudoconstant' => array(
          'optionGroupName' => 'contact_view_options',
        ),
        'default' => array(
          '0' => '1',
          '1' => '2',
          '2' => '3',
          '3' => '4',
          '4' => '5',
          '5' => '6',
          '6' => '7',
          '7' => '8',
          '8' => '9',
          '9' => '10',
          '10' => '11',
          '11' => '13',
        ),
        'add' => '4.1',
        'title' => 'Viewing Contacts',
        'is_domain' => '1',
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
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
        'default' => array(
          '0' => '1',
          '1' => '2',
          '2' => '3',
          '3' => '4',
          '4' => '5',
          '5' => '6',
          '6' => '7',
          '7' => '8',
          '8' => '9',
          '9' => '10',
          '10' => '11',
        ),
        'add' => '4.1',
        'title' => 'Editing Contacts',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'advanced_search_options' => array(
        'group_name' => 'CiviCRM Preferences',
        'name' => 'advanced_search_options',
        'type' => 'String',
        'html_type' => 'checkboxes',
        'pseudoconstant' => array(
          'optionGroupName' => 'advanced_search_options',
        ),
        'default' => array(
          '0' => '1',
          '1' => '2',
          '2' => '3',
          '3' => '4',
          '4' => '5',
          '5' => '6',
          '6' => '7',
          '7' => '8',
          '8' => '9',
          '9' => '10',
          '10' => '12',
          '11' => '13',
          '12' => '15',
          '13' => '16',
          '14' => '17',
          '15' => '18',
          '16' => '19',
        ),
        'add' => '4.1',
        'title' => 'Contact Search',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
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
        'default' => array(
          '0' => '1',
          '1' => '2',
          '2' => '3',
          '3' => '4',
          '4' => '5',
          '5' => '7',
          '6' => '8',
          '7' => '9',
        ),
        'add' => '4.1',
        'title' => 'Contact Dashboard',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
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
        'default' => array(
          '0' => '1',
          '1' => '2',
          '2' => '4',
          '3' => '5',
          '4' => '8',
          '5' => '9',
          '6' => '10',
          '7' => '11',
        ),
        'add' => '4.1',
        'title' => 'Addressing Options',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'address_format' => array(
        'group_name' => 'CiviCRM Preferences',
        'group' => 'core',
        'name' => 'address_format',
        'type' => 'String',
        'html_type' => 'TextArea',
        'default' => '{contact.address_name}
{contact.street_address}
{contact.supplemental_address_1}
{contact.supplemental_address_2}
{contact.city}{, }{contact.state_province}{ }{contact.postal_code}
{contact.country}',
        'add' => '4.1',
        'title' => 'Address Format',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'mailing_format' => array(
        'group_name' => 'CiviCRM Preferences',
        'group' => 'core',
        'name' => 'mailing_format',
        'type' => 'String',
        'html_type' => 'Text',
        'default' => '{contact.addressee}
{contact.street_address}
{contact.supplemental_address_1}
{contact.supplemental_address_2}
{contact.city}{, }{contact.state_province}{ }{contact.postal_code}
{contact.country}',
        'add' => '4.1',
        'title' => 'Mailing Format',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
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
        'description' => '',
        'help_text' => '',
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
        'description' => '',
        'help_text' => '',
      ),
      'editor_id' => array(
        'group_name' => 'CiviCRM Preferences',
        'group' => 'core',
        'name' => 'editor_id',
        'type' => 'String',
        'html_type' => 'Text',
        'default' => '',
        'add' => '4.1',
        'title' => 'Wysiwig Editor',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'contact_ajax_check_similar' => array(
        'group_name' => 'CiviCRM Preferences',
        'group' => 'core',
        'name' => 'contact_ajax_check_similar',
        'type' => 'String',
        'html_type' => 'Text',
        'default' => '',
        'add' => '4.1',
        'title' => 'Ajax Check Similar',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
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
        'description' => '',
        'help_text' => '',
      ),
      'activity_assignee_notification' => array(
        'group_name' => 'CiviCRM Preferences',
        'group' => 'core',
        'name' => 'activity_assignee_notification',
        'type' => 'String',
        'html_type' => 'Text',
        'default' => '',
        'add' => '4.1',
        'title' => 'Notify Activity Assignees',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'activity_assignee_notification_ics' => array(
        'group_name' => 'CiviCRM Preferences',
        'group' => 'core',
        'name' => 'activity_assignee_notification_ics',
        'type' => 'String',
        'html_type' => 'Text',
        'default' => '',
        'add' => '4.3',
        'title' => 'Include ICal Invite to Activity Assignees',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'contact_autocomplete_options' => array(
        'group_name' => 'CiviCRM Preferences',
        'group' => 'core',
        'name' => 'contact_autocomplete_options',
        'type' => 'String',
        'html_type' => 'checkboxes',
        'pseudoconstant' => array(
          'optionGroupName' => 'contact_autocomplete_options',
        ),
        'default' => array(
          '0' => '1',
          '1' => '2',
          '2' => '3',
          '3' => '4',
          '4' => '5',
          '5' => '6',
          '6' => '7',
        ),
        'add' => '4.1',
        'title' => 'Contact Reference Autocomplete Options',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'contact_reference_options' => array(
        'group_name' => 'CiviCRM Preferences',
        'group' => 'core',
        'name' => 'contact_reference_options',
        'type' => 'String',
        'html_type' => 'checkboxes',
        'pseudoconstant' => array(
          'optionGroupName' => 'contact_reference_options',
        ),
        'default' => array(
          '0' => '1',
          '1' => '2',
          '2' => '3',
          '3' => '4',
          '4' => '5',
          '5' => '6',
          '6' => '7',
        ),
        'add' => '4.1',
        'title' => 'Contact Reference Options',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'max_attachments' => array(
        'group_name' => 'CiviCRM Preferences',
        'group' => 'core',
        'name' => 'max_attachments',
        'legacy_key' => 'maxAttachments',
        'prefetch' => 0,
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
        'help_text' => '',
      ),
      'maxFileSize' => array(
        'group_name' => 'CiviCRM Preferences',
        'group' => 'core',
        'name' => 'maxFileSize',
        'prefetch' => 1,
        'config_only' => 1,
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
        'help_text' => '',
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
        'help_text' => '',
      ),
      'allowPermDeleteFinancial' => array(
        'group_name' => 'CiviCRM Preferences',
        'group' => 'core',
        'name' => 'allowPermDeleteFinancial',
        'type' => 'Boolean',
        'quick_form_type' => 'YesNo',
        'default' => '',
        'add' => '4.3',
        'title' => 'Contact Permanent Delete',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'Allow Permanent Delete for contacts who are linked to live financial transactions',
        'help_text' => '',
      ),
      'securityAlert' => array(
        'group_name' => 'CiviCRM Preferences',
        'group' => 'core',
        'name' => 'securityAlert',
        'type' => 'Boolean',
        'quick_form_type' => 'YesNo',
        'default' => 1,
        'add' => '4.4',
        'title' => 'Security Audits',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'If enabled, CiviCRM will automatically run checks for significant mis-configurations such as ineffective file protections.',
        'help_text' => '',
      ),
      'doNotAttachPDFReceipt' => array(
        'group_name' => 'CiviCRM Preferences',
        'group' => 'core',
        'name' => 'doNotAttachPDFReceipt',
        'prefetch' => 1,
        'config_only' => 1,
        'type' => 'Boolean',
        'quick_form_type' => 'YesNo',
        'default' => 1,
        'add' => '4.3',
        'title' => 'Attach PDF copy to receipts',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'If enabled, CiviCRM sends PDF receipt as an attachment during event signup or online contribution.',
        'help_text' => '',
      ),
      'wkhtmltopdfPath' => array(
        'group_name' => 'CiviCRM Preferences',
        'group' => 'core',
        'name' => 'wkhtmltopdfPath',
        'prefetch' => 1,
        'config_only' => 1,
        'type' => 'String',
        'quick_form_type' => 'Element',
        'html_attributes' => array(
          'size' => 64,
          'maxlength' => 256,
        ),
        'html_type' => 'Text',
        'default' => '',
        'add' => '4.3',
        'title' => 'Path to wkhtmltopdf executable',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'recaptchaPublicKey' => array(
        'group_name' => 'CiviCRM Preferences',
        'group' => 'core',
        'name' => 'recaptchaPublicKey',
        'prefetch' => 1,
        'config_only' => 1,
        'type' => 'String',
        'quick_form_type' => 'Element',
        'html_attributes' => array(
          'size' => 64,
          'maxlength' => 64,
        ),
        'html_type' => 'Text',
        'default' => '',
        'add' => '4.3',
        'title' => 'Recaptcha Site Key',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'recaptchaPrivateKey' => array(
        'group_name' => 'CiviCRM Preferences',
        'group' => 'core',
        'name' => 'recaptchaPrivateKey',
        'prefetch' => 1,
        'config_only' => 1,
        'type' => 'String',
        'quick_form_type' => 'Element',
        'html_attributes' => array(
          'size' => 64,
          'maxlength' => 64,
        ),
        'html_type' => 'Text',
        'default' => '',
        'add' => '4.3',
        'title' => 'Recaptcha Secret Key',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'checksumTimeout' => array(
        'group_name' => 'CiviCRM Preferences',
        'group' => 'core',
        'name' => 'checksumTimeout',
        'prefetch' => 1,
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
        'description' => '',
        'help_text' => '',
      ),
      'blogUrl' => array(
        'group_name' => 'CiviCRM Preferences',
        'group' => 'core',
        'name' => 'blogUrl',
        'prefetch' => 0,
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
        'help_text' => 'Use \"*default*\" for the system default or override with a custom URL',
      ),
      'communityMessagesUrl' => array(
        'group_name' => 'CiviCRM Preferences',
        'group' => 'core',
        'name' => 'communityMessagesUrl',
        'prefetch' => 0,
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
        'help_text' => 'Use \"*default*\" for the system default or override with a custom URL',
      ),
      'resCacheCode' => array(
        'group_name' => 'CiviCRM Preferences',
        'group' => 'core',
        'name' => 'resCacheCode',
        'prefetch' => 1,
        'config_only' => 1,
        'type' => 'String',
        'quick_form_type' => 'Element',
        'html_attributes' => array(
          'size' => 16,
          'maxlength' => 16,
        ),
        'html_type' => 'Text',
        'default' => '',
        'add' => '4.3',
        'title' => 'Resource Cache Code',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'Code appended to resource URLs (JS/CSS) to coerce HTTP caching',
        'help_text' => '',
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
        'description' => 'If disabled, backend HTTPS services will allow unverified, insecure connections',
        'help_text' => 'Unless you are absolutely unable to configure your server to check the SSL certificate of the remote server you should leave this set to Yes',
      ),
      'wpBasePage' => array(
        'group_name' => 'CiviCRM Preferences',
        'group' => 'core',
        'name' => 'wpBasePage',
        'type' => 'String',
        'html_type' => 'text',
        'quick_form_type' => 'Element',
        'prefetch' => 1,
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
        'prefetch' => 1,
        'type' => 'Boolean',
        'quick_form_type' => 'YesNo',
        'default' => 0,
        'add' => '4.3',
        'title' => 'Allow second-degree relationship permissions',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'If enabled, contacts with the permission to edit a related contact will inherit that contact\'s permission to edit other related contacts',
        'help_text' => '',
      ),
      'enable_components' => array(
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
        'default' => array(
          '0' => 'CiviEvent',
          '1' => 'CiviContribute',
          '2' => 'CiviMember',
          '3' => 'CiviMail',
          '4' => 'CiviReport',
          '5' => 'CiviPledge',
        ),
        'add' => '4.4',
        'title' => 'Enable Components',
        'is_domain' => '1',
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
        'on_change' => array(
          '0' => 'CRM_Case_Info::onToggleComponents',
          '1' => 'CRM_Core_Component::flushEnabledComponents',
          '2' => 'call://resources/resetCacheCode',
        ),
      ),
      'disable_core_css' => array(
        'group_name' => 'CiviCRM Preferences',
        'group' => 'core',
        'name' => 'disable_core_css',
        'type' => 'Boolean',
        'quick_form_type' => 'YesNo',
        'default' => 0,
        'add' => '4.4',
        'title' => 'Disable CiviCRM css',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'Prevent the stylesheet \"civicrm.css\" from being loaded.',
        'help_text' => '',
      ),
      'empoweredBy' => array(
        'group_name' => 'CiviCRM Preferences',
        'group' => 'core',
        'name' => 'empoweredBy',
        'prefetch' => 1,
        'type' => 'Boolean',
        'quick_form_type' => 'YesNo',
        'default' => 1,
        'add' => '4.5',
        'title' => 'Display \"empowered by CiviCRM\"',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'When enabled, \"empowered by CiviCRM\" is displayed at the bottom of public forms.',
        'help_text' => '',
      ),
      'userFrameworkLogging' => array(
        'group_name' => 'Developer Preferences',
        'group' => 'developer',
        'name' => 'userFrameworkLogging',
        'type' => 'Boolean',
        'quick_form_type' => 'YesNo',
        'default' => 0,
        'add' => '4.3',
        'title' => 'Enable Drupal Watchdog Logging',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'Set this value to Yes if you want CiviCRM error/debugging messages to appear in the Drupal error logs',
        'prefetch' => 1,
        'help_text' => 'Set this value to Yes if you want CiviCRM error/debugging messages the appear in your CMS\' error log. In the case of Drupal, this will cause all CiviCRM error messages to appear in the watchdog (assuming you have Drupal\'s watchdog enabled)',
      ),
      'debug_enabled' => array(
        'group_name' => 'Developer Preferences',
        'group' => 'developer',
        'name' => 'debug_enabled',
        'config_key' => 'debug',
        'type' => 'Boolean',
        'quick_form_type' => 'YesNo',
        'default' => 0,
        'add' => '4.3',
        'title' => 'Enable Debugging',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'Set this value to Yes if you want to use one of CiviCRM\'s debugging tools. This feature should NOT be enabled for production sites',
        'prefetch' => 1,
        'help_text' => 'Do not turn this on on production sites',
      ),
      'backtrace' => array(
        'group_name' => 'Developer Preferences',
        'group' => 'developer',
        'name' => 'backtrace',
        'type' => 'Boolean',
        'quick_form_type' => 'YesNo',
        'default' => 0,
        'add' => '4.3',
        'title' => 'Display Backtrace',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'Set this value to Yes if you want to display a backtrace listing when a fatal error is encountered. This feature should NOT be enabled for production sites',
        'prefetch' => 1,
      ),
      'fatalErrorHandler' => array(
        'group_name' => 'Developer Preferences',
        'group' => 'developer',
        'name' => 'fatalErrorHandler',
        'type' => 'String',
        'quick_form_type' => 'Element',
        'html_type' => 'text',
        'default' => '',
        'add' => '4.3',
        'title' => 'Fatal Error Handler',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'Enter the path and class for a custom PHP error-handling function if you want to override built-in CiviCRM error handling for your site.',
        'prefetch' => 1,
      ),
      'uploadDir' => array(
        'group_name' => 'Directory Preferences',
        'group' => 'directory',
        'name' => 'uploadDir',
        'type' => 'Url',
        'html_type' => 'Text',
        'default' => '',
        'add' => '4.1',
        'prefetch' => 1,
        'title' => 'Upload Directory',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'imageUploadDir' => array(
        'group_name' => 'Directory Preferences',
        'group' => 'directory',
        'name' => 'imageUploadDir',
        'type' => 'Url',
        'html_type' => 'Text',
        'default' => '',
        'add' => '4.1',
        'prefetch' => 1,
        'title' => 'Image Directory',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'customFileUploadDir' => array(
        'group_name' => 'Directory Preferences',
        'group' => 'directory',
        'name' => 'customFileUploadDir',
        'type' => 'Url',
        'html_type' => 'Text',
        'default' => '',
        'add' => '4.1',
        'prefetch' => 1,
        'title' => 'Custom Files Upload Directory',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'customTemplateDir' => array(
        'group_name' => 'Directory Preferences',
        'group' => 'directory',
        'name' => 'customTemplateDir',
        'type' => 'Url',
        'html_type' => 'Text',
        'default' => '',
        'add' => '4.1',
        'prefetch' => 1,
        'title' => 'Custom Template Directory',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'customPHPPathDir' => array(
        'group_name' => 'Directory Preferences',
        'group' => 'directory',
        'name' => 'customPHPPathDir',
        'type' => 'Url',
        'html_type' => 'Text',
        'default' => '',
        'add' => '4.1',
        'prefetch' => 1,
        'title' => 'Custom PHP Path',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'extensionsDir' => array(
        'group_name' => 'Directory Preferences',
        'group' => 'directory',
        'name' => 'extensionsDir',
        'type' => 'Url',
        'html_type' => 'Text',
        'default' => '',
        'add' => '4.1',
        'prefetch' => 1,
        'title' => 'Extensions Directory',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'event_enable_cart' => array(
        'name' => 'enable_cart',
        'group_name' => 'Event Preferences',
        'group' => 'event',
        'type' => 'String',
        'quick_form_type' => 'Element',
        'default' => 0,
        'add' => '4.1',
        'title' => 'Enable Event Cart',
        'is_domain' => 1,
        'is_contact' => 1,
        'description' => 'WRITE ME',
        'help_text' => 'WRITE ME',
      ),
      'monetaryThousandSeparator' => array(
        'group_name' => 'Localization Preferences',
        'group' => 'localization',
        'name' => 'monetaryThousandSeparator',
        'prefetch' => 1,
        'config_only' => 1,
        'type' => 'String',
        'quick_form_type' => 'Element',
        'html_type' => 'text',
        'html_attributes' => array(
          'size' => 2,
        ),
        'default' => ',',
        'add' => '4.3',
        'title' => 'Thousands Separator',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'monetaryDecimalPoint' => array(
        'group_name' => 'Localization Preferences',
        'group' => 'localization',
        'name' => 'monetaryDecimalPoint',
        'prefetch' => 1,
        'config_only' => 1,
        'type' => 'String',
        'quick_form_type' => 'Element',
        'html_type' => 'text',
        'html_attributes' => array(
          'size' => 2,
        ),
        'default' => '.',
        'add' => '4.3',
        'title' => 'Decimal Delimiter',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'moneyformat' => array(
        'group_name' => 'Localization Preferences',
        'group' => 'localization',
        'name' => 'moneyformat',
        'prefetch' => 1,
        'config_only' => 1,
        'type' => 'String',
        'quick_form_type' => 'Element',
        'html_type' => 'text',
        'default' => '%c %a',
        'add' => '4.3',
        'title' => 'Monetary Amount Display',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'moneyvalueformat' => array(
        'group_name' => 'Localization Preferences',
        'group' => 'localization',
        'name' => 'moneyvalueformat',
        'prefetch' => 1,
        'config_only' => 1,
        'type' => 'String',
        'quick_form_type' => 'Element',
        'html_type' => 'text',
        'default' => '%!i',
        'add' => '4.3',
        'title' => 'Monetary Amount Display',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'defaultCurrency' => array(
        'group_name' => 'Localization Preferences',
        'group' => 'localization',
        'name' => 'defaultCurrency',
        'prefetch' => 1,
        'config_only' => 1,
        'type' => 'String',
        'quick_form_type' => 'Element',
        'html_type' => 'text',
        'html_attributes' => array(
          'size' => 2,
        ),
        'default' => 'USD',
        'add' => '4.3',
        'title' => 'Default Currency',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'Default currency assigned to contributions and other monetary transactions.',
        'help_text' => '',
      ),
      'defaultContactCountry' => array(
        'group_name' => 'Localization Preferences',
        'group' => 'localization',
        'name' => 'defaultContactCountry',
        'prefetch' => 1,
        'config_only' => 1,
        'type' => 'String',
        'quick_form_type' => 'Element',
        'html_type' => 'text',
        'html_attributes' => array(
          'size' => 4,
        ),
        'default' => '1228',
        'add' => '4.4',
        'title' => 'Default Country',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'This value is selected by default when adding a new contact address.',
        'help_text' => '',
      ),
      'countryLimit' => array(
        'group_name' => 'Localization Preferences',
        'group' => 'localization',
        'name' => 'countryLimit',
        'prefetch' => 1,
        'config_only' => 1,
        'type' => 'Array',
        'quick_form_type' => 'Element',
        'html_type' => 'advmultiselect',
        'html_attributes' => array(
          'size' => 5,
          'style' => 'width:150px',
          'class' => 'advmultiselect',
        ),
        'default' => 'null',
        'add' => '4.3',
        'title' => 'Available Countries',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'provinceLimit' => array(
        'group_name' => 'Localization Preferences',
        'group' => 'localization',
        'name' => 'provinceLimit',
        'prefetch' => 1,
        'config_only' => 1,
        'type' => 'Array',
        'quick_form_type' => 'Element',
        'html_type' => 'advmultiselect',
        'html_attributes' => array(
          'size' => 5,
          'style' => 'width:150px',
          'class' => 'advmultiselect',
        ),
        'default' => 'null',
        'add' => '4.3',
        'title' => 'Available States and Provinces',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'inheritLocale' => array(
        'group_name' => 'Localization Preferences',
        'group' => 'localization',
        'name' => 'inheritLocale',
        'prefetch' => 1,
        'config_only' => 1,
        'type' => 'Boolean',
        'quick_form_type' => 'YesNo',
        'default' => 0,
        'add' => '4.3',
        'title' => 'Inherit CMS Language',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'dateformatDatetime' => array(
        'group_name' => 'Localization Preferences',
        'group' => 'localization',
        'name' => 'dateformatDatetime',
        'prefetch' => 1,
        'config_only' => 1,
        'type' => 'String',
        'default' => '%B %E%f, %Y %l:%M %P',
        'add' => '4.3',
        'title' => 'Complete Date and Time',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'dateformatFull' => array(
        'group_name' => 'Localization Preferences',
        'group' => 'localization',
        'name' => 'dateformatFull',
        'prefetch' => 1,
        'config_only' => 1,
        'type' => 'String',
        'default' => '%B %E%f, %Y',
        'add' => '4.3',
        'title' => 'Complete Date',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'dateformatPartial' => array(
        'group_name' => 'Localization Preferences',
        'group' => 'localization',
        'name' => 'dateformatPartial',
        'prefetch' => 1,
        'config_only' => 1,
        'type' => 'String',
        'default' => '%B %Y',
        'add' => '4.3',
        'title' => 'Month and Year',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'lcMessages' => array(
        'group_name' => 'Localization Preferences',
        'group' => 'localization',
        'name' => 'lcMessages',
        'prefetch' => 1,
        'config_only' => 1,
        'type' => 'String',
        'default' => 'en_US',
        'add' => '4.3',
        'title' => 'Default Language',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'profile_double_optin' => array(
        'group_name' => 'Mailing Preferences',
        'group' => 'mailing',
        'name' => 'profile_double_optin',
        'type' => 'Integer',
        'html_type' => 'checkbox',
        'default' => 0,
        'add' => '4.1',
        'title' => 'Enable Double Opt-in for Profile Group(s) field',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'When CiviMail is enabled, users who \"subscribe\" to a group from a profile Group(s) checkbox will receive a confirmation email. They must respond (opt-in) before they are added to the group.',
        'help_text' => '',
      ),
      'track_civimail_replies' => array(
        'group_name' => 'Mailing Preferences',
        'group' => 'mailing',
        'name' => 'track_civimail_replies',
        'type' => 'Integer',
        'html_type' => 'checkbox',
        'default' => 0,
        'add' => '4.1',
        'title' => 'Track replies using VERP in Reply-To header',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'If checked, mailings will default to tracking replies using VERP-ed Reply-To. ',
        'help_text' => '',
        'validate_callback' => 'CRM_Core_BAO_Setting::validateBoolSetting',
      ),
      'civimail_workflow' => array(
        'group_name' => 'Mailing Preferences',
        'group' => 'mailing',
        'name' => 'civimail_workflow',
        'type' => 'Integer',
        'html_type' => 'checkbox',
        'default' => 0,
        'add' => '4.1',
        'title' => 'Use CiviMail Workflow',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'When CiviMail is enabled, users who \"subscribe\" to a group from a profile Group(s) checkbox will receive a confirmation email. They must respond (opt-in) before they are added to the group.',
        'help_text' => '',
      ),
      'civimail_server_wide_lock' => array(
        'group_name' => 'Mailing Preferences',
        'group' => 'mailing',
        'name' => 'civimail_server_wide_lock',
        'type' => 'Integer',
        'html_type' => 'checkbox',
        'default' => 0,
        'add' => '4.1',
        'title' => 'Lock Mails Server-Wide for Mail Sending',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'mailing_backend' => array(
        'group_name' => 'Mailing Preferences',
        'group' => 'mailing',
        'name' => 'mailing_backend',
        'type' => 'Array',
        'html_type' => 'checkbox',
        'default' => 0,
        'add' => '4.1',
        'title' => 'Mailing Backend',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'profile_add_to_group_double_optin' => array(
        'group_name' => 'Mailing Preferences',
        'group' => 'mailing',
        'name' => 'profile_add_to_group_double_optin',
        'type' => 'Integer',
        'html_type' => 'checkbox',
        'default' => 0,
        'add' => '4.1',
        'title' => 'Enable Double Opt-in for Profile Group(s) field',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'When CiviMail is enabled, users who \"subscribe\" to a group from a profile Group(s) checkbox will receive a confirmation email. They must respond (opt-in) before they are added to the group.',
        'help_text' => '',
      ),
      'disable_mandatory_tokens_check' => array(
        'group_name' => 'Mailing Preferences',
        'group' => 'mailing',
        'name' => 'disable_mandatory_tokens_check',
        'type' => 'Integer',
        'html_type' => 'checkbox',
        'default' => 0,
        'add' => '4.4',
        'title' => 'Disable check for mandatory tokens',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'Don\'t check for presence of mandatory tokens (domain address; unsubscribe/opt-out) before sending mailings. WARNING: Mandatory tokens are a safe-guard which facilitate compliance with the US CAN-SPAM Act. They should only be disabled if your organization adopts other mechanisms for compliance or if your organization is not subject to CAN-SPAM.',
        'help_text' => '',
      ),
      'dedupe_email_default' => array(
        'group_name' => 'Mailing Preferences',
        'group' => 'mailing',
        'name' => 'dedupe_email_default',
        'type' => 'Integer',
        'html_type' => 'checkbox',
        'default' => 1,
        'add' => '4.5',
        'title' => 'CiviMail dedupes e-mail addresses by default',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'Set the \"dedupe e-mail\" option when sending a new mailing to \"true\" by default.',
        'help_text' => '',
      ),
      'hash_mailing_url' => array(
        'group_name' => 'Mailing Preferences',
        'group' => 'mailing',
        'name' => 'hash_mailing_url',
        'type' => 'Integer',
        'html_type' => 'checkbox',
        'default' => 0,
        'add' => '4.5',
        'title' => 'Hashed Mailing URL\'s',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'If enabled, a randomized hash key will be used to reference the mailing URL in the mailing.viewUrl token, instead of the mailing ID',
        'help_text' => '',
      ),
      'default_renewal_contribution_page' => array(
        'group_name' => 'Member Preferences',
        'group' => 'member',
        'name' => 'default_renewal_contribution_page',
        'type' => 'Integer',
        'html_type' => 'Select',
        'default' => '',
        'pseudoconstant' => array(
          'name' => 'contributionPage',
        ),
        'add' => '4.1',
        'title' => 'Default online membership renewal page',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'If you select a default online contribution page for self-service membership renewals, a \"renew\" link pointing to that page will be displayed on the Contact Dashboard for memberships which were entered offline. You will need to ensure that the membership block for the selected online contribution page includes any currently available memberships.',
        'help_text' => '',
      ),
      'is_enabled' => array(
        'group_name' => 'Multi Site Preferences',
        'group' => 'multisite',
        'name' => 'is_enabled',
        'title' => 'Multisite Is enabled',
        'type' => 'Integer',
        'default' => '',
        'add' => '4.1',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'Multisite is enabled',
        'help_text' => '',
      ),
      'domain_group_id' => array(
        'group_name' => 'Multi Site Preferences',
        'group' => 'multisite',
        'name' => 'domain_group_id',
        'title' => 'Multisite Domain Group',
        'type' => 'Integer',
        'default' => '',
        'add' => '4.1',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'event_price_set_domain_id' => array(
        'group_name' => 'Multi Site Preferences',
        'group' => 'multisite',
        'name' => 'event_price_set_domain_id',
        'title' => 'Domain Event Price Set',
        'type' => 'Integer',
        'default' => '',
        'add' => '4.1',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'uniq_email_per_site' => array(
        'group_name' => 'Multi Site Preferences',
        'group' => 'multisite',
        'name' => 'uniq_email_per_site',
        'type' => 'Integer',
        'title' => 'Unique Email per Domain?',
        'default' => '',
        'add' => '4.1',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'search_autocomplete_count' => array(
        'group_name' => 'Search Preferences',
        'group' => 'Search Preferences',
        'name' => 'search_autocomplete_count',
        'prefetch' => 0,
        'type' => 'Integer',
        'quick_form_type' => 'Element',
        'html_type' => 'text',
        'html_attributes' => array(
          'size' => 2,
          'maxlength' => 2,
        ),
        'default' => 10,
        'add' => '4.3',
        'title' => 'Autocomplete Results',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'The maximum number of contacts to show at a time when typing in an autocomplete field.',
        'help_text' => '',
      ),
      'enable_innodb_fts' => array(
        'group_name' => 'Search Preferences',
        'group' => 'Search Preferences',
        'name' => 'enable_innodb_fts',
        'prefetch' => 0,
        'type' => 'Boolean',
        'quick_form_type' => 'YesNo',
        'default' => 0,
        'add' => '4.4',
        'title' => 'InnoDB Full Text Search',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'Enable InnoDB full-text search optimizations. (Requires MySQL 5.6+)',
        'help_text' => '',
        'on_change' => array(
          '0' => array(
            '0' => 'CRM_Core_InnoDBIndexer',
            '1' => 'onToggleFts',
          ),
        ),
      ),
      'fts_query_mode' => array(
        'group_name' => 'Search Preferences',
        'group' => 'Search Preferences',
        'name' => 'fts_query_mode',
        'prefetch' => 0,
        'type' => 'String',
        'quick_form_type' => 'Element',
        'html_attributes' => array(
          'size' => 64,
          'maxlength' => 64,
        ),
        'html_type' => 'Text',
        'default' => 'simple',
        'add' => '4.5',
        'title' => 'How to handle full-tet queries',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => '',
        'help_text' => '',
      ),
      'userFrameworkResourceURL' => array(
        'group' => 'url',
        'group_name' => 'URL Preferences',
        'name' => 'userFrameworkResourceURL',
        'title' => 'Script and CSS Resources URL',
        'type' => 'String',
        'default' => '',
        'add' => '4.1',
        'prefetch' => 1,
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'CiviCRM Resource URL',
        'help_text' => '',
        'validate_callback' => 'CRM_Utils_Rule::url',
      ),
      'imageUploadURL' => array(
        'group' => 'url',
        'group_name' => 'URL Preferences',
        'title' => 'Image URL Prefix',
        'name' => 'imageUploadURL',
        'type' => 'String',
        'default' => '',
        'add' => '4.1',
        'prefetch' => 1,
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'Image Upload URL',
        'help_text' => '',
        'validate_callback' => 'CRM_Utils_Rule::url',
      ),
      'customCSSURL' => array(
        'group' => 'url',
        'group_name' => 'URL Preferences',
        'name' => 'customCSSURL',
        'title' => 'Custom CSS',
        'type' => 'String',
        'default' => '',
        'add' => '4.1',
        'prefetch' => 1,
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'Custom CiviCRM CSS URL',
        'help_text' => '',
        'validate_callback' => 'CRM_Utils_Rule::url',
      ),
    ),
  );

  return $expectedResult;
}

/*
* This example has been generated from the API test suite.
* The test that created it is called "testGetFields"
* and can be found at:
* https://github.com/civicrm/civicrm-core/blob/master/tests/phpunit/api/v3/SettingTest.php
*
* You can see the outcome of the API tests at
* https://test.civicrm.org/job/CiviCRM-master-git/
*
* To Learn about the API read
* http://wiki.civicrm.org/confluence/display/CRMDOC/Using+the+API
*
* Browse the api on your own site with the api explorer
* http://MYSITE.ORG/path/to/civicrm/api
*
* Read more about testing here
* http://wiki.civicrm.org/confluence/display/CRM/Testing
*
* API Standards documentation:
* http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
*/
