<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

require_once( dirname( __FILE__ ) . '/helpers/simplehtml.php' );

jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );

/**
 * Class provides methods to crawl websites and retrieve the contents.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 *
 */
class SocialCrawler
{
	/**
	 * Available hooks.
	 * @var	Array
	 */
	private $hooks	= array();

	/**
	 * Raw contents
	 * @var	string
	 */
	private $contents	= null;

	/**
	 * Class constructor.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function __construct()
	{
		// Load all adapters
		// $this->loadAdapters();
	}

	/**
	 *
	 */
	public function factory()
	{
		$obj 	= new self();

		return $obj;
	}

	/**
	 * Invoke the crawling.
	 *
	 * @since	1.0
	 * @access	public
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function crawl( $url )
	{
		// Ensure that urls always contains a protocol
		if( stristr( $url , 'http://') === false && stristr( $url , 'https://') === false )
		{
			$url 	= 'http://' . $url;
		}

		// Load up the connector first.
		$connector 		= FD::get( 'Connector' );
		$connector->addUrl( $url );
		$connector->connect();

		// Get the result and parse them.
		$info			= parse_url( $url );
		$this->contents	= $connector->getResult( $url );

		// Replace any href="// with href="scheme://"
		$this->contents 		= str_ireplace( 'src="//' , 'src="' . $info['scheme' ] . '://' , $this->contents );

		// Get the final url, if there's any redirection.
		$originalUrl 	= $url;
		$url 			= $connector->getFinalUrl( $url );

		$this->parse( $originalUrl , $url );

		return $this;
	}

	/**
	 * Loads adapters into the current namespace allowing the processing part
	 * to call these adapters.
	 *
	 * @param	string		The URL
	 * @return	boolean		True on success, false if no adapters found.
	 */
	private function parse( $originalUrl , $url )
	{
		// Load available hooks.
		// @TODO: Allow 3rd party to add their own custom rules in the future.
		$hooks 	= JFolder::files( dirname( __FILE__ ) . '/hooks' );

		$parser	= SocialSimpleHTML::str_get_html( $this->contents );

		if (!$parser) {
			return false;
		}

		$info = parse_url( $url );
		$uri = $info[ 'scheme' ] . '://' . $info[ 'host' ];
		$absoluteUrl = $url;

		foreach ($hooks as $hook) {
			$file 	= SOCIAL_LIB . '/crawler/hooks/' . $hook;

			require_once($file);
			$name = str_ireplace( '.php' , '' , $hook );

			$class		= 'SocialCrawler' . ucfirst( $name );

			// When item doesn't exist set it to false.
			if (!class_exists($class)) {
				continue;
			}

			$obj = new $class();

			$result = $obj->process($parser, $this->contents, $uri, $absoluteUrl, $originalUrl, $this->hooks);

			$this->hooks[$name] = $result;
		}

		return true;
	}

	/**
	 * Retrieves the hooks values.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getData()
	{
		return $this->hooks;
	}
}
