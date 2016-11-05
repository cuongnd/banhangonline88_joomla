<?php
/**
 * SocialBacklinks Base abstract table
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

/**
 * SocialBacklinks Base abstract table
 */
abstract class SBTablesBase extends JTable
{
	/**
	 * Retrieve row field value
	 * @param  	string 	The user-specified column name.
	 * @return 	string 	The corresponding column value.
	 */
	public function __get( $column_name )
	{
		if ( $column_name == 'id' ) {
			$column_name = $this->getKeyName( );
		}
		return $this->get( $column_name );
	}
	
	/**
	 * Set row field value
	 * @param  	string 	The user-specified column name.
	 * @return 	string 	The corresponding column value.
	 */
	public function __set( $column_name, $value )
	{
		if ( $column_name == 'id' ) {
			$column_name = $this->getKeyName( );
		}
		return $this->set( $column_name, $value );
	}
	
	/**
	 * Resets the default properties
	 * @return	void
	 */
	public function reset( )
	{
		$k = $this->_tbl_key;
		$this->$k = 0;
		
		parent::reset();
	}
	
}
