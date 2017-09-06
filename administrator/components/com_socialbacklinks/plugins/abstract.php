<?php
/**
 * SocialBacklinks Basic adapter for plugins
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
 * Basic adapter for SocialBacklinks plugins
 * @abstract
 */
abstract class SBPluginsAbstract implements SBPluginsInterface
{
	/**
	 * The list of plugin options
	 * @var ArrayObject
	 */
	private $_options;

	/**
	 * The list of default options
	 * @var ArrayObject
	 */
	private $_default;

	/**
	 * The caller class
	 * @var string
	 */
	private $_caller;

	/**
	 * Constructor
	 * @param  JPlugin Object that has registered current plugin
	 * @param  array  The list of plugin options
	 * @return void
	 */
	public function __construct( $caller, $options )
	{
		$this->_caller = get_class( $caller );
		$options = array_merge( array(
			'icon' => null,
			'title' => 'Yet another Plugin'
		), $options );
		$this->_default = new ArrayObject( $options );
	}

	/**
	 * Returns the option of the plugin
	 * @param  string Option name
	 * @return mixed
	 */
	public function getOption( $option )
	{
		$this->_checkOptions( );
		return ($this->_options->offsetExists( $option )) ? $this->_options->offsetGet( $option ) : null;
	}

	/**
	 * Returns the list of plugin options
	 * @return array
	 */
	public function getOptions( )
	{
		$this->_checkOptions( );
		return (array)$this->_options;
	}

	/**
	 * Sets to plugin saved in the db list of options
	 * @return void
	 */
	private function _checkOptions( )
	{
		if ( is_null( $this->_options ) ) {
			$this->_options = new ArrayObject( );
			$options = JModelLegacy::getInstance( 'SBModelsPlugins' )->section( $this->getType( ) )->name( $this->getAlias( ) )->getList( );
			$this->setOptions( array_merge( (array)$this->_default, $options ) );
		}
	}

	/**
	 * Sets the option of the plugin
	 * @param  string Option name
	 * @param  mixed  Option value
	 * @return void
	 */
	public function setOption( $option, $value )
	{
		$this->_checkOptions( );
		$this->_options->offsetSet( $option, $value );
	}

	/**
	 * Sets the list of plugin options
	 * @param  array List of options
	 * @return void
	 */
	public function setOptions( $options )
	{
		foreach ($options as $key => $value) {
			$this->setOption( $key, $value );
		}
	}

	/**
	 * Magic get method. Returns plugin option
	 * @param  string Option to be returned. If null, will return the list of options
	 * @return mixed
	 */
	public function __get( $option = null )
	{
		return is_null( $option ) ? $this->getOptions( ) : $this->getOption( $option );
	}

	/**
	 * Magic set method. Sets the option and returns plugin instance
	 * @param string|array Option name ot the list of options to be applied
	 * @param mixed        Option value
	 * @return SBPluginInterface
	 */
	public function __set( $option, $value = null )
	{
		if ( is_array( $option ) ) {
			$this->setOptions( $option );
		}
		else {
			$this->setOption( $option, $value );
		}
		return $this;
	}

	/**
	 * Returns plugin's type
	 * @return string
	 */
	public function getType( )
	{
		$r = null;
		if ( !@preg_match( '/SBPlugins(.*)sInterface/i', array_shift( class_implements( $this ) ), $r ) ) {
			throw new SBException( JText::_( 'SB_CANT_GET_INTERFACE_NAME' ) );
		}
		return strtolower( $r[1] );
	}

	/**
	 * Returns class of caller
	 * @return string
	 */
	public function getCaller( )
	{
		return $this->_caller;
	}

	/**
	 * Saves options
	 * @return void
	 */
	public function save( )
	{
		if ( !is_null( $this->_options ) ) {
			$data = array(
				'section' => $this->getType( ),
				'name' => $this->getAlias( ),
				'value' => $this->getOptions( )
			);
			JModelLegacy::getInstance( 'SBModelsPlugins' )->reset()->setData( $data )->update( );
		}
	}

}
