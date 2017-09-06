<?php
/**
 * SocialBacklinks Base abstract model
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and change.
 * Otherwise, please feel free to contact us at contact@joomunited.com
 *
 * @package 	Social Backlinks
 * @copyright 	Copyright (C) 2012 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @license 	GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */
 
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );

/**
 * SocialBacklinks Base abstract model
 * @abstract
 */
abstract class SBModelsBase extends JModelLegacy
{
	/**
	 * Data object. Stores the date to be saved
	 * @var	JObject
	 */
	protected $_data;

	/**
	 * Default table
	 * @var string
	 */
	protected $_table = '';

	/**
	 * The list of fields to be ignored in Where clause
	 * @var array
	 */
	protected $_ignore_where_fields = array(
		'select',
		'join',
		'filter_order',
		'filter_order_Dir',
		'limit',
		'group_by',
		'limitstart'
	);

	/**
	 * Constructor
	 * @return void
	 */
	public function __construct( $config = array( ) )
	{
		if ( array_key_exists( 'data', $config ) ) {
			$this->_data = $config['data'];
		}
		else {
			$this->_data = new JObject( );
		}
		parent::__construct( $config );
		
		$this->option = 'com_socialbacklinks';
	}

	/**
	 * Magic method. Sets states
	 * @param string Property name
	 * @param mixed  Value
	 * @return SBModelsBase
	 */
	public function __call( $property, $arguments = array() )
	{
		$arg = count( $arguments ) ? $arguments[0] : null;
		$this->setState( $property, $arg );
		return $this;
	}

	/**
	 * Magic method. Returns state
	 * @param string Prioperty to be returned
	 * @return mixed
	 */
	public function __get( $property )
	{
		return parent::getState( $property );
	}

	/**
	 * Sets model data variables
	 * @param	mixed	$property	The name of the property or array of params
	 * @param	mixed	$value		The value of the property to set
	 * @param	boolean	$reset		Show must we reset the array or not
	 * @return	SBModelsBase
	 */
	public function setData( $property, $value = null, $reset = true )
	{
		if ( $reset ) {
			$this->_data = new JObject( );
		}

		if ( is_array( $property ) ) {
			foreach ($property as $name => $value) {
				$this->_data->set( $name, $value );
			}
		}
		elseif ( is_object( $property ) ) {
			foreach (get_object_vars( $property ) as $name => $value) {
				$this->_data->set( $name, $value );
			}
		}
		else {
			$this->_data->set( $property, $value );
		}
		return $this;
	}

	/**
	 * Returns model data variables
	 * @param	string	Optional parameter name
	 * @return	object	The property where specified, the data object where omitted
	 */
	public function getData( $property = null )
	{
		return ($property === null) ? $this->_data : $this->_data->get( $property );
	}
	
	/**
	 * Applies data to model states
	 * @return SBModelsBase
	 */
	public function applyData( )
	{
		foreach ($this->getData()->getProperties() as $key => $value) {
			$this->setState( $key, $value );
		}
		return $this;
	}

	/**
	 * @see JModelLegacy::getTable()
	 */
	public function getTable( $name = '', $prefix = 'SBTables', $options = array( ) )
	{
		$name = empty( $name ) ? $this->_table : $name;
		return parent::getTable( $name, $prefix, $options );
	}

	/**
	 * Resets states
	 * @return SBModelsBase
	 */
	public function reset( )
	{
		$this->state = new JObject( );
		return $this;
	}

	/**
	 * Returns count of all rows
	 * @return integer
	 */
	public function getTotal( $where = array() )
	{
		$query = $this->_buildSelect( ) . $this->_buildJoin( ) . $this->_buildWhere( $where );
		return $this->_getListCount( $query );
	}

	/**
	 * @see JModelLegacy::getName()
	 */
	public function getName( )
	{
		$name = $this->name;

		if ( empty( $name ) ) {
			$r = null;
			if ( !preg_match( '/Models(.*)/i', get_class( $this ), $r ) ) {
				JError::raiseError(500, JText::_('JLIB_APPLICATION_ERROR_MODEL_GET_NAME'));
			}
			$name = strtolower( $r[1] );
		}

		return $name;
	}

