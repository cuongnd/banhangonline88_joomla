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
namespace OAuth\OAuth2\Token;
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use OAuth\Common\Token\AbstractToken;

/**
 * Standard OAuth2 token implementation.
 * Implements OAuth\OAuth2\Token\TokenInterface for any functionality that might not be provided by AbstractToken.
 */
class StdOAuth2Token extends AbstractToken implements TokenInterface
{
}
