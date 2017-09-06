<?php
/**
 * SocialBacklinks plugins model
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
 * SocialBacklinks Model Plugins class, which manage different plugins
 */
class SBModelsPlugins extends SBModelsConfig
{
	/**
	 * @see SBModelsBase
	 */
	protected $_table = 'config';

	/**
	 * @see SBModelsBase
	 */
	public function getList( $where = array(), $limitstart = 0, $limit = 0 )
	{
		$result = array( );

		if ( ($item = parent::getItem( )) && isset( $item->value ) ) {
			$value = json_decode( $item->value, true );
			if ( is_array( $value ) ) {
				$result = $value;
			}
		}
		return $result;
	}

	/**
	 * @see SBModelsBase
	 */
	public function update( )
	{
		$data = $this->getData( );
		$section = $data->get( 'section' );
		$name = $data->get( 'name' );
		$value = $this->getData( )->get( 'value' );
		$item = $this->reset( )->section( $section )->name( $name )->getItem( );

		if ( !empty( $item ) && !empty( $item->value ) ) {
			$item_value = json_decode( $item->value, true );
			if ( !is_array( $item_value ) ) {
				$item_value = array( );
			}
		}
		else {
			$item_value = array( );
		}

		$this->setData( 'value', json_encode( array_merge( $item_value, $value ) ), false );
		return parent::update( );
	}

}
