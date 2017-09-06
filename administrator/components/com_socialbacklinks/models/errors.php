<?php
/**
 * SocialBacklinks errors model
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
 * SocialBacklinks Model Errors class, which manage different configuration
 */
class SBModelsErrors extends SBModelsBase
{
	/**
	 * @see SBModelsBase::$_table
	 */
	protected $_table = 'error';

	/**
	 * Updates error record or create new one
	 * @return boolean
	 */
	public function update( )
	{
		$data = $this->getData( );

		if ( $id = $data->get( 'id', 0 ) ) {
			$params = $data->getProperties( );
			if ( $item = $this->reset( )->id( $id )->getItem( ) ) {
				return $item->save( $params );
			}
		}
		else {
			$this->reset( )->applyData( )->ignore( array(
				'created',
				'message'
			) );

			if ( ($item = $this->getItem()) || ($item = $this->createItem( $data->getProperties() )) ) {
				$item->created = $data->get( 'created' );
				$item->message = $data->get( 'message' );

				if ( $item->check( ) ) {
					return $item->store( );
				}
			}
		}
	}

	/**
	 * Deletes error item
	 * @return boolean
	 */
	public function delete( )
	{
		$data = $this->getData( );

		$items = $data->get( 'cid', 0 );
		$table = $this->getTable( );

		$success = true;
		if ( $items ) {
			if ( !is_array( $items ) ) {
				$items = array( $items );
			}
			foreach ($items as $item) {
				if ( !$table->delete( $item ) ) {
					$success = false;
				}
			}
		}
		return $success;
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
		$this->ignore( array('plugin', 'last_id') );
		if ( !$plugin = $this->plugin ) {
			return array( );
		}
		$select = $this->select ? $this->select : ('`' . $plugin->get( 'items_table.title' ) . '` AS title');
		if ( ($select == '*') && ($plugin->get( 'items_table.title' ) != 'title') ) {
			$select = '*, plugin.`' . $plugin->get( 'items_table.title' ) . '` AS title';
		}

		$table = $plugin->get( 'items_table.__table' );
		$id = $plugin->get( 'items_table.id' );
		$this->select( "errors.*, plugin.$select FROM " . $this->getTable( )->getTableName( ) . ' AS errors' );
		$this->join( array( "JOIN $table AS plugin ON errors.`item_id`=plugin.`$id`" ) );
		$this->setState( 'errors.extension', $plugin->getAlias( ) );
		
		$last_id = ( int ) $this->getState( 'last_id', 0 );
		if ( $last_id ) {
			$where[] = "`socialbacklinks_error_id` > {$last_id}";
		}
		$ret = null;
		try
		{
			$ret = parent::getList( $where, $limitstart, $limit );
		}
		catch (Exception $e)
		{
		}
		return $ret;
	}

}
