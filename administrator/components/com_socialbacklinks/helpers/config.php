<?php
/**
 * SocialBacklinks Configuration helper file
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
 * SocialBacklinks Configuration helper class
 *
 * @static
 */
class SBHelpersConfig extends JObject
{
	/**
	 * Model of the config
	 * @var SBModelsConfig
	 */
	protected static $_model;

	/**
	 * The data of config table
	 * @var stdClass
	 */
	protected static $_data;

	/**
	 * The ids of config table
	 * @var stdClass
	 */
	protected static $_ids;

	/**
	 * Returns model of the config
	 * @return SBModelsConfig
	 */
	protected static function _getModel( )
	{
		if ( self::$_model === null ) {
			return JModelLegacy::getInstance( 'config', 'SBModels' );
		}
		else {
			return self::$_model;
		}
	}

	/**
	 * Returns the config data
	 * @return stdClass
	 */
	protected static function _getData( )
	{
		if ( self::$_data === null ) {
			$list = self::_getModel( )->getList( );
			$data = array( );
			$ids = array( );
			foreach ($list as $item) {
				$data[$item->section][$item->name] = $item->value;
				$ids[$item->section][$item->name] = $item->socialbacklinks_config_id;
			}
			foreach ($data as $key => $value) {
				if ( is_array( $value ) ) {
					$data[$key] = ( object )$value;
				}
			}
			foreach ($ids as $key => $value) {
				if ( is_array( $value ) ) {
					$ids[$key] = ( object )$value;
				}
			}
			self::$_data = ( object )$data;
			self::$_ids = ( object )$ids;
		}
		return self::$_data;
	}

	/**
	 * Builds a status massage based on current configuration
	 *
	 * @param 	array $data current configuration
	 *
	 * @return 	string
	 */
	public static function buildStatusMsg( array $data )
	{
		$msg = '';
		$url = JRoute::_( 'index.php?option=com_socialbacklinks&view=config&tmpl=component' );
		$link_start = '<a href="' . $url . '" class="modal" rel="{handler:\'iframe\',size:{x:800,y:250}}"' . ' onclick="SqueezeBox.fromElement(this, { parse: \'rel\' }); return false;">';
		$link_end = '</a>';

		if ( !empty( $data['sbsynchronizer'] ) ) {
			if ( $data['sync_periodicity'] > 1 ) {
				$msg = JText::sprintf( 'SB_N_CONTENT_SYNCED_AUTHOMATICALLY_MORE', $link_start, $link_end, $link_start, $data['sync_periodicity'], $link_end ) . '<br /><br />';
			}
			else {
				$msg = JText::sprintf( 'SB_N_CONTENT_SYNCED_AUTHOMATICALLY_1', $link_start, $link_end, $link_start, $link_end ) . '<br /><br />';
			}

			if ( isset( $data['errors_recipient_type'] ) ) {
				if ( $data['errors_recipient_type'] == 2 ) {
					$msg .= ' ' . JText::sprintf( 'SB_SEND_ERRORS_TO_SUPER_ADMINISTRATORS', $link_start, $link_end ) . '<br /><br />';
				}
				elseif ( $data['errors_recipient_type'] == 1 ) {
					$msg .= ' ' . JText::sprintf( 'SB_SEND_ERRORS_TO_ANOTHER_RECIPIENT', $link_start, $data['send_errors_email'], $link_end ) . '<br /><br />';
				}
			}
		}
		else {
			$msg = JText::sprintf( 'SB_CONTENT_SYNCED_MANUALLY', $link_start, $link_end ) . '<br /><br />';
		}
		if ( !empty( $data['clean_history'] ) ) {
			if ( $data['clean_history_periodicity'] > 1 ) {
				$msg .= ' ' . JText::sprintf( 'SB_HISTORY_IS_CLEARED_MORE', $link_start, $data['clean_history_periodicity'], $link_end ) . '<br /><br />';
			}
			else {
				$msg .= ' ' . JText::sprintf( 'SB_HISTORY_IS_CLEARED_1', $link_start, $link_end ) . '<br /><br />';
			}
		}
		else {
			$msg .= ' ' . JText::sprintf( 'SB_HISTORY_KEPT_FOREVER', $link_start, $link_end ) . '<br /><br />';
		}

		return $msg;
	}

