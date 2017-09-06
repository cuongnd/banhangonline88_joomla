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
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2016
 *
 * Generated from xml/schema/CRM/Financial/PaymentToken.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:5eb52139a66fec2b47a6b3f461339599)
 */
require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Type.php';
class CRM_Financial_DAO_PaymentToken extends CRM_Core_DAO {
  /**
   * static instance to hold the table name
   *
   * @var string
   */
  static $_tableName = 'civicrm_payment_token';
  /**
   * static value to see if we should log any modifications to
   * this table in the civicrm_log table
   *
   * @var boolean
   */
  static $_log = false;
  /**
   * Payment Token ID
   *
   * @var int unsigned
   */
  public $id;
  /**
   * FK to Contact ID for the owner of the token
   *
   * @var int unsigned
   */
  public $contact_id;
  /**
   *
   * @var int unsigned
   */
  public $payment_processor_id;
  /**
   * Externally provided token string
   *
   * @var string
   */
  public $token;
  /**
   * Date created
   *
   * @var timestamp
   */
  public $created_date;
  /**
   * Contact ID of token creator
   *
   * @var int unsigned
   */
  public $created_id;
  /**
   * Date this token expires
   *
   * @var datetime
   */
  public $expiry_date;
  /**
   * Email at the time of token creation. Useful for fraud forensics
   *
   * @var string
   */
  public $email;
  /**
   * Billing first name at the time of token creation. Useful for fraud forensics
   *
   * @var string
   */
  public $billing_first_name;
  /**
   * Billing middle name at the time of token creation. Useful for fraud forensics
   *
   * @var string
   */
  public $billing_middle_name;
  /**
   * Billing last name at the time of token creation. Useful for fraud forensics
   *
   * @var string
   */
  public $billing_last_name;
  /**
   * Holds the part of the card number or account details that may be retained or displayed
   *
   * @var string
   */
  public $masked_account_number;
  /**
   * IP used when creating the token. Useful for fraud forensics
   *
   * @var string
   */
  public $ip_address;
  /**
   * class constructor
   *
   * @return civicrm_payment_token
   */
  function __construct() {
    $this->__table = 'civicrm_payment_token';
    parent::__construct();
  }
  /**
   * Returns foreign keys and entity references
   *
   * @return array
   *   [CRM_Core_Reference_Interface]
   */
  static function getReferenceColumns() {
    if (!isset(Civi::$statics[__CLASS__]['links'])) {
      Civi::$statics[__CLASS__]['links'] = static ::createReferenceColumns(__CLASS__);
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName() , 'contact_id', 'civicrm_contact', 'id');
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName() , 'payment_processor_id', 'civicrm_payment_processor', 'id');
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName() , 'created_id', 'civicrm_contact', 'id');
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'links_callback', Civi::$statics[__CLASS__]['links']);
    }
    return Civi::$statics[__CLASS__]['links'];
  }
  /**
   * Returns all the column names of this table
   *
   * @return array
   */
  static function &fields() {
    if (!isset(Civi::$statics[__CLASS__]['fields'])) {
      Civi::$statics[__CLASS__]['fields'] = array(
        'payment_token_id' => array(
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Payment Token ID') ,
          'description' => 'Payment Token ID',
          'required' => true,
        ) ,
        'contact_id' => array(
          'name' => 'contact_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Contact ID') ,
          'description' => 'FK to Contact ID for the owner of the token',
          'required' => true,
          'FKClassName' => 'CRM_Contact_DAO_Contact',
        ) ,
        'payment_processor_id' => array(
          'name' => 'payment_processor_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Payment Processor ID') ,
          'required' => true,
          'FKClassName' => 'CRM_Financial_DAO_PaymentProcessor',
        ) ,
        'token' => array(
          'name' => 'token',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Token') ,
          'description' => 'Externally provided token string',
          'required' => true,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
        'created_date' => array(
          'name' => 'created_date',
          'type' => CRM_Utils_Type::T_TIMESTAMP,
          'title' => ts('Created Date') ,
          'description' => 'Date created',
          'default' => 'CURRENT_TIMESTAMP',
        ) ,
        'created_id' => array(
          'name' => 'created_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Created ID') ,
          'description' => 'Contact ID of token creator',
          'FKClassName' => 'CRM_Contact_DAO_Contact',
        ) ,
        'expiry_date' => array(
          'name' => 'expiry_date',
          'type' => CRM_Utils_Type::T_DATE + CRM_Utils_Type::T_TIME,
          'title' => ts('Expiry Date') ,
          'description' => 'Date this token expires',
        ) ,
        'email' => array(
          'name' => 'email',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Email') ,
          'description' => 'Email at the time of token creation. Useful for fraud forensics',
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
        'billing_first_name' => array(
          'name' => 'billing_first_name',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Billing First Name') ,
          'description' => 'Billing first name at the time of token creation. Useful for fraud forensics',
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
        'billing_middle_name' => array(
          'name' => 'billing_middle_name',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Billing Middle Name') ,
          'description' => 'Billing middle name at the time of token creation. Useful for fraud forensics',
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
        'billing_last_name' => array(
          'name' => 'billing_last_name',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Billing Last Name') ,
          'description' => 'Billing last name at the time of token creation. Useful for fraud forensics',
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
        'masked_account_number' => array(
          'name' => 'masked_account_number',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Masked Account Number') ,
          'description' => 'Holds the part of the card number or account details that may be retained or displayed',
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
        'ip_address' => array(
          'name' => 'ip_address',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('IP Address') ,
          'description' => 'IP used when creating the token. Useful for fraud forensics',
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
      );
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'fields_callback', Civi::$statics[__CLASS__]['fields']);
    }
    return Civi::$statics[__CLASS__]['fields'];
  }
  /**
   * Return a mapping from field-name to the corresponding key (as used in fields()).
   *
   * @return array
   *   Array(string $name => string $uniqueName).
   */
  static function &fieldKeys() {
    if (!isset(Civi::$statics[__CLASS__]['fieldKeys'])) {
      Civi::$statics[__CLASS__]['fieldKeys'] = array_flip(CRM_Utils_Array::collect('name', self::fields()));
    }
    return Civi::$statics[__CLASS__]['fieldKeys'];
  }
  /**
   * Returns the names of this table
   *
   * @return string
   */
  static function getTableName() {
    return self::$_tableName;
  }
  /**
   * Returns if this table needs to be logged
   *
   * @return boolean
   */
  function getLog() {
    return self::$_log;
  }
  /**
   * Returns the list of fields that can be imported
   *
   * @param bool $prefix
   *
   * @return array
   */
  static function &import($prefix = false) {
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'payment_token', $prefix, array());
    return $r;
  }
  /**
   * Returns the list of fields that can be exported
   *
   * @param bool $prefix
   *
   * @return array
   */
  static function &export($prefix = false) {
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'payment_token', $prefix, array());
    return $r;
  }
}
