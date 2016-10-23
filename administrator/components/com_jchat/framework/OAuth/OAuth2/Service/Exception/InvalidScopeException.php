<?php
// namespace administrator\components\com_jchat\framework;
/**
 *
 * @package JCHAT::CONFIG::administrator::components::com_jchat
 * @subpackage framework
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace OAuth\OAuth2\Service\Exception;
defined ( '_JEXEC' ) or die ( 'Restricted access' );
/**
 * @author David Desberg <david@daviddesberg.com>
 * Released under the MIT license.
 */


use OAuth\Common\Exception\Exception;

/**
 * Exception thrown when a scope provided to a service is invalid.
 */
class InvalidScopeException extends Exception
{
}
