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
 * Generated from xml/schema/CRM/ACL/ACL.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:5727b59234c51d62e5d279c582cbd95b)
 */
require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Type.php';
class CRM_ACL_DAO_ACL extends CRM_Core_DAO {
  /**
   * static instance to hold the table name
   *
   * @var string
   */
  static $_tableName = 'civicrm_acl';
  /**
   * static value to see if we should log any modifications to
   * this table in the civicrm_log table
   *
   * @var boolean
   */
  static $_log = false;
  /**
   * Unique table ID
   *
   * @var int unsigned
   */
  public $id;
  /**
   * ACL Name.
   *
   * @var string
   */
  public $name;
  /**
   * Is this ACL entry Allow  (0) or Deny (1) ?
   *
   * @var boolean
   */
  public $deny;
  /**
   * Table of the object possessing this ACL entry (Contact, Group, or ACL Group)
   *
   * @var string
   */
  public $entity_table;
  /**
   * ID of the object possessing this ACL
   *
   * @var int unsigned
   */
  public $entity_id;
  /**
   * What operation does this ACL entry control?
   *
   * @var string
   */
  public $operation;
  /**
   * The table of the object controlled by this ACL entry
   *
   * @var string
   */
  public $object_table;
  /**
   * The ID of the object controlled by this ACL entry
   *
   * @var int unsigned
   */
  public $object_id;
  /**
   * If this is a grant/revoke entry, what table are we granting?
   *
   * @var string
   */
  public $acl_table;
  /**
   * ID of the ACL or ACL group being granted/revoked
   *
   * @var int unsigned
   */
  public $acl_id;
  /**
   * Is this property active?
   *
   * @var boolean
   */
  public $is_active;
  /**
   * class constructor
   *
   * @return civicrm_acl
   */
  function __construct() {
    $this->__table = 'civicrm_acl';
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
          'title' => ts('ACL ID') ,
          'description' => 'Unique table ID',
          'required' => true,
        ) ,
        'name' => array(
          'name' => 'name',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('ACL Name') ,
          'description' => 'ACL Name.',
          'maxlength' => 64,
          'size' => CRM_Utils_Type::BIG,
          'html' => array(
            'type' => 'Text',
          ) ,
        ) ,
        'deny' => array(
          'name' => 'deny',
          'type' => CRM_Utils_Type::T_BOOLEAN,
          'title' => ts('Deny ACL?') ,
          'description' => 'Is this ACL entry Allow  (0) or Deny (1) ?',
          'required' => true,
          'html' => array(
            'type' => 'Radio',
          ) ,
        ) ,
        'entity_table' => array(
          'name' => 'entity_table',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('ACL Entity') ,
          'description' => 'Table of the object possessing this ACL entry (Contact, Group, or ACL Group)',
          'required' => true,
          'maxlength' => 64,
          'size' => CRM_Utils_Type::BIG,
        ) ,
        'entity_id' => array(
          'name' => 'entity_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Entity ID') ,
          'description' => 'ID of the object possessing this ACL',
        ) ,
        'operation' => array(
          'name' => 'operation',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('ACL Operation') ,
          'description' => 'What operation does this ACL entry control?',
          'required' => true,
          'maxlength' => 8,
          'size' => CRM_Utils_Type::EIGHT,
          'html' => array(
            'type' => 'Select',
          ) ,
          'pseudoconstant' => array(
            'callback' => 'CRM_ACL_BAO_ACL::operation',
          )
        ) ,
        'object_table' => array(
          'name' => 'object_table',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('ACL Object') ,
          'description' => 'The table of the object controlled by this ACL entry',
          'maxlength' => 64,
          'size' => CRM_Utils_Type::BIG,
        ) ,
        'object_id' => array(
          'name' => 'object_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('ACL Object ID') ,
          'description' => 'The ID of the object controlled by this ACL entry',
        ) ,
        'acl_table' => array(
          'name' => 'acl_table',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('ACL Table') ,
          'description' => 'If this is a grant/revoke entry, what table are we granting?',
          'maxlength' => 64,
          'size' => CRM_Utils_Type::BIG,
        ) ,
        'acl_id' => array(
          'name' => 'acl_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('ACL Group ID') ,
          'description' => 'ID of the ACL or ACL group being granted/revoked',
        ) ,
        'is_active' => array(
          'name' => 'is_active',
          'type' => CRM_Utils_Type::T_BOOLEAN,
          'title' => ts('ACL Is Active?') ,
          'description' => 'Is this property active?',
          'html' => array(
            'type' => 'Checkbox',
          ) ,
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
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'acl', $prefix, array());
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
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'acl', $prefix, array());
    return $r;
  }
}
