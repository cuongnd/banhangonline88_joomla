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
 * Generated from xml/schema/CRM/Mailing/MailingGroup.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:da1368978867c9bcae28f74bdf76f199)
 */
require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Type.php';
class CRM_Mailing_DAO_MailingGroup extends CRM_Core_DAO {
  /**
   * static instance to hold the table name
   *
   * @var string
   */
  static $_tableName = 'civicrm_mailing_group';
  /**
   * static value to see if we should log any modifications to
   * this table in the civicrm_log table
   *
   * @var boolean
   */
  static $_log = false;
  /**
   *
   * @var int unsigned
   */
  public $id;
  /**
   * The ID of a previous mailing to include/exclude recipients.
   *
   * @var int unsigned
   */
  public $mailing_id;
  /**
   * Are the members of the group included or excluded?.
   *
   * @var string
   */
  public $group_type;
  /**
   * Name of table where item being referenced is stored.
   *
   * @var string
   */
  public $entity_table;
  /**
   * Foreign key to the referenced item.
   *
   * @var int unsigned
   */
  public $entity_id;
  /**
   * The filtering search. custom search id or -1 for civicrm api search
   *
   * @var int
   */
  public $search_id;
  /**
   * The arguments to be sent to the search function
   *
   * @var text
   */
  public $search_args;
  /**
   * class constructor
   *
   * @return civicrm_mailing_group
   */
  function __construct() {
    $this->__table = 'civicrm_mailing_group';
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
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName() , 'mailing_id', 'civicrm_mailing', 'id');
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Dynamic(self::getTableName() , 'entity_id', NULL, 'id', 'entity_table');
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
        'id' => array(
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Mailing Group ID') ,
          'required' => true,
        ) ,
        'mailing_id' => array(
          'name' => 'mailing_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Mailing') ,
          'description' => 'The ID of a previous mailing to include/exclude recipients.',
          'required' => true,
          'FKClassName' => 'CRM_Mailing_DAO_Mailing',
        ) ,
        'group_type' => array(
          'name' => 'group_type',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Mailing Group Type') ,
          'description' => 'Are the members of the group included or excluded?.',
          'maxlength' => 8,
          'size' => CRM_Utils_Type::EIGHT,
          'html' => array(
            'type' => 'Select',
          ) ,
          'pseudoconstant' => array(
            'callback' => 'CRM_Core_SelectValues::getMailingGroupTypes',
          )
        ) ,
        'entity_table' => array(
          'name' => 'entity_table',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Mailing Group Entity Table') ,
          'description' => 'Name of table where item being referenced is stored.',
          'required' => true,
          'maxlength' => 64,
          'size' => CRM_Utils_Type::BIG,
          'pseudoconstant' => array(
            'callback' => 'CRM_Mailing_BAO_Mailing::mailingGroupEntityTables',
          )
        ) ,
        'entity_id' => array(
          'name' => 'entity_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Mailing Group Entity') ,
          'description' => 'Foreign key to the referenced item.',
          'required' => true,
        ) ,
        'search_id' => array(
          'name' => 'search_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Mailing Group Search') ,
          'description' => 'The filtering search. custom search id or -1 for civicrm api search',
        ) ,
        'search_args' => array(
          'name' => 'search_args',
          'type' => CRM_Utils_Type::T_TEXT,
          'title' => ts('Mailing Group Search Arguments') ,
          'description' => 'The arguments to be sent to the search function',
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
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'mailing_group', $prefix, array());
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
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'mailing_group', $prefix, array());
    return $r;
  }
}