	/**
	 * Returns the list of available networks
	 * @param  bool $all If true will return all networks, not only available
	 * @return array
	 */
	public static function getAvailableNetworks( $all = false )
	{
		$list = $all ? self::_getModel( )->reset( )->section( 'social' )->getList( ) : self::_getModel( )->reset( )->section( 'social' )->value( '1' )->getList( );
		$result = array( );
		foreach ($list as $value) {
			$result[] = $value->name;
		}
		return $result;
	}

	/**
	 * Returns the config value for the category
	 * If proprty is null it will return all config data
	 * @param  string $property
	 * @param  string $category
	 * @param  mixed  $default
	 * @return string
	 */
	public static function getProperty( $property = null, $category = 'basic', $default = null )
	{
		$data = self::_getData( );
		if ( $property === null ) {
			return ( array )$data;
		}
		if ( isset( $data->$category ) && isset( $data->$category->$property ) ) {
			return $data->$category->$property;
		}
		else {
			return $default;
		}
	}

	/**
	 * Sets the value to the cofig table
	 * @param  string $property
	 * @param  mixed $value
	 * @param  string $category
	 * @return void
	 */
	public static function setProperty( $property, $value, $category = 'basic' )
	{
		self::updateProperty( $property, $value, $category );
		self::_getModel( )->setData( array( 'section' => $category, 'name' => $property, 'value' => $value ) )->update( );
	}

	/**
	 * Updates the value only in the config table
	 * @param  string $property
	 * @param  mixed $value
	 * @param  string $category
	 * @return void
	 */
	public static function updateProperty( $property, $value, $category = 'basic' )
	{
		self::$_data->$category->$property = $value;
	}

	/**
	 * Returns properties of specified category
	 * @param  string $category
	 * @param  bool   $is_return_id If true it will return item ids instead data
	 * @return mixed
	 */
	public static function getCategory( $category, $is_return_id = false )
	{
		$data = self::_getData( );
		if ( $is_return_id ) {
			return isset( self::$_ids->$category ) ? self::$_ids->$category : null;
		}
		else
			return isset( $data->$category ) ? $data->$category : null;
	}

	/**
	 * Returns the id of the property
	 * @param  string $property
	 * @param  string $category
	 * @return int
	 */
	public static function getId( $property = null, $category = 'basic' )
	{
		$data = self::_getData( );
		if ( $property === null ) {
			return null;
		}
		if ( isset( $data->$category ) && isset( $data->$category->$property ) ) {
			return self::$_ids->$category->$property;
		}
		else {
			return null;
		}
	}

	/**
	 * Returns the Basic Config
	 * @uses SBHelpersRequirements::getPluginsStatusList( )
	 * @return array
	 */
	public static function getBasicConfig( )
	{
		$items = ( array )self::getCategory( 'basic' );

		// Get plugins status
		$rows = SBHelpersRequirements::getPluginsStatusList( );
		foreach ($rows as $name => $value) {
			if ( isset( $value->enabled ) ) {
				$items[$name] = $value->enabled;
			}
		}

		// Get default configuration
		if ( !isset( $items['sbsynchronizer'] ) ) {
			$items['sbsynchronizer'] = false;
		}
		if ( !isset( $items['sync_periodicity'] ) ) {
			$items['sync_periodicity'] = 5;
		}
		if ( !isset( $items['errors_recipient_type'] ) ) {
			$items['errors_recipient_type'] = 0;
		}
		if ( !isset( $items['send_errors_email'] ) ) {
			$items['send_errors_email'] = '';
		}
		if ( !isset( $items['clean_history'] ) ) {
			$items['clean_history'] = 1;
		}
		if ( !isset( $items['clean_history_periodicity'] ) ) {
			$items['clean_history_periodicity'] = 30;
		}
		return $items;
	}

}