	/**
	 * Builds select statements
	 * @return string
	 */
	protected function _buildSelect( )
	{
		$select = $this->select;
		if ( empty( $select ) ) {
			$select = '* FROM ' . $this->getTable( )->getTableName( );
		}
		$query = str_replace( 'SELECT DISTINCT SELECT', 'SELECT DISTINCT', "SELECT DISTINCT $select " );
		$query = str_replace( 'DISTINCT DISTINCT', 'DISTINCT', $query);
		return $query;
	}

	/**
	 * Builds Join statements
	 * @return string
	 */
	protected function _buildJoin( )
	{
		if ( !$join = $this->join ) {
			$join = array( );
		};
		$query = '';
		foreach ($join as $key => $value) {
			$query .= " $value ";
		}
		return $query;
	}
	
	/**
	 * Builds Where clauses for the query
	 * @param array  The list of predefined where clauses
	 * @param bool   Responsible for converting array to string
	 * @return string|array
	 */
	protected function _buildWhere( $where = array(), $to_string = true )
	{
		if ( $to_string ) {
			foreach ($this->getState()->getProperties() as $state => $value) {
				if ( !empty( $value ) && !in_array( $state, $this->_ignore_where_fields ) ) {
					if ( strpos( $state, '.' ) !== false ) {
						list( $prefix, $state ) = explode( '.', $state );
						$where[] = "$prefix.`$state` = '$value'";
					}
					else {
						$where[] = "`$state` = '$value'";
					}
				}
			}
			$where = (count( $where ) > 0) ? (' WHERE ( ' . implode( ' ) AND ( ', $where ) . ' )') : '';
		}
		else {
			foreach ($this->getState()->getProperties() as $state => $value) {
				if ( !empty( $value ) && !in_array( $state, $this->_ignore_where_fields ) ) {
					$where[$state] = $value;
				}
			}
		}
		return $where;
	}

	/**
	 * Adds field to ignore list
	 * @param  string|array Property or the list of properties to be ignoored
	 * @return SBModelsBase
	 */
	public function ignore( $property )
	{
		if ( is_array( $property ) ) {
			$this->_ignore_where_fields = array_merge( $this->_ignore_where_fields, $property );
		}
		else {
			$this->_ignore_where_fields[] = $property;
		}
		return $this;
	}

	/**
	 * Builds Group clauses
	 * @return string
	 */
	protected function _buildGroupBy( )
	{
		if ( $group = $this->group_by ) {
			return ' GROUP BY ' . $this->group_by;
		}
		else {
			return '';
		}
	}

	/**
	 * Builds query by Order
	 * @return string
	 */
	protected function _buildOrder( )
	{
		if ( ($filter_order = $this->filter_order) && ($filter_order_Dir = $this->filter_order_Dir) ) {
			return ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir;
		}
		return '';
	}

	/**
	 * Returns the list of rows
	 * @param  array The list of predefined where clauses
	 * @param  int Limit start
	 * @param  int Limit count
	 * @return array
	 */
	public function getList( $where = array(), $limitstart = 0, $limit = 0 )
	{
		$query = $this->_buildSelect( ) . $this->_buildJoin( ) . $this->_buildWhere( $where ) . $this->_buildGroupBy( ) . $this->_buildOrder( );
		return $this->_getList( $query, $limitstart, $limit );
	}

	/**
	 * Returns the item from the table
	 * @param  array The list of predefined where clauses
	 * @return JTable
	 */
	public function getItem( $where = array() )
	{
		$where = $this->_buildWhere( $where, false );
		$table = $this->getTable( );
		return $table->load( $where ) ? $table : null;
	}

	/**
	 * Returns an instance of table
	 * @param  array Values to be binded to table
	 * @return JTable|null
	 */
	public function createItem( $properties = array() )
	{
		$table = $this->getTable( );
		return $table->bind( $properties ) ? $table : null;
	}

}
