<?php
// namespace components\com_jchat\libraries\http\transport;
/**
 * @package JCHAT::LIBRARIES::components::com_jchat
 * @subpackage libraries
 * @subpackage http
 * @subpackage transport
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');

/**
 * HTTP transport class interface.
 *
 * @package JCHAT::LIBRARIES::components::com_jchat
 * @subpackage libraries
 * @subpackage http
 * @subpackage transport
 * @since 1.0
 */
interface JChatHttpTransport {

	/**
	 * Send a request to the server and return a JChatHttpResponse object with the response.
	 *
	 * @param   string   $method     The HTTP method for sending the request.
	 * @param   JUri     $uri        The URI to the resource to request.
	 * @param   mixed    $data       Either an associative array or a string to be sent with the request.
	 * @param   array    $headers    An array of request headers to send with the request.
	 * @param   integer  $timeout    Read timeout in seconds.
	 * @param   string   $userAgent  The optional user agent string to send with the request.
	 *
	 * @return  JChatHttpResponse
	 *
	 * @since   11.3
	 */
	public function request($method, JUri $uri, $data = null, array $headers = null, $timeout = null, $userAgent = null);
}
