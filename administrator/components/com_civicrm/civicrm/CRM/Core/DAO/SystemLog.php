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
 * Generated from xml/schema/CRM/Core/SystemLog.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:de437db587f1fb4d7f90320e3e42db2c)
 */
require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Type.php';
class CRM_Core_DAO_SystemLog extends CRM_Core_DAO {
  /**
   * static instance to hold the table name
   *
   * @var string
   */
  static $_tableName = 'civicrm_system_log';
  /**
   * static value to see if we should log any modifications to
   * this table in the civicrm_log table
   *
   * @var boolean
   */
  static $_log = false;
  /**
   * Primary key ID
   *
   * @var int unsigned
   */
  public $id;
  /**
   * Standardized message
   *
   * @var string
   */
  public $message;
  /**
   * JSON encoded data
   *
   * @var longtext
   */
  public $context;
  /**
   * error level per PSR3
   *
   * @var string
   */
  public $level;
  /**
   * Timestamp of when event occurred.
   *
   * @var timestamp
   */
  public $timestamp;
  /**
   * Optional Contact ID that created the log. Not an FK as we keep this regardless
   *
   * @var int unsigned
   */
  public $contact_id;
  /**
   * Optional Name of logging host
   *
   * @var string
   */
  public $hostname;
  /**
   * class constructor
   *
   * @return civicrm_system_log
   */
  function __construct() {
    $this->__table = 'civicrm_system_log';
    parent::__construct();
  }
  /**
   * Returns all the column names of this table
   *
   * @return array
   */
  static function &fields() {
    if (!isset(Civi::$statics[__CLASS__]['fields'])) {
      Civi::$statics[__CLASS__]['fields'] = array(
        'id' => array(
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('System Log ID') ,
          'description' => 'Primary key ID',
          'required' => true,
        ) ,
        'message' => array(
          'name' => 'message',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('System Log Message') ,
          'description' => 'Standardized message',
          'required' => true,
          'maxlength' => 128,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
        'context' => array(
          'name' => 'context',
          'type' => CRM_Utils_Type::T_LONGTEXT,
          'title' => ts('Detailed Log Data') ,
          'description' => 'JSON encoded data',
        ) ,
        'level' => array(
          'name' => 'level',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Detailed Log Data') ,
          'description' => 'error level per PSR3',
          'maxlength' => 9,
          'size' => CRM_Utils_Type::TWELVE,
          'default' => 'info',
        ) ,
        'timestamp' => array(
          'name' => 'timestamp',
          'type' => CRM_Utils_Type::T_TIMESTAMP,
          'title' => ts('Log Timestamp') ,
          'description' => 'Timestamp of when event occurred.',
          'default' => 'CURRENT_TIMESTAMP',
        ) ,
        'contact_id' => array(
          'name' => 'contact_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Log Contact ID') ,
          'description' => 'Optional Contact ID that created the log. Not an FK as we keep this regardless',
        ) ,
        'hostname' => array(
          'name' => 'hostname',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Log Host') ,
          'description' => 'Optional Name of logging host',
          'maxlength' => 128,
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
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'system_log', $prefix, array());
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
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'system_log', $prefix, array());
    return $r;
  }
}
