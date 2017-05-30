<?php
// namespace components\com_jchat\libraries\http;
/**
 * @package JCHAT::LIBRARIES::components::com_jchat
 * @subpackage libraries
 * @subpackage http
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');

/**
 * HTTP response data object class.
 *
 * @package JCHAT::LIBRARIES::components::com_jchat
 * @subpackage libraries
 * @subpackage http
 * @since 1.0
 */
class JChatHttpResponse {
	/**
	 * @var    integer  The server response code.
	 * @since  11.3
	 */
	public $code;

	/**
	 * @var    array  Response headers.
	 * @since  11.3
	 */
	public $headers = array();

	/**
	 * @var    string  Server response body.
	 * @since  11.3
	 */
	public $body;
}
