<?php
/**
 * SocialBacklinks config model
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
 * SocialBacklinks Model Config class, which manage different configuration
 */
class SBModelsConfig extends SBModelsBase
{
	/**
	 * Updates configuration record or create new one
	 * @return boolean
	 */
	public function update( )
	{
		$data = $this->getData( );
		$section = $data->get( 'section' );
		$name = $data->get( 'name' );
		$value = $this->getData( )->get( 'value' );

		if ( $id = $data->get( 'id', 0 ) ) {
			$params = $data->getProperties( );
			if ( $item = $this->reset( )->id( $id )->getItem( ) ) {
				return $item->save( $params );
			}
		}
		else {
			$this->reset( )->ignore( 'value' )->applyData( );

			$data = $data->getProperties( );

			if ( $item = $this->getItem( ) ) {
				if ( $item->check( ) )
					return $item->save( $data );
			}
			elseif ( $item = $this->createItem( $data ) ) {
				if ( $item->check( ) )
					return $item->store( );
			}
		}
		return false;
	}

	/**
	 * Deletes configuration record
	 * @return boolean
	 */
	public function delete( )
	{
		$data = $this->getData( );

		$id = ( int )$data->get( 'id', 0 );
		$table = $this->getTable( );

		if ( $id ) {
			return $table->delete( $id );
		}
		else {
			$this->reset( )->applyData( );

			if ( $item = $this->getItem( ) ) {
				return $item->delete( );
			}
			else 
				return false;
		}
	}

}
