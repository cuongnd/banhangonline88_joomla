<?php
/**
 * SocialBacklinks Back-End
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
 * Request Wrapper
 */
class SBControllersLibsRequest
{
	/**
	 * The request data
	 * @var ArrayObject
	 */
	protected $_data;

	/**
	 * Constructor
	 * @param  array Data to be set
	 * @return void
	 */
	public function __construct( $data = array() )
	{
		$this->_data = new ArrayObject( array( ) );

		foreach ($data as $key => $value) {
			if ( is_array( $value ) ) {
				$value = new ArrayObject( $value );
			}
			$this->_data->offsetSet( $key, $value );
		}
	}

	/**
	 * Returns the value from the request. If object's value is empty it will try to get from JRequest by assigned {$type}
	 * @param  string Parameter to be returned.  'post.var' will return value of 'var' from post hash, 'cmd' will search everywhere
	 * @param  mixed  Default value
	 * @param  string $type Parameter type
	 * @return mixed
	 */
	public function get( $param, $default = null, $type = 'cmd' )
	{
		if ( strpos( $param, '.' ) !== false ) {
			list( $hash, $param ) = explode( '.', $param );
		}
		else {
			$hash = 'default';
		}

		if ( $this->_data->offsetExists( $param ) ) {
			return $this->_data->offsetGet( $param );
		}
		else {
			$method = 'get' . ucfirst( $type );
			if ( method_exists( 'JRequest', $method ) ) {
				return JRequest::$method( $param, $default, $hash );
			}
			else {
				return JRequest::getVar( $param, $default, $hash, $type );
			}

		}
		return $default;
	}

	/**
	 * Returns all hash data
	 */
	public function getHash( $hash )
	{
		return JRequest::get( $hash );
	}

	/**
	 * Magic method, returns data from request
	 * @param string Parameter ro be returned
	 * @return mixed
	 */
	public function __get( $param )
	{
		return $this->get( $param );
	}

	/**
	 * Checks existing parameter in request
	 * @param  string Key
	 * @return bool
	 */
	public function __isset( $param )
	{
		return $this->_data->offsetExists( $param );
	}

	/**
	 * Adds to requests list of values
	 * @param array The list of values
	 * @return void
	 */
	public function set( $data = array() )
	{
		foreach ($data as $key => $value) {
			if ( is_array( $value ) ) {
				$value = new ArrayObject( $value );
			}
			$this->_data->offsetSet( $key, $value );
		}
	}

}
