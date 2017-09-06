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
/*
 * Settings metadata file
 */

return array(
  'customTranslateFunction' => array(
    'add' => '4.7',
    'help_text' => NULL,
    'is_domain' => 1,
    'is_contact' => 0,
    'group_name' => 'Localization Preferences',
    'group' => 'localization',
    'name' => 'customTranslateFunction',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_type' => 'text',
    'html_attributes' => array(
      'size' => '30',
      'maxlength' => '100',
    ),
    'default' => NULL,
    'title' => 'Custom Translate Function',
    'description' => '',
  ),
  'monetaryThousandSeparator' => array(
    'group_name' => 'Localization Preferences',
    'group' => 'localization',
    'name' => 'monetaryThousandSeparator',
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
    'description' => NULL,
    'help_text' => NULL,
  ),
  'monetaryDecimalPoint' => array(
    'group_name' => 'Localization Preferences',
    'group' => 'localization',
    'name' => 'monetaryDecimalPoint',
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
    'description' => NULL,
    'help_text' => NULL,
  ),
  'moneyformat' => array(
    'group_name' => 'Localization Preferences',
    'group' => 'localization',
    'name' => 'moneyformat',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_type' => 'text',
    'default' => '%c %a',
    'add' => '4.3',
    'title' => 'Monetary Amount Display',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => NULL,
    'help_text' => NULL,
  ),
  'moneyvalueformat' => array(
    'group_name' => 'Localization Preferences',
    'group' => 'localization',
    'name' => 'moneyvalueformat',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_type' => 'text',
    'default' => '%!i',
    'add' => '4.3',
    'title' => 'Monetary Value Display',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => NULL,
    'help_text' => NULL,
  ),
  'defaultCurrency' => array(
    'group_name' => 'Localization Preferences',
    'group' => 'localization',
    'name' => 'defaultCurrency',
    'type' => 'String',
    'quick_form_type' => 'Select',
    'html_type' => 'Select',
    'html_attributes' => array(
      'class' => 'crm-select2',
    ),
    'default' => 'USD',
    'add' => '4.3',
    'title' => 'Default Currency',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Default currency assigned to contributions and other monetary transactions.',
    'help_text' => NULL,
    'pseudoconstant' => array(
      'callback' => 'CRM_Admin_Form_Setting_Localization::getCurrencySymbols',
    ),
  ),
  'defaultContactCountry' => array(
    'group_name' => 'Localization Preferences',
    'group' => 'localization',
    'name' => 'defaultContactCountry',
    'type' => 'String',
    'quick_form_type' => 'Select',
    'html_type' => 'Select',
    'html_attributes' => array(
      //'class' => 'crm-select2',
    ),
    'default' => '1228',
    'add' => '4.4',
    'title' => 'Default Country',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'This value is selected by default when adding a new contact address.',
    'help_text' => NULL,
    'pseudoconstant' => array(
      'callback' => 'CRM_Admin_Form_Setting_Localization::getAvailableCountries',
    ),
  ),
  'defaultContactStateProvince' => array(
    'add' => '4.7',
    'help_text' => NULL,
    'is_domain' => 1,
    'is_contact' => 0,
    'group_name' => 'Localization Preferences',
    'group' => 'localization',
    'name' => 'defaultContactStateProvince',
    'type' => 'Integer',
    'quick_form_type' => 'ChainSelect',
    'html_type' => 'ChainSelect',
    //'pseudoconstant' => array(
    //  'callback' => 'CRM_Core_PseudoConstant::stateProvince',
    //),
    //'html_attributes',
    'default' => NULL,
    'title' => 'Default State/Province',
    'description' => 'This value is selected by default when adding a new contact address.',
  ),
  'countryLimit' => array(
    'group_name' => 'Localization Preferences',
    'group' => 'localization',
    'name' => 'countryLimit',
    'type' => 'Array',
    'quick_form_type' => 'Element',
    'html_type' => 'advmultiselect',
    'html_attributes' => array(
      'size' => 5,
      'style' => 'width:150px',
      'class' => 'advmultiselect',
    ),
    'default' => array(),
    'add' => '4.3',
    'title' => 'Available Countries',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => '',
    'help_text' => NULL,
    'pseudoconstant' => array(
      'callback' => 'CRM_Admin_Form_Setting_Localization::getAvailableCountries',
    ),
  ),
  'provinceLimit' => array(
    'group_name' => 'Localization Preferences',
    'group' => 'localization',
    'name' => 'provinceLimit',
    'type' => 'Array',
    'quick_form_type' => 'Element',
    'html_type' => 'advmultiselect',
    'html_attributes' => array(
      'size' => 5,
      'style' => 'width:150px',
      'class' => 'advmultiselect',
    ),
    'default' => array(),
    'add' => '4.3',
    'title' => 'Available States and Provinces (by Country)',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => '',
    'help_text' => NULL,
    'pseudoconstant' => array(
      'callback' => 'CRM_Admin_Form_Setting_Localization::getAvailableCountries',
    ),
  ),
  'inheritLocale' => array(
    'group_name' => 'Localization Preferences',
    'group' => 'localization',
    'name' => 'inheritLocale',
    'type' => 'Boolean',
    'quick_form_type' => 'YesNo',
    'default' => '0',
    'add' => '4.3',
    'title' => 'Inherit CMS Language',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => '',
    'help_text' => NULL,
  ),
  'dateformatDatetime' => array(
    'group_name' => 'Localization Preferences',
    'group' => 'localization',
    'name' => 'dateformatDatetime',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_type' => 'text',
    'default' => '%B %E%f, %Y %l:%M %P',
    'add' => '4.3',
    'title' => 'Date Format: Complete Date and Time',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => '',
    'help_text' => NULL,
  ),
  'dateformatFull' => array(
    'group_name' => 'Localization Preferences',
    'group' => 'localization',
    'name' => 'dateformatFull',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_type' => 'text',
    'default' => '%B %E%f, %Y',
    'add' => '4.3',
    'title' => 'Date Format: Complete Date',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => '',
    'help_text' => NULL,
  ),
  'dateformatPartial' => array(
    'group_name' => 'Localization Preferences',
    'group' => 'localization',
    'name' => 'dateformatPartial',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_type' => 'text',
    'default' => '%B %Y',
    'add' => '4.3',
    'title' => 'Date Format: Month and Year',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => '',
    'help_text' => NULL,
  ),
  'dateformatTime' => array(
    'add' => '4.7',
    'help_text' => NULL,
    'is_domain' => 1,
    'is_contact' => 0,
    'group_name' => 'Localization Preferences',
    'group' => 'localization',
    'name' => 'dateformatTime',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_type' => 'text',
    'html_attributes' => array(
      'size' => '12',
      'maxlength' => '60',
    ),
    'default' => '%l:%M %P',
    'title' => 'Date Format: Time Only',
    'description' => '',
  ),
  'dateformatYear' => array(
    'add' => '4.7',
    'help_text' => NULL,
    'is_domain' => 1,
    'is_contact' => 0,
    'group_name' => 'Localization Preferences',
    'group' => 'localization',
    'name' => 'dateformatYear',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_type' => 'text',
    'html_attributes' => array(
      'size' => '12',
      'maxlength' => '60',
    ),
    'default' => '%Y',
    'title' => 'Date Format: Year Only',
    'description' => '',
  ),
  'dateformatFinancialBatch' => array(
    'add' => '4.7',
    'help_text' => NULL,
    'is_domain' => 1,
    'is_contact' => 0,
    'group_name' => 'Localization Preferences',
    'group' => 'localization',
    'name' => 'dateformatFinancialBatch',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_type' => 'text',
    'html_attributes' => array(
      'size' => '12',
      'maxlength' => '60',
    ),
    'default' => '%m/%d/%Y',
    'title' => 'Date Format: Financial Batch',
    'description' => '',
  ),
  'dateInputFormat' => array(
    'add' => '4.7',
    'help_text' => NULL,
    'is_domain' => 1,
    'is_contact' => 0,
    'group_name' => 'Localization Preferences',
    'group' => 'localization',
    'name' => 'dateInputFormat',
    'type' => 'String',
    'quick_form_type' => 'Select',
    'html_type' => 'Select',
    'pseudoconstant' => array(
      'callback' => 'CRM_Core_SelectValues::getDatePluginInputFormats',
    ),
    'default' => 'mm/dd/yy',
    'title' => 'Date Input Format',
    'description' => '',
  ),
  'fieldSeparator' => array(
    'add' => '4.7',
    'help_text' => NULL,
    'is_domain' => 1,
    'is_contact' => 0,
    'group_name' => 'Localization Preferences',
    'group' => 'localization',
    'name' => 'fieldSeparator',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_type' => 'text',
    'html_attributes' => array(
      'size' => '2',
      'maxlength' => '8',
    ),
    'default' => ',',
    'title' => 'Import / Export Field Separator',
    'description' => 'Global CSV separator character. Modify this setting to enable import and export of different kinds of CSV files (for example: \',\' \';\' \':\' \'|\' ).',
  ),
  'fiscalYearStart' => array(
    'add' => '4.7',
    'help_text' => NULL,
    'is_domain' => 1,
    'is_contact' => 0,
    'group_name' => 'Localization Preferences',
    'group' => 'localization',
    'name' => 'fiscalYearStart',
    'type' => 'Array',
    'quick_form_type' => 'MonthDay',
    'html_type' => 'MonthDay',
    'default' => array('M' => 1, 'd' => 1),
    'title' => 'Fiscal Year Start',
    'description' => '',
  ),
  'languageLimit' => array(
    'group_name' => 'Localization Preferences',
    'group' => 'localization',
    'name' => 'languageLimit',
    'type' => 'Array',
    'quick_form_type' => 'Select',
    'html_type' => 'Select',
    'html_attributes' => array(
      'multiple' => 1,
      'class' => 'crm-select2',
    ),
    'default' => NULL,
    'add' => '4.3',
    'title' => 'Available Languages (Multi-lingual)',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => '',
    'help_text' => NULL,
    'pseudoconstant' => array(
      'callback' => 'CRM_Core_I18n::languages',
    ),
  ),
  'lcMessages' => array(
    'group_name' => 'Localization Preferences',
    'group' => 'localization',
    'name' => 'lcMessages',
    'type' => 'String',
    'quick_form_type' => 'Select',
    'html_type' => 'Select',
    'html_attributes' => array(
      'class' => 'crm-select2',
    ),
    'default' => 'en_US',
    'add' => '4.3',
    'title' => 'Default Language',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => '',
    'help_text' => NULL,
    'pseudoconstant' => array(
      'callback' => 'CRM_Admin_Form_Setting_Localization::getDefaultLocaleOptions',
    ),
    'on_change' => array(
      'CRM_Admin_Form_Setting_Localization::onChangeLcMessages',
    ),
  ),
  'legacyEncoding' => array(
    'add' => '4.7',
    'help_text' => NULL,
    'is_domain' => 1,
    'is_contact' => 0,
    'group_name' => 'Localization Preferences',
    'group' => 'localization',
    'name' => 'legacyEncoding',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_type' => 'text',
    'html_attributes' => array(
      'size' => '12',
      'maxlength' => '30',
    ),
    'default' => 'Windows-1252',
    'title' => 'Legacy Encoding',
    'description' => 'If import files are NOT encoded as UTF-8, specify an alternate character encoding for these files. The default of Windows-1252 will work for Excel-created .CSV files on many computers.',
  ),
  'timeInputFormat' => array(
    'add' => '4.7',
    'help_text' => NULL,
    'is_domain' => 1,
    'is_contact' => 0,
    'group_name' => 'Localization Preferences',
    'group' => 'localization',
    'name' => 'timeInputFormat',
    'type' => 'String',
    'quick_form_type' => 'Select',
    'html_type' => 'Select',
    'pseudoconstant' => array(
      'callback' => 'CRM_Core_SelectValues::getTimeFormats',
    ),
    'default' => '1',
    'title' => 'Time Input Format',
    'description' => '',
    'on_change' => array(
      'CRM_Core_BAO_PreferencesDate::onChangeSetting',
    ),
  ),
  'weekBegins' => array(
    'group_name' => 'Localization Preferences',
    'group' => 'localization',
    'name' => 'weekBegins',
    'type' => 'String',
    'quick_form_type' => 'Select',
    'html_type' => 'Select',
    'pseudoconstant' => array(
      'callback' => 'CRM_Utils_Date::getFullWeekdayNames',
    ),
    'default' => '0',
    'add' => '4.7',
    'title' => 'Week begins on',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => "",
    'help_text' => NULL,
  ),
  'contact_default_language' => array(
    'group_name' => 'Localization Preferences',
    'group' => 'localization',
    'name' => 'contact_default_language',
    'type' => 'String',
    'quick_form_type' => 'Select',
    'html_type' => 'Select',
    'html_attributes' => array(
      'class' => 'crm-select2',
    ),
    'pseudoconstant' => array(
      'callback' => 'CRM_Admin_Form_Setting_Localization::getDefaultLanguageOptions',
    ),
    'default' => '*default*',
    'add' => '4.7',
    'title' => 'Default Language for contacts',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Default language (if any) for contact records',
    'help_text' => 'If a contact is created with no language this setting will determine the language data (if any) to save.'
    . 'You may or may not wish to make an assumption here about whether it matches the site language',
  ),
);
