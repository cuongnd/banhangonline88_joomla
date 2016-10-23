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
namespace OAuth\Common\Storage\Exception;
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Exception thrown when a token is not found in storage.
 */
class TokenNotFoundException extends StorageException
{
}
