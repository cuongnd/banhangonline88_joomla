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
 * Exception thrown when the state parameter received during the authorization process is invalid.
 */
class InvalidAuthorizationStateException extends \Exception
{
}
